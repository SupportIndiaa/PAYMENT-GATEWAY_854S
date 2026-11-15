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

// Função para adicionar o IP à lista de permissões
function addIpToWhitelist($ip, $userId) {
    $pdo = connectDb();

    // Verificar se o IP já está na lista de permissões
    $stmt = $pdo->prepare("SELECT * FROM ip_whitelist WHERE user_id = :user_id AND ip = :ip");
    $stmt->bindParam(':user_id', $userId);
    $stmt->bindParam(':ip', $ip);
    $stmt->execute();

    if ($stmt->rowCount() > 0) {
        return ['statusCode' => 400, 'error' => 'Este IP já está cadastrado.'];
    }

    // Contar o número de IPs registrados para o usuário
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM ip_whitelist WHERE user_id = :user_id");
    $stmt->bindParam(':user_id', $userId);
    $stmt->execute();

    $ipCount = $stmt->fetchColumn();

    // Verificar se o número de IPs já cadastrados atingiu o limite (4 IPs)
    if ($ipCount >= 4) {
        return ['statusCode' => 400, 'error' => 'Você atingiu o limite de IPs permitidos.'];
    }

    // Inserir o IP na tabela ip_whitelist
    $stmt = $pdo->prepare("INSERT INTO ip_whitelist (user_id, ip) VALUES (:user_id, :ip)");
    $stmt->bindParam(':user_id', $userId);
    $stmt->bindParam(':ip', $ip);
    $stmt->execute();

    return ['success' => true];
}

// Carregar as variáveis do arquivo .env
loadEnv(__DIR__ . '/../../.env');  // Ajuste o caminho conforme necessário

// Configurar o cabeçalho HTTP para resposta JSON
header('Content-Type: application/json');

// Receber os dados da requisição
$ip = $_POST['ip'] ?? '';  // IP enviado no FormData
$pin = $_POST['pin'] ?? '';  // PIN enviado no FormData
$userId = $_SESSION['user_id'] ?? null; // Obter o ID do usuário dos cookies

// Verificar se o usuário está autenticado via sessão
if (!$userId) {
    // Retornar erro JSON para usuário não autenticado
    $response = ['statusCode' => 400, 'message' => 'Usuário não autenticado.'];
    echo json_encode($response);
    exit;
}

// Verificar se o IP e o PIN foram fornecidos
if (empty($ip) || empty($pin)) {
    // Retornar erro JSON para IP ou PIN não fornecido
    $response = ['statusCode' => 400, 'message' => 'IP ou PIN não fornecido.'];
    echo json_encode($response);
    exit;
}

// Conectar ao banco de dados para verificar o PIN
$pdo = connectDb();
$stmt = $pdo->prepare("SELECT pin FROM users WHERE id = :id");
$stmt->bindParam(':id', $userId);
$stmt->execute();

$row = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$row) {
    // Retornar erro JSON para usuário não encontrado
    $response = ['statusCode' => 404, 'error' => 'Usuário não encontrado.'];
    echo json_encode($response);
    exit;
}

// Verificar se o PIN está vazio
if (empty($row['pin'])) {
    // Retornar erro JSON para PIN não configurado
    $response = ['statusCode' => 400, 'error' => 'Você precisa configurar um PIN primeiro.'];
    echo json_encode($response);
    exit;
}

// Verificar se o PIN está correto
if ($pin !== $row['pin']) {
    // Retornar erro JSON para PIN incorreto
    $response = ['statusCode' => 401, 'error' => 'PIN incorreto.'];
    echo json_encode($response);
    exit;
}

// Se tudo estiver correto, adicionar o IP à whitelist
$response = addIpToWhitelist($ip, $userId);

// Retornar a resposta em formato JSON
http_response_code(200);
echo json_encode($response);
?>
