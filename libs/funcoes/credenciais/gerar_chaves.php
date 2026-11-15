<?php
session_start();

// Exibir erros (somente para desenvolvimento)
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Configurar um handler global para erros e exceções
set_exception_handler(function ($e) {
    http_response_code(500);
    echo json_encode([
        'statusCode' => 500,
        'error' => 'Internal Server Errorr',
        'message' => $e->getMessage(),
        'file' => $e->getFile(),
        'line' => $e->getLine()
    ]);
    exit;
});

set_error_handler(function ($severity, $message, $file, $line) {
    http_response_code(500);
    echo json_encode([
        'statusCode' => 500,
        'error' => 'Internal Server Errorr',
        'message' => $message,
        'file' => $file,
        'line' => $line
    ]);
    exit;
});

// Função para carregar as variáveis do arquivo .env
function loadEnv($file) {
    if (!file_exists($file)) {
        throw new Exception("Arquivo .env não encontrado!");
    }

    $lines = file($file, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);

    foreach ($lines as $line) {
        if (strpos(trim($line), '#') === 0) {
            continue;
        }

        list($key, $value) = explode('=', $line, 2);
        $key = trim($key);
        $value = trim($value);

        putenv("$key=$value");
        $_ENV[$key] = $value;
    }
}

// Carregar as variáveis do arquivo .env
loadEnv(__DIR__ . '/../../../.env');  // Ajuste o caminho conforme necessário

// Função para conectar ao banco de dados
function connectDb() {
    $dbHost = getenv('DB_HOST');
    $dbName = getenv('DB_NAME');
    $dbUser = getenv('DB_USER');
    $dbPass = getenv('DB_PASS');

    try {
        // Conexão PDO com o banco de dados
        $pdo = new PDO("mysql:host=$dbHost;dbname=$dbName", $dbUser, $dbPass);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        return $pdo;
    } catch (PDOException $e) {
        throw new Exception("Error ao conectar ao banco de dados: " . $e->getMessage());
    }
}

// Verificar se o usuário está autenticado
if (!isset($_SESSION['user_id'])) {
    echo json_encode(['statusCode' => 400, 'message' => 'Usuário não autenticado.']);
    exit;
}

// Receber os dados do form-data
$name = $_POST['nome'] ?? '';
$description = $_POST['descricao'] ?? '';
$domain = $_POST['dominio'] ?? '';  // Este é o domínio
$pin = $_POST['pin'] ?? '';  // Usado para validação, se necessário
$userId = $_SESSION['user_id'];  // O ID do usuário já está na sessão

// Validar se todos os campos foram recebidos
if (empty($name) || empty($description) || empty($domain) || empty($pin)) {
    echo json_encode(['statusCode' => 400, 'message' => 'Dados incompletos.']);
    exit;
}

// Conectar ao banco de dados
$pdo = connectDb();

// Verificar se o PIN do usuário está configurado
$stmt = $pdo->prepare("SELECT pin, username FROM users WHERE id = :user_id");
$stmt->bindParam(':user_id', $userId);
$stmt->execute();
$user = $stmt->fetch(PDO::FETCH_ASSOC);

// Verificar se o PIN está vazio
if (empty($user['pin'])) {
    echo json_encode(['statusCode' => 400, 'message' => 'Você precisa configurar um PIN primeiro.']);
    exit;
}

// Verificar se o PIN recebido é o mesmo do banco
if ($pin !== $user['pin']) {
    echo json_encode(['statusCode' => 401, 'message' => 'PIN incorreto.']);
    exit;
}

// Verificar se já existe um registro de integração para o usuário
$stmt = $pdo->prepare("SELECT id FROM users_integration_keys WHERE user_id = :user_id LIMIT 1");
$stmt->bindParam(':user_id', $userId);
$stmt->execute();

// Função para gerar um client_secret mais longo (usando SHA-256)
function generateClientSecret() {
    return hash('sha256', bin2hex(random_bytes(32))); // Gera um client_secret aleatório de 64 caracteres
}

// Função para gerar um client_id com base no nome de usuário e um número aleatório
function generateClientId($username) {
    return $username . '_' . rand(1000000000, 9999999999); // Exemplo: gutierry_8710507501
}

if ($stmt->rowCount() > 0) {
    // Se já existe, vamos atualizar o registro
    $stmt = $pdo->prepare("
        UPDATE users_integration_keys 
        SET name = :name, description = :description, client_id = :client_id, domain = :domain, client_secret = :client_secret, created_at = NOW()
        WHERE user_id = :user_id
    ");
    $stmt->bindParam(':name', $name);
    $stmt->bindParam(':description', $description);
    $stmt->bindParam(':domain', $domain);
    $stmt->bindParam(':user_id', $userId);

    // Gerar client_id e client_secret
    $clientSecret = generateClientSecret();
    $clientId = generateClientId($user['username']); // Usar o username da tabela users

    $stmt->bindParam(':client_id', $clientId);
    $stmt->bindParam(':client_secret', $clientSecret);
    $stmt->execute();

    // Response de sucesso
    $response = [
        'statusCode' => 200,
        'message' => [
            'client_id' => $clientId,
            'client_secret' => $clientSecret,
            'message' => 'Credenciais atualizadas'
        ]
    ];
} else {
    // Se não existe, vamos criar um novo registro
    $stmt = $pdo->prepare("
        INSERT INTO users_integration_keys (user_id, client_id, name, description, domain, client_secret, created_at)
        VALUES (:user_id, :client_id, :name, :description, :domain, :client_secret, NOW())
    ");
    $stmt->bindParam(':user_id', $userId);
    $stmt->bindParam(':name', $name);
    $stmt->bindParam(':description', $description);
    $stmt->bindParam(':domain', $domain);

    // Gerar client_id e client_secret
    $clientSecret = generateClientSecret();
    $clientId = generateClientId($user['username']); // Usar o username da tabela users

    $stmt->bindParam(':client_id', $clientId);
    $stmt->bindParam(':client_secret', $clientSecret);
    $stmt->execute();

    // Response de sucesso
    $response = [
        'statusCode' => 200,
        'message' => [
            'client_id' => $clientId,
            'client_secret' => $clientSecret,
            'message' => 'Credenciais criadas com sucesso'
        ]
    ];
}

// Retorna a resposta
echo json_encode($response);
?>
