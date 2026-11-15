<?php
// Settings de erro para desenvolvimento
error_reporting(E_ALL);
ini_set('display_errors', 0);
ini_set('log_errors', 1);

// Função para enviar email de boas-vindas
function enviarEmailBoasVindas($nome, $email) {
    try {
        $remetente = "RPay Solutions. <contato@rpay.team>";
        $destinatario = $email;
        $assunto = "Bem-vindo à RPay Solutions! 💙";

        $logoUrl = "https://" . $_SERVER['HTTP_HOST'] . "/img/l.png";
        $siteUrl = "https://" . $_SERVER['HTTP_HOST'];
        
        $mensagem = "
        <!DOCTYPE html>
        <html lang='pt-BR'>
        <head>
            <meta charset='UTF-8'>
            <meta name='viewport' content='width=device-width, initial-scale=1.0'>
            <title>Bem-vindo à RPay Solutions</title>
            <style>
                * {
                    margin: 0;
                    padding: 0;
                    box-sizing: border-box;
                }
                body {
                    font-family: 'Arial', sans-serif;
                    background-color: #f8fafc;
                    padding: 20px;
                }
                .email-container {
                    max-width: 600px;
                    margin: 0 auto;
                    background: #2d3748;
                    border-radius: 16px;
                    overflow: hidden;
                    box-shadow: 0 10px 25px rgba(0,0,0,0.1);
                }
                .email-header {
                    background: linear-gradient(135deg, #17a2b8 0%, #138496 100%);
                    padding: 40px 30px;
                    text-align: center;
                    position: relative;
                }
                .logo-container {
                    background: rgba(255,255,255,0.1);
                    border-radius: 12px;
                    padding: 20px;
                    display: inline-block;
                    margin-bottom: 20px;
                }
                .logo {
                    height: 60px;
                    width: auto;
                    max-width: 200px;
                }
                .email-body {
                    background: white;
                    padding: 40px 30px;
                    text-align: center;
                }
                .welcome-title {
                    font-size: 28px;
                    font-weight: 600;
                    color: #2d3748;
                    margin-bottom: 20px;
                }
                .welcome-text {
                    font-size: 16px;
                    color: #64748b;
                    line-height: 1.6;
                    margin-bottom: 30px;
                }
                .activation-button {
                    background: linear-gradient(135deg, #17a2b8 0%, #138496 100%);
                    color: white;
                    padding: 16px 40px;
                    border-radius: 50px;
                    text-decoration: none;
                    font-weight: 600;
                    font-size: 16px;
                    display: inline-block;
                    margin: 20px 0;
                    box-shadow: 0 4px 15px rgba(23, 162, 184, 0.3);
                    transition: all 0.3s ease;
                }
                .activation-button:hover {
                    transform: translateY(-2px);
                    box-shadow: 0 8px 25px rgba(23, 162, 184, 0.4);
                }
                .footer-text {
                    font-size: 14px;
                    color: #94a3b8;
                    margin-top: 30px;
                    line-height: 1.5;
                }
                .email-footer {
                    background: #1e293b;
                    color: #94a3b8;
                    padding: 30px;
                    text-align: center;
                    font-size: 14px;
                }
                .company-name {
                    color: #17a2b8;
                    font-weight: 600;
                }
                .divider {
                    height: 1px;
                    background: linear-gradient(to right, transparent, #e2e8f0, transparent);
                    margin: 30px 0;
                }
            </style>
        </head>
        <body>
            <div class='email-container'>
                <div class='email-header'>
                    <div class='logo-container'>
                        <img src='$logoUrl' alt='Logo RPay' class='logo' style='height: 60px; width: auto;' />
                    </div>
                </div>
                
                <div class='email-body'>
                    <h1 class='welcome-title'>Bem-vindo à RPay!</h1>
                    
                    <p class='welcome-text'>
                        Olá <strong>$nome</strong>,
                    </p>
                    
                    <p class='welcome-text'>
                        Parabéns!🎉 Sua conta foi criada com sucesso! Para começar a usar nossa plataforma, faça login and thetive sua conta clicando no botão abaixo 🔐💙.
                    </p>
                    
                    <a href='$siteUrl' class='activation-button'>
                        Acessar minha conta
                    </a>
                    
                    <div class='divider'></div>
                    
                    <p class='footer-text'>
                        Agradecemos por escolher a RPay. Se precisar dand thejuda, estamos à disposição.
                    </p>
                </div>
                
                <div class='email-footer'>
                    <p><span class='company-name'>RPAY SOLUTIONS LTDA</span></p>
                    <p style='margin-top: 10px;'>Gateway de Pagamento Seguro</p>
                </div>
            </div>
        </body>
        </html>
        ";
        
        $headers  = "From: $remetente\r\n";
        $headers .= "Reply-To: $remetente\r\n";
        $headers .= "MIME-Version: 1.0\r\n";
        $headers .= "Content-Type: text/html; charset=UTF-8\r\n";
        
        return mail($destinatario, $assunto, $mensagem, $headers);
    } catch (Exception $e) {
        error_log("Error ao enviar email: " . $e->getMessage());
        return false;
    }
}

// Função para salvar usuário no banco
function salvarUsuario($dados) {
    try {
        // ATENÇÃO: Configure suas credenciais do banco aqui
        $host = 'localhost'; // ou seu host do banco
        $dbname = 'u321369575_Rgatepay77'; // nome do seu banco
        $username = 'u321369575_Rgatepay77'; // seu usuário do banco
        $password = 'Rgatepay77'; // sua senha do banco
        
        // Criar conexão PDO
        $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $username, $password);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        
        // Verificar se email já existe
        $stmtCheck = $pdo->prepare("SELECT COUNT(*) FROM users WHERE email = ?");
        $stmtCheck->execute([$dados['email']]);
        
        if ($stmtCheck->fetchColumn() > 0) {
            throw new Exception('Este email já está cadastrado');
        }
        
        // Verificar se usuário já existe
        $stmtCheckUser = $pdo->prepare("SELECT COUNT(*) FROM users WHERE username = ?");
        $stmtCheckUser->execute([$dados['usuario']]);
        
        if ($stmtCheckUser->fetchColumn() > 0) {
            throw new Exception('Este nome de usuário já está em uso');
        }
        
        // Inserir novo usuário - usando os nomes corretos dos campos da tabela
$stmt = $pdo->prepare("INSERT INTO users (name, email, telefone, username, cnpj, password, role, nivel, balance, cash_in_active, cash_out_active, documents_checked) VALUES (?, ?, ?, ?, ?, ?, 'USER', 'bronze', 0.00, 1, 1, 0)");

$resultado = $stmt->execute([
    $dados['nome'], 
    $dados['email'], 
    $dados['telefone'], 
    $dados['usuario'], 
    $dados['cnpj'], 
    $dados['senha'] 
]);

return $resultado;

        
        return $resultado;
        
    } catch (PDOException $e) {
        error_log("Error de banco de dados: " . $e->getMessage());
        
        // Se for erro de conexão, retornar mensagem específica
        if ($e->getCode() == 1049 || $e->getCode() == 2002) {
            throw new Exception('Error de conexão com o banco de dados. Verifiquand thes configurações.');
        }
        
        // Se for erro de tabela não encontrada
        if ($e->getCode() == '42S02') {
            throw new Exception('Tabela de usuários não encontrada. Verifique se o banco está configurado corretamente.');
        }
        
        throw new Exception('Error ao salvar usuário no banco de dados');
        
    } catch (Exception $e) {
        error_log("Error geral ao salvar usuário: " . $e->getMessage());
        throw $e;
    }
}

// Processar o cadastro se os dados foram enviados
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Garantir que retornamos JSON
    header('Content-Type: application/json; charset=utf-8');
    
    try {
        $input = json_decode(file_get_contents('php://input'), true);
        
        if (!$input) {
            throw new Exception('Dados inválidos recebidos');
        }
        
        // Sanitizar e validar dados
        $nome = trim($input['nome'] ?? '');
        $email = trim($input['email'] ?? '');
        $telefone = trim($input['telefone'] ?? '');
        $usuario = trim($input['usuario'] ?? '');
        $cnpj = trim($input['cnpj'] ?? '');
        $senha = $input['senha'] ?? '';
        $confirmar_senha = $input['confirmar_senha'] ?? '';
        $termos = $input['termos'] ?? false;
        
        // Validações detalhadas
        if (empty($nome)) {
            throw new Exception('Nome é obrigatório');
        }
        
        if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
            throw new Exception('Email Address válido é obrigatório');
        }
        
        if (empty($telefone)) {
            throw new Exception('Mobile Number é obrigatório');
        }
        
        if (empty($usuario)) {
            throw new Exception('Username é obrigatório');
        }
        
        if (empty($cnpj)) {
            throw new Exception('Aadhaar/PAN/CNPJ é obrigatório');
        }
        
        if (empty($senha)) {
            throw new Exception('Senha é obrigatória');
        }
        
        if (strlen($senha) < 6) {
            throw new Exception('Senha deve ter pelo menos 6 caracteres');
        }
        
        if ($senha !== $confirmar_senha) {
            throw new Exception('As senhas não coincidem');
        }
        
        if (!$termos) {
            throw new Exception('Você devand theceitar os termos e condições');
        }
        
        // Preparar dados para salvar
        $dadosUsuario = [
            'nome' => $nome,
            'email' => $email,
            'telefone' => $telefone,
            'usuario' => $usuario,
            'cnpj' => $cnpj,
            'senha' => $senha
        ];
        
        // PRIMEIRO: Tentar salvar o usuário
        $usuarioSalvo = salvarUsuario($dadosUsuario);
        
        if (!$usuarioSalvo) {
            throw new Exception('Error ao criar usuário. Tente novamente.');
        }
        
        // SOMENTE SE O USUÁRIO FOI SALVO: tentar enviar email
        $emailEnviado = enviarEmailBoasVindas($nome, $email);
        
        // Response de sucesso - usuário foi criado
        if ($emailEnviado) {
            echo json_encode([
                'status' => 'success', 
                'message' => 'Cadastro realizado com sucesso!'
            ]);
        } else {
            echo json_encode([
                'status' => 'success', 
                'message' => 'Cadastro realizado com sucesso! (Email Address de confirmação não pôde ser enviado)'
            ]);
        }
        
    } catch (Exception $e) {
        // Log do erro para debug
        error_log("Error no cadastro: " . $e->getMessage());
        
        echo json_encode([
            'status' => 'error', 
            'message' => $e->getMessage()
        ]);
    }
    exit;
}
?>
<!DOCTYPE html>
<html lang="pt" dir="ltr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>RPay - Create account</title>
    <meta name="Description" content="Dashboard principal do gateway de pagamento RPay">
    <meta name="Author" content="Ellodev">
    <meta name="keywords" content="Gateway de pagamento">
    <link rel="icon" href="assets/images/brand-logos/favicon.ico" type="image/x-icon">
    
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" integrity="sha512-1ycn6IcaQQ40/MKBW2W4Rhis/DbILU74C1vSrLJxCq57o941Ym01SwNsOMqvEBFlcgUa6xLiPY/NS5R+E6ztJQ==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    
    <style>
        :root {
            --primary: #17a2b8;
            --primary-dark: #138496;
            --primary-light: #b8e6ea;
            --primary-gradient: linear-gradient(135deg, #17a2b8 0%, #138496 100%);
            --white: #ffffff;
            --gray-50: #f8fafc;
            --gray-100: #f1f5f9;
            --gray-200: #e2e8f0;
            --gray-300: #cbd5e1;
            --gray-400: #94a3b8;
            --gray-500: #64748b;
            --gray-600: #475569;
            --gray-700: #334155;
            --gray-800: #1e293b;
            --gray-900: #0f172a;
            --dark-bg: #0f172a;
            --dark-card: #1e293b;
            --dark-text: #e2e8f0;
            --error: #ef4444;
            --success: #10b981;
            --warning: #f59e0b;
            --shadow-sm: 0 1px 2px 0 rgb(0 0 0 / 0.05);
            --shadow: 0 1px 3px 0 rgb(0 0 0 / 0.1), 0 1px 2px -1px rgb(0 0 0 / 0.1);
            --shadow-md: 0 4px 6px -1px rgb(0 0 0 / 0.1), 0 2px 4px -2px rgb(0 0 0 / 0.1);
            --shadow-lg: 0 10px 15px -3px rgb(0 0 0 / 0.1), 0 4px 6px -4px rgb(0 0 0 / 0.1);
            --shadow-xl: 0 20px 25px -5px rgb(0 0 0 / 0.1), 0 8px 10px -6px rgb(0 0 0 / 0.1);
            --shadow-2xl: 0 25px 50px -12px rgb(0 0 0 / 0.25);
        }
        
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            -webkit-tap-highlight-color: transparent;
        }
        
        html, body {
            height: 100%;
            overflow-x: hidden;
            overflow-y: auto;
            -webkit-text-size-adjust: 100%;
            -webkit-font-smoothing: antialiased;
            -moz-osx-font-smoothing: grayscale;
        }
        
        body {
            font-family: 'Poppins', 'Inter', sans-serif;
            background: var(--gray-50);
            min-height: 100vh;
            padding: 16px;
            display: flex;
            justify-content: center;
            align-items: flex-start;
            padding-top: 20px;
            touch-action: manipulation;
        }
        
        /* Enhanced Background with Animated Gradients */
        body::before {
            content: '';
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: 
                radial-gradient(circland thet 20% 50%, rgba(23, 162, 184, 0.15) 0%, transparent 50%),
                radial-gradient(circland thet 80% 20%, rgba(19, 132, 150, 0.15) 0%, transparent 50%),
                radial-gradient(circland thet 40% 80%, rgba(184, 230, 234, 0.1) 0%, transparent 50%),
                linear-gradient(135deg, var(--gray-50) 0%, #f8fafc 100%);
            z-index: -2;
            animation: backgroundShift 20s ease-in-out infinite;
        }
        
        body.dark-mode {
            background: var(--dark-bg);
            color: var(--dark-text);
        }
        
        body.dark-mode::before {
            background: 
                radial-gradient(circland thet 20% 50%, rgba(23, 162, 184, 0.08) 0%, transparent 50%),
                radial-gradient(circland thet 80% 20%, rgba(19, 132, 150, 0.08) 0%, transparent 50%),
                radial-gradient(circland thet 40% 80%, rgba(184, 230, 234, 0.08) 0%, transparent 50%),
                linear-gradient(135deg, var(--dark-bg) 0%, #1e293b 100%);
        }
        
        @keyframes backgroundShift {
            0%, 100% { 
                transform: translateX(0) translateY(0);
            }
            25% { 
                transform: translateX(-5px) translateY(-10px);
            }
            50% { 
                transform: translateX(10px) translateY(5px);
            }
            75% { 
                transform: translateX(-8px) translateY(8px);
            }
        }
        
        /* Container Responsivo */
        .register-container {
            width: 100%;
            z-index: 10;
            position: relative;
            max-width: 560px;
        }
        
        /* Card principal responsivo */
        .register-card {
            width: 100%;
            margin: 0 auto;
            border: none;
            border-radius: 24px;
            backdrop-filter: blur(20px);
            -webkit-backdrop-filter: blur(20px);
            background: rgba(255, 255, 255, 0.95);
            border: 1px solid rgba(255, 255, 255, 0.2);
            box-shadow: var(--shadow-2xl);
            overflow: hidden;
            position: relative;
            animation: cardEntrance 0.8s cubic-bezier(0.16, 1, 0.3, 1);
            margin-bottom: 40px;
        }
        
        body.dark-mode .register-card {
            background: rgba(30, 41, 59, 0.95);
            border: 1px solid rgba(23, 162, 184, 0.1);
            box-shadow: 
                0 25px 50px -12px rgba(0, 0, 0, 0.4),
                0 0 0 1px rgba(23, 162, 184, 0.1);
        }
        
        /* Enhanced Header with Gradient */
        .card-header {
            background: var(--primary-gradient);
            color: white;
            text-align: center;
            padding: 32px 24px;
            border-bottom: none;
            position: relative;
            overflow: hidden;
        }
        
        .card-header::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.1), transparent);
            animation: shimmer 3s infinite;
        }
        
        .card-header .logo {
            width: auto;
            height: 60px;
            background: none;
            border-radius: 0;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 16px;
            overflow: hidden;
        }
        
        .card-header .logo img {
            width: auto;
            height: 60px;
            max-width: 200px;
            object-fit: contain;
            border-radius: 0;
        }
        
        .card-header h2 {
            font-weight: 600;
            margin: 0;
            font-size: 1.75rem;
            letter-spacing: -0.5px;
            text-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }
        
        .card-header p {
            margin: 8px 0 0 0;
            font-size: 0.9rem;
            opacity: 0.9;
        }
        
        .card-body {
            padding: 32px 24px;
            position: relative;
        }
        
        body.dark-mode .card-body {
            color: var(--dark-text);
        }
        
        /* Enhanced Form Elements */
        .form-label {
            font-weight: 500;
            margin-bottom: 8px;
            color: var(--gray-700);
            font-size: 0.9rem;
            display: block;
        }
        
        body.dark-mode .form-label {
            color: var(--gray-300);
        }
        
        .input-group {
            position: relative;
            margin-bottom: 8px;
        }
        
        .input-group-text {
            position: absolute;
            left: 12px;
            top: 50%;
            transform: translateY(-50%);
            background: none;
            border: none;
            color: var(--gray-400);
            font-size: 16px;
            z-index: 10;
            pointer-events: none;
        }
        
        .form-control {
            width: 100%;
            height: 48px;
            border-radius: 12px;
            border: 1px solid var(--gray-200);
            padding: 12px 16px 12px 44px;
            font-size: 16px; /* Prevents zoom on iOS */
            background: rgba(255, 255, 255, 0.8);
            color: var(--gray-900);
            transition: all 0.3s cubic-bezier(0.16, 1, 0.3, 1);
            font-weight: 400;
            -webkit-appearance: none;
            -moz-appearance: none;
            appearance: none;
            outline: none;
            user-select: text;
            -webkit-user-select: text;
            -moz-user-select: text;
            -ms-user-select: text;
            touch-action: manipulation;
        }
        
        body.dark-mode .form-control {
            background: rgba(255, 255, 255, 0.05);
            border-color: var(--gray-600);
            color: var(--dark-text);
        }
        
        .form-control:focus {
            border-color: var(--primary);
            box-shadow: 0 0 0 3px rgba(23, 162, 184, 0.1);
            background: rgba(255, 255, 255, 1);
            outline: none;
        }
        
        body.dark-mode .form-control:focus {
            background: rgba(255, 255, 255, 0.1);
            box-shadow: 0 0 0 3px rgba(23, 162, 184, 0.2);
        }
        
        .form-control:active,
        .form-control:hover {
            border-color: var(--primary);
        }
        
        /* Two column layout for large fields */
        .form-row {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 16px;
        }
        
        @media (max-width: 576px) {
            .form-row {
                grid-template-columns: 1fr;
                gap: 0;
            }
        }
        
        /* Enhanced Button */
        .btn-register {
            background: var(--primary-gradient);
            border: none;
            color: white;
            padding: 14px 20px;
            font-size: 1rem;
            font-weight: 500;
            border-radius: 12px;
            width: 100%;
            margin-top: 16px;
            letter-spacing: 0.3px;
            position: relative;
            overflow: hidden;
            box-shadow: var(--shadow-md);
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            cursor: pointer;
            transition: all 0.3s ease;
            touch-action: manipulation;
            -webkit-tap-highlight-color: transparent;
        }
        
        .btn-register:hover {
            transform: translateY(-2px);
            box-shadow: var(--shadow-lg);
        }
        
        .btn-register:disabled {
            opacity: 0.7;
            cursor: not-allowed;
            transform: none;
        }
        
        /* Enhanced Links */
        .login-link {
            color: var(--primary);
            text-decoration: none;
            font-size: 0.85rem;
            font-weight: 500;
            transition: all 0.3s ease;
            touch-action: manipulation;
        }
        
        .login-link:hover {
            color: var(--primary-dark);
            text-decoration: underline;
        }
        
        body.dark-mode .login-link {
            color: var(--primary-light);
        }
        
        .terms-link {
            color: var(--primary);
            font-weight: 500;
            text-decoration: none;
            transition: all 0.3s ease;
            cursor: pointer;
            touch-action: manipulation;
        }
        
        .terms-link:hover {
            color: var(--primary-dark);
            text-decoration: underline;
        }
        
        /* Enhanced Password Toggle */
        .password-toggle {
            position: absolute;
            right: 12px;
            top: 50%;
            transform: translateY(-50%);
            background: none;
            border: none;
            color: var(--gray-400);
            cursor: pointer;
            padding: 8px;
            border-radius: 6px;
            transition: all 0.3s ease;
            z-index: 10;
            touch-action: manipulation;
            -webkit-tap-highlight-color: transparent;
        }
        
        .password-toggle:hover {
            background: rgba(23, 162, 184, 0.1);
            color: var(--primary);
        }
        
        /* Enhanced Theme Toggle */
        .theme-toggle {
            position: fixed;
            bottom: 24px;
            left: 24px;
            width: 48px;
            height: 48px;
            border-radius: 50%;
            background: var(--primary-gradient);
            color: white;
            border: none;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 18px;
            box-shadow: var(--shadow-lg);
            z-index: 1000;
            transition: all 0.4s cubic-bezier(0.16, 1, 0.3, 1);
            touch-action: manipulation;
            -webkit-tap-highlight-color: transparent;
        }
        
        .theme-toggle:hover {
            transform: scale(1.1);
            box-shadow: 0 8px 20px rgba(23, 162, 184, 0.4);
        }
        
        /* Pop-up Modal Styles */
        .modal-content {
            border: none;
            border-radius: 16px;
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(20px);
            box-shadow: var(--shadow-2xl);
        }
        
        body.dark-mode .modal-content {
            background: rgba(30, 41, 59, 0.95);
            color: var(--dark-text);
        }
        
        .modal-header {
            background: var(--primary-gradient);
            color: white;
            border-radius: 16px 16px 0 0;
            border-bottom: none;
            padding: 20px 24px;
        }
        
        .modal-header .btn-close {
            filter: invert(1);
            opacity: 0.8;
        }
        
        .modal-body {
            padding: 24px;
            max-height: 400px;
            overflow-y: auto;
        }
        
        .modal-body h5 {
            color: var(--primary);
            margin-bottom: 16px;
            font-weight: 600;
        }
        
        body.dark-mode .modal-body h5 {
            color: var(--primary-light);
        }
        
        .modal-body ul {
            padding-left: 20px;
        }
        
        .modal-body li {
            margin-bottom: 8px;
            line-height: 1.6;
        }
        
        /* Toast Notifications */
        .toast-container {
            position: fixed;
            top: 20px;
            right: 20px;
            z-index: 9999;
            max-width: 360px;
            width: calc(100% - 40px);
        }
        
        .toast {
            border: none;
            border-radius: 12px;
            backdrop-filter: blur(20px);
            overflow: hidden;
            margin-bottom: 12px;
            background: rgba(255, 255, 255, 0.95);
            border: 1px solid rgba(255, 255, 255, 0.2);
            box-shadow: var(--shadow-lg);
            animation: toastSlideIn 0.5s cubic-bezier(0.16, 1, 0.3, 1) forwards;
            font-family: 'Poppins', sans-serif;
            position: relative;
        }
        
        body.dark-mode .toast {
            background: rgba(30, 41, 59, 0.95);
            border: 1px solid rgba(23, 162, 184, 0.1);
        }
        
        .toast.success {
            border-left: 4px solid var(--success);
            background: rgba(16, 185, 129, 0.1);
        }
        
        .toast.error {
            border-left: 4px solid var(--error);
            background: rgba(239, 68, 68, 0.1);
        }
        
        .toast-header {
            border-bottom: 1px solid rgba(0, 0, 0, 0.05);
            background: transparent;
            color: var(--gray-900);
            font-weight: 600;
            padding: 16px 20px 8px;
            font-size: 1rem;
        }
        
        body.dark-mode .toast-header {
            color: var(--dark-text);
            border-bottom-color: rgba(255, 255, 255, 0.1);
        }
        
        .toast-body {
            padding: 8px 20px 16px;
            color: var(--gray-700);
            font-size: 0.95rem;
            line-height: 1.6;
        }
        
        body.dark-mode .toast-body {
            color: var(--gray-300);
        }
        
        /* Keyframe Animations */
        @keyframes cardEntrance {
            0% {
                opacity: 0;
                transform: translateY(30px) scale(0.9);
            }
            100% {
                opacity: 1;
                transform: translateY(0) scale(1);
            }
        }
        
        @keyframes shimmer {
            0% { left: -100%; }
            100% { left: 100%; }
        }
        
        @keyframes toastSlideIn {
            0% {
                transform: translateX(100%);
                opacity: 0;
            }
            100% {
                transform: translateX(0);
                opacity: 1;
            }
        }
        
        /* Mobile Responsive - Corrigir problemas de touch */
        @media (max-width: 576px) {
            html, body {
                height: auto;
                min-height: 100vh;
                overflow-y: auto;
                position: static;
            }
            
            body {
                padding: 12px;
                align-items: flex-start;
                padding-top: 20px;
                padding-bottom: 60px;
            }
            
            .register-card {
                max-width: 100%;
                margin: 0 0 40px 0;
            }
            
            .card-header {
                padding: 24px 20px;
            }
            
            .card-header h2 {
                font-size: 1.5rem;
            }
            
            .card-body {
                padding: 24px 20px;
            }
            
            .form-control {
                height: 48px;
                font-size: 16px; /* Prevents zoom on iOS */
                padding: 14px 16px 14px 44px;
            }
            
            .btn-register {
                padding: 14px 16px;
                font-size: 1rem;
                height: 52px;
            }
            
            .theme-toggle {
                width: 44px;
                height: 44px;
                bottom: 20px;
                left: 20px;
                font-size: 16px;
            }
            
            /* Fix for input fields on mobile */
            input[type="text"],
            input[type="email"],
            input[type="password"],
            input[type="tel"] {
                font-size: 16px !important;
                -webkit-appearance: none;
                border-radius: 12px;
                background-clip: padding-box;
            }
        }
        
        /* Desktop - Ensure fixed size */
        @media (min-width: 768px) {
            .register-card {
                width: 560px;
                max-width: none;
            }
        }
        
        /* Prevent zoom on double tap */
        @media screen and (max-width: 768px) {
            * {
                touch-action: manipulation;
            }
        }
    </style>
