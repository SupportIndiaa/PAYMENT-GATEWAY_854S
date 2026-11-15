<?php
// Conectar ao banco de dados e pegar o caminho do logo
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

    // Consultar o caminho do logo na tabela de configurações
    $query = "SELECT togglelogo FROM config LIMIT 1";
    $stmt = $pdo->prepare($query);
    $stmt->execute();
    $config = $stmt->fetch(PDO::FETCH_ASSOC);

    // Retornar o caminho do logo, se encontrado, ou um padrão
    if ($config && $config['togglelogo']) {
        echo htmlspecialchars($config['togglelogo']);
    } else {
        // Caso o logo não esteja configurado, retornar um logo padrão
        echo 'logo.png';  // Caminho do logo padrão
    }
} catch (PDOException $e) {
    die("Error ao conectar ao banco de dados: " . $e->getMessage());
}
?>
