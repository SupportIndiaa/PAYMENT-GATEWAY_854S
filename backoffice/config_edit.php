
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

// Consultar as configurações do site
$query = "SELECT * FROM config LIMIT 1";
$stmt = $pdo->prepare($query);
$stmt->execute();
$config = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$config) {
    die("Settings não encontradas.");
}

$message = "";
$messageType = "";

// Atualizar as configurações se o formulário for enviado
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['update'])) {
        // Captura os novos valores do formulário
        $name = $_POST['name'];
        $favicon = $_POST['favicon'];
        $logo = $_POST['logo'];
        $toggleLogo = $_POST['togglelogo'];
        $namePayLogo = isset($_POST['name_pay_logo']) ? 1 : 0;
        $primaryColor = $_POST['primary_color'];
        $secondaryColor = $_POST['secondary_color'];
        $antiFraudeMin = $_POST['anti_fraude_min'];

        // Captura e valida as taxas
        $taxMin = (float)$_POST['tax_min'];
        $taxCashIn = (int)$_POST['tax_cashin'];
        $taxCashOut = (int)$_POST['tax_cashout'];
        $taxInternal = (int)$_POST['tax_internal'];

        // Definir os valores mínimos
        $minTaxMin = 0.80;
        $minTaxCashIn = 3;
        $minTaxCashOut = 1;
        $minTaxInternal = 0;

        // Validar os valores
        if ($taxMin < $minTaxMin || $taxCashIn < $minTaxCashIn || $taxCashOut < $minTaxCashOut || $taxInternal < $minTaxInternal) {
            $message = "Os valores das taxas não podem ser menores que os valores mínimos.";
            $messageType = "error";
        } else {
            $query = "UPDATE config 
                      SET name = :name, favicon = :favicon, logo = :logo, togglelogo = :togglelogo, name_pay_logo = :name_pay_logo, 
                          primary_color = :primary_color, secondary_color = :secondary_color, anti_fraude_min = :anti_fraude_min,
                          tax_min = :tax_min, tax_cashin = :tax_cashin, tax_cashout = :tax_cashout, tax_internal = :tax_internal
                      WHERE id = 0";
            $stmt = $pdo->prepare($query);
            $stmt->execute([
                ':name' => $name,
                ':favicon' => $favicon,
                ':logo' => $logo,
                ':togglelogo' => $toggleLogo,
                ':name_pay_logo' => $namePayLogo,
                ':primary_color' => $primaryColor,
                ':secondary_color' => $secondaryColor,
                ':anti_fraude_min' => $antiFraudeMin,
                ':tax_min' => $taxMin,
                ':tax_cashin' => $taxCashIn,
                ':tax_cashout' => $taxCashOut,
                ':tax_internal' => $taxInternal
            ]);

            // Atualizar a variável para refletir as alterações
            $config = array_merge($config, [
                'name' => $name,
                'favicon' => $favicon,
                'logo' => $logo,
                'togglelogo' => $toggleLogo,
                'name_pay_logo' => $namePayLogo,
                'primary_color' => $primaryColor,
                'secondary_color' => $secondaryColor,
                'anti_fraude_min' => $antiFraudeMin,
                'tax_min' => $taxMin,
                'tax_cashin' => $taxCashIn,
                'tax_cashout' => $taxCashOut,
                'tax_internal' => $taxInternal
            ]);

            $message = "Settings atualizadas com sucesso!";
            $messageType = "success";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="pt-BR" dir="ltr" data-nav-layout="vertical" data-theme-mode="light" data-header-styles="light" data-menu-styles="light" data-toggled="close">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>RPay - Settings do Site</title>
    <link rel="icon" href="../img/favicon.png" type="image/png">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@200;300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    
    <style>
        :root {
            --primary-rgb: 59, 130, 246;
            --secondary-rgb: 16, 185, 129;
            
            /* Light Theme */
            --bg-primary: #f8fafc;
            --bg-secondary: rgba(255, 255, 255, 0.95);
            --bg-card: rgba(255, 255, 255, 0.9);
            --text-primary: #1e293b;
            --text-muted: #64748b;
            --border-color: rgba(59, 130, 246, 0.1);
            --sidebar-bg: linear-gradient(180deg, #ffffff 0%, #f8fafc 100%);
            --sidebar-item-hover: rgba(59, 130, 246, 0.08);
            --sidebar-item-active: linear-gradient(135deg, rgba(59, 130, 246, 0.1), rgba(6, 182, 212, 0.1));
            
            /* Shadows */
            --shadow-sm: 0 1px 2px 0 rgba(0, 0, 0, 0.05);
            --shadow: 0 1px 3px 0 rgba(0, 0, 0, 0.1), 0 1px 2px 0 rgba(0, 0, 0, 0.06);
            --shadow-md: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
            --shadow-lg: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05);
            --shadow-xl: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
            --shadow-2xl: 0 25px 50px -12px rgba(0, 0, 0, 0.25);
        }

        /* Dark Theme */
        [data-theme-mode="dark"] {
            --bg-primary: #0f1419;
            --bg-secondary: #1a1b2e;
            --bg-card: #16213e;
            --text-primary: #ffffff;
            --text-muted: #94a3b8;
            --border-color: rgba(255, 255, 255, 0.1);
            --sidebar-bg: linear-gradient(180deg, #1a1b2e 0%, #16213e 100%);
            --sidebar-item-hover: rgba(59, 130, 246, 0.15);
            --sidebar-item-active: linear-gradient(135deg, rgba(59, 130, 246, 0.2), rgba(6, 182, 212, 0.2));
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        html, body {
            height: 100%;
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif;
            overflow-x: hidden;
        }

        body {
            background: var(--bg-primary) !important;
            color: var(--text-primary) !important;
            font-weight: 300;
            line-height: 1.6;
            min-height: 100vh;
            position: relative;
            transition: all 0.3s ease;
            background-image: 
                radial-gradient(circland thet 20% 80%, rgba(59, 130, 246, 0.15) 0%, transparent 50%),
                radial-gradient(circland thet 80% 20%, rgba(6, 182, 212, 0.15) 0%, transparent 50%),
                radial-gradient(circland thet 40% 40%, rgba(16, 185, 129, 0.1) 0%, transparent 50%);
        }

        /* Layout Container */
        .app-container {
            display: flex;
            min-height: 100vh;
        }

        /* Enhanced Sidebar */
        .sidebar {
            position: fixed;
            left: 0;
            top: 0;
            bottom: 0;
            width: 280px;
            background: var(--sidebar-bg);
            backdrop-filter: blur(20px);
            border-right: 1px solid var(--border-color);
            z-index: 1000;
            transform: translateX(0);
            transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
            box-shadow: 8px 0 40px rgba(0, 0, 0, 0.15);
            display: flex;
            flex-direction: column;
            border-radius: 0 30px 30px 0;
        }

        .sidebar-header {
            padding: 24px 20px 20px;
            border-bottom: 1px solid var(--border-color);
            display: flex;
            align-items: center;
            justify-content: center;
            background: rgba(59, 130, 246, 0.05);
            backdrop-filter: blur(20px);
            min-height: 80px;
            flex-shrink: 0;
        }

        .sidebar-logo {
            height: 36px;
            filter: drop-shadow(0 4px 12px rgba(0, 0, 0, 0.3));
            opacity: 1;
            transition: all 0.3s ease;
        }

        [data-theme-mode="light"] .sidebar-logo {
            filter: drop-shadow(0 4px 12px rgba(0, 0, 0, 0.2)) brightness(0.8) contrast(1.2);
            background: rgba(255, 255, 255, 0.9);
            border-radius: 8px;
            padding: 4px 8px;
        }

        .sidebar-nav {
            flex: 1;
            padding: 16px 0;
            overflow-y: auto;
            scrollbar-width: none;
            -ms-overflow-style: none;
        }

        .sidebar-nav::-webkit-scrollbar {
            display: none;
        }

        .nav-item {
            display: flex;
            align-items: center;
            gap: 14px;
            padding: 12px 20px;
            color: var(--text-muted);
            text-decoration: none;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            margin: 2px 16px;
            border-radius: 25px;
            font-weight: 300;
            font-size: 14px;
            position: relative;
            overflow: hidden;
            cursor: pointer;
        }

        .nav-item::before {
            content: '';
            position: absolute;
            left: 0;
            top: 0;
            bottom: 0;
            width: 0;
            background: var(--sidebar-item-active);
            transition: width 0.4s cubic-bezier(0.4, 0, 0.2, 1);
            border-radius: 25px;
        }

        .nav-item:hover::before,
        .nav-item.active::before {
            width: 100%;
        }

        .nav-item:hover,
        .nav-item.active {
            color: rgb(var(--primary-rgb));
            transform: translateX(4px);
            background: var(--sidebar-item-hover);
        }

        .nav-item i {
            width: 18px;
            height: 18px;
            font-size: 16px;
            z-index: 1;
            transition: all 0.3s ease;
            flex-shrink: 0;
        }

        .nav-item:hover i,
        .nav-item.active i {
            color: rgb(var(--primary-rgb));
            transform: scale(1.1);
        }

        .nav-item span {
            z-index: 1;
            transition: all 0.3s ease;
        }

        /* Main Content */
        .main-content {
            margin-left: 280px;
            flex: 1;
            padding: 2rem;
            min-height: 100vh;
            transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
        }

        /* Enhanced Header Section */
        .page-header {
            background: var(--bg-card);
            backdrop-filter: blur(20px);
            border-radius: 24px;
            padding: 3rem 2rem;
            margin-bottom: 2rem;
            box-shadow: var(--shadow-xl);
            border: 1px solid var(--border-color);
            position: relative;
            overflow: hidden;
            animation: fadeInUp 0.8s ease;
        }

        .page-header::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 4px;
            background: linear-gradient(90deg, rgb(var(--primary-rgb)), rgb(var(--secondary-rgb)), rgb(var(--primary-rgb)));
            border-radius: 24px 24px 0 0;
        }

        .header-content {
            display: flex;
            align-items: center;
            justify-content: space-between;
            flex-wrap: wrap;
            gap: 1.5rem;
        }

        .header-left {
            display: flex;
            align-items: center;
            gap: 1rem;
            flex: 1;
            min-width: 0;
        }

        .header-icon {
            width: 64px;
            height: 64px;
            background: linear-gradient(135deg, rgb(var(--primary-rgb)), rgb(var(--primary-rgb)));
            border-radius: 20px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 28px;
            box-shadow: var(--shadow-lg);
            animation: pulse 2s infinite;
            flex-shrink: 0;
        }

        @keyframes pulse {
            0%, 100% { transform: scale(1); }
            50% { transform: scale(1.05); }
        }

        .header-text {
            flex: 1;
            min-width: 0;
        }

        .header-text h1 {
            font-size: 2.5rem;
            font-weight: 300;
            color: var(--text-primary);
            margin-bottom: 0.5rem;
            display: flex;
            align-items: center;
            gap: 1rem;
        }

        .header-text p {
            font-size: 1.1rem;
            color: var(--text-muted);
            font-weight: 300;
            max-width: 500px;
        }

        /* Form Container */
        .form-container {
            background: var(--bg-card);
            backdrop-filter: blur(20px);
            border-radius: 20px;
            padding: 2rem;
            box-shadow: var(--shadow-lg);
            border: 1px solid var(--border-color);
            animation: fadeInUp 0.8s ease 0.2s both;
        }

        .form-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 1.5rem;
        }

        .form-group {
            margin-bottom: 1.5rem;
        }

        .form-label {
            display: block;
            margin-bottom: 0.5rem;
            font-weight: 500;
            color: var(--text-primary);
            font-size: 0.875rem;
            text-transform: uppercase;
            letter-spacing: 0.05em;
        }

        .form-input, .form-select {
            width: 100%;
            padding: 0.875rem 1rem;
            background: rgba(59, 130, 246, 0.05);
            border: 1px solid var(--border-color);
            border-radius: 12px;
            color: var(--text-primary);
            font-size: 1rem;
            font-weight: 300;
            transition: all 0.3s ease;
        }

        .form-input:focus, .form-select:focus {
            outline: none;
            border-color: rgb(var(--primary-rgb));
            box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
            background: rgba(59, 130, 246, 0.08);
        }

        .form-input::placeholder {
            color: var(--text-muted);
        }

        .form-help {
            font-size: 0.75rem;
            color: var(--text-muted);
            margin-top: 0.25rem;
        }

        .checkbox-group {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            padding: 0.875rem 0;
        }

        .checkbox-input {
            width: 18px;
            height: 18px;
            accent-color: rgb(var(--primary-rgb));
        }

        .btn {
            padding: 0.875rem 2rem;
            border: none;
            border-radius: 12px;
            font-weight: 500;
            cursor: pointer;
            transition: all 0.3s ease;
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            font-size: 1rem;
            text-decoration: none;
        }

        .btn-primary {
            background: linear-gradient(135deg, rgb(var(--primary-rgb)), rgb(var(--primary-rgb)));
            color: white;
        }

        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(59, 130, 246, 0.4);
        }

        /* Fixed Popup Notification */
        .popup-notification {
            position: fixed;
            top: 2rem;
            right: 2rem;
            padding: 1.5rem 2rem;
            background: var(--bg-card);
            backdrop-filter: blur(20px);
            border-radius: 12px;
            border: 1px solid var(--border-color);
            color: var(--text-primary);
            z-index: 10000;
            transform: translateX(400px);
            transition: all 0.5s cubic-bezier(0.4, 0, 0.2, 1);
            box-shadow: var(--shadow-lg);
            min-width: 320px;
            max-width: 380px;
        }

        .popup-notification.show {
            transform: translateX(0);
        }

        .popup-notification.success {
            border-left: 4px solid #10b981;
        }

        .popup-notification.error {
            border-left: 4px solid #ef4444;
        }

        .popup-content {
            display: flex;
            align-items: center;
            gap: 1rem;
        }

        .popup-icon {
            font-size: 1.5rem;
            flex-shrink: 0;
        }

        .popup-icon.success {
            color: #10b981;
        }

        .popup-icon.error {
            color: #ef4444;
        }

        .popup-message {
            flex: 1;
            font-weight: 400;
            font-size: 0.95rem;
            line-height: 1.4;
        }

        .popup-close {
            background: none;
            border: none;
            color: var(--text-muted);
            cursor: pointer;
            font-size: 1.25rem;
            padding: 0.25rem;
            border-radius: 8px;
            transition: all 0.3s ease;
            flex-shrink: 0;
            width: 28px;
            height: 28px;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .popup-close:hover {
            color: var(--text-primary);
            background: rgba(59, 130, 246, 0.1);
        }

        /* Mobile Menu Toggle */
        .mobile-menu-toggle {
            position: fixed;
            top: 16px;
            left: 16px;
            z-index: 1001;
            display: none;
            width: 48px;
            height: 48px;
            background: var(--bg-card);
            border: 1px solid var(--border-color);
            border-radius: 12px;
            color: var(--text-primary);
            cursor: pointer;
            backdrop-filter: blur(20px);
            box-shadow: var(--shadow-lg);
            align-items: center;
            justify-content: center;
            font-size: 18px;
        }

        .mobile-menu-toggle:hover {
            background: rgb(var(--primary-rgb));
            color: white;
        }

        /* Theme Toggle */
        .theme-toggle {
            position: fixed;
            top: 16px;
            right: 16px;
            z-index: 1001;
            width: 48px;
            height: 48px;
            background: var(--bg-card);
            border: 1px solid var(--border-color);
            border-radius: 12px;
            color: var(--text-primary);
            cursor: pointer;
            backdrop-filter: blur(20px);
            box-shadow: var(--shadow-lg);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 18px;
            transition: all 0.2s ease;
        }

        .theme-toggle:hover {
            background: rgb(var(--primary-rgb));
            color: white;
            transform: scale(1.05);
        }

        /* Sidebar Overlay */
        .sidebar-overlay {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: rgba(0, 0, 0, 0.4);
            z-index: 1001;
            opacity: 0;
            visibility: hidden;
            transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
            backdrop-filter: blur(12px);
        }

        .sidebar-overlay.active {
            opacity: 1;
            visibility: visible;
        }

        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(30px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        /* MOBILE RESPONSIVE */
        @media (max-width: 1024px) {
            .sidebar {
                transform: translateX(-100%);
                z-index: 1002;
                border-radius: 0 25px 25px 0;
                width: 280px;
            }

            .sidebar.open {
                transform: translateX(0);
            }

            .main-content {
                margin-left: 0;
                padding: 16px;
                padding-top: 80px;
                width: 100%;
                max-width: 100%;
            }

            .mobile-menu-toggle {
                display: flex;
            }

            .page-header {
                padding: 1.5rem 1rem;
            }

            .header-content {
                flex-direction: column;
                align-items: stretch;
                gap: 1rem;
            }

            .header-left {
                gap: 0.75rem;
                align-items: flex-start;
            }

            .header-icon {
                width: 48px;
                height: 48px;
                font-size: 20px;
                flex-shrink: 0;
            }

            .header-text h1 {
                font-size: 1.5rem;
                line-height: 1.2;
            }

            .header-text p {
                font-size: 0.9rem;
            }

            .form-container {
                padding: 1.5rem;
            }

            .form-grid {
                grid-template-columns: 1fr;
                gap: 1rem;
            }

            .popup-notification {
                top: 1rem;
                right: 1rem;
                left: 1rem;
                min-width: auto;
                max-width: none;
                transform: translateY(-100px);
                width: auto;
            }

            .popup-notification.show {
                transform: translateY(0);
            }
        }

        @media (max-width: 640px) {
            .header-icon {
                width: 40px;
                height: 40px;
                font-size: 18px;
            }

            .header-text h1 {
                font-size: 1.25rem;
            }

            .form-container {
                padding: 1rem;
            }

            .popup-notification {
                top: 0.5rem;
                right: 0.5rem;
                left: 0.5rem;
                padding: 1rem 1.5rem;
            }
        }

        /* Prevent horizontal scroll */
        body, html {
            overflow-x: hidden;
            max-width: 100%;
        }
    </style>
</head>

<body data-theme-mode="light">
    <!-- Sidebar Overlay -->
    <div class="sidebar-overlay" id="sidebarOverlay"></div>

    <!-- Mobile Menu Toggle -->
    <button class="mobile-menu-toggle" id="mobileMenuToggle">
        <i class="fas fa-bars"></i>
    </button>

    <!-- Theme Toggle -->
    <button class="theme-toggle" id="themeToggle">
        <i class="fas fa-moon"></i>
    </button>

    <div class="app-container">
       <!-- Enhanced Sidebar -->
        <aside class="sidebar" id="sidebar">
            <div class="sidebar-header">
                <img id="sidebarLogo" class="sidebar-logo" src="teste.png" alt="Logo" />
            </div>

            <nav class="sidebar-nav">
                <a href="dashboard.php" class="nav-item active">
                    <i class="fas fa-chart-line"></i>
                    <span>Dashboard</span>
                </a>

                <a href="user_list.php" class="nav-item">
                    <i class="fas fa-users"></i>
                    <span>Users</span>
                </a>

                <a href="transaction_list.php" class="nav-item">
                    <i class="fas fa-exchange-alt"></i>
                    <span>Transactions</span>
                </a>

                <a href="adq.php" class="nav-item">
                    <i class="fas fa-credit-card"></i>
                    <span>Adquirentes</span>
                </a>

                <a href="documents_list.php" class="nav-item">
                    <i class="fas fa-clipboard-check"></i>
                    <span>Aprovação</span>
                </a>

                <a href="documents_view.php" class="nav-item">
                    <i class="fas fa-file-alt"></i>
                    <span>Todos Documentos</span>
                </a>

                <a href="premios.php" class="nav-item">
                    <i class="fas fa-trophy"></i>
                    <span>Rewards</span>
                </a>

                <a href="config_edit.php" class="nav-item">
                    <i class="fas fa-cogs"></i>
                    <span>Settings</span>
                </a>

                <a href="Webhooks.php" class="nav-item">
                    <i class="fas fa-link"></i>
                    <span>Webhooks</span>
                </a>

                <a href="logout.php" class="nav-item" style="margin-top: auto; color: #ef4444;">
                    <i class="fas fa-sign-out-alt"></i>
                    <span>Logout</span>
                </a>
            </nav>
        </aside>

        <!-- Main Content -->
        <main class="main-content">
            <!-- Enhanced Page Header -->
            <header class="page-header">
                <div class="header-content">
                    <div class="header-left">
                        <div class="header-icon">
                            <i class="fas fa-cogs"></i>
                        </div>
                        <div class="header-text">
                            <h1>Settings do Site</h1>
                            <p>Configure todas as opções e parâmetros do seu sistema</p>
                        </div>
                    </div>
                </div>
            </header>

            <!-- Form Container -->
            <section class="form-container">
                <form method="POST" id="configForm">
                    <div class="form-grid">
                        <!-- Settings Gerais -->
                        <div class="form-group">
                            <label for="name" class="form-label">
                                <i class="fas fa-globe"></i> Nome do Site
                            </label>
                            <input type="text" name="name" id="name" class="form-input" value="<?php echo htmlspecialchars($config['name']); ?>" required>
                        </div>

                        <div class="form-group">
                            <label for="favicon" class="form-label">
                                <i class="fas fa-image"></i> Favicon
                            </label>
                            <input type="text" name="favicon" id="favicon" class="form-input" value="<?php echo htmlspecialchars($config['favicon']); ?>" required>
                        </div>

                        <div class="form-group">
                            <label for="logo" class="form-label">
                                <i class="fas fa-image"></i> Logo Principal
                            </label>
                            <input type="text" name="logo" id="logo" class="form-input" value="<?php echo htmlspecialchars($config['logo']); ?>" required>
                        </div>

                        <div class="form-group">
                            <label for="togglelogo" class="form-label">
                                <i class="fas fa-image"></i> Mini Logo
                            </label>
                            <input type="text" name="togglelogo" id="togglelogo" class="form-input" value="<?php echo htmlspecialchars($config['togglelogo']); ?>" required>
                        </div>

                        <!-- Cores do Sistema -->
                        <div class="form-group">
                            <label for="primary_color" class="form-label">
                                <i class="fas fa-palette"></i> Cor Primária (RGB)
                            </label>
                            <input type="text" name="primary_color" id="primary_color" class="form-input" value="<?php echo htmlspecialchars($config['primary_color']); ?>" placeholder="Ex: 0, 203, 161" required>
                        </div>

                        <div class="form-group">
                            <label for="secondary_color" class="form-label">
                                <i class="fas fa-palette"></i> Cor Secundária (RGB)
                            </label>
                            <input type="text" name="secondary_color" id="secondary_color" class="form-input" value="<?php echo htmlspecialchars($config['secondary_color']); ?>" placeholder="Ex: 255, 87, 34" required>
                        </div>

                        <!-- Settings Anti-Fraude -->
                        <div class="form-group">
                            <label for="anti_fraude_min" class="form-label">
                                <i class="fas fa-shield-alt"></i> Valor Mínimo Anti-Fraude
                            </label>
                            <input type="text" name="anti_fraude_min" id="anti_fraude_min" class="form-input" value="<?php echo htmlspecialchars($config['anti_fraude_min']); ?>" placeholder="Ex: 100.00" required>
                            <div class="form-help">
                                Quando o depósito atingir ou exceder este valor, o cash-out será desativado.
                            </div>
                        </div>

                        <!-- Settings de Taxas -->
                        <div class="form-group">
                            <label for="tax_min" class="form-label">
                                <i class="fas fa-percentage"></i> Taxa Mínima
                            </label>
                            <input type="number" step="0.01" name="tax_min" id="tax_min" class="form-input" value="<?php echo htmlspecialchars($config['tax_min']); ?>" required>
                            <div class="form-help">Valor mínimo: 0.80</div>
                        </div>

                        <div class="form-group">
                            <label for="tax_cashin" class="form-label">
                                <i class="fas fa-percentage"></i> Taxa de Cash-In (%)
                            </label>
                            <input type="number" name="tax_cashin" id="tax_cashin" class="form-input" value="<?php echo htmlspecialchars($config['tax_cashin']); ?>" required>
                            <div class="form-help">Valor mínimo: 3%</div>
                        </div>

                        <div class="form-group">
                            <label for="tax_cashout" class="form-label">
                                <i class="fas fa-percentage"></i> Taxa de Cash-Out (%)
                            </label>
                            <input type="number" name="tax_cashout" id="tax_cashout" class="form-input" value="<?php echo htmlspecialchars($config['tax_cashout']); ?>" required>
                            <div class="form-help">Valor mínimo: 1%</div>
                        </div>

                        <div class="form-group">
                            <label for="tax_internal" class="form-label">
                                <i class="fas fa-percentage"></i> Taxa Interna (%)
                            </label>
                            <input type="number" name="tax_internal" id="tax_internal" class="form-input" value="<?php echo htmlspecialchars($config['tax_internal']); ?>" required>
                            <div class="form-help">Valor mínimo: 0%</div>
                        </div>
                    </div>

                    <!-- Checkbox Option -->
                    <div class="form-group">
                        <div class="checkbox-group">
                            <input type="checkbox" name="name_pay_logo" id="name_pay_logo" class="checkbox-input" <?php echo $config['name_pay_logo'] ? 'checked' : ''; ?>>
                            <label for="name_pay_logo" class="form-label" style="margin: 0; text-transform: none;">
                                <i class="fas fa-text-width"></i> Exibir Nome PAY depois da Logo
                            </label>
                        </div>
                    </div>

                    <button type="submit" name="update" class="btn btn-primary">
                        <i class="fas fa-save"></i>
                        Save Settings
                    </button>
                </form>
            </section>
        </main>
    </div>

    <!-- Fixed Popup Notification -->
    <div id="popup-notification" class="popup-notification">
        <div class="popup-content">
            <i id="popup-icon" class="popup-icon"></i>
            <div id="popup-message" class="popup-message"></div>
            <button id="popup-close" class="popup-close">
                <i class="fas fa-times"></i>
            </button>
        </div>
    </div>

    <script>
        // Theme functionality
        const themeToggle = document.getElementById('themeToggle');
        const themeIcon = themeToggle.querySelector('i');
        const htmlElement = document.documentElement;
        const sidebarLogo = document.getElementById('sidebarLogo');
        
        // Load saved theme
        const savedTheme = localStorage.getItem('admin_theme') || 'light';
        htmlElement.setAttribute('data-theme-mode', savedTheme);
        updateThemeIcon(savedTheme);
        updateLogo(savedTheme);
        
        themeToggle.addEventListener('click', () => {
            const currentTheme = htmlElement.getAttribute('data-theme-mode');
            const newTheme = currentTheme === 'light' ? 'dark' : 'light';
            
            htmlElement.setAttribute('data-theme-mode', newTheme);
            localStorage.setItem('admin_theme', newTheme);
            updateThemeIcon(newTheme);
            updateLogo(newTheme);
        });
        
        function updateThemeIcon(theme) {
            if (theme === 'dark') {
                themeIcon.className = 'fas fa-sun';
            } else {
                themeIcon.className = 'fas fa-moon';
            }
        }

        function updateLogo(theme) {
            if (theme === 'dark') {
                sidebarLogo.src = '../img/l.png';
            } else {
                sidebarLogo.src = '../teste.png';
            }
        }

        // Sidebar functionality
        const mobileMenuToggle = document.getElementById('mobileMenuToggle');
        const sidebar = document.getElementById('sidebar');
        const sidebarOverlay = document.getElementById('sidebarOverlay');

        function toggleSidebar() {
            sidebar.classList.toggle('open');
            sidebarOverlay.classList.toggle('active');
            document.body.style.overflow = sidebar.classList.contains('open') ? 'hidden' : '';
        }

        mobileMenuToggle.addEventListener('click', toggleSidebar);
        sidebarOverlay.addEventListener('click', toggleSidebar);

        // Popup functionality
        function showPopup(message, type, duration = 5000) {
            const popup = document.getElementById('popup-notification');
            const icon = document.getElementById('popup-icon');
            const messageEl = document.getElementById('popup-message');
            
            messageEl.textContent = message;
            popup.className = `popup-notification ${type}`;
            icon.className = `popup-icon ${type}`;
            
            if (type === 'success') {
                icon.className += ' fas fa-check-circle';
            } else {
                icon.className += ' fas fa-exclamation-circle';
            }
            
            popup.classList.add('show');
            
            // Auto hidand thefter duration
            setTimeout(() => {
                if (popup.classList.contains('show')) {
                    popup.classList.remove('show');
                }
            }, duration);
        }

        document.getElementById('popup-close').addEventListener('click', () => {
            document.getElementById('popup-notification').classList.remove('show');
        });

        // Show popup if there's a message
        <?php if (!empty($message)): ?>
            showPopup(<?php echo json_encode($message); ?>, <?php echo json_encode($messageType); ?>);
        <?php endif; ?>
    </script>
</body>
</html>
