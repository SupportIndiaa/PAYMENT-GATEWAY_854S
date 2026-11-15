
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

// Calcular estatísticas do dashboard
$stats = [];

// Faturamento total
$query = "SELECT COALESCE(SUM(amount), 0) as total FROM transactions WHERE status = 'PAID'";
$stmt = $pdo->prepare($query);
$stmt->execute();
$stats['faturamento_total'] = $stmt->fetchColumn();

// Faturamento do mês atual
$query = "SELECT COALESCE(SUM(amount), 0) as total FROM transactions WHERE status = 'PAID' AND DATE_FORMAT(created_at, '%Y-%m') = DATE_FORMAT(NOW(), '%Y-%m')";
$stmt = $pdo->prepare($query);
$stmt->execute();
$stats['faturamento_mes'] = $stmt->fetchColumn();

// Faturamento do mês passado
$query = "SELECT COALESCE(SUM(amount), 0) as total FROM transactions WHERE status = 'PAID' AND DATE_FORMAT(created_at, '%Y-%m') = DATE_FORMAT(DATE_SUB(NOW(), INTERVAL 1 MONTH), '%Y-%m')";
$stmt = $pdo->prepare($query);
$stmt->execute();
$stats['faturamento_mes_passado'] = $stmt->fetchColumn();

// Calcular crescimento mensal
$stats['crescimento_mensal'] = $stats['faturamento_mes_passado'] > 0 
    ? (($stats['faturamento_mes'] - $stats['faturamento_mes_passado']) / $stats['faturamento_mes_passado']) * 100 
    : 0;

// Transactions por status
$query = "SELECT status, COUNT(*) as count FROM transactions GROUP BY status";
$stmt = $pdo->prepare($query);
$stmt->execute();
$transactionsByStatus = $stmt->fetchAll(PDO::FETCH_ASSOC);

foreach ($transactionsByStatus as $row) {
    $stats['transacoes_' . strtolower($row['status'])] = $row['count'];
}

// Garantir que todas as estatísticas existam
$stats['transacoes_paid'] = $stats['transacoes_paid'] ?? 0;
$stats['transacoes_pending'] = $stats['transacoes_pending'] ?? 0;
$stats['transacoes_cancelled'] = $stats['transacoes_cancelled'] ?? 0;
$stats['transacoes_reversed'] = $stats['transacoes_reversed'] ?? 0;

// Total de transações
$stats['transacoes_total'] = array_sum(array_filter($stats, function($key) {
    return strpos($key, 'transacoes_') === 0 && $key !== 'transacoes_total';
}, ARRAY_FILTER_USE_KEY));

// Total de usuários (TODOS, não mensal)
$query = "SELECT COUNT(*) as total FROM users";
$stmt = $pdo->prepare($query);
$stmt->execute();
$stats['usuarios_total'] = $stmt->fetchColumn();

// Users ativos (com transações nos últimos 30 dias)
$query = "SELECT COUNT(DISTINCT user_id) as total FROM transactions WHERE created_at >= DATE_SUB(NOW(), INTERVAL 30 DAY)";
$stmt = $pdo->prepare($query);
$stmt->execute();
$stats['usuarios_ativos'] = $stmt->fetchColumn();

// Documentos
$query = "SELECT approved, COUNT(*) as count FROM users_documents GROUP BY approved";
$stmt = $pdo->prepare($query);
$stmt->execute();
$documentsByStatus = $stmt->fetchAll(PDO::FETCH_ASSOC);

$stats['documentos_pendentes'] = 0;
$stats['documentos_aprovados'] = 0;
$stats['documentos_rejeitados'] = 0;

foreach ($documentsByStatus as $row) {
    if ($row['approved'] == 0) $stats['documentos_pendentes'] = $row['count'];
    if ($row['approved'] == 1) $stats['documentos_aprovados'] = $row['count'];
    if ($row['approved'] == -1) $stats['documentos_rejeitados'] = $row['count'];
}

// Dados para gráficos - Últimos 7 dias
$query = "
    SELECT 
        DATE(created_at) as date,
        COUNT(*) as transactions,
        COALESCE(SUM(CASE WHEN status = 'PAID' THEN amount ELSE 0 END), 0) as revenue
    FROM transactions 
    WHERE created_at >= DATE_SUB(NOW(), INTERVAL 7 DAY)
    GROUP BY DATE(created_at)
    ORDER BY DATE(created_at)
