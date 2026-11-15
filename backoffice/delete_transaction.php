
<?php
session_start();

// Verificar se está logado como admin
if (!isset($_SESSION['admin_id'])) {
    header('Location: login.php');
    exit();
}

use Dotenv\Dotenv;
require __DIR__ . '/../vendor/autoload.php';

$dotenv = Dotenv::createImmutable(__DIR__ . '/../');
$dotenv->load();

try {
    $dbHost = $_ENV['DB_HOST'];
    $dbName = $_ENV['DB_NAME'];
    $dbUser = $_ENV['DB_USER'];
    $dbPass = $_ENV['DB_PASS'];

    $dsn = "mysql:host=$dbHost;dbname=$dbName;charset=utf8";
    $pdo = new PDO($dsn, $dbUser, $dbPass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Error ao conectar ao banco de dados: " . $e->getMessage());
}

// Obter o ID da transação
if (!isset($_GET['id'])) {
    die("ID da transação não fornecido.");
}

$transaction_id = $_GET['id'];

// Excluir a transação
try {
    $query = "DELETE FROM transactions WHERE id = :id";
    $stmt = $pdo->prepare($query);
    $stmt->execute([':id' => $transaction_id]);
    
    // Redirecionar com sucesso
    header('Location: transaction_list.php?deleted=1');
    exit;
} catch (Exception $e) {
    // Redirecionar com erro
    header('Location: transaction_list.php?error=1');
    exit;
}
?>
