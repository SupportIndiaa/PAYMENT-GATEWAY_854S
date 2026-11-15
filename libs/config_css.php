<?php
use Dotenv\Dotenv;
require __DIR__ . '/../vendor/autoload.php';

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
} catch (PDOException $e) {
    die("Error ao conectar ao banco de dados: " . $e->getMessage());
}

// Consultar a configuração 'name_pay_logo'
$query = "SELECT name_pay_logo FROM config LIMIT 1";
$stmt = $pdo->prepare($query);
$stmt->execute();
$config = $stmt->fetch(PDO::FETCH_ASSOC);

// Condicional para verificar o valor de 'name_pay_logo' e carregar o CSS correto
if ($config && $config['name_pay_logo'] == 0) {
    echo '<link href="assets/css/styles-pixup.css" rel="stylesheet">';
} elseif ($config && $config['name_pay_logo'] == 1) {
    echo '<link href="assets/css/styles-bspay.css" rel="stylesheet">';
} else {
    // Caso contrário, pode ser opcional um estilo padrão
    echo '<link href="assets/css/styles.css" rel="stylesheet">';
}
?>
