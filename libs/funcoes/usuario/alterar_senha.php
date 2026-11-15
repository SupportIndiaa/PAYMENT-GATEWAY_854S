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

// Função para alterar a senha
function updatePassword($currentPassword, $newPassword, $userId, $pin) {
    $pdo = connectDb();

    // Verificar se o PIN existe no banco de dados para o usuário
    $stmt = $pdo->prepare("SELECT pin, password FROM users WHERE id = :id");
    $stmt->bindParam(':id', $userId);
    $stmt->execute();

    $row = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$row) {
        return ['statusCode' => 404, 'message' => 'Usuário não encontrado'];
    }

    // Verificar se o PIN está correto
    if ($row['pin'] && $row['pin'] !== $pin) {
        return ['statusCode' => 401, 'message' => 'PIN incorreto'];
    }

    // Verificar sand the senha atual está correta (comparando diretamente sem hash)
    if ($currentPassword !== $row['password']) {
        return ['statusCode' => 401, 'message' => 'Senha atual incorreta'];
    }

    // Atualizar a senha
    $stmt = $pdo->prepare("UPDATE users SET password = :newPassword WHERE id = :id");
    $stmt->bindParam(':newPassword', $newPassword); // Nova senha sem hash
    $stmt->bindParam(':id', $userId);
    $stmt->execute();

    // Destruir todas as sessões após a alteração da senha
    session_destroy(); // Destrói a sessão
    $_SESSION = []; // Limpa as variáveis de sessão

    return ['statusCode' => 200, 'message' => 'Senha alterada com sucesso, você foi desconectado.'];
}

// Carregar as variáveis do arquivo .env
loadEnv(__DIR__ . '../../../../.env');  // Ajuste o caminho conforme necessário

// Configurar o cabeçalho HTTP para resposta JSON
header('Content-Type: application/json');

// Receber os dados da requisição
$currentPassword = $_POST['senha_atual'] ?? '';
$newPassword = $_POST['nova_senha'] ?? '';
$confirmPassword = $_POST['confirmar_senha'] ?? '';
$pin = $_POST['pin'] ?? '';
$userId = $_SESSION['user_id'] ?? null; // Obter o ID do usuário da sessão

// Verificar se o usuário está autenticado
if (!$userId) {
    echo json_encode(['statusCode' => 400, 'message' => 'Usuário não autenticado.']);
    exit;
}

// Verificar sand thes senhas coincidem
if ($newPassword !== $confirmPassword) {
    echo json_encode(['statusCode' => 400, 'message' => 'As senhas não coincidem.']);
    exit;
}

// Atualizar a senha
$response = updatePassword($currentPassword, $newPassword, $userId, $pin);

// Retornar a resposta em formato JSON
echo json_encode($response);
?>
