<?php
// Conectar ao banco de dados e verificar o valor de name_pay_logo
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

    // Consultar o valor de name_pay_logo na tabela de configurações
    $query = "SELECT name_pay_logo FROM config LIMIT 1";
    $stmt = $pdo->prepare($query);
    $stmt->execute();
    $config = $stmt->fetch(PDO::FETCH_ASSOC);

    // Verificar se o valor de name_pay_logo é igual a 1
    if ($config && $config['name_pay_logo'] == 1) {
        echo '<div class="texto_logo" style="padding-top: 5px; padding-left: 5px;">PAY</div>';
    }
} catch (PDOException $e) {
    die("Error ao conectar ao banco de dados: " . $e->getMessage());
}
?>
