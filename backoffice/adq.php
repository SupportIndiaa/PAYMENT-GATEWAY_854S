
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

// Função para atualizar arquivo .env
function updateEnvFile($key, $value) {
    $envPath = __DIR__ . '/../.env';
    $envContent = file_get_contents($envPath);
    
    // Sand the chave já existe, substitui
    if (preg_match("/^{$key}=.*$/m", $envContent)) {
        $envContent = preg_replace("/^{$key}=.*$/m", "{$key}={$value}", $envContent);
    } else {
        // Se não existe, adiciona
        $envContent .= "\n{$key}={$value}";
    }
    
    file_put_contents($envPath, $envContent);
}

// Processar formulário
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    $response = ['success' => false, 'message' => ''];
    
    if ($action === 'update_adquirente') {
        $adquirente = $_POST['adquirente'] ?? '';
        $client_id = $_POST['client_id'] ?? '';
        $client_secret = $_POST['client_secret'] ?? '';
        $ativo = $_POST['ativo'] ?? 'false';
        
        try {
            // Atualizar as credenciais sempre
            updateEnvFile('BSPAY_CLIENT_ID', $client_id);
            updateEnvFile('BSPAY_CLIENT_SECRET', $client_secret);
            
            if ($adquirente === 'bspay') {
                // Configurar URL específica para BSPAY
                updateEnvFile('BSPAY_URL', 'https://api.bspay.co/v2/');
                
                // Sand thetivando BSPAY, desativar PIXUP
                if ($ativo === 'true') {
                    updateEnvFile('PIXUP_ACTIVE', 'false');
                    updateEnvFile('BSPAY_ACTIVE', 'true');
                } else {
                    updateEnvFile('BSPAY_ACTIVE', 'false');
                }
                
                $response = ['success' => true, 'message' => 'Settings do BSPAY atualizadas com sucesso!'];
            } elseif ($adquirente === 'pixup') {
                // Configurar URL específica para PIXUP
                updateEnvFile('BSPAY_URL', 'https://api.pixupbr.com/v2/');
                
                // Sand thetivando PIXUP, desativar BSPAY
                if ($ativo === 'true') {
                    updateEnvFile('BSPAY_ACTIVE', 'false');
                    updateEnvFile('PIXUP_ACTIVE', 'true');
                } else {
                    updateEnvFile('PIXUP_ACTIVE', 'false');
                }
                
                $response = ['success' => true, 'message' => 'Settings do PIXUP atualizadas com sucesso!'];
            }
        } catch (Exception $e) {
            $response = ['success' => false, 'message' => 'Error ao atualizar configurações: ' . $e->getMessage()];
        }
    }
    
    header('Content-Type: application/json');
    echo json_encode($response);
    exit();
}

