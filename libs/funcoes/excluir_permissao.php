<?php
session_start();

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
        die("Error ao conectar ao banco de dados: " . $e->getMessage());
    }
}

// Função para excluir o IP da lista de permissões
function deleteIpFromWhitelist($ipIndex, $userId) {
    $pdo = connectDb();

    // Verificar se o IP existe na lista de permissões para o usuário
    $stmt = $pdo->prepare("SELECT * FROM ip_whitelist WHERE id = :ipIndex AND user_id = :user_id");
    $stmt->bindParam(':ipIndex', $ipIndex);
    $stmt->bindParam(':user_id', $userId);
    $stmt->execute();

    if ($stmt->rowCount() == 0) {
        return ['statusCode' => 400, 'error' => 'IP não encontrado ou não pertencand the este usuário.'];
    }

    // Excluir o IP da tabela ip_whitelist
    $stmt = $pdo->prepare("DELETE FROM ip_whitelist WHERE id = :ipIndex AND user_id = :user_id");
    $stmt->bindParam(':ipIndex', $ipIndex);
    $stmt->bindParam(':user_id', $userId);
    $stmt->execute();

    return ['success' => true];
}

// Carregar as variáveis do arquivo .env
loadEnv(__DIR__ . '/../../.env');  // Ajuste o caminho conforme necessário

// Configurar o cabeçalho HTTP para resposta JSON
header('Content-Type: application/json');

// Receber os dados da requisição
$action = $_POST['action'] ?? '';  // Ação (delete)
$ipIndex = $_POST['ipIndex'] ?? '';  // ID do IP na whitelist
$userId = $_SESSION['user_id'] ?? null; // Obter o ID do usuário dos cookies

// Verificar se o usuário está autenticado via sessão
if (!$userId) {
    // Retornar erro JSON para usuário não autenticado
    $response = ['statusCode' => 400, 'message' => 'Usuário não autenticado.'];
    echo json_encode($response);
    exit;
}

// Verificar sand the ação é 'delete' e se o ipIndex foi fornecido
if ($action !== 'delete' || empty($ipIndex)) {
    // Retornar erro JSON para ação inválida ou IP não fornecido
    $response = ['statusCode' => 400, 'message' => 'Ação inválida ou IP não fornecido.'];
    echo json_encode($response);
    exit;
}

// Deletar o IP da whitelist
$response = deleteIpFromWhitelist($ipIndex, $userId);

// Retornar a resposta em formato JSON
http_response_code(200);
echo json_encode($response);
?>