";
$stmt = $pdo->prepare($query);
$stmt->execute();
$chartData = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="pt-BR" dir="ltr" data-nav-layout="vertical" data-theme-mode="light" data-header-styles="light" data-menu-styles="light" data-toggled="close">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>RPay - Dashboard Administrativo</title>
    <link rel="icon" href="../img/favicon.png" type="image/png">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@200;300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    
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

        /* Stats Grid */
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
            gap: 1.5rem;
            margin-bottom: 2rem;
            animation: fadeInUp 0.8s ease 0.2s both;
        }

        .stat-card {
            background: var(--bg-card);
            backdrop-filter: blur(20px);
            border-radius: 20px;
            padding: 1.5rem;
            box-shadow: var(--shadow-lg);
            border: 1px solid var(--border-color);
            transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
            position: relative;
            overflow: hidden;
        }

        .stat-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 4px;
            background: linear-gradient(90deg, rgb(var(--primary-rgb)), rgb(var(--secondary-rgb)));
        }

        .stat-card:hover {
            transform: translateY(-8px);
            box-shadow: var(--shadow-2xl);
        }

        .stat-header {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            margin-bottom: 1rem;
        }

        .stat-title {
            font-size: 0.875rem;
            font-weight: 500;
            color: var(--text-muted);
            text-transform: uppercase;
            letter-spacing: 0.05em;
        }

        .stat-icon {
            width: 40px;
            height: 40px;
            background: rgba(59, 130, 246, 0.2);
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: rgb(var(--primary-rgb));
            font-size: 1.25rem;
        }

        .stat-value {
            font-size: 1.75rem;
            font-weight: 600;
            color: var(--text-primary);
            margin-bottom: 0.5rem;
        }

        .stat-change {
            display: flex;
            align-items: center;
            gap: 0.25rem;
            font-size: 0.875rem;
            font-weight: 400;
            color: rgb(var(--secondary-rgb));
        }

        /* Charts Grid */
        .charts-grid {
            display: grid;
            grid-template-columns: 2fr 1fr;
            gap: 1.5rem;
            margin-bottom: 2rem;
        }

        .chart-card {
            background: var(--bg-card);
            border-radius: 20px;
            padding: 1.5rem;
            box-shadow: var(--shadow-lg);
            border: 1px solid var(--border-color);
        }

        .chart-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 1.5rem;
            padding-bottom: 1rem;
            border-bottom: 1px solid var(--border-color);
        }

        .chart-title {
            font-size: 1.25rem;
            font-weight: 600;
            color: var(--text-primary);
        }

        .chart-container {
            position: relative;
            height: 300px;
        }

        .chart-container.small {
            height: 250px;
        }

        /* Activity Section */
        .activity-section {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 1.5rem;
        }

        .activity-card {
            background: var(--bg-card);
            border-radius: 20px;
            padding: 1.5rem;
            box-shadow: var(--shadow-lg);
            border: 1px solid var(--border-color);
        }

        .activity-header {
            display: flex;
            align-items: center;
            justify-content: between;
            margin-bottom: 1rem;
            padding-bottom: 1rem;
            border-bottom: 1px solid var(--border-color);
        }

        .activity-title {
            font-size: 1.125rem;
            font-weight: 600;
            color: var(--text-primary);
        }

        .activity-item {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            padding: 0.75rem 0;
            border-bottom: 1px solid var(--border-color);
        }

        .activity-item:last-child {
            border-bottom: none;
        }

        .activity-icon {
            width: 32px;
            height: 32px;
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 0.875rem;
            color: white;
        }

        .activity-icon.success { background: rgb(var(--secondary-rgb)); }
        .activity-icon.warning { background: #f59e0b; }
        .activity-icon.danger { background: #ef4444; }

        .activity-content {
            flex: 1;
        }

        .activity-text {
            font-size: 0.875rem;
            color: var(--text-primary);
            margin-bottom: 0.25rem;
        }

        .activity-time {
            font-size: 0.75rem;
            color: var(--text-muted);
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

            .stats-grid {
                grid-template-columns: 1fr;
                gap: 1rem;
            }

            .charts-grid {
                grid-template-columns: 1fr;
            }

            .activity-section {
                grid-template-columns: 1fr;
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

            .stat-card {
                padding: 1rem;
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
                            <i class="fas fa-chart-line"></i>
                        </div>
                        <div class="header-text">
                            <h1>Dashboard Administrativo</h1>
                            <p>Visão geral completa do seu gateway de pagamentos</p>
                        </div>
                    </div>
                </div>
            </header>

            <!-- Stats Grid -->
            <section class="stats-grid">
                <!-- Faturamento Total -->
                <div class="stat-card">
                    <div class="stat-header">
                        <div>
                            <div class="stat-title">Faturamento Total</div>
                            <div class="stat-value">R$ <?php echo number_format($stats['faturamento_total'], 2, ',', '.'); ?></div>
                            <div class="stat-change">
                                <i class="fas fa-chart-line"></i>
                                <span>Transactions pagas</span>
                            </div>
                        </div>
                        <div class="stat-icon">
                            <i class="fas fa-dollar-sign"></i>
                        </div>
                    </div>
                </div>

                <!-- Faturamento do Mês -->
                <div class="stat-card">
                    <div class="stat-header">
                        <div>
                            <div class="stat-title">Faturamento do Mês</div>
                            <div class="stat-value">R$ <?php echo number_format($stats['faturamento_mes'], 2, ',', '.'); ?></div>
                            <div class="stat-change">
                                <i class="fas fa-arrow-<?php echo $stats['crescimento_mensal'] >= 0 ? 'up' : 'down'; ?>"></i>
                                <span><?php echo number_format(abs($stats['crescimento_mensal']), 1); ?>% vs mês anterior</span>
                            </div>
                        </div>
                        <div class="stat-icon">
                            <i class="fas fa-calendar-alt"></i>
                        </div>
                    </div>
                </div>

                <!-- Total de Users -->
                <div class="stat-card">
                    <div class="stat-header">
                        <div>
                            <div class="stat-title">Total de Users</div>
                            <div class="stat-value"><?php echo number_format($stats['usuarios_total']); ?></div>
                            <div class="stat-change">
                                <i class="fas fa-users"></i>
                                <span><?php echo number_format($stats['usuarios_ativos']); ?> ativos (30 dias)</span>
                            </div>
                        </div>
                        <div class="stat-icon">
                            <i class="fas fa-users"></i>
                        </div>
                    </div>
                </div>

                <!-- Transactions Total -->
                <div class="stat-card">
                    <div class="stat-header">
                        <div>
                            <div class="stat-title">Transactions Total</div>
                            <div class="stat-value"><?php echo number_format($stats['transacoes_total']); ?></div>
                            <div class="stat-change">
                                <i class="fas fa-check-circle"></i>
                                <span><?php echo number_format($stats['transacoes_paid']); ?> aprovadas</span>
                            </div>
                        </div>
                        <div class="stat-icon">
                            <i class="fas fa-exchange-alt"></i>
                        </div>
                    </div>
                </div>
            </section>

            <!-- Charts Grid -->
            <section class="charts-grid">
                <!-- Revenue Chart -->
                <div class="chart-card">
                    <div class="chart-header">
                        <h3 class="chart-title">Faturamento dos Last 7 Days</h3>
                        <div style="color: rgb(var(--secondary-rgb)); font-weight: 600;">
                            <i class="fas fa-arrow-up"></i> +12% esta semana
                        </div>
                    </div>
                    <div class="chart-container">
                        <canvas id="revenueChart"></canvas>
                    </div>
                </div>

                <!-- Status Pie Chart -->
                <div class="chart-card">
                    <div class="chart-header">
                        <h3 class="chart-title">Status das Transactions</h3>
                    </div>
                    <div class="chart-container small">
                        <canvas id="statusChart"></canvas>
                    </div>
                </div>
            </section>

            <!-- Activity Section -->
            <section class="activity-section">
                <!-- Quick Stats -->
                <div class="activity-card">
                    <div class="activity-header">
                        <h3 class="activity-title">Estatísticas Rápidas</h3>
                    </div>
                    <div class="activity-item">
                        <div class="activity-icon success">
                            <i class="fas fa-check"></i>
                        </div>
                        <div class="activity-content">
                            <div class="activity-text"><?php echo number_format($stats['transacoes_paid']); ?> Transactions Aprovadas</div>
                            <div class="activity-time">Status Success</div>
                        </div>
                    </div>
                    <div class="activity-item">
                        <div class="activity-icon warning">
                            <i class="fas fa-clock"></i>
                        </div>
                        <div class="activity-content">
                            <div class="activity-text"><?php echo number_format($stats['transacoes_pending']); ?> Transactions Pendings</div>
                            <div class="activity-time">Status Pending</div>
                        </div>
                    </div>
                    <div class="activity-item">
                        <div class="activity-icon danger">
                            <i class="fas fa-ban"></i>
                        </div>
                        <div class="activity-content">
                            <div class="activity-text"><?php echo number_format($stats['transacoes_cancelled']); ?> Transactions Canceladas</div>
                            <div class="activity-time">Status Cancelado</div>
                        </div>
                    </div>
                </div>

                <!-- Documents Stats -->
                <div class="activity-card">
                    <div class="activity-header">
                        <h3 class="activity-title">Status dos Documentos</h3>
                    </div>
                    <div class="activity-item">
                        <div class="activity-icon success">
                            <i class="fas fa-check"></i>
                        </div>
                        <div class="activity-content">
                            <div class="activity-text"><?php echo number_format($stats['documentos_aprovados']); ?> Documentos Aprovados</div>
                            <div class="activity-time">Verificados and theprovados</div>
                        </div>
                    </div>
                    <div class="activity-item">
                        <div class="activity-icon warning">
                            <i class="fas fa-file-alt"></i>
                        </div>
                        <div class="activity-content">
                            <div class="activity-text"><?php echo number_format($stats['documentos_pendentes']); ?> Documentos Pendings</div>
                            <div class="activity-time">Aguardando análise</div>
                        </div>
                    </div>
                    <div class="activity-item">
                        <div class="activity-icon danger">
                            <i class="fas fa-ban"></i>
                        </div>
                        <div class="activity-content">
                            <div class="activity-text"><?php echo number_format($stats['documentos_rejeitados']); ?> Documentos Rejeitados</div>
                            <div class="activity-time">No aprovados</div>
                        </div>
                    </div>
                </div>
            </section>
        </main>
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

        // Chart.js configurations
        const chartOptions = {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    display: true,
                    position: 'bottom'
                }
            }
        };

        // Revenue Chart
        const revenueCtx = document.getElementById('revenueChart').getContext('2d');
        const revenueChart = new Chart(revenueCtx, {
            type: 'line',
            data: {
                labels: [
                    <?php 
                    $labels = [];
                    $revenues = [];
                    
                    // Fill last 7 days
                    for ($i = 6; $i >= 0; $i--) {
                        $date = date('Y-m-d', strtotime("-$i days"));
                        $labels[] = "'" . date('d/m', strtotime($date)) . "'";
                        
                        $found = false;
                        foreach ($chartData as $data) {
                            if ($data['date'] === $date) {
                                $revenues[] = $data['revenue'];
                                $found = true;
                                break;
                            }
                        }
                        if (!$found) {
                            $revenues[] = 0;
                        }
                    }
                    
                    echo implode(', ', $labels);
                    ?>
                ],
                datasets: [{
                    label: 'Faturamento (R$)',
                    data: [<?php echo implode(', ', $revenues); ?>],
                    borderColor: 'rgb(59, 130, 246)',
                    backgroundColor: 'rgba(59, 130, 246, 0.1)',
                    borderWidth: 3,
                    fill: true,
                    tension: 0.4
                }]
            },
            options: {
                ...chartOptions,
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            callback: function(value) {
                                return 'R$ ' + value.toLocaleString('pt-BR');
                            }
                        }
                    }
                }
            }
        });

        // Status Pie Chart
        const statusCtx = document.getElementById('statusChart').getContext('2d');
        const statusChart = new Chart(statusCtx, {
            type: 'doughnut',
            data: {
                labels: ['Aprovadas', 'Pendings', 'Canceladas', 'Estornadas'],
                datasets: [{
                    data: [
                        <?php echo $stats['transacoes_paid']; ?>,
                        <?php echo $stats['transacoes_pending']; ?>,
                        <?php echo $stats['transacoes_cancelled']; ?>,
                        <?php echo $stats['transacoes_reversed']; ?>
                    ],
                    backgroundColor: ['#10b981', '#f59e0b', '#ef4444', '#8b5cf6'],
                    borderWidth: 0
                }]
            },
            options: {
                ...chartOptions,
                cutout: '60%'
            }
        });
    </script>
</body>
</html>
