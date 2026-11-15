<?php
// Conectar ao banco de dados e pegar o caminho do favicon
use Dotenv\Dotenv;

require __DIR__ . '/../vendor/autoload.php'; // Caminho para o autoload

$dotenv = Dotenv::createImmutable(__DIR__ . '/../');
$dotenv->load();

try {
    $dbHost = $_ENV['DB_HOST'];
    $dbnome = $_ENV['DB_NAME'];
    $dbUser = $_ENV['DB_USER'];
    $dbPass = $_ENV['DB_PASS'];

    $dsn = "mysql:host=$dbHost;dbname=$dbnome;charset=utf8";
    $pdo = new PDO($dsn, $dbUser, $dbPass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Consultar o caminho do favicon na tabela de configurações
    $query = "SELECT favicon FROM config LIMIT 1";
    $stmt = $pdo->prepare($query);
    $stmt->execute();
    $config = $stmt->fetch(PDO::FETCH_ASSOC);

    // Retornar o caminho do favicon, se encontrado, ou um padrão
    if ($config && $config['favicon']) {
        echo htmlspecialchars($config['favicon']);
    } else {
        // Caso o favicon não esteja configurado, retornar um favicon padrão
        echo 'favicon.ico';  // Caminho do favicon padrão
    }
} catch (PDOException $e) {
    die("Error ao conectar ao banco de dados: " . $e->getMessage());
}
?>
