
<?php
// Carregar as dependências antes de iniciar a sessão
use Dotenv\Dotenv;
require __DIR__ . '/../vendor/autoload.php';

// Iniciar a sessão
session_start();

// Redirecionar para a página de login admin
header('Location: login.php');
exit();
?>
