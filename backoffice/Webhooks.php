
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

// Configuração inicial de variáveis
$search = "";
$keys = [];
$ipWhitelist = [];

// Verificar se há busca
if (isset($_GET['search']) && !empty($_GET['search'])) {
    $search = trim($_GET['search']);
}

// Consultar os dados da tabela `users_integration_keys`
$query = "SELECT * FROM users_integration_keys";
if (!empty($search)) {
    $query .= " WHERE client_id LIKE :search OR name LIKE :search";
}
$query .= " ORDER BY created_at DESC";

$stmt = $pdo->prepare($query);

if (!empty($search)) {
    $stmt->bindValue(':search', "%$search%", PDO::PARAM_STR);
}
$stmt->execute();
$keys = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Consultar os dados da tabela `ip_whitelist`
$queryIpWhitelist = "SELECT * FROM ip_whitelist";
if (!empty($search)) {
    $queryIpWhitelist .= " WHERE user_id LIKE :search";
}

$stmtIpWhitelist = $pdo->prepare($queryIpWhitelist);
if (!empty($search)) {
    $stmtIpWhitelist->bindValue(':search', "%$search%", PDO::PARAM_STR);
}
$stmtIpWhitelist->execute();
$ipWhitelist = $stmtIpWhitelist->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="pt-BR" dir="ltr" data-nav-layout="vertical" data-theme-mode="light" data-header-styles="light" data-menu-styles="light" data-toggled="close">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>RPay - Lista de Webhooks</title>
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
            margin-bottom: 2rem;
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

        .form-input::placeholder {
            color: var(--text-muted);
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

        /* Table Container */
        .table-container {
            background: var(--bg-card);
            backdrop-filter: blur(20px);
            border-radius: 20px;
            overflow: hidden;
            border: 1px solid var(--border-color);
            box-shadow: var(--shadow-xl);
            animation: fadeInUp 0.8s ease 0.4s both;
        }

        .table {
            width: 100%;
            border-collapse: collapse;
            margin: 0;
        }

        .table th {
            background: rgba(59, 130, 246, 0.1);
            padding: 1.25rem 1rem;
            text-align: left;
            font-weight: 500;
            color: var(--text-primary);
            border-bottom: 1px solid var(--border-color);
            font-size: 0.875rem;
            text-transform: uppercase;
            letter-spacing: 0.05em;
        }

        .table td {
            padding: 1.25rem 1rem;
            border-bottom: 1px solid var(--border-color);
            color: var(--text-muted);
            transition: all 0.3s ease;
            font-weight: 300;
        }

        .table tr {
            transition: all 0.3s ease;
        }

        .table tr:hover {
            background: rgba(59, 130, 246, 0.05);
        }

        .table tr:hover td {
            color: var(--text-primary);
        }

        .webhook-key {
            font-family: 'Courier New', monospace;
            background: rgba(59, 130, 246, 0.1);
            padding: 0.25rem 0.5rem;
            border-radius: 0.25rem;
            font-size: 0.875rem;
            color: rgb(var(--primary-rgb));
        }

        .webhook-ip {
            background: rgba(16, 185, 129, 0.1);
            color: rgb(var(--secondary-rgb));
            padding: 0.25rem 0.5rem;
            border-radius: 0.25rem;
            font-family: 'Courier New', monospace;
            font-size: 0.875rem;
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

            .table-container {
                overflow-x: auto;
            }

            .table {
                min-width: 900px;
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

                <a href="Webhooks.php" class="nav-item active">
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
                            <i class="fas fa-link"></i>
                        </div>
                        <div class="header-text">
                            <h1>Lista de Webhooks</h1>
                            <p>Visualize todas as integrações e webhooks do sistema</p>
                        </div>
                    </div>
                </div>
            </header>

            <!-- Form Container -->
            <section class="form-container">
                <form method="GET" action="">
                    <div class="form-grid">
                        <div class="form-group">
                            <label for="search" class="form-label">
                                <i class="fas fa-search"></i> Buscar Webhook
                            </label>
                            <input 
                                type="text" 
                                name="search" 
                                id="search" 
                                class="form-input" 
                                placeholder="Digite client_id, nome ou user_id..." 
                                value="<?php echo htmlspecialchars($search); ?>"
                            >
                        </div>
                    </div>

                    <button type="submit" name="filter" class="btn btn-primary">
                        <i class="fas fa-search"></i>
                        Filtrar Webhooks
                    </button>
                </form>
            </section>

            <!-- Table Container -->
            <section class="table-container">
                <table class="table">
                    <thead>
                        <tr>
                            <th><i class="fas fa-hashtag"></i> ID</th>
                            <th><i class="fas fa-user"></i> User ID</th>
                            <th><i class="fas fa-key"></i> Client ID</th>
                            <th><i class="fas fa-lock"></i> Client Secret</th>
                            <th><i class="fas fa-tag"></i> Nome</th>
                            <th><i class="fas fa-comment"></i> Descrição</th>
                            <th><i class="fas fa-calendar"></i> Criado em</th>
                            <th><i class="fas fa-globe"></i> Domínio</th>
                            <th><i class="fas fa-map-marker-alt"></i> IP</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($keys)): ?>
                            <tr>
                                <td colspan="9" style="text-align: center; padding: 3rem;">
                                    <i class="fas fa-link" style="font-size: 3rem; color: var(--text-muted); margin-bottom: 1rem; display: block;"></i>
                                    <div style="color: var(--text-muted);">
                                        <?php if (!empty($search)): ?>
                                            Nenhum webhook encontrado com os filtros aplicados.
                                        <?php else: ?>
                                            Nenhum webhook encontrado no sistema.
                                        <?php endif; ?>
                                    </div>
                                </td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($keys as $key): ?>
                                <tr>
                                    <td><strong>#<?php echo htmlspecialchars($key['id']); ?></strong></td>
                                    <td><?php echo htmlspecialchars($key['user_id']); ?></td>
                                    <td><code class="webhook-key"><?php echo htmlspecialchars($key['client_id']); ?></code></td>
                                    <td><code class="webhook-key"><?php echo htmlspecialchars(substr($key['client_secret'], 0, 20) . '...'); ?></code></td>
                                    <td><strong><?php echo htmlspecialchars($key['name']); ?></strong></td>
                                    <td><?php echo htmlspecialchars($key['description']); ?></td>
                                    <td><?php echo date('d/m/Y H:i', strtotime($key['created_at'])); ?></td>
                                    <td><?php echo htmlspecialchars($key['domain']); ?></td>
                                    <td>
                                        <?php 
                                        $ip = 'N/A';
                                        foreach ($ipWhitelist as $ipData) {
                                            if ($ipData['user_id'] == $key['user_id']) {
                                                $ip = htmlspecialchars($ipData['ip']);
                                                break;
                                            }
                                        }
                                        ?>
                                        <?php if ($ip !== 'N/A'): ?>
                                            <span class="webhook-ip"><?php echo $ip; ?></span>
                                        <?php else: ?>
                                            <span style="color: var(--text-muted);">N/A</span>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
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
    </script>
</body>
</html>
