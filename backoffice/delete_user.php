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

// Verificar se o id do usuário foi passado
if (!isset($_GET['id'])) {
    header('Location: index'); // Redireciona se o id não for fornecido
    exit;
}

$user_id = $_GET['id'];

// Deletar o usuário do banco de dados
$query = "DELETE FROM users WHERE id = :user_id";
$stmt = $pdo->prepare($query);
$stmt->execute([':user_id' => $user_id]);

// Redirecionar de volta para a lista de usuários
header('Location: index');
exit;
