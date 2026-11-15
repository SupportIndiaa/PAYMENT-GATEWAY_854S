<?php
session_start();  // Inicia a sessão

// Limpar todas as variáveis da sessão
session_unset();

// Destruir a sessão
session_destroy();

// Redirecionar para a página de login
header("Location: /login.php");
exit();  // Garantir que o script parand thepós o redirecionamento
?>