// Carregar configurações atuais - CORRIGIDO
$adquirentes = [
    'bspay' => [
        'name' => 'BSPAY',
        'description' => 'Processamento de pagamentos via BSPay',
        'url' => 'https://api.bspay.co/v2/', // URL CORRIGIDA
        'client_id' => $_ENV['BSPAY_CLIENT_ID'] ?? '',
        'client_secret' => $_ENV['BSPAY_CLIENT_SECRET'] ?? '',
        'active' => ($_ENV['BSPAY_ACTIVE'] ?? 'false') === 'true',
        'functional' => true,
        'icon' => 'fas fa-building'
    ],
    'pixup' => [
        'name' => 'PIXUP',
        'description' => 'Processamento de pagamentos via PixUp',
        'url' => 'https://api.pixupbr.com/v2/',
        'client_id' => $_ENV['BSPAY_CLIENT_ID'] ?? '',
        'client_secret' => $_ENV['BSPAY_CLIENT_SECRET'] ?? '',
        'active' => ($_ENV['PIXUP_ACTIVE'] ?? 'false') === 'true',
        'functional' => true,
        'icon' => 'fas fa-qrcode'
    ],
    'primepag' => [
        'name' => 'PRIMEPAG',
        'description' => 'Em desenvolvimento',
        'url' => 'https://api.primepag.com/v2/',
        'client_id' => '',
        'client_secret' => '',
        'active' => false,
        'functional' => false,
        'icon' => 'fas fa-star'
    ],
    'voluti' => [
        'name' => 'VOLUTI',
        'description' => 'Em desenvolvimento',
        'url' => 'https://api.voluti.com/v2/',
        'client_id' => '',
        'client_secret' => '',
        'active' => false,
        'functional' => false,
        'icon' => 'fas fa-rocket'
    ]
];
?>
<!DOCTYPE html>
<html lang="pt-BR" dir="ltr" data-nav-layout="vertical" data-theme-mode="light" data-header-styles="light" data-menu-styles="light" data-toggled="close">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>RPay - Adquirentes</title>
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

        /* Enhanced Adquirentes Grid */
        .adquirentes-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 1.5rem;
            animation: fadeInUp 0.8s ease 0.2s both;
        }

        .adquirente-card {
            background: var(--bg-card);
            backdrop-filter: blur(20px);
            border-radius: 24px;
            padding: 2rem;
            box-shadow: var(--shadow-lg);
            border: 1px solid var(--border-color);
            transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
            position: relative;
            overflow: hidden;
            min-height: 250px;
            display: flex;
            flex-direction: column;
        }

        .adquirente-card:hover {
            transform: translateY(-8px);
            box-shadow: var(--shadow-2xl);
        }

        .adquirente-card.active::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 4px;
            background: linear-gradient(90deg, rgb(var(--secondary-rgb)), rgb(var(--primary-rgb)));
            border-radius: 24px 24px 0 0;
        }

        .adquirente-card.disabled {
            opacity: 0.7;
        }

        .adquirente-card.disabled:hover {
            transform: translateY(-4px);
            box-shadow: var(--shadow-lg);
        }

        .adquirente-header {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            margin-bottom: 1.5rem;
        }

        .adquirente-info h3 {
            font-size: 1.5rem;
            font-weight: 600;
            color: var(--text-primary);
            margin-bottom: 0.5rem;
            display: flex;
            align-items: center;
            gap: 0.75rem;
        }

        .adquirente-info h3 i {
            width: 24px;
            height: 24px;
            color: rgb(var(--primary-rgb));
            transition: all 0.3s ease;
        }

        .adquirente-card.active .adquirente-info h3 i {
            color: rgb(var(--secondary-rgb));
            transform: scale(1.1);
        }

        .adquirente-description {
            font-size: 1rem;
            color: var(--text-muted);
            margin-bottom: 1rem;
            line-height: 1.5;
        }

        .adquirente-url {
            font-size: 0.875rem;
            color: var(--text-muted);
            font-family: 'Courier New', monospace;
            background: rgba(59, 130, 246, 0.1);
            padding: 0.5rem 0.75rem;
            border-radius: 12px;
            display: inline-block;
            word-break: break-all;
            margin-bottom: 1.5rem;
        }

        .adquirente-status {
            position: absolute;
            top: 1.5rem;
            right: 1.5rem;
        }

        .status-badge {
            padding: 0.5rem 1rem;
            border-radius: 25px;
            font-size: 0.75rem;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            white-space: nowrap;
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
        }

        .status-badge.active {
            background: rgba(16, 185, 129, 0.15);
            color: rgb(var(--secondary-rgb));
            border: 1px solid rgba(16, 185, 129, 0.3);
        }

        .status-badge.inactive {
            background: rgba(107, 114, 128, 0.15);
            color: #6b7280;
            border: 1px solid rgba(107, 114, 128, 0.3);
        }

        .status-badge.disabled {
            background: rgba(249, 115, 22, 0.15);
            color: #f97316;
            border: 1px solid rgba(249, 115, 22, 0.3);
        }

        .adquirente-actions {
            display: flex;
            gap: 0.75rem;
            margin-top: auto;
            padding-top: 1rem;
        }

        .btn {
            padding: 0.875rem 1.5rem;
            border: none;
            border-radius: 14px;
            font-weight: 500;
            cursor: pointer;
            transition: all 0.3s ease;
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            font-size: 0.875rem;
            text-decoration: none;
            flex: 1;
            justify-content: center;
        }

        .btn-primary {
            background: linear-gradient(135deg, rgb(var(--primary-rgb)), rgb(var(--primary-rgb)));
            color: white;
        }

        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(59, 130, 246, 0.4);
        }

        .btn-success {
            background: linear-gradient(135deg, rgb(var(--secondary-rgb)), rgb(var(--secondary-rgb)));
            color: white;
        }

        .btn-success:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(16, 185, 129, 0.4);
        }

        .btn-secondary {
            background: rgba(107, 114, 128, 0.1);
            color: var(--text-muted);
            border: 1px solid var(--border-color);
        }

        .btn-secondary:hover {
            background: rgba(59, 130, 246, 0.1);
            color: var(--text-primary);
            transform: translateY(-2px);
        }

        .btn:disabled {
            opacity: 0.5;
            cursor: not-allowed;
            transform: none !important;
        }

        /* Modal Styles */
        .modal-overlay {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: rgba(0, 0, 0, 0.5);
            backdrop-filter: blur(12px);
            z-index: 2000;
            opacity: 0;
            visibility: hidden;
            transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
        }

        .modal-overlay.active {
            opacity: 1;
            visibility: visible;
        }

        .modal {
            position: fixed;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%) scale(0.9);
            background: var(--bg-card);
            border-radius: 20px;
            padding: 2rem;
            box-shadow: var(--shadow-2xl);
            border: 1px solid var(--border-color);
            z-index: 2001;
            min-width: 500px;
            max-width: 90vw;
            transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
            opacity: 0;
            backdrop-filter: blur(20px);
        }

        .modal.active {
            transform: translate(-50%, -50%) scale(1);
            opacity: 1;
        }

        .modal-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 2rem;
            padding-bottom: 1rem;
            border-bottom: 1px solid var(--border-color);
        }

        .modal-title {
            font-size: 1.5rem;
            font-weight: 600;
            color: var(--text-primary);
        }

        .modal-close {
            width: 40px;
            height: 40px;
            border: none;
            background: rgba(107, 114, 128, 0.1);
            border-radius: 10px;
            color: var(--text-muted);
            cursor: pointer;
            transition: all 0.3s ease;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .modal-close:hover {
            background: rgba(239, 68, 68, 0.1);
            color: #ef4444;
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

        .form-input {
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

        .form-input:focus {
            outline: none;
            border-color: rgb(var(--primary-rgb));
            box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
            background: rgba(59, 130, 246, 0.08);
        }

        .form-input:disabled {
            opacity: 0.6;
            cursor: not-allowed;
        }

        .switch {
            position: relative;
            display: inline-block;
            width: 60px;
            height: 34px;
        }

        .switch input {
            opacity: 0;
            width: 0;
            height: 0;
        }

        .slider {
            position: absolute;
            cursor: pointer;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background-color: #ccc;
            transition: .4s;
            border-radius: 34px;
        }

        .slider:before {
            position: absolute;
            content: "";
            height: 26px;
            width: 26px;
            left: 4px;
            bottom: 4px;
            background-color: white;
            transition: .4s;
            border-radius: 50%;
        }

        input:checked + .slider {
            background-color: rgb(var(--secondary-rgb));
        }

        input:checked + .slider:before {
            transform: translateX(26px);
        }

        /* Toast Notification */
        .toast {
            position: fixed;
            top: 20px;
            right: 20px;
            background: var(--bg-card);
            border-radius: 12px;
            padding: 1rem 1.5rem;
            box-shadow: var(--shadow-xl);
            border: 1px solid var(--border-color);
            z-index: 3000;
            transform: translateX(400px);
            transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
            min-width: 300px;
            backdrop-filter: blur(20px);
        }

        .toast.show {
            transform: translateX(0);
        }

        .toast.success {
            border-left: 4px solid rgb(var(--secondary-rgb));
        }

        .toast.error {
            border-left: 4px solid #ef4444;
        }

        .toast-content {
            display: flex;
            align-items: center;
            gap: 0.75rem;
        }

        .toast-icon {
            width: 20px;
            height: 20px;
            flex-shrink: 0;
        }

        .toast-icon.success {
            color: rgb(var(--secondary-rgb));
        }

        .toast-icon.error {
            color: #ef4444;
        }

        .toast-message {
            flex: 1;
            color: var(--text-primary);
            font-weight: 500;
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

            .adquirentes-grid {
                grid-template-columns: 1fr;
                gap: 1rem;
            }

            .adquirente-card {
                padding: 1.5rem;
                min-height: auto;
            }

            .adquirente-header {
                flex-direction: column;
                gap: 1rem;
                margin-bottom: 1rem;
            }

            .adquirente-status {
                position: static;
                align-self: flex-start;
            }

            .adquirente-actions {
                flex-direction: column;
                gap: 0.75rem;
            }

            .modal {
                min-width: 90vw;
                margin: 1rem;
                padding: 1.5rem;
            }

            .toast {
                right: 10px;
                left: 10px;
                min-width: auto;
                transform: translateY(-100px);
            }

            .toast.show {
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

            .adquirente-card {
                padding: 1.25rem;
            }

            .modal {
                padding: 1rem;
            }

            .btn {
                padding: 0.75rem 1rem;
                font-size: 0.8rem;
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
                <a href="dashboard.php" class="nav-item">
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

                <a href="adq.php" class="nav-item active">
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
                            <i class="fas fa-credit-card"></i>
                        </div>
                        <div class="header-text">
                            <h1>Adquirentes</h1>
                            <p>Gerenciand thes configurações das adquirentes de pagamento</p>
                        </div>
                    </div>
                </div>
            </header>

            <!-- Enhanced Adquirentes Grid -->
            <section class="adquirentes-grid">
                <?php foreach ($adquirentes as $key => $adquirente): ?>
                    <div class="adquirente-card <?php echo $adquirente['active'] ? 'active' : ''; ?> <?php echo !$adquirente['functional'] ? 'disabled' : ''; ?>">
                        <div class="adquirente-status">
                            <?php if (!$adquirente['functional']): ?>
                                <span class="status-badge disabled">
                                    <i class="fas fa-clock"></i>
                                    Em Breve
                                </span>
                            <?php elseif ($adquirente['active']): ?>
                                <span class="status-badgand thective">
                                    <i class="fas fa-check-circle"></i>
                                    Ativo
                                </span>
                            <?php else: ?>
                                <span class="status-badge inactive">
                                    <i class="fas fa-pause-circle"></i>
                                    Inativo
                                </span>
                            <?php endif; ?>
                        </div>

                        <div class="adquirente-header">
                            <div class="adquirente-info">
                                <h3>
                                    <i class="<?php echo $adquirente['icon']; ?>"></i>
                                    <?php echo $adquirente['name']; ?>
                                </h3>
                                <div class="adquirente-description"><?php echo $adquirente['description']; ?></div>
                            </div>
                        </div>

                        <div class="adquirente-url"><?php echo $adquirente['url']; ?></div>

                        <div class="adquirente-actions">
                            <?php if ($adquirente['functional']): ?>
                                <button class="btn btn-primary" onclick="editAdquirente('<?php echo $key; ?>')">
                                    <i class="fas fa-edit"></i>
                                    Configurar
                                </button>
                                <?php if ($adquirente['active']): ?>
                                    <button class="btn btn-secondary" onclick="toggleAdquirente('<?php echo $key; ?>', false)">
                                        <i class="fas fa-pause"></i>
                                        Desativar
                                    </button>
                                <?php else: ?>
                                    <button class="btn btn-success" onclick="toggleAdquirente('<?php echo $key; ?>', true)">
                                        <i class="fas fa-play"></i>
                                        Ativar
                                    </button>
                                <?php endif; ?>
                            <?php else: ?>
                                <button class="btn btn-secondary" disabled>
                                    <i class="fas fa-lock"></i>
                                    Indisponível
                                </button>
                            <?php endif; ?>
                        </div>
                    </div>
                <?php endforeach; ?>
            </section>
        </main>
    </div>

    <!-- Modal de Edição -->
    <div class="modal-overlay" id="modalOverlay">
        <div class="modal" id="editModal">
            <div class="modal-header">
                <h2 class="modal-title" id="modalTitle">Editar Adquirente</h2>
                <button class="modal-close" onclick="closeModal()">
                    <i class="fas fa-times"></i>
                </button>
            </div>

            <form id="editForm">
                <input type="hidden" id="adquirente" name="adquirente">
                <input type="hidden" name="action" value="update_adquirente">

                <div class="form-group">
                    <label for="url" class="form-label">
                        <i class="fas fa-link"></i> URL da API
                    </label>
                    <input type="url" id="url" name="url" class="form-input" readonly>
                </div>

                <div class="form-group">
                    <label for="client_id" class="form-label">
                        <i class="fas fa-key"></i> Client ID
                    </label>
                    <input type="text" id="client_id" name="client_id" class="form-input" placeholder="Digite o Client ID..." required>
                </div>

                <div class="form-group">
                    <label for="client_secret" class="form-label">
                        <i class="fas fa-lock"></i> Client Secret
                    </label>
                    <input type="password" id="client_secret" name="client_secret" class="form-input" placeholder="Digite o Client Secret..." required>
                </div>

                <div class="form-group">
                    <label class="form-label">
                        <i class="fas fa-toggle-on"></i> Status
                    </label>
                    <label class="switch">
                        <input type="checkbox" id="ativo" name="ativo" value="true">
                        <span class="slider"></span>
                    </label>
                </div>

                <div class="adquirente-actions">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save"></i>
                        Save Settings
                    </button>
                    <button type="button" class="btn btn-secondary" onclick="closeModal()">
                        <i class="fas fa-times"></i>
                        Cancel
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Toast Notification -->
    <div class="toast" id="toast">
        <div class="toast-content">
            <div class="toast-icon" id="toastIcon">
                <i class="fas fa-check-circle"></i>
            </div>
            <div class="toast-message" id="toastMessage"></div>
        </div>
    </div>

    <script>
        // Dados das adquirentes
        const adquirentes = <?php echo json_encode($adquirentes); ?>;

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

        // Modal functionality
        function editAdquirente(key) {
            const adquirente = adquirentes[key];
            
            document.getElementById('modalTitle').textContent = `Configurar ${adquirente.name}`;
            document.getElementById('adquirente').value = key;
            document.getElementById('url').value = adquirente.url;
            document.getElementById('client_id').value = adquirente.client_id;
            document.getElementById('client_secret').value = adquirente.client_secret;
            document.getElementById('ativo').checked = adquirente.active;
            
            openModal();
        }

        function toggleAdquirente(key, ativo) {
            const adquirente = adquirentes[key];
            
            const formData = new FormData();
            formData.append('action', 'update_adquirente');
            formData.append('adquirente', key);
            formData.append('client_id', adquirente.client_id);
            formData.append('client_secret', adquirente.client_secret);
            formData.append('ativo', ativo ? 'true' : 'false');
            
            fetch('', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                showToast(data.message, data.success ? 'success' : 'error');
                if (data.success) {
                    setTimeout(() => location.reload(), 1500);
                }
            })
            .catch(error => {
                showToast('Error ao processar solicitação', 'error');
                console.error('Errorr:', error);
            });
        }

        function openModal() {
            document.getElementById('modalOverlay').classList.add('active');
            document.getElementById('editModal').classList.add('active');
            document.body.style.overflow = 'hidden';
        }

        function closeModal() {
            document.getElementById('modalOverlay').classList.remove('active');
            document.getElementById('editModal').classList.remove('active');
            document.body.style.overflow = '';
        }

        // Form submission - CORRIGIDO
        document.getElementById('editForm').addEventListener('submit', function(e) {
            e.preventDefault();
            
            const formData = new FormData(this);
            
            // As URLs já estão corretas no array $adquirentes
            // No precisa mais alterar aqui
            
            fetch('', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                showToast(data.message, data.success ? 'success' : 'error');
                if (data.success) {
                    closeModal();
                    setTimeout(() => location.reload(), 1500);
                }
            })
            .catch(error => {
                showToast('Error ao processar solicitação', 'error');
                console.error('Errorr:', error);
            });
        });

        // Toast functionality
        function showToast(message, type = 'success') {
            const toast = document.getElementById('toast');
            const toastIcon = document.getElementById('toastIcon');
            const toastMessage = document.getElementById('toastMessage');
            
            // Reset classes
            toast.className = 'toast';
            toastIcon.className = 'toast-icon';
            
            // Set type
            toast.classList.add(type);
            toastIcon.classList.add(type);
            
            // Set content
            toastMessage.textContent = message;
            
            if (type === 'success') {
                toastIcon.innerHTML = '<i class="fas fa-check-circle"></i>';
            } else {
                toastIcon.innerHTML = '<i class="fas fa-exclamation-circle"></i>';
            }
            
            // Show toast
            toast.classList.add('show');
            
            // Hidand thefter 4 seconds
            setTimeout(() => {
                toast.classList.remove('show');
            }, 4000);
        }

        // Close modal on overlay click
        document.getElementById('modalOverlay').addEventListener('click', function(e) {
            if (e.target === this) {
                closeModal();
            }
        });
    </script>
</body>
</html>
