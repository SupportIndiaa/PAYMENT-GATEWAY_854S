<?php
session_start();

// Função para carregar as variáveis do arquivo .env
function loadEnv($file) {
    if (!file_exists($file)) {
        throw new Exception("Arquivo .env não encontrado!" . $file);
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

// Função para atualizar o PIN
function updatePin($pin, $userId) {
    $pdo = connectDb();

    // Verificar se o usuário existe no banco de dados
    $stmt = $pdo->prepare("SELECT id FROM users WHERE id = :id");
    $stmt->bindParam(':id', $userId);
    $stmt->execute();

    $row = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$row) {
        return ['statusCode' => 404, 'message' => 'Usuário não encontrado'];
    }

    // Atualizar o PIN no banco de dados
    $stmt = $pdo->prepare("UPDATE users SET pin = :pin WHERE id = :id");
    $stmt->bindParam(':pin', $pin);
    $stmt->bindParam(':id', $userId);
    $stmt->execute();

    return ['statusCode' => 200, 'message' => 'PIN atualizado com sucesso.'];
}

// Carregar as variáveis do arquivo .env
loadEnv(__DIR__ . '/../../../.env');  // Ajuste o caminho conforme necessário

// Configurar o cabeçalho HTTP para resposta JSON
header('Content-Type: application/json');

// Receber os dados da requisição
$pin = $_POST['pin'] ?? '';  // Pega o PIN enviado pelo FormData
$userId = $_SESSION['user_id'] ?? null; // Obter o ID do usuário da sessão

// Verificar se o usuário está autenticado
if (!$userId) {
    echo json_encode(['statusCode' => 400, 'message' => 'Usuário não autenticado.' . $userId]);
    exit;
}

// Verificar se o PIN foi fornecido
if (empty($pin)) {
    echo json_encode(['statusCode' => 400, 'message' => 'PIN não fornecido.']);
    exit;
}

// Atualizar o PIN
$response = updatePin($pin, $userId);

// Retornar a resposta em formato JSON
echo json_encode($response);
?>
