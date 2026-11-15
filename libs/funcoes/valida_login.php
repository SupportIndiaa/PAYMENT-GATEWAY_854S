<?php
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

// Função para validar o usuário and the senha
function validateUser($username, $password) {
    $pdo = connectDb();

    // Prepara a consulta SQL para procurar o usuário
    $stmt = $pdo->prepare("SELECT * FROM users WHERE username = :username");
    $stmt->bindParam(':username', $username);
    $stmt->execute();

    // Verifica se o usuário existe
    if ($stmt->rowCount() > 0) {
        // O usuário existe, agora pega a senha armazenada e outros dados
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        $storedPassword = $row['password'];

        // Comparando a senha diretamente (sem hash)
        if ($password === $storedPassword) {
            // Armazenar dados na sessão
            $_SESSION['user_id'] = $row['id'];
            $_SESSION['username'] = $row['username'];
            $_SESSION['email'] = $row['email'];
            $_SESSION['name'] = $row['name'];
            $_SESSION['cnpj'] = $row['cnpj'];
            $_SESSION['pin'] = $row['pin'];
            $_SESSION['telefone'] = $row['telefone'];
            $_SESSION['documents_checked'] = $row['documents_checked'];

            return json_encode(["statusCode" => 200, "message" => "Login realizado"]);
        } else {
            return json_encode(["status" => "false", "message" => "Dados inválidos"]);
        }
    } else {
        // Usuário não encontrado
        return json_encode(["status" => "false", "message" => "Dados inválidos"]);
    }
}

// Iniciar a sessão
session_start();

// Carregar as variáveis do arquivo .env
loadEnv(__DIR__ . '/../../.env');  // Ajuste o caminho conforme necessário

// Receber os dados do formulário via POST
$username = $_POST['usuario'] ?? '';
$password = $_POST['senha'] ?? '';

// Validar o usuário and the senha
$response = validateUser($username, $password);

// Retornar a resposta para o cliente
echo $response;
?>