</head>
<body>

    <!-- Toast Notifications Container -->
    <div class="toast-container"></div>
    
    <!-- Theme Toggle Button -->
    <button class="theme-toggle" id="themeToggle" aria-label="Alternar tema">
        <i class="fas fa-moon"></i>
    </button>
    
    <!-- Termos Modal -->
    <div class="modal fade" id="termosModal" tabindex="-1" aria-labelledby="termosModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="termosModalLabel">
                        <i class="fas fa-file-contract me-2"></i>
                        Terms and Conditions
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <h5>1. Acceptance of Terms</h5>
                    <p>Ao utilizar os serviços da RPay, você concorda com todos os termos e condições estabelecidos neste documento.</p>
                    
                    <h5>2. Description of Services</h5>
                    <ul>
                        <li>Processamento de pagamentos via PIX, cartão de crédito e débito</li>
                        <li>Gateway de pagamento para e-commerce</li>
                        <li>Gestão de cobranças e recebimentos</li>
                        <li>Relatórios financeiros and thenálises de vendas</li>
                    </ul>
                    
                    <h5>3. User Responsibilities</h5>
                    <ul>
                        <li>Fornecer informações verdadeiras and thetualizadas</li>
                        <li>Manter a segurança de sua conta e senha</li>
                        <li>Cumprir todas as leis aplicáveis</li>
                        <li>No utilizar os serviços para atividades fraudulentas</li>
                    </ul>
                    
                    <h5>4. Fees and Payments</h5>
                    <p>As taxas pelos serviços prestados serão cobradas conforme tabela disponível em nosso site. Os valores podem ser alterados mediantand theviso prévio de 30 dias.</p>
                    
                    <h5>5. Limitations of Liability</h5>
                    <p>A RPay não se responsabiliza por perdas indiretas, lucros cessantes ou danos consequenciais decorrentes do uso dos serviços.</p>
                    
                    <h5>6. Modifications of Terms</h5>
                    <p>Reservamo-nos o direito de modificar estes termos a qualquer momento, sendo as alterações comunicadas através de nossos canais oficiais.</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Privacy Policy Modal -->
    <div class="modal fade" id="privacidadeModal" tabindex="-1" aria-labelledby="privacidadeModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="privacidadeModalLabel">
                        <i class="fas fa-shield-alt me-2"></i>
                        Privacy Policy
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <h5>1. Information Collection</h5>
                    <p>Coletamos informações pessoais necessárias para prestação dos serviços:</p>
                    <ul>
                        <li>Dados pessoais: nome, Aadhaar/PAN/CNPJ, endereço, telefone, e-mail</li>
                        <li>Dados bancários: conta bancária para repasses</li>
                        <li>Dados de transações: histórico de pagamentos e vendas</li>
                        <li>Dados técnicos: logs dand thecesso, IP, dispositivo utilizado</li>
                    </ul>
                    
                    <h5>2. Use of Information</h5>
                    <ul>
                        <li>Processar pagamentos e transferências</li>
                        <li>Prevenir fraudes e garantir segurança</li>
                        <li>Cumprir obrigações legais e regulatórias</li>
                        <li>Melhorar nossos serviços e experiência do usuário</li>
                        <li>Comunicar sobrand thetualizações e novos produtos</li>
                    </ul>
                    
                    <h5>3. Data Sharing</h5>
                    <p>Suas informações podem ser compartilhadas com:</p>
                    <ul>
                        <li>Bancos e instituições financeiras parceiras</li>
                        <li>Órgãos reguladores and theutoridades competentes</li>
                        <li>Provedores de serviços terceirizados (sob contratos de confidencialidade)</li>
                        <li>Empresas do grupo empresarial da RPay</li>
                    </ul>
                    
                    <h5>4. Data Security</h5>
                    <p>Implementamos medidas de segurança técnicas e organizacionais para proteger suas informações, incluindo criptografia, firewalls e controles dand thecesso.</p>
                    
                    <h5>5. Your Rights</h5>
                    <ul>
                        <li>Acesso aos seus dados pessoais</li>
                        <li>Correção de informações inexatas</li>
                        <li>Exclusão de dados quando aplicável</li>
                        <li>Portabilidade dos dados</li>
                        <li>Oposição ao tratamento para certas finalidades</li>
                    </ul>
                    
                    <h5>6. Data Retention</h5>
                    <p>Mantemos seus dados pelo tempo necessário para cumprir as finalidades descritas e obrigações legais, que pode ser dand theté 10 anos conforme legislação aplicável.</p>
                    
                    <h5>7. Contact</h5>
                    <p>Para questões sobre privacidade, entre em contato pelo e-mail: contato@RPay.team</p>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Main Register Container -->
    <div class="register-container">
        <div class="register-card">
            <div class="card-header">
                <div class="logo">
                    <img src="logoUrl" alt="Logo RPay" onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';">
                    <i class="fas fa-university" style="display: none; font-size: 20px;"></i>
                </div>
                <h2>RPay Solutions 🚀</h2>
                <p>Create account</p>
            </div>
            <div class="card-body">
                <form id="registerForm" novalidate>
                    <div class="mb-3">
                        <label for="nome_completo" class="form-label">
                            Nome completo <span style="color: var(--error);">*</span>
                        </label>
                        <div class="input-group">
                            <i class="fas fa-user input-group-text"></i>
                            <input 
                                type="text" 
                                class="form-control" 
                                id="nome_completo" 
                                name="nome" 
                                placeholder="João da Silva" 
                                required 
                                autocomplete="name"
                            >
                        </div>
                    </div>

                    <div class="form-row">
                        <div class="mb-3">
                            <label for="usuario" class="form-label">
                                Username <span style="color: var(--error);">*</span>
                            </label>
                            <div class="input-group">
                                <i class="fas fa-at input-group-text"></i>
                                <input 
                                    type="text" 
                                    class="form-control" 
                                    id="usuario" 
                                    name="usuario" 
                                    placeholder="joaosilva2" 
                                    required 
                                    autocomplete="username"
                                >
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="email" class="form-label">
                                Email Address <span style="color: var(--error);">*</span>
                            </label>
                            <div class="input-group">
                                <i class="fas fa-envelope input-group-text"></i>
                                <input 
                                    type="email" 
                                    class="form-control" 
                                    id="email" 
                                    name="email" 
                                    placeholder="joao@exemplo.com" 
                                    required 
                                    autocomplete="email"
                                >
                            </div>
                        </div>
                    </div>

                    <div class="form-row">
                        <div class="mb-3">
                            <label for="telefone" class="form-label">
                                Mobile Number <span style="color: var(--error);">*</span>
                            </label>
                            <div class="input-group">
                                <i class="fas fa-phone input-group-text"></i>
                                <input 
                                    type="tel" 
                                    class="form-control" 
                                    id="telefone" 
                                    name="telefone" 
                                    placeholder="(11) 90000-0000" 
                                    required 
                                    autocomplete="tel"
                                >
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="cnpj" class="form-label">
                                Aadhaar/PAN/CNPJ <span style="color: var(--error);">*</span>
                            </label>
                            <div class="input-group">
                                <i class="fas fa-id-card input-group-text"></i>
                                <input 
                                    type="text" 
                                    class="form-control" 
                                    id="cnpj" 
                                    name="cnpj" 
                                    placeholder="000.000.000-00" 
                                    required
                                >
                            </div>
                        </div>
                    </div>

                    <div class="form-row">
                        <div class="mb-3">
                            <label for="senha" class="form-label">
                                Senha <span style="color: var(--error);">*</span>
                            </label>
                            <div class="input-group">
                                <i class="fas fa-lock input-group-text"></i>
                                <input 
                                    type="password" 
                                    class="form-control" 
                                    id="senha" 
                                    name="senha" 
                                    placeholder="Enter your password" 
                                    required 
                                    autocomplete="new-password"
                                >
                                <button 
                                    type="button" 
                                    class="password-toggle" 
                                    id="togglePassword"
                                    aria-label="Mostrar/ocultar senha"
                                >
                                    <i class="fas fa-eye"></i>
                                </button>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="confirmar_senha" class="form-label">
                                Confirmand the senha <span style="color: var(--error);">*</span>
                            </label>
                            <div class="input-group">
                                <i class="fas fa-lock input-group-text"></i>
                                <input 
                                    type="password" 
                                    class="form-control" 
                                    id="confirmar_senha" 
                                    name="confirmar_senha" 
                                    placeholder="Confirm your password" 
                                    required 
                                    autocomplete="new-password"
                                >
                                <button 
                                    type="button" 
                                    class="password-toggle" 
                                    id="toggleConfirmPassword"
                                    aria-label="Mostrar/ocultar senha"
                                >
                                    <i class="fas fa-eye"></i>
                                </button>
                            </div>
                        </div>
                    </div>

                    <div class="mb-3">
                        <div class="form-check">
                            <input 
                                class="form-check-input" 
                                type="checkbox" 
                                id="concordo_termos" 
                                name="termos" 
                                required
                            >
                            <label class="form-check-label" for="concordo_termos" style="font-size: 0.9rem; color: var(--gray-600);">
                                I have read and agree to the <a class="terms-link" data-bs-toggle="modal" data-bs-target="#termosModal">Termos e condições</a> and the <a class="terms-link" data-bs-toggle="modal" data-bs-target="#privacidadeModal">Privacy Policy</a>
                            </label>
                        </div>
                    </div>
                    
                    <button type="submit" class="btn btn-register" id="cadastro-button">
                        <i class="fas fa-user-plus"></i> 
                        Register
                    </button>
                    
                    <div class="text-center mt-4">
                        <p class="mb-0" style="font-size: 0.9rem; color: var(--gray-600);">
                            Already have an account? 
                            <a href="login.php" class="login-link">
                                Login here
                            </a>
                        </p>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Bootstrap Bundle with Popper -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    
    <script>
        // Prevent zoom and enhance mobile experience
        document.addEventListener('DOMContentLoaded', function() {
            // Prevent zoom on iOS
            if (/iPhone|iPad|iPod/i.test(navigator.userAgent)) {
                document.addEventListener('gesturestart', function(e) {
                    e.preventDefault();
                });
                
                let lastTouchEnd = 0;
                document.addEventListener('touchend', function(event) {
                    const now = new Date().getTime();
                    if (now - lastTouchEnd <= 300) {
                        event.preventDefault();
                    }
                    lastTouchEnd = now;
                }, false);
            }
            
            // Auto-focus on desktop only
            if (window.innerWidth > 768) {
                document.getElementById('nome_completo').focus();
            }
            
            // Enhance mobile touch experience
            const inputs = document.querySelectorAll('input, button, a');
            inputs.forEach(input => {
                input.addEventListener('touchstart', function() {
                    this.style.transform = 'scale(0.98)';
                });
                
                input.addEventListener('touchend', function() {
                    this.style.transform = 'scale(1)';
                });
            });
        });
        
        // Password toggle functionality
        document.getElementById('togglePassword').addEventListener('click', function() {
            const passwordInput = document.getElementById('senha');
            const icon = this.querySelector('i');
            
            if (passwordInput.type === 'password') {
                passwordInput.type = 'text';
                icon.classList.remove('fa-eye');
                icon.classList.add('fa-eye-slash');
            } else {
                passwordInput.type = 'password';
                icon.classList.remove('fa-eye-slash');
                icon.classList.add('fa-eye');
            }
        });

        document.getElementById('toggleConfirmPassword').addEventListener('click', function() {
            const passwordInput = document.getElementById('confirmar_senha');
            const icon = this.querySelector('i');
            
            if (passwordInput.type === 'password') {
                passwordInput.type = 'text';
                icon.classList.remove('fa-eye');
                icon.classList.add('fa-eye-slash');
            } else {
                passwordInput.type = 'password';
                icon.classList.remove('fa-eye-slash');
                icon.classList.add('fa-eye');
            }
        });
        
        // Theme toggle functionality
        document.getElementById('themeToggle').addEventListener('click', function() {
            document.body.classList.toggle('dark-mode');
            const icon = this.querySelector('i');
            
            if (document.body.classList.contains('dark-mode')) {
                icon.classList.remove('fa-moon');
                icon.classList.add('fa-sun');
                localStorage.setItem('theme', 'dark');
            } else {
                icon.classList.remove('fa-sun');
                icon.classList.add('fa-moon');
                localStorage.setItem('theme', 'light');
            }
        });
        
        // Initialize theme
        function initializeTheme() {
            const savedTheme = localStorage.getItem('theme');
            const systemDark = window.matchMedia('(prefers-color-scheme: dark)').matches;
            
            if (savedTheme === 'dark' || (!savedTheme && systemDark)) {
                document.body.classList.add('dark-mode');
                const themeToggle = document.querySelector('#themeToggle i');
                themeToggle.classList.remove('fa-moon');
                themeToggle.classList.add('fa-sun');
            }
        }
        
        initializeTheme();
        
        // Toast notification function
        function showToast(type, title, message) {
            const toastContainer = document.querySelector('.toast-container');
            const toastId = 'toast-' + Date.now();
            
            let iconClass, toastClass;
            if (type === 'success') {
                iconClass = 'fas fa-check-circle success';
                toastClass = 'success';
            } else if (type === 'error') {
                iconClass = 'fas fa-exclamation-circle error';
                toastClass = 'error';
            } else if (type === 'warning') {
                iconClass = 'fas fa-exclamation-triangle warning';
                toastClass = 'warning';
            } else {
                iconClass = 'fas fa-info-circle info';
                toastClass = 'info';
            }
            
            const toastHTML = `
                <div class="toast ${toastClass}" id="${toastId}" role="alert" aria-live="assertive" aria-atomic="true">
                    <div class="toast-header">
                        <i class="${iconClass} icon"></i>
                        <strong class="me-auto">${title}</strong>
                        <button type="button" class="btn-close" data-bs-dismiss="toast" aria-label="Fechar notificação"></button>
                    </div>
                    <div class="toast-body">
                        ${message}
                    </div>
                </div>
            `;
            
            toastContainer.insertAdjacentHTML('beforeend', toastHTML);
            
            const toastElement = document.getElementById(toastId);
            const toast = new bootstrap.Toast(toastElement, {
                autohide: true,
                delay: 5000
            });
            toast.show();
            
            setTimeout(function() {
                toast.hide();
            }, 5000);
            
            toastElement.addEventListener('hidden.bs.toast', function() {
                toastElement.remove();
            });
        }

        // Phone mask
        document.getElementById('telefone').addEventListener('input', function() {
            let value = this.value.replace(/\D/g, '');
            value = value.replace(/^(\d{2})(\d)/g, '($1) $2');
            value = value.replace(/(\d)(\d{4})$/, '$1-$2');
            this.value = value;
        });

        // Aadhaar/PAN/CNPJ mask
        document.getElementById('cnpj').addEventListener('input', function() {
            let value = this.value.replace(/\D/g, '');
            
            if (value.length <= 11) {
                value = value.replace(/(\d{3})(\d)/, '$1.$2');
                value = value.replace(/(\d{3})(\d)/, '$1.$2');
                value = value.replace(/(\d{3})(\d{1,2})$/, '$1-$2');
            } else {
                value = value.substring(0, 14);
                value = value.replace(/^(\d{2})(\d)/, '$1.$2');
                value = value.replace(/^(\d{2})\.(\d{3})(\d)/, '$1.$2.$3');
                value = value.replace(/\.(\d{3})(\d)/, '.$1/$2');
                value = value.replace(/(\d{4})(\d)/, '$1-$2');
            }
            
            this.value = value;
        });

        // Username formatting
        document.getElementById('usuario').addEventListener('input', function() {
            let value = this.value.replace(/[^a-zA-Z0-9]/g, '').toLowerCase();
            this.value = value;
        });

        // CPF validation
        function validateCPF(cpf) {
            cpf = cpf.replace(/[^\d]+/g, '');
            if (cpf.length !== 11 || /^(\d)\1+$/.test(cpf)) return false;
            
            let sum = 0, remainder;
            for (let i = 1; i <= 9; i++) sum += parseInt(cpf.substring(i-1, i)) * (11 - i);
            remainder = (sum * 10) % 11;
            if (remainder === 10 || remainder === 11) remainder = 0;
            if (remainder !== parseInt(cpf.substring(9, 10))) return false;
            
            sum = 0;
            for (let i = 1; i <= 10; i++) sum += parseInt(cpf.substring(i-1, i)) * (12 - i);
            remainder = (sum * 10) % 11;
            if (remainder === 10 || remainder === 11) remainder = 0;
            if (remainder !== parseInt(cpf.substring(10, 11))) return false;
            
            return true;
        }

        // CNPJ validation
        function validateCNPJ(cnpj) {
            cnpj = cnpj.replace(/[^\d]+/g, '');
            if (cnpj.length !== 14) return false;
            
            let length = cnpj.length - 2;
            let numbers = cnpj.substring(0, length);
            let digits = cnpj.substring(length);
            let sum = 0;
            let pos = length - 7;
            
            for (let i = length; i >= 1; i--) {
                sum += numbers.charAt(length - i) * pos--;
                if (pos < 2) pos = 9;
            }
            
            let result = sum % 11 < 2 ? 0 : 11 - sum % 11;
            if (result !== parseInt(digits.charAt(0))) return false;
            
            length = length + 1;
            numbers = cnpj.substring(0, length);
            sum = 0;
            pos = length - 7;
            
            for (let i = length; i >= 1; i--) {
                sum += numbers.charAt(length - i) * pos--;
                if (pos < 2) pos = 9;
            }
            
            result = sum % 11 < 2 ? 0 : 11 - sum % 11;
            if (result !== parseInt(digits.charAt(1))) return false;
            
            return true;
        }
        
        // Form validation and submission
        $(document).ready(function() {
            $('#registerForm').on('submit', function(e) {
                e.preventDefault();
                
                const formData = {
                    nome: $('#nome_completo').val().trim(),
                    email: $('#email').val().trim(),
                    telefone: $('#telefone').val().trim(),
                    usuario: $('#usuario').val().trim(),
                    cnpj: $('#cnpj').val().trim(),
                    senha: $('#senha').val(),
                    confirmar_senha: $('#confirmar_senha').val(),
                    termos: $('#concordo_termos').is(':checked')
                };
                
                // Basic validation
                if (!formData.nome || !formData.email || !formData.telefone || !formData.usuario || !formData.cnpj || !formData.senha || !formData.confirmar_senha || !formData.termos) {
                    showToast('error', 'Campo obrigatório', 'Preencha todos os dados corretamente');
                    return;
                }
                
                // Email Address validation
                const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
                if (!emailRegex.test(formData.email)) {
                    showToast('error', 'Email Address inválido', 'Por favor, insira um email válido');
                    return;
                }
                
                // Password match validation
                if (formData.senha !== formData.confirmar_senha) {
                    showToast('error', 'Error', 'As senhas não coincidem');
                    return;
                }
                
                // Password strength validation
                if (formData.senha.length < 6) {
                    showToast('error', 'Senha fraca', 'A senha deve ter pelo menos 6 caracteres');
                    return;
                }
                
                // Aadhaar/PAN/CNPJ validation
                const docNumber = formData.cnpj.replace(/\D/g, '');
                const isValid = docNumber.length <= 11 ? validateCPF(formData.cnpj) : validateCNPJ(formData.cnpj);
                
                if (!isValid) {
                    showToast('error', 'Error', docNumber.length <= 11 ? 'Aadhaar/PAN inválido' : 'CNPJ inválido');
                    return;
                }
                
                const button = $('#cadastro-button');
                button.prop('disabled', true);
                button.html('<span class="spinner-border spinner-border-sm me-2"></span> Criando conta...');
                
                // AJAX call to same page for processing
                $.ajax({
                    url: window.location.href,
                    type: 'POST',
                    contentType: 'application/json',
                    data: JSON.stringify(formData),
                    timeout: 30000, // 30 seconds timeout
                    success: function(response) {
                        try {
                            let data;
                            if (typeof response === 'string') {
                                data = JSON.parse(response);
                            } else {
                                data = response;
                            }
                            
                            if (data.status === 'success') {
                                showToast('success', 'Success', data.message);
                                button.html('<i class="fas fa-check me-2"></i> Conta criada');
                                
                                // Reset form
                                $('#registerForm')[0].reset();
                                
                                setTimeout(() => {
                                    showToast('success', 'Redirecionando...', 'Aguarde um momento');
                                }, 1500);
                                
                                setTimeout(() => {
                                    window.location.href = './';
                                }, 3000);
                            } else {
                                button.prop('disabled', false);
                                button.html('<i class="fas fa-user-plus me-2"></i> Create account');
                                showToast('error', 'Error', data.message || 'Error desconhecido');
                            }
                        } catch (e) {
                            console.error('Error ao processar resposta:', e);
                            button.prop('disabled', false);
                            button.html('<i class="fas fa-user-plus me-2"></i> Create account');
                            showToast('error', 'Error de Sistema', 'Response inválida do servidor');
                        }
                    },
                    error: function(xhr, status, error) {
                        console.error('Error AJAX:', status, error);
                        button.prop('disabled', false);
                        button.html('<i class="fas fa-user-plus me-2"></i> Create account');
                        
                        if (status === 'timeout') {
                            showToast('error', 'Timeout', 'A requisição demorou muito para responder');
                        } else {
                            showToast('error', 'Error de Conexão', 'Falha na conexão com o servidor');
                        }
                    }
                });
            });
        });
    </script>
</body>
</html>