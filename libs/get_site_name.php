<?php
// Arquivo para se conectar ao MySQL e pegar o nome do site
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

    // Consultar o nome do site
    $query = "SELECT name FROM config LIMIT 1";
    $stmt = $pdo->prepare($query);
    $stmt->execute();
    $config = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($config) {
        echo $config['name'];
    } else {
        echo "Nome não encontrado!";
    }
} catch (PDOException $e) {
    die("Error ao conectar ao banco de dados: " . $e->getMessage());
}
?>
