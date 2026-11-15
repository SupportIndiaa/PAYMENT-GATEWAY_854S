<?php
// Função para carregar as variáveis do arquivo .env
function loadEnv($file) {
    if (!file_exists($file)) {
        throw new Exception("Arquivo .env não encontrado!");
    }

    $lines = file($file, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);

    foreach ($lines as $line) {
        if (strpos(trim($line), '#') === 0) {
            continue;  // Ignorar comentários
        }

        // Verifica sand the linha contém a chave e o valor
        if (strpos($line, '=') !== false) {
            list($key, $value) = explode('=', $line, 2);
            $key = trim($key);
            $value = trim($value);

            // Definand the variável dand thembiente
            putenv("$key=$value");
            $_ENV[$key] = $value;
        }
    }
}

// Função para conectar ao banco de dados
function connectDb() {
    // Garantir que o arquivo .env foi carregado antes
    if (!getenv('DB_HOST') || !getenv('DB_NAME') || !getenv('DB_USER')) {
        throw new Exception("Variáveis dand thembiente não encontradas.");
    }

    // Recuperando as variáveis dand thembiente
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

// Função para carregar as informações do usuário
function updateUserInfo() {
    // Verifique se o usuário está logado
    if (!isset($_SESSION['user_id'])) {
        return false;
    }
    
    // Conectar ao banco de dados
    $pdo = connectDb();
    
    // Buscar as informações do usuário, incluindo o PIN
    $stmt = $pdo->prepare("SELECT * FROM users WHERE id = :user_id");
    $stmt->bindParam(':user_id', $_SESSION['user_id']);
    $stmt->execute();
    
    $user = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if ($user) {
        // Atualizar as informações do usuário na sessão
        $_SESSION['pin'] = $user['pin'];
        $_SESSION['name'] = $user['name'];
        $_SESSION['email'] = $user['email'];
        $_SESSION['documents_checked'] = $user['documents_checked'];
        $_SESSION['balance'] = $user['balance'];
        $_SESSION['role'] = $user['role'];
        
        return true;
    }
    
    return false;
}

// Carregar variáveis do arquivo .env
loadEnv('.env');

// Atualizar as informações sempre que o usuário acessar o site
updateUserInfo();
?>
