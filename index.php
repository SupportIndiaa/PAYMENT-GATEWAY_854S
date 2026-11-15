
<?php

session_start();

include('libs/updateUserInfo.php');

if (!isset($_SESSION['user_id'])) {
    header("Location: /login.php");
    exit();
}
if (isset($_SESSION['documents_checked'])) {
    if($_SESSION['documents_checked'] == 0) {
        header("Location: /settings.php");
        exit();
    }
}
?>
<!DOCTYPE html>
<html lang="pt-br" dir="ltr" data-nav-layout="vertical" data-theme-mode="light" data-header-styles="light" data-menu-styles="light" data-toggled="close">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0, user-scalable=no">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <title> <?php include 'libs/get_site_name.php'; ?> - https://t.me/TOXIC_ADMINN</title>
  <meta name="Description" content="Dashboard principal do gateway de pagamento <?php include 'libs/get_site_name.php'; ?>">
  <meta name="Author" content="Ellodev">
  <meta name="keywords" content="Gateway de pagamento">
  <!-- Favicon -->
 <link rel="icon" href="assets/images/brand-logos/favicon.ico" type="image/x-icon">

  <!-- Choices JS -->
  <script src="assets/libs/choices.js/public/assets/scripts/choices.min.js"></script>
  <!-- Main Theme Js -->
  <script src="assets/js/main.js"></script>
  <!-- Bootstrap Css -->
  <link id="style" href="assets/libs/bootstrap/css/bootstrap.min.css" rel="stylesheet">
  <!-- Style Css --> <?php include 'libs/config_css.php'; ?>
  <!-- Icons Css -->
  <link href="assets/css/icons.css" rel="stylesheet">
  <!-- Node Waves Css -->
  <link href="assets/libs/node-waves/waves.min.css" rel="stylesheet">
  <!-- Yesplebar Css -->
  <link href="assets/libs/simplebar/simplebar.min.css" rel="stylesheet">
  <!-- Color Picker Css -->
  <link rel="stylesheet" href="assets/libs/flatpickr/flatpickr.min.css">
  <link rel="stylesheet" href="assets/libs/@simonwep/pickr/themes/nano.min.css">
  <!-- Choices Css -->
  <link rel="stylesheet" href="assets/libs/choices.js/public/assets/styles/choices.min.css">
  <!-- Bootstrap Icons -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css" rel="stylesheet">
  
  <?php
    try {
        // Definir variáveis de conexão
        $dbHost = $_ENV['DB_HOST'];
        $dbnome = $_ENV['DB_NAME'];
        $dbUser = $_ENV['DB_USER'];
        $dbPass = $_ENV['DB_PASS'];

        // Conectar ao banco de dados
        $dsn = "mysql:host=$dbHost;dbname=$dbnome;charset=utf8";
        $pdo = new PDO($dsn, $dbUser, $dbPass);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        // Consultar as cores RGB armazenadas no banco de dados
        $query = "SELECT primary_color, secondary_color FROM config LIMIT 1";
        $stmt = $pdo->prepare($query);
        $stmt->execute();
        $config = $stmt->fetch(PDO::FETCH_ASSOC);

        // Usar cores padrão se não houver configuração
        $primaryRgb = isset($config['primary_color']) ? $config['primary_color'] : '59, 130, 246'; // Blue
        $secondaryRgb = isset($config['secondary_color']) ? $config['secondary_color'] : '16, 185, 129'; // Green
    } catch (PDOException $e) {
        die("Error ao conectar ao banco de dados: " . $e->getMessage());
    }
  ?>
  
  <style>
    :root {
        --primary-rgb: <?php echo $primaryRgb; ?>;
        --secondary-rgb: <?php echo $secondaryRgb; ?>;
        
        /* Modern Dashboard Colors */
        --dashboard-bg: #0f1419;
        --dashboard-surface: #1a1b2e;
        --dashboard-card: #16213e;
        --dashboard-accent: #0066ff;
        --dashboard-blue: #3b82f6;
        --dashboard-cyan: #06b6d4;
        --dashboard-green: #10b981;
        --dashboard-purple: #8b5cf6;
        --dashboard-orange: #f59e0b;
        --dashboard-red: #ef4444;
        --dashboard-text: #ffffff;
        --dashboard-text-muted: #94a3b8;
        --dashboard-border: rgba(255, 255, 255, 0.1);
        
        /* Light theme */
        --light-bg: #f8fafc;
        --light-surface: rgba(255, 255, 255, 0.95);
        --light-card: rgba(255, 255, 255, 0.9);
        --light-text: #1e293b;
        --light-text-muted: #64748b;
        --light-border: rgba(59, 130, 246, 0.1);
    }
    
    /* Dark theme (default) */
    [data-theme-mode="dark"] {
        --bg-primary: var(--dashboard-bg);
        --bg-secondary: var(--dashboard-surface);
        --bg-card: var(--dashboard-card);
        --text-primary: var(--dashboard-text);
        --text-muted: var(--dashboard-text-muted);
        --border-color: var(--dashboard-border);
        --sidebar-bg: linear-gradient(180deg, #1a1b2e 0%, #16213e 100%);
        --sidebar-item-hover: rgba(59, 130, 246, 0.15);
        --sidebar-item-active: linear-gradient(135deg, rgba(59, 130, 246, 0.2), rgba(6, 182, 212, 0.2));
    }
    
    /* Light theme */
    [data-theme-mode="light"] {
        --bg-primary: var(--light-bg);
        --bg-secondary: var(--light-surface);
        --bg-card: var(--light-card);
        --text-primary: var(--light-text);
        --text-muted: var(--light-text-muted);
        --border-color: var(--light-border);
        --sidebar-bg: linear-gradient(180deg, #ffffff 0%, #f8fafc 100%);
        --sidebar-item-hover: rgba(59, 130, 246, 0.08);
        --sidebar-item-active: linear-gradient(135deg, rgba(59, 130, 246, 0.1), rgba(6, 182, 212, 0.1));
    }

    /* Light theme background with waves */
    [data-theme-mode="light"] body::before {
        content: '';
        position: fixed;
        top: 0;
        left: 0;
        right: 0;
        height: 50vh;
        background: linear-gradient(135deg, #3b82f6 0%, #06b6d4 50%, #0ea5e9 100%);
        border-radius: 0 0 50% 50% / 0 0 100px 100px;
        z-index: -1;
        pointer-events: none;
    }

    @media (max-width: 768px) {
        [data-theme-mode="light"] body::before {
            height: 40vh;
            border-radius: 0 0 40% 40% / 0 0 80px 80px;
        }
    }
    
    * {
        margin: 0;
        padding: 0;
        box-sizing: border-box;
    }
    
    html, body {
        height: 100%;
        width: 100%;
        overflow-x: hidden;
    }
    
    body {
        background: var(--bg-primary) !important;
        color: var(--text-primary) !important;
        font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Inter', sans-serif;
        font-weight: 300;
        min-height: 100vh;
        width: 100vw;
        position: relative;
    }
    
    /* Enhanced Header Styles */
    .floating-header {
        position: fixed;
        top: 24px;
        right: 24px;
        z-index: 999;
        display: flex;
        align-items: center;
        gap: 12px;
    }
    
    .floating-header-left {
        position: fixed;
        top: 24px;
        left: 24px;
        z-index: 999;
        display: none; /* Hidden on desktop */
    }
    
    .header-btn {
        width: 48px;
        height: 48px;
        border-radius: 50%;
        background: var(--bg-card);
        border: 1px solid var(--border-color);
        display: flex;
        align-items: center;
        justify-content: center;
        color: var(--text-primary);
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        cursor: pointer;
        text-decoration: none;
        backdrop-filter: blur(20px);
        position: relative;
        overflow: hidden;
        box-shadow: 0 8px 32px rgba(0, 0, 0, 0.1);
    }
    
    .header-btn::before {
        content: '';
        position: absolute;
        top: 0;
        left: -100%;
        width: 100%;
        height: 100%;
        background: linear-gradient(90deg, transparent, rgba(255,255,255,0.2), transparent);
        transition: left 0.6s ease;
    }
    
    .header-btn:hover::before {
        left: 100%;
    }
    
    .header-btn:hover {
        color: white;
        background: linear-gradient(135deg, var(--dashboard-blue), var(--dashboard-cyan));
        border-color: var(--dashboard-cyan);
        transform: translateY(-3px) scale(1.05);
        box-shadow: 0 12px 40px rgba(59, 130, 246, 0.4);
    }
    
    .header-btn svg {
        width: 20px;
        height: 20px;
        stroke-width: 2;
    }
    
    /* Compact Sidebar like the image */
    .dashboard-sidebar {
        position: fixed;
        left: 0;
        top: 0;
        bottom: 0;
        width: 280px;
        background: var(--sidebar-bg);
        border-right: 1px solid var(--border-color);
        z-index: 1000;
        transform: translateX(0);
        transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
        overflow: hidden;
        box-shadow: 8px 0 40px rgba(0, 0, 0, 0.15);
        backdrop-filter: blur(20px);
        display: flex;
        flex-direction: column;
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
    
    .sidebar-header img {
        height: 36px;
        filter: drop-shadow(0 4px 12px rgba(0, 0, 0, 0.3));
        /* Fix for light theme visibility */
        opacity: 1;
    }
    
    /* Enhanced logo visibility in light theme */
    [data-theme-mode="light"] .sidebar-header img {
        filter: drop-shadow(0 4px 12px rgba(0, 0, 0, 0.2)) brightness(0.8) contrast(1.2);
        background: rgba(255, 255, 255, 0.9);
        border-radius: 8px;
        padding: 4px 8px;
    }
    
    .sidebar-menu {
        flex: 1;
        padding: 16px 0;
        overflow-y: auto;
        scrollbar-width: none;
        -ms-overflow-style: none;
    }
    
    .sidebar-menu::-webkit-scrollbar {
        display: none;
    }
    
    .menu-section {
        margin-bottom: 20px;
    }
    
    .menu-item {
        display: flex;
        align-items: center;
        gap: 14px;
        padding: 12px 20px;
        color: var(--text-muted);
        text-decoration: none;
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        margin: 2px 16px;
        border-radius: 20px; /* Increased border radius for mobile */
        font-weight: 400;
        font-size: 14px;
        position: relative;
        overflow: hidden;
        cursor: pointer;
    }
    
    .menu-item::before {
        content: '';
        position: absolute;
        left: 0;
        top: 0;
        bottom: 0;
        width: 0;
        background: var(--sidebar-item-active);
        transition: width 0.4s cubic-bezier(0.4, 0, 0.2, 1);
        border-radius: 20px;
    }
    
    .menu-item:hover::before,
    .menu-item.active::before {
        width: 100%;
    }
    
    .menu-item:hover,
    .menu-item.active {
        color: var(--dashboard-blue);
        transform: translateX(4px);
        background: var(--sidebar-item-hover);
    }
    
    .menu-item svg {
        width: 18px;
        height: 18px;
        stroke-width: 2;
        z-index: 1;
        transition: all 0.3s ease;
        flex-shrink: 0;
    }
    
    .menu-item:hover svg,
    .menu-item.active svg {
        color: var(--dashboard-blue);
        transform: scale(1.1);
    }
    
    .menu-item span {
        z-index: 1;
        transition: all 0.3s ease;
    }
    
    .menu-item .arrow {
        margin-left: auto;
        width: 14px;
        height: 14px;
        transition: transform 0.3s ease;
    }
    
    .menu-item.expanded .arrow {
        transform: rotate(90deg);
    }
    
    /* Submenu */
    .submenu {
        max-height: 0;
        overflow: hidden;
        transition: max-height 0.3s ease;
        margin-left: 16px;
    }
    
    .submenu.expanded {
        max-height: 200px;
    }
    
    .submenu-item {
        display: flex;
        align-items: center;
        gap: 12px;
        padding: 8px 20px 8px 36px;
        color: var(--text-muted);
        text-decoration: none;
        transition: all 0.3s ease;
        border-radius: 15px;
        font-size: 13px;
        margin: 1px 16px;
    }
    
    .submenu-item:hover {
        color: var(--dashboard-blue);
        background: var(--sidebar-item-hover);
        transform: translateX(4px);
    }
    
    .submenu-item svg {
        width: 16px;
        height: 16px;
        stroke-width: 2;
    }
    
    /* Enhanced Progress Section with Icon */
    .progress-section {
        flex-shrink: 0;
        padding: 20px;
        background: rgba(59, 130, 246, 0.05);
        border-top: 1px solid var(--border-color);
        backdrop-filter: blur(20px);
    }
    
    .progress-header {
        display: flex;
        align-items: center;
        gap: 12px;
        margin-bottom: 12px;
    }
    
    .progress-icon {
        width: 32px;
        height: 32px;
        border-radius: 12px;
        background: linear-gradient(135deg, var(--dashboard-blue), var(--dashboard-cyan));
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
        box-shadow: 0 8px 20px rgba(59, 130, 246, 0.3);
    }
    
    .progress-icon svg {
        width: 18px;
        height: 18px;
        stroke-width: 2;
    }
    
    .progress-amount {
        font-size: 14px;
        font-weight: 500;
        color: var(--text-primary);
        flex: 1;
    }
    
    .progress-bar-container {
        position: relative;
        height: 12px;
        background: rgba(59, 130, 246, 0.15);
        border-radius: 8px;
        overflow: hidden;
        margin-top: 8px;
        box-shadow: inset 0 0 10px rgba(59, 130, 246, 0.2);
    }
    
    .progress-bar {
        height: 100%;
        background: linear-gradient(135deg, var(--dashboard-blue), var(--dashboard-cyan));
        border-radius: 8px;
        transition: width 0.8s cubic-bezier(0.4, 0, 0.2, 1);
        position: relative;
        overflow: hidden;
        box-shadow: 0 0 20px rgba(59, 130, 246, 0.6), inset 0 0 20px rgba(255, 255, 255, 0.3);
        animation: neon-pulse 2s ease-in-out infinitand thelternate;
    }
    
    /* Neon animation for progress bar */
    @keyframes neon-pulse {
        0% {
            box-shadow: 0 0 20px rgba(59, 130, 246, 0.6), inset 0 0 20px rgba(255, 255, 255, 0.3);
        }
        100% {
            box-shadow: 0 0 30px rgba(59, 130, 246, 0.9), 0 0 40px rgba(6, 182, 212, 0.6), inset 0 0 20px rgba(255, 255, 255, 0.5);
        }
    }
    
    .progress-bar::after {
        content: '';
        position: absolute;
        top: 0;
        left: -100%;
        width: 100%;
        height: 100%;
        background: linear-gradient(90deg, transparent, rgba(255,255,255,0.4), transparent);
        animation: progress-shine 2s infinite;
    }
    
    @keyframes progress-shine {
        0% { left: -100%; }
        50% { left: 100%; }
        100% { left: 100%; }
    }
    
    /* Main Content with Sidebar Offset */
    .main-container {
        min-height: 100vh;
        width: 100%;
        padding: 32px 32px 32px 312px;
        max-width: 100vw;
        transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
    }
    
    /* Mobile Responsive */
    @media (max-width: 1024px) {
        .dashboard-sidebar {
            transform: translateX(-100%);
            z-index: 1002;
            width: 280px;
            border-radius: 0 25px 25px 0; /* Rounded edges on mobile */
        }
        
        .dashboard-sidebar.open {
            transform: translateX(0);
        }
        
        .main-container {
            padding: 32px 20px 120px; /* Added bottom padding for mobile scroll */
        }
        
        .floating-header {
            top: 20px;
            right: 20px;
        }
        
        .floating-header-left {
            display: flex;
            top: 20px;
            left: 20px;
        }
        
        /* Fix mobile scroll issues */
        body.sidebar-open {
            overflow: hidden;
            position: fixed;
            width: 100%;
        }
        
        .sidebar-menu {
            /* Enable proper scrolling in mobile sidebar */
            height: calc(100vh - 180px);
            overflow-y: scroll;
            -webkit-overflow-scrolling: touch;
        }

        /* Mobile responsive charts */
        .charts-container {
            grid-template-columns: 1fr !important;
            gap: 20px;
        }
        
        .chart-card {
            order: 1;
        }
        
        .activity-card {
            order: 2;
        }
        
        /* Ensure mobile users can scroll to see Status da Conta */
        .reports-section {
            margin-bottom: 60px;
        }
        
        .balance-summary {
            margin-bottom: 40px;
        }
    }
    
    @media (max-width: 768px) {
        .dashboard-sidebar {
            width: 280px;
        }
        
        .floating-header {
            top: 16px;
            right: 16px;
        }
        
        .floating-header-left {
            top: 16px;
            left: 16px;
        }
        
        .header-btn {
            width: 44px;
            height: 44px;
        }
        
        .main-container {
            padding: 90px 16px 100px; /* Increased bottom padding */
        }
    }
    
        .dashboard-title {
    margin-bottom: 24px;
    position: relative;
    padding-left: 16px;
}

.dashboard-title h1 {
    font-size: 2.5rem; /* 40px */
    font-weight: 700;
    color: var(--text-primary);
    margin-bottom: 12px;
    letter-spacing: -0.03em;
    line-height: 1.2;
    position: relative;
    display: inline-block;
}

.dashboard-title h1::before {
    content: '';
    position: absolute;
    left: -16px;
    top: 0;
    height: 100%;
    width: 4px;
    background: linear-gradient(to bottom, 
              var(--dashboard-blue), 
              var(--dashboard-cyan));
    border-radius: 4px;
}

.dashboard-title p {
    color: var(--text-secondary);
    font-size: 1.125rem; /* 18px */
    font-weight: 400;
    line-height: 1.6;
    max-width: 680px;
    padding-left: 12px;
    position: relative;
}

.dashboard-title p::before {
    content: '•';
    position: absolute;
    left: 0;
    color: var(--dashboard-cyan);
    font-size: 1.5rem;
    line-height: 1;
    top: 2px;
}
    
    .dashboard-title p {
        color: var(--text-muted);
        font-size: 16px;
        font-weight: 300;
    }
    
    .dashboard-title:hover h1::before {
    width: 6px;
    left: -18px;
    transition: all 0.3s ease;
}
    
    /* Stats Grid */
    .stats-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
        gap: 28px;
        margin-bottom: 40px;
        width: 100%;
    }
    
    .stat-card {
        background: var(--bg-card);
        border: 1px solid var(--border-color);
        border-radius: 24px;
        padding: 32px;
        position: relative;
        overflow: hidden;
        transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
        backdrop-filter: blur(20px);
        box-shadow: 0 8px 40px rgba(0, 0, 0, 0.08);
    }
    
    .stat-card:hover {
        transform: translateY(-12px);
        box-shadow: 0 24px 60px rgba(0, 0, 0, 0.15);
    }
    
    .stat-card::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        height: 4px;
        background: var(--gradient);
    }
    
    .stat-card.blue::before {
        background: linear-gradient(135deg, var(--dashboard-blue), var(--dashboard-cyan));
    }
    
    .stat-card.green::before {
        background: linear-gradient(135deg, var(--dashboard-green), #34d399);
    }
    
    .stat-card.purple::before {
        background: linear-gradient(135deg, var(--dashboard-purple), #a78bfa);
    }
    
    .stat-card.orange::before {
        background: linear-gradient(135deg, var(--dashboard-orange), #fbbf24);
    }
    
    .stat-header {
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
        margin-bottom: 24px;
    }
    
    .stat-info h3 {
        font-size: 13px;
        font-weight: 400;
        color: var(--text-muted);
        margin-bottom: 20px;
        text-transform: uppercase;
        letter-spacing: 1px;
    }
    
    .stat-value {
        font-size: 32px;
        font-weight: 300;
        color: var(--text-primary);
        transition: filter 0.3s ease;
        line-height: 1;
        letter-spacing: -0.02em;
    }
    
    .stat-icon {
        width: 60px;
        height: 60px;
        border-radius: 20px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 24px;
        color: white;
        position: relative;
        overflow: hidden;
        box-shadow: 0 12px 30px rgba(0, 0, 0, 0.2);
        flex-shrink: 0; /* Prevent icon from shrinking */
    }
    
    .stat-icon svg {
        width: 28px !important;
        height: 28px !important;
        stroke-width: 2;
        fill: none;
        stroke: currentColor;
    }
    
    .stat-icon::before {
        content: '';
        position: absolute;
        top: 0;
        left: -100%;
        width: 100%;
        height: 100%;
        background: linear-gradient(90deg, transparent, rgba(255,255,255,0.3), transparent);
        animation: icon-shine 4s infinite;
    }
    
    @keyframes icon-shine {
        0% { left: -100%; }
        50% { left: 100%; }
        100% { left: 100%; }
    }
    
    .stat-card.blue .stat-icon {
        background: linear-gradient(135deg, var(--dashboard-blue), var(--dashboard-cyan));
    }
    
    .stat-card.green .stat-icon {
        background: linear-gradient(135deg, var(--dashboard-green), #34d399);
    }
    
    .stat-card.purple .stat-icon {
        background: linear-gradient(135deg, var(--dashboard-purple), #a78bfa);
    }
    
    .stat-card.orange .stat-icon {
        background: linear-gradient(135deg, var(--dashboard-orange), #fbbf24);
    }
    
    /* Charts Section */
    .charts-container {
        display: grid;
        grid-template-columns: 2fr 1fr;
        gap: 28px;
        margin-bottom: 40px;
        width: 100%;
    }
    
    .chart-card {
        background: var(--bg-card);
        border: 1px solid var(--border-color);
        border-radius: 24px;
        padding: 32px;
        backdrop-filter: blur(20px);
        box-shadow: 0 8px 40px rgba(0, 0, 0, 0.08);
    }
    
    .chart-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 32px;
        flex-wrap: wrap;
        gap: 20px;
    }
    
    .chart-title {
        font-size: 20px;
        font-weight: 400;
        color: var(--text-primary);
        letter-spacing: -0.01em;
    }
    
    .chart-filters {
        display: flex;
        gap: 6px;
        background: rgba(59, 130, 246, 0.08);
        padding: 8px;
        border-radius: 16px;
        backdrop-filter: blur(20px);
    }
    
    .filter-btn {
        padding: 12px 24px;
        border: none;
        background: transparent;
        color: var(--text-muted);
        border-radius: 12px;
        font-size: 14px;
        font-weight: 400;
        cursor: pointer;
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        position: relative;
        overflow: hidden;
    }
    
    .filter-btn::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: linear-gradient(135deg, var(--dashboard-blue), var(--dashboard-cyan));
        opacity: 0;
        transition: opacity 0.3s ease;
        border-radius: 12px;
    }
    
    .filter-btn.active::before,
    .filter-btn:hover::before {
        opacity: 1;
    }
    
    .filter-btn.active,
    .filter-btn:hover {
        color: white;
        transform: translateY(-2px);
    }
    
    .filter-btn span {
        position: relative;
        z-index: 1;
    }
    
    /* Activity Feed */
    .activity-card {
        background: var(--bg-card);
        border: 1px solid var(--border-color);
        border-radius: 24px;
        overflow: hidden;
        backdrop-filter: blur(20px);
        box-shadow: 0 8px 40px rgba(0, 0, 0, 0.08);
    }
    
    .activity-header {
        padding: 28px 32px;
        border-bottom: 1px solid var(--border-color);
        background: rgba(59, 130, 246, 0.05);
    }
    
    .activity-list {
        max-height: 480px;
        overflow-y: auto;
    }
    
    .activity-item {
        padding: 24px 32px;
        border-bottom: 1px solid var(--border-color);
        display: flex;
        align-items: center;
        gap: 20px;
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        position: relative;
    }
    
    .activity-item::before {
        content: '';
        position: absolute;
        left: 0;
        top: 0;
        bottom: 0;
        width: 0;
        background: linear-gradient(135deg, var(--dashboard-blue), var(--dashboard-cyan));
        transition: width 0.3s ease;
    }
    
    .activity-item:hover::before {
        width: 4px;
    }
    
    .activity-item:hover {
        background: rgba(59, 130, 246, 0.04);
        transform: translateX(12px);
    }
    
    .activity-item:last-child {
        border-bottom: none;
    }
    
    .activity-avatar {
        width: 52px;
        height: 52px;
        border-radius: 18px;
        background: linear-gradient(135deg, var(--dashboard-blue), var(--dashboard-cyan));
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
        font-weight: 400;
        font-size: 16px;
        box-shadow: 0 8px 20px rgba(59, 130, 246, 0.3);
    }
    
    .activity-content {
        flex: 1;
        min-width: 0;
    }
    
    .activity-name {
        font-weight: 400;
        color: var(--text-primary);
        margin-bottom: 6px;
        font-size: 15px;
    }
    
    .activity-detail {
        font-size: 13px;
        color: var(--text-muted);
        font-weight: 300;
    }
    
    .activity-amount {
        font-weight: 400;
        font-size: 16px;
        padding: 8px 16px;
        border-radius: 12px;
        backdrop-filter: blur(10px);
    }
    
    .activity-amount.positive {
        color: var(--dashboard-green);
        background: rgba(16, 185, 129, 0.1);
    }
    
    .activity-amount.negative {
        color: var(--dashboard-red);
        background: rgba(239, 68, 68, 0.1);
    }
    
    /* Reports Table */
    .reports-section {
        background: var(--bg-card);
        border: 1px solid var(--border-color);
        border-radius: 24px;
        overflow: hidden;
        backdrop-filter: blur(20px);
        box-shadow: 0 8px 40px rgba(0, 0, 0, 0.08);
    }
    
    .reports-header {
        padding: 28px 32px;
        border-bottom: 1px solid var(--border-color);
        text-align: center;
        background: rgba(59, 130, 246, 0.05);
    }
    
    .reports-header h3 {
        font-size: 20px;
        font-weight: 400;
        color: var(--text-primary);
        letter-spacing: -0.01em;
    }
    
    .reports-table {
        width: 100%;
        border-collapse: collapse;
    }
    
    .reports-table th {
        background: rgba(59, 130, 246, 0.08);
        color: var(--text-primary);
        font-weight: 400;
        padding: 24px 32px;
        text-align: left;
        border-bottom: 1px solid var(--border-color);
        font-size: 14px;
        text-transform: uppercase;
        letter-spacing: 1px;
    }
    
    .reports-table td {
        padding: 20px 32px;
        border-bottom: 1px solid var(--border-color);
        color: var(--text-primary);
        font-size: 15px;
        font-weight: 300;
    }
    
    .reports-table tbody tr:hover {
        background: rgba(59, 130, 246, 0.04);
    }
    
    .total-row {
        background: rgba(59, 130, 246, 0.08) !important;
        font-weight: 400;
    }
    
    .total-row.positive {
        background: rgba(16, 185, 129, 0.08) !important;
    }
    
    .total-row.negative {
        background: rgba(239, 68, 68, 0.08) !important;
    }
    
    .balance-summary {
        padding: 28px 32px;
        text-align: center;
        background: linear-gradient(135deg, rgba(59, 130, 246, 0.08), rgba(6, 182, 212, 0.08));
        border-top: 1px solid var(--border-color);
        font-size: 20px;
        font-weight: 400;
        color: var(--dashboard-blue);
        letter-spacing: -0.01em;
    }
    
    /* Blur effect for hidden values */
    .financial-value {
        transition: filter 0.3s ease;
    }
    
    .value-blurred {
        filter: blur(8px);
        user-select: none;
    }
    
    /* Enhanced Modal Backdrop Blur with higher z-index */
    .modal-backdrop {
        backdrop-filter: blur(16px) !important;
        background: rgba(0, 0, 0, 0.4) !important;
        z-index: 1055 !important; /* Increased z-index */
    }
    
    .modal.show {
        backdrop-filter: blur(16px);
        z-index: 1060 !important; /* Increased z-index */
    }
    
    .modal-content {]
        height:1vh;
        width: 100vw;
    }
    }
    
    body.modal-open .floating-header,
    body.modal-open .floating-header-left,
    body.modal-open .dashboard-sidebar,
    body.modal-open .main-container {
        filter: blur(12px) !important;
        pointer-events: none !important;
        transition: filter 0.3s ease !important;
    }
    
    body.modal-open .modal,
    body.modal-open .modal *,
    body.modal-open .modal-backdrop {
        filter: none !important;
        pointer-events: auto !important;
    }
    
    /* Overlay for mobile sidebar */
    .sidebar-overlay {
        position: fixed;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: rgba(0, 0, 0, 0.6);
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
    
    /* Enhanced Dropdown Menu with smooth animation */
    .dropdown-menu {
        background: var(--bg-card) !important;
        border: 1px solid var(--border-color) !important;
        border-radius: 20px !important;
        box-shadow: 0 24px 60px rgba(0, 0, 0, 0.3) !important;
        min-width: 240px;
        z-index: 1003 !important;
        backdrop-filter: blur(20px) !important;
        padding: 12px !important;
        animation: dropdownFadeIn 0.4s cubic-bezier(0.68, -0.55, 0.25, 1.35); /* Smoother animation */
        transform-origin: top right;
    }

    @keyframes dropdownFadeIn {
        from {
            opacity: 0;
            transform: translateY(-20px) scale(0.9);
        }
        to {
            opacity: 1;
            transform: translateY(0) scale(1);
        }
    }
    
    .dropdown-item {
        color: var(--text-primary) !important;
        padding: 16px 20px;
        border-radius: 16px;
        margin: 6px 0;
        transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1); /* Smoother transition */
        font-weight: 300;
        display: flex;
        align-items: center;
        gap: 16px;
        position: relative;
        overflow: hidden;
    }

    .dropdown-item::before {
        content: '';
        position: absolute;
        top: 0;
        left: -100%;
        width: 100%;
        height: 100%;
        background: linear-gradient(90deg, transparent, rgba(59, 130, 246, 0.1), transparent);
        transition: left 0.6s ease; /* Slower animation */
    }
    
    .dropdown-item:hover::before {
        left: 100%;
    }
    
    .dropdown-item:hover {
        background: linear-gradient(135deg, rgba(59, 130, 246, 0.1), rgba(6, 182, 212, 0.1)) !important;
        color: var(--dashboard-blue) !important;
        transform: translateX(8px) scale(1.02); /* Added scale */
    }

    .dropdown-item svg {
        width: 18px;
        height: 18px;
        stroke-width: 2;
        transition: all 0.4s ease; /* Smoother transition */
    }

    .dropdown-item:hover svg {
        transform: scale(1.15) rotate(5deg); /* More dynamic animation */
    }

    /* Animated X icon for logout */
    .logout-icon {
        transition: all 0.4s cubic-bezier(0.68, -0.55, 0.25, 1.35); /* Bouncy animation */
    }

    .dropdown-item:hover .logout-icon {
        transform: rotate(180deg) scale(1.2);
    }

    /* Revenue requirement alert */
    .revenue-alert {
        position: fixed;
        top: 50%;
        left: 50%;
        transform: translate(-50%, -50%);
        background: var(--bg-card);
        border: 1px solid var(--border-color);
        border-radius: 20px;
        padding: 32px;
        max-width: 400px;
        width: 90%;
        z-index: 1070; /* Higher than modals */
        backdrop-filter: blur(20px);
        box-shadow: 0 24px 80px rgba(0, 0, 0, 0.3);
        text-align: center;
        opacity: 0;
        visibility: hidden;
        transform: translate(-50%, -50%) scale(0.8);
        transition: all 0.4s cubic-bezier(0.68, -0.55, 0.25, 1.35);
    }

    .revenue-alert.show {
        opacity: 1;
        visibility: visible;
        transform: translate(-50%, -50%) scale(1);
    }

    .alert-overlay {
        position: fixed;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: rgba(0, 0, 0, 0.6);
        z-index: 1069; /* Higher than modals */
        backdrop-filter: blur(12px);
        opacity: 0;
        visibility: hidden;
        transition: all 0.3s ease;
    }

    .alert-overlay.show {
        opacity: 1;
        visibility: visible;
    }

    .alert-icon {
        width: 64px;
        height: 64px;
        margin: 0 auto 20px;
        border-radius: 20px;
        background: linear-gradient(135deg, var(--dashboard-orange), #fbbf24);
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
        box-shadow: 0 12px 30px rgba(245, 158, 11, 0.3);
    }

    .alert-title {
        font-size: 20px;
        font-weight: 400;
        color: var(--text-primary);
        margin-bottom: 12px;
    }

    .alert-message {
        color: var(--text-muted);
        margin-bottom: 24px;
        line-height: 1.5;
    }

    .alert-close {
        background: linear-gradient(135deg, var(--dashboard-blue), var(--dashboard-cyan));
        color: white;
        border: none;
        border-radius: 12px;
        padding: 12px 24px;
        font-weight: 400;
        cursor: pointer;
        transition: all 0.3s ease;
    }

    .alert-close:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 20px rgba(59, 130, 246, 0.4);
    }
    
    /* Custom Enhanced Scrollbar */
    ::-webkit-scrollbar {
        width: 6px;
        height: 6px;
    }
    
    ::-webkit-scrollbar-track {
        background: rgba(0, 0, 0, 0.05);
        border-radius: 3px;
    }
    
    ::-webkit-scrollbar-thumb {
        background: linear-gradient(135deg, var(--dashboard-blue), var(--dashboard-cyan));
        border-radius: 3px;
    }
    
    ::-webkit-scrollbar-thumb:hover {
        background: linear-gradient(135deg, var(--dashboard-cyan), var(--dashboard-blue));
    }

    /* Toast Notifications - Fixed positioning */
    .toast-container {
        position: fixed;
        top: 24px;
        right: 24px;
        display: flex;
        flex-direction: column;
        gap: 16px;
        z-index: 10000;
        max-width: 400px;
        width: auto;
        pointer-events: none;
    }
    
    .toast {
        position: relative;
        border-radius: 16px;
        background: var(--bg-card);
        color: var(--text-primary);
        padding: 20px 48px 20px 20px;
        box-shadow: 0 16px 40px rgba(0, 0, 0, 0.2);
        border: 1px solid var(--border-color);
        overflow: hidden;
        transform: translateX(calc(100% + 32px));
        transition: all 0.5s cubic-bezier(0.25, 0.8, 0.25, 1);
        display: block !important;
        min-width: 320px;
        max-width: 400px;
        backdrop-filter: blur(20px);
        pointer-events: auto;
    }
    
    .toast.show {
        transform: translateX(0);
    }
    
    .toast::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        height: 3px;
        background: linear-gradient(135deg, var(--dashboard-blue), var(--dashboard-cyan));
    }
    
    .toast-content {
        display: flex;
        align-items: center;
        gap: 16px;
    }
    
    .toast-check {
        display: flex;
        justify-content: center;
        align-items: center;
        background: linear-gradient(135deg, var(--dashboard-blue), var(--dashboard-cyan));
        border-radius: 50%;
        color: #fff;
        font-size: 16px;
        width: 40px;
        height: 40px;
        position: relative;
        overflow: hidden;
        box-shadow: 0 8px 20px rgba(59, 130, 246, 0.4);
        flex-shrink: 0;
    }
    
    .toast-check::before {
        content: '';
        position: absolute;
        top: 0;
        left: -100%;
        width: 100%;
        height: 100%;
        background: linear-gradient(90deg, transparent, rgba(255,255,255,0.3), transparent);
        animation: toast-shine 3s infinite;
    }
    
    @keyframes toast-shine {
        0% { left: -100%; }
        50% { left: 100%; }
        100% { left: 100%; }
    }
    
    .message {
        display: flex;
        flex-direction: column;
        gap: 4px;
        flex: 1;
        min-width: 0;
    }
    
    .message-text {
        font-size: 14px;
        font-weight: 400;
        word-wrap: break-word;
    }
    
    .text-1 {
        color: var(--text-primary);
        font-weight: 500;
    }
    
    .text-2 {
        color: var(--text-muted);
        font-weight: 300;
        font-size: 13px;
    }
    
    .toast-close {
        position: absolute;
        top: 12px;
        right: 12px;
        padding: 6px;
        cursor: pointer;
        opacity: 0.7;
        color: var(--dashboard-blue);
        border-radius: 6px;
        transition: all 0.2s ease;
        background: none;
        border: none;
    }
    
    .toast-close:hover {
        opacity: 1;
        background: rgba(59, 130, 246, 0.1);
    }
    
    .progress {
        position: absolute;
        bottom: 0;
        left: 0;
        height: 2px;
        width: 100%;
        background: rgba(59, 130, 246, 0.1);
        overflow: hidden;
    }
    
    .progress::before {
        content: "";
        position: absolute;
        bottom: 0;
        right: 0;
        height: 100%;
        width: 100%;
        background: linear-gradient(135deg, var(--dashboard-blue), var(--dashboard-cyan));
    }
    
    .progress.active::before {
        animation: progress 5s linear forwards;
    }
    
    @keyframes progress {
        100% {
            right: 100%;
        }
    }

    /* Mobile responsive for toasts */
    @media (max-width: 768px) {
        .toast-container {
            top: 20px;
            right: 16px;
            left: 16px;
            max-width: none;
        }
        
        .toast {
            min-width: auto;
            max-width: none;
            width: 100%;
        }
    }
  </style>
</head>

<body>
  <div id="loader">
    <img src="assets/images/media/loader.svg" alt="">
  </div>
  
  <!-- ... keep existing code (sidebar overlay, sidebar, header, main content) the same ... -->
  <!-- Sidebar Overlay -->
  <div class="sidebar-overlay" id="sidebarOverlay"></div>
  
  <!-- Compact Sidebar -->
  <div class="dashboard-sidebar" id="sidebar">
    <div class="sidebar-header">
      <div class="d-flex align-items-center">
        <img src="teste.png" alt="logo">
      </div>
    </div>
    
    <nav class="sidebar-menu">
      <div class="menu-section">
        <a href="./" class="menu-item active">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor">
            <rect x="3" y="3" width="7" height="7"/>
            <rect x="14" y="3" width="7" height="7"/>
            <rect x="14" y="14" width="7" height="7"/>
            <rect x="3" y="14" width="7" height="7"/>
          </svg>
          <span>Dashboard</span>
        </a>
        
        <a class="menu-item openModal" data-target="libs/includes/falar_gerente">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor">
            <path d="M21 11.5a8.38 8.38 0 0 1-.9 3.8 8.5 8.5 0 0 1-7.6 4.7 8.38 8.38 0 0 1-3.8-.9L3 21l1.9-5.7a8.38 8.38 0 0 1-.9-3.8 8.5 8.5 0 0 1 4.7-7.6 8.38 8.38 0 0 1 3.8-.9h.5a8.48 8.48 0 0 1 8 8v.5z"/>
          </svg>
          <span>Support Manager</span>
        </a>
        
        <a class="menu-item" href="premios.php">
  <svg viewBox="0 0 24 24" fill="none" stroke="currentColor">
    <path d="M20 12v7a2 2 0 0 1-2 2H6a2 2 0 0 1-2-2v-7"/>
    <path d="M3 7h18"/>
    <path d="M12 22V7"/>
    <path d="M7 7a3 3 0 1 1 6 0"/>
    <path d="M17 7a3 3 0 1 1-6 0"/>
  </svg>
  <span>Rewards</span>
</a>

        
        <?php if(isset($_SESSION['documents_checked']) && $_SESSION['documents_checked'] == 1) { ?>
        <!-- Transfers with submenu -->
        <div class="menu-item" id="transferenciasMenu">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor">
            <path d="M21 12c0 1.66-4 3-9 3s-9-1.34-9-3"/>
            <path d="M3 5c0-1.66 4-3 9-3s9 1.34 9 3v14c0 1.66-4 3-9 3s-9-1.34-9-3V5z"/>
            <path d="M3 12c0-1.66 4-3 9-3s9 1.34 9 3"/>
          </svg>
          <span>Transfers</span>
          <svg class="arrow" viewBox="0 0 24 24" fill="none" stroke="currentColor">
            <polyline points="9,18 15,12 9,6"/>
          </svg>
        </div>
        <div class="submenu" id="transferenciasSubmenu">
          <a data-target="libs/includes/gerar_pix" class="submenu-item openModal">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor">
              <rect x="1" y="4" width="22" height="16" rx="2" ry="2"/>
              <line x1="1" y1="10" x2="23" y2="10"/>
            </svg>
            <span>Add Money</span>
          </a>
          
          <a data-target="libs/includes/gerar_saque" class="submenu-item openModal">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor">
              <line x1="7" y1="17" x2="17" y2="7"/>
              <polyline points="7,7 17,7 17,17"/>
            </svg>
            <span>Withdrawal</span>
          </a>
          
          <a data-target="libs/includes/transferencia_interna" class="submenu-item openModal">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor">
              <polyline points="17,1 21,5 17,9"/>
              <path d="M3 11V9a4 4 0 0 1 4-4h14"/>
              <polyline points="7,23 3,19 7,15"/>
              <path d="M21 13v2a4 4 0 0 1-4 4H3"/>
            </svg>
            <span>Internal Transfer</span>
          </a>
        </div>
        
        <!-- Relatórios with submenu -->
        <div class="menu-item" id="relatoriosMenu">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor">
            <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/>
            <polyline points="14,2 14,8 20,8"/>
            <line x1="16" y1="13" x2="8" y2="13"/>
            <line x1="16" y1="17" x2="8" y2="17"/>
            <polyline points="10,9 9,9 8,9"/>
          </svg>
          <span>Reports</span>
          <svg class="arrow" viewBox="0 0 24 24" fill="none" stroke="currentColor">
            <polyline points="9,18 15,12 9,6"/>
          </svg>
        </div>
        <div class="submenu" id="relatoriosSubmenu">
          <a href="cashin.php" class="submenu-item">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor">
              <polyline points="22,12 18,12 15,21 9,3 6,12 2,12"/>
            </svg>
            <span>Recharges / Deposits</span>
          </a>
          
          <a href="cashout.php" class="submenu-item">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor">
              <polyline points="22,7 13.5,15.5 8.5,10.5 2,17"/>
              <polyline points="16,7 22,7 22,13"/>
            </svg>
            <span>Withdrawals</span>
          </a>
          
          
        </div>
        
        <!-- API with submenu -->
        <div class="menu-item" id="apiMenu">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor">
            <polyline points="16,18 22,12 16,6"/>
            <polyline points="8,6 2,12 8,18"/>
          </svg>
          <span>API</span>
          <svg class="arrow" viewBox="0 0 24 24" fill="none" stroke="currentColor">
            <polyline points="9,18 15,12 9,6"/>
          </svg>
        </div>
        <div class="submenu" id="apiSubmenu">
          <a href="keys.php" class="submenu-item">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor">
              <path d="M21 2l-2 2m-7.61 7.61a5.5 5.5 0 1 1-7.778 7.778 5.5 5.5 0 0 1 7.777-7.777zm0 0L15.5 7.5m0 0l3 3L22 7l-3-3m-3.5 3.5L19 4"/>
            </svg>
            <span>API Keys</span>
          </a>
        </div>
        
        

<?php
// Menu Item
?>
<a class="menu-item" href="#" onclick="showCheckoutAlert()">
  <svg viewBox="0 0 24 24" fill="none" stroke="currentColor">
    <path d="M6 6h15l-1.5 9h-13z"/>
    <circle cx="9" cy="21" r="1"/>
    <circle cx="20" cy="21" r="1"/>
  </svg>
  <span>Checkout</span>
</a>

<!-- Enhanced Checkout Modal -->
<div id="checkoutAlert" style="
  position: fixed;
  top: 0;
  left: 0;
  width: 100%;
  height: 100%;
  background: rgba(0, 0, 0, 0.5);
  backdrop-filter: blur(8px);
  display: none;
  align-items: center;
  justify-content: center;
  z-index: 9999;
  opacity: 0;
  transition: all 0.5s cubic-bezier(0.16, 1, 0.3, 1);
  padding: 20px;
  font-family: 'Poppins', 'Inter', sans-serif;
">
  <div class="checkout-modal-content" style="
    background: rgba(255, 255, 255, 0.95);
    backdrop-filter: blur(20px);
    border-radius: 24px;
    box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25), 0 10px 20px -5px rgba(0, 0, 0, 0.1);
    border: 1px solid rgba(64, 224, 208, 0.2);
    max-width: 480px;
    width: 90%;
    position: relative;
    overflow: hidden;
    transform: scale(0.8) translateY(50px);
    transition: all 0.5s cubic-bezier(0.16, 1, 0.3, 1);
    margin: auto;
  ">
    <!-- Header -->
    <div class="checkout-modal-header" style="
      background: linear-gradient(135deg, #00CED1 0%, #40E0D0 50%, #20B2AA 100%);
      color: white;
      text-align: center;
      padding: 32px 24px;
      position: relative;
      overflow: hidden;
    ">
      <div class="checkout-modal-icon" style="
        width: 64px;
        height: 64px;
        background: rgba(255, 255, 255, 0.2);
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        margin: 0 auto 16px;
        animation: iconPulse 2s infinite;
        font-size: 28px;
      ">
        🛒
      </div>
      <h3 style="
        font-size: 1.5rem;
        font-weight: 600;
        margin: 0;
        text-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
      ">Checkout Unavailable</h3>
      
      <!-- Close Button -->
      <button onclick="hideCheckoutAlert()" style="
        position: absolute;
        top: 16px;
        right: 16px;
        width: 40px;
        height: 40px;
        background: rgba(255, 255, 255, 0.2);
        border: none;
        border-radius: 50%;
        color: white;
        cursor: pointer;
        display: flex;
        align-items: center;
        justify-content: center;
        transition: all 0.3s ease;
        font-size: 18px;
        font-weight: bold;
      ">×</button>
    </div>

    <!-- Body -->
    <div class="checkout-modal-body" style="
      padding: 32px 24px;
      text-align: center;
    ">
      <div class="checkout-warning-icon" style="
        width: 80px;
        height: 80px;
        background: linear-gradient(135deg, #fff3cd, #ffeaa7);
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        margin: 0 auto 24px;
        animation: bounce 2s infinite;
        font-size: 36px;
      ">⚠️</div>
      
      <p style="
        color: #4a5568;
        font-size: 1.1rem;
        line-height: 1.6;
        margin-bottom: 32px;
      ">This account does not have an active checkout yet.</p>

      <!-- Action Buttons -->
      <div style="
        display: flex;
        flex-direction: column;
        gap: 12px;
      ">
        <button onclick="hideCheckoutAlert()" style="
          background: linear-gradient(135deg, #00CED1 0%, #40E0D0 50%, #20B2AA 100%);
          color: white;
          border: none;
          padding: 14px 24px;
          border-radius: 12px;
          font-weight: 500;
          font-size: 1rem;
          cursor: pointer;
          transition: all 0.3s ease;
          position: relative;
          overflow: hidden;
        ">✓ Understood</button>
      </div>
    </div>

    <!-- Progress Bar -->
    <div class="checkout-progress-bar" style="
      position: absolute;
      bottom: 0;
      left: 0;
      height: 4px;
      background: linear-gradient(135deg, #00CED1 0%, #40E0D0 50%, #20B2AA 100%);
      border-radius: 0 0 24px 24px;
      width: 100%;
      animation: checkoutProgressBar 4s linear forwards;
    "></div>
  </div>
</div>


<!-- Enhanced Animations and Scripts -->
<style>
@keyframes iconPulse {
  0%, 100% { transform: scale(1); }
  50% { transform: scale(1.05); }
}

@keyframes bounce {
  0%, 20%, 50%, 80%, 100% { transform: translateY(0); }
  40% { transform: translateY(-10px); }
  60% { transform: translateY(-5px); }
}

@keyframes checkoutProgressBar {
  0% { width: 100%; }
  100% { width: 0%; }
}

@keyframes shimmer {
  0% { left: -100%; }
  100% { left: 100%; }
}

#checkoutAlert.show {
  display: flex !important;
  opacity: 1 !important;
}

#checkoutAlert.show .checkout-modal-content {
  transform: scale(1) translateY(0) !important;
}

.checkout-modal-header::before {
  content: '';
  position: absolute;
  top: 0;
  left: -100%;
  width: 100%;
  height: 100%;
  background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.2), transparent);
  animation: shimmer 2s infinite;
}

/* Hover Effects */
button:hover {
  transform: translateY(-2px) !important;
  box-shadow: 0 8px 20px rgba(0, 206, 209, 0.4) !important;
}

.checkout-close-btn:hover {
  background: rgba(255, 255, 255, 0.3) !important;
  transform: scale(1.1) !important;
}

/* Mobile Responsive */
@media (max-width: 576px) {
  #checkoutAlert {
    padding: 10px !important;
  }
  
  .checkout-modal-content {
    width: 95% !important;
    max-width: none !important;
  }
  
  .checkout-modal-header {
    padding: 24px 20px !important;
  }
  
  .checkout-modal-body {
    padding: 24px 20px !important;
  }
}
</style>

<script>
let checkoutModalTimer;

function showCheckoutAlert() {
  const modal = document.getElementById('checkoutAlert');
  const progressBar = modal.querySelector('.checkout-progress-bar');
  
  // Reset progress bar
  progressBar.style.width = '100%';
  progressBar.style.animation = 'none';
  
  // Show modal with animation
  modal.style.display = 'flex';
  setTimeout(() => {
    modal.classList.add('show');
  }, 10);
  
  // Start progress bar animation
  setTimeout(() => {
    progressBar.style.animation = 'checkoutProgressBar 4s linear forwards';
  }, 100);
  
  // Auto closand thefter 4 seconds
  checkoutModalTimer = setTimeout(() => {
    hideCheckoutAlert();
  }, 4000);
}

function hideCheckoutAlert() {
  const modal = document.getElementById('checkoutAlert');
  modal.classList.remove('show');
  
  // Clear timer if exists
  if (checkoutModalTimer) {
    clearTimeout(checkoutModalTimer);
  }
  
  // Hide modal after animation
  setTimeout(() => {
    modal.style.display = 'none';
  }, 500);
}

// Close modal with ESC key
document.addEventListener('keydown', function(event) {
  if (event.key === 'Escape') {
    hideCheckoutAlert();
  }
});

// Close modal when clicking outside
document.getElementById('checkoutAlert').addEventListener('click', function(event) {
  if (event.target === this) {
    hideCheckoutAlert();
  }
});
</script>



<a href="/dev/" target="_blank" class="menu-item">
  <svg viewBox="0 0 24 24" fill="none" stroke="currentColor">
    <path d="M12 21a9 9 0 1 0 0-18 9 9 0 0 0 0 18z"/>
    <path d="M16 8h-6a2 2 0 1 0 0 4h4a2 2 0 1 1 0 4H8"/>
    <path d="M12 18V6"/>
  </svg>
  <span>Documentation</span>
</a>
<?php } ?>



      
        
        
        <a href="settings.php" class="menu-item">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor">
            <circle cx="12" cy="12" r="3"/>
            <path d="M19.4 15a1.65 1.65 0 0 0 .33 1.82l.06.06a2 2 0 0 1 0 2.83 2 2 0 0 1-2.83 0l-.06-.06a1.65 1.65 0 0 0-1.82-.33 1.65 1.65 0 0 0-1 1.51V21a2 2 0 0 1-2 2 2 2 0 0 1-2-2v-.09A1.65 1.65 0 0 0 9 19.4a1.65 1.65 0 0 0-1.82.33l-.06.06a2 2 0 0 1-2.83 0 2 2 0 0 1 0-2.83l.06-.06a1.65 1.65 0 0 0 .33-1.82 1.65 1.65 0 0 0-1.51-1H3a2 2 0 0 1-2-2 2 2 0 0 1 2-2h.09A1.65 1.65 0 0 0 4.6 9a1.65 1.65 0 0 0-.33-1.82l-.06-.06a2 2 0 0 1 0-2.83 2 2 0 0 1 2.83 0l.06.06a1.65 1.65 0 0 0 1.82.33H9a1.65 1.65 0 0 0 1 1.51V3a2 2 0 0 1 2-2 2 2 0 0 1 2 2v.09a1.65 1.65 0 0 0 1 1.51 1.65 1.65 0 0 0 1.82-.33l.06-.06a2 2 0 0 1 2.83 0 2 2 0 0 1 0 2.83l-.06.06a1.65 1.65 0 0 0-.33 1.82V9a1.65 1.65 0 0 0 1.51 1H21a2 2 0 0 1 2 2 2 2 0 0 1-2 2h-.09a1.65 1.65 0 0 0-1.51 1z"/>
          </svg>
          <span>Settings</span>
        </a>
      </div>
    </nav>
    
    <!-- Progress Section with Icon -->
    <div class="progress-section">
      <?php
      // Calculate current revenuand thend determine goal
      function connectDbProgress() {
          $dbHost = getenv('DB_HOST');
          $dbName = getenv('DB_NAME');
          $dbUser = getenv('DB_USER');
          $dbPass = getenv('DB_PASS');

          try {
              $pdo = new PDO("mysql:host=$dbHost;dbname=$dbName", $dbUser, $dbPass);
              $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
              return $pdo;
          } catch (PDOException $e) {
              die("Error ao conectar ao banco de dados: " . $e->getMessage());
          }
      }

      function getUserRevenue($pdo, $user_id) {
          $stmt = $pdo->prepare("
              SELECT SUM(amount) AS total 
              FROM transactions 
              WHERE 
                  status = 'PAID' 
                  AND (
                      (type = 'DEPOSIT' AND user_id = :user_id) OR 
                      (type = 'INTERNAL_TRANSFER' AND external_id = :user_id)
                  )
                  AND MONTH(created_at) = MONTH(CURRENT_DATE())
                  AND YEAR(created_at) = YEAR(CURRENT_DATE())
          ");
          $stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
          $stmt->execute();

          $result = $stmt->fetch(PDO::FETCH_ASSOC);
          return $result['total'] ?? 0;
      }

      $pdo = connectDbProgress();
      $user_id = $_SESSION['user_id'];
      $currentRevenue = getUserRevenue($pdo, $user_id);

      // Determine goal based on current revenue
      if ($currentRevenue < 10000) {
          $goal = 10000;
      } else {
          $goal = 100000;
      }

      $progressPercentage = min(($currentRevenue / $goal) * 100, 100);
      ?>
      
      <div class="progress-header">
        <div class="progress-icon">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor">
            <path d="M22 12h-4l-3 9L9 3l-3 9H2"/>
          </svg>
        </div>
        <div class="progress-amount">
          R$ <?= number_format($currentRevenue, 2, ',', '.') ?> / <?= $goal >= 100000 ? '100K' : '10K' ?>
        </div>
      </div>
      <div class="progress-bar-container">
        <div class="progress-bar" style="width: <?= $progressPercentage ?>%"></div>
      </div>
    </div>
  </div>
  
  <!-- Mobile Menu Toggle (Left) -->
  <div class="floating-header-left">
    <button class="header-btn" id="menuToggle">
      <svg viewBox="0 0 24 24" fill="none" stroke="currentColor">
        <line x1="3" y1="6" x2="21" y2="6"/>
        <line x1="3" y1="12" x2="21" y2="12"/>
        <line x1="3" y1="18" x2="21" y2="18"/>
      </svg>
    </button>
  </div>
  
  <!-- Floating Header Actions (Right) -->
  <div class="floating-header">
    <button class="header-btn" id="themeToggle">
      <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" data-light>
        <path d="M21 12.79A9 9 0 1 1 11.21 3 7 7 0 0 0 21 12.79z"/>
      </svg>
      <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" data-dark style="display: none;">
        <circle cx="12" cy="12" r="5"/>
        <line x1="12" y1="1" x2="12" y2="3"/>
        <line x1="12" y1="21" x2="12" y2="23"/>
        <line x1="4.22" y1="4.22" x2="5.64" y2="5.64"/>
        <line x1="18.36" y1="18.36" x2="19.78" y2="19.78"/>
        <line x1="1" y1="12" x2="3" y2="12"/>
        <line x1="21" y1="12" x2="23" y2="12"/>
        <line x1="4.22" y1="19.78" x2="5.64" y2="18.36"/>
        <line x1="18.36" y1="5.64" x2="19.78" y2="4.22"/>
      </svg>
    </button>
    
    <button class="header-btn" id="toggleValues" title="Ocultar/Mostrar Valores">
      <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" id="eyeIcon">
        <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/>
        <circle cx="12" cy="12" r="3"/>
      </svg>
    </button>
    
    <div class="dropdown">
      <button class="header-btn" data-bs-toggle="dropdown">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor">
          <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/>
          <circle cx="12" cy="7" r="4"/>
        </svg>
      </button>
      <ul class="dropdown-menu dropdown-menu-end">
        <li><a class="dropdown-item" href="profile.php">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor">
            <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/>
            <circle cx="12" cy="7" r="4"/>
          </svg>Profile</a></li>
        <li><a class="dropdown-item" id="solicitarPremio" href="#" style="cursor: pointer;">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor">
            <path d="M6 9l6 6 6-6"/>
            <rect x="3" y="4" width="18" height="11" rx="2" ry="2"/>
            <path d="M7 4v-1a2 2 0 0 1 2-2h6a2 2 0 0 1 2 2v1"/>
          </svg>Claim Reward</a></li>
        <li><a class="dropdown-item" href="logout.php">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" class="logout-icon">
            <path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"/>
            <polyline points="16,17 21,12 16,7"/>
            <line x1="21" y1="12" x2="9" y2="12"/>
          </svg>Logout</a></li>
      </ul>
    </div>
  </div>

  <!-- Revenue Requirement Alert -->
  <div class="alert-overlay" id="alertOverlay"></div>
  <div class="revenue-alert" id="revenueAlert">
    <div class="alert-icon">
      <svg viewBox="0 0 24 24" fill="none" stroke="currentColor">
        <path d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
      </svg>
    </div>
    <div class="alert-title">Insufficient Revenue</div>
    <div class="alert-message">
      Você ainda não atingiu o faturamento necessário de R$ 10.000,00 para solicitar o prêmio básico. Continue trabalhando para alcançar esta meta!
    </div>
    <button class="alert-close" onclick="closeRevenueAlert()">Understood</button>
  </div>
  
  <!-- Main Content -->
  <main class="main-container">
    <?php
    function connectDb245() {
        $dbHost = getenv('DB_HOST');
        $dbName = getenv('DB_NAME');
        $dbUser = getenv('DB_USER');
        $dbPass = getenv('DB_PASS');

        try {
            $pdo = new PDO("mysql:host=$dbHost;dbname=$dbName", $dbUser, $dbPass);
            $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            return $pdo;
        } catch (PDOException $e) {
            die("Error ao conectar ao banco de dados: " . $e->getMessage());
        }
    }

    function getUserBalance($pdo, $user_id) {
        $stmt = $pdo->prepare("
            SELECT SUM(amount) AS total 
            FROM transactions 
            WHERE 
                status = 'PAID' 
                AND (
                    (type = 'DEPOSIT' AND user_id = :user_id) OR 
                    (type = 'INTERNAL_TRANSFER' AND external_id = :user_id)
                )
        ");
        $stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
        $stmt->execute();

        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result['total'] ?? 0;
    }

    function getUserAverageTransaction($pdo, $user_id) {
        $stmt = $pdo->prepare("SELECT amount FROM transactions WHERE (user_id = :user_id OR external_id = :user_id) AND status = 'PAID'");
        $stmt->bindParam(':user_id', $user_id);
        $stmt->execute();

        $totalAmount = 0;
        $transactionCount = 0;

        while ($transaction = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $totalAmount += $transaction['amount'];
            $transactionCount++;
        }

        if ($transactionCount > 0) {
            return $totalAmount / $transactionCount;
        } else {
            return 0;
        }
    }

    function getUserTransactionCount($pdo, $user_id) {
        $stmt = $pdo->prepare("SELECT COUNT(*) FROM transactions WHERE (user_id = :user_id OR external_id = :user_id) AND status = 'PAID'");
        $stmt->bindParam(':user_id', $user_id);
        $stmt->execute();

        return $stmt->fetchColumn();
    }

    function getDailyTransactionData($pdo, $user_id, $days = 7) {
        $stmt = $pdo->prepare("
            SELECT 
                DATE(created_at) as date,
                COUNT(*) as count,
                SUM(amount) as total
            FROM transactions 
            WHERE (user_id = :user_id OR external_id = :user_id) 
            AND status = 'PAID' 
            AND created_at >= DATE_SUB(NOW(), INTERVAL :days DAY)
            GROUP BY DATE(created_at)
            ORDER BY date ASC
        ");
        $stmt->bindParam(':user_id', $user_id);
        $stmt->bindParam(':days', $days, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    function getTodayTransactionData($pdo, $user_id) {
        $stmt = $pdo->prepare("
            SELECT 
                HOUR(created_at) as hour,
                COUNT(*) as count,
                SUM(amount) as total
            FROM transactions 
            WHERE (user_id = :user_id OR external_id = :user_id) 
            AND status = 'PAID' 
            AND DATE(created_at) = CURDATE()
            GROUP BY HOUR(created_at)
            ORDER BY hour ASC
        ");
        $stmt->bindParam(':user_id', $user_id);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    function getRecentTransactions($pdo, $user_id, $limit = 10) {
        $stmt = $pdo->prepare("
            SELECT 
                t.*,
                u_payer.namand thes payer_name,
                u_receiver.namand thes receiver_name
            FROM transactions t
            LEFT JOIN users u_payer ON t.user_id = u_payer.id
            LEFT JOIN users u_receiver ON t.external_id = u_receiver.id
            WHERE (t.user_id = :user_id OR t.external_id = :user_id) 
            AND t.status = 'PAID'
            ORDER BY t.created_at DESC
            LIMIT :limit
        ");
        $stmt->bindParam(':user_id', $user_id);
        $stmt->bindParam(':limit', $limit, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    $pdo = connectDb245();

    if (isset($_SESSION['user_id'])) {
        $user_id = $_SESSION['user_id'];
        $totalAmount = getUserBalance($pdo, $user_id);
        $averageAmount = getUserAverageTransaction($pdo, $user_id);
        $transactionCount = getUserTransactionCount($pdo, $user_id);
        $dailyData7 = getDailyTransactionData($pdo, $user_id, 7);
        $dailyData30 = getDailyTransactionData($pdo, $user_id, 30);
        $todayData = getTodayTransactionData($pdo, $user_id);
        $recentTransactions = getRecentTransactions($pdo, $user_id);
    } else {
        $totalAmount = 0;
        $averageAmount = 0;
        $transactionCount = 0;
        $dailyData7 = [];
        $dailyData30 = [];
        $todayData = [];
        $recentTransactions = [];
    }
    ?>
    
    <div class="dashboard-title">
      <h1>Dashboard</h1>
      <p>A bright futurand thewaits you! 🚀 </p>
    </div>

    <!-- Stats Grid -->
    <div class="stats-grid">
      <div class="stat-card blue">
        <div class="stat-header">
          <div class="stat-info">
            <h3>Available Balance</h3>
            <div class="stat-value financial-value">R$ <?= str_replace('.', ',', number_format($_SESSION['balance'], 2)) ?></div>
          </div>
          <div class="stat-icon">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor">
              <rect x="1" y="4" width="22" height="16" rx="2" ry="2"/>
              <line x1="1" y1="10" x2="23" y2="10"/>
            </svg>
          </div>
        </div>
      </div>
      
      <div class="stat-card green">
        <div class="stat-header">
          <div class="stat-info">
            <h3>Total Received</h3>
            <div class="stat-value financial-value">R$ <?= str_replace('.', ',', number_format($totalAmount, 2)) ?></div>
          </div>
          <div class="stat-icon">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor">
              <polyline points="22,7 13.5,15.5 8.5,10.5 2,17"/>
              <polyline points="16,7 22,7 22,13"/>
            </svg>
          </div>
        </div>
      </div>
      
      <div class="stat-card orange">
        <div class="stat-header">
          <div class="stat-info">
            <h3>Average Ticket</h3>
            <div class="stat-value financial-value">R$ <?= str_replace('.', ',', number_format($averageAmount, 2)) ?></div>
          </div>
          <div class="stat-icon">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor">
              <line x1="18" y1="20" x2="18" y2="10"/>
              <line x1="12" y1="20" x2="12" y2="4"/>
              <line x1="6" y1="20" x2="6" y2="14"/>
            </svg>
          </div>
        </div>
      </div>
      
      <div class="stat-card purple">
        <div class="stat-header">
          <div class="stat-info">
            <h3>Total Transactions</h3>
            <div class="stat-value financial-value transaction-count"><?= $transactionCount ?></div>
          </div>
          <div class="stat-icon">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor">
              <polyline points="22,12 18,12 15,21 9,3 6,12 2,12"/>
            </svg>
          </div>
        </div>
      </div>
    </div>

    <!-- Charts Container -->
    <div class="charts-container">
      <!-- Transaction Trend Chart -->
      <div class="chart-card">
        <div class="chart-header">
          <h3 class="chart-title">Sales in Last Days</h3>
          <div class="chart-filters">
            <button class="filter-btn active" data-period="7"><span>7 Days</span></button>
            <button class="filter-btn" data-period="30"><span>30 Days</span></button>
            <button class="filter-btn" data-period="today"><span>Today</span></button>
          </div>
        </div>
        <div id="transactionChart" style="height: 360px;"></div>
      </div>

      <!-- Recent Activity -->
      <div class="activity-card">
        <div class="activity-header">
          <h3 class="chart-title">Recent Activities</h3>
        </div>
        <div class="activity-list">
          <?php if (empty($recentTransactions)): ?>
            <div class="activity-item">
              <div class="activity-avatar" style="background: rgba(100, 116, 139, 0.2);">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" style="width: 24px; height: 24px;">
                  <circle cx="12" cy="12" r="10"/>
                  <line x1="12" y1="6" x2="12" y2="12"/>
                  <line x1="12" y1="16" x2="12.01" y2="16"/>
                </svg>
              </div>
              <div class="activity-content">
                <div class="activity-name">No transaction found</div>
                <div class="activity-detail">When you receive transactions, they will appear here</div>
              </div>
            </div>
          <?php else: ?>
            <?php foreach ($recentTransactions as $transaction): ?>
              <?php
              $isIncoming = false;
              $transactionName = '';
              $transactionDetail = '';
              
              if ($transaction['type'] == 'DEPOSIT') {
                  $isIncoming = true;
                  $transactionName = $transaction['payer_name'] ?? 'PIX Recebido';
                  $transactionDetail = 'Depósito PIX';
              } elseif ($transaction['type'] == 'WITHDRAWAL') {
                  $isIncoming = false;
                  $transactionName = $transaction['receiver_name'] ?? 'PIX Enviado';
                  $transactionDetail = 'Transferência PIX';
              } elseif ($transaction['type'] == 'INTERNAL_TRANSFER') {
                  if ($transaction['external_id'] == $user_id) {
                      // Receiving internal transfer
                      $isIncoming = true;
                      $transactionName = $transaction['payer_name'] ?? 'Transferência Recebida';
                      $transactionDetail = 'Pagamento Interno Recebido';
                  } else {
                      // Sending internal transfer
                      $isIncoming = false;
                      $transactionName = $transaction['receiver_name'] ?? 'Pagamento Enviado';
                      $transactionDetail = 'Pagamento Interno Enviado';
                  }
              }
              ?>
              <div class="activity-item">
                <div class="activity-avatar">
                  <?= strtoupper(substr($transactionName, 0, 1)) ?>
                </div>
                <div class="activity-content">
                  <div class="activity-name"><?= htmlspecialchars($transactionName) ?></div>
                  <div class="activity-detail">
                    <?= $transactionDetail ?> • 
                    <?= date('d/m/Y H:i', strtotime($transaction['created_at'])) ?>
                  </div>
                </div>
                <div class="activity-amount <?= $isIncoming ? 'positive' : 'negative' ?> financial-value">
                  <?= $isIncoming ? '+' : '-' ?>R$ <?= str_replace('.', ',', number_format($transaction['amount'], 2)) ?>
                </div>
              </div>
            <?php endforeach; ?>
          <?php endif; ?>
        </div>
      </div>
    </div>

    <!-- Reports Section -->
    <div class="reports-section">
      <div class="reports-header">
        <h3>Financial Summary</h3>
      </div>
      <table class="reports-table">
        <thead>
          <tr>
            <th>Type</th>
            <th>This Month</th>
            <th>Last Month</th>
            <th>Variação</th>
          </tr>
        </thead>
        <tbody>
          <?php
          // Get current month data
          function getMonthlyData($pdo, $user_id, $month_offset = 0) {
              $stmt = $pdo->prepare("
                  SELECT 
                      SUM(amount) AS total,
                      COUNT(*) AS count
                  FROM transactions 
                  WHERE 
                      status = 'PAID' 
                      AND (
                          (type = 'DEPOSIT' AND user_id = :user_id) OR 
                          (type = 'INTERNAL_TRANSFER' AND external_id = :user_id)
                      )
                      AND MONTH(created_at) = MONTH(DATE_SUB(NOW(), INTERVAL :offset MONTH))
                      AND YEAR(created_at) = YEAR(DATE_SUB(NOW(), INTERVAL :offset MONTH))
              ");
              $stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
              $stmt->bindParam(':offset', $month_offset, PDO::PARAM_INT);
              $stmt->execute();

              $result = $stmt->fetch(PDO::FETCH_ASSOC);
              return [
                  'total' => $result['total'] ?? 0,
                  'count' => $result['count'] ?? 0
              ];
          }

          $currentMonth = getMonthlyData($pdo, $user_id, 0);
          $lastMonth = getMonthlyData($pdo, $user_id, 1);
          
          $revenue_variation = $lastMonth['total'] > 0 ? (($currentMonth['total'] - $lastMonth['total']) / $lastMonth['total']) * 100 : 0;
          $transactions_variation = $lastMonth['count'] > 0 ? (($currentMonth['count'] - $lastMonth['count']) / $lastMonth['count']) * 100 : 0;
          ?>
          <tr>
            <td>Receita Total</td>
            <td class="financial-value">R$ <?= str_replace('.', ',', number_format($currentMonth['total'], 2)) ?></td>
            <td class="financial-value">R$ <?= str_replace('.', ',', number_format($lastMonth['total'], 2)) ?></td>
            <td style="color: <?= $revenue_variation >= 0 ? 'var(--dashboard-green)' : 'var(--dashboard-red)' ?>">
              <?= $revenue_variation >= 0 ? '+' : '' ?><?= number_format($revenue_variation, 1) ?>%
            </td>
          </tr>
          <tr>
            <td>Transactions</td>
            <td><?= $currentMonth['count'] ?></td>
            <td><?= $lastMonth['count'] ?></td>
            <td style="color: <?= $transactions_variation >= 0 ? 'var(--dashboard-green)' : 'var(--dashboard-red)' ?>">
              <?= $transactions_variation >= 0 ? '+' : '' ?><?= number_format($transactions_variation, 1) ?>%
            </td>
          </tr>
          <tr class="total-row <?= $_SESSION['balance'] >= 0 ? 'positive' : 'negative' ?>">
            <td><strong>Saldo Atual</strong></td>
            <td colspan="3" class="financial-value">
              <strong>R$ <?= str_replace('.', ',', number_format($_SESSION['balance'], 2)) ?></strong>
            </td>
          </tr>
        </tbody>
      </table>
      <div class="balance-summary">
        Account Status: <?= $_SESSION['balance'] >= 0 ? 'Positivo' : 'Negativo' ?>
      </div>
    </div>
  </main>

  <!-- Scripts -->
  <script src="assets/js/jquery-3.6.1.min.js"></script>
  <script src="assets/libs/@popperjs/core/umd/popper.min.js"></script>
  <script src="assets/libs/bootstrap/js/bootstrap.bundle.min.js"></script>
  <script src="assets/js/defaultmenu.min.js"></script>
  <script src="assets/libs/node-waves/waves.min.js"></script>
  <script src="assets/js/sticky.js"></script>
  <script src="assets/libs/simplebar/simplebar.min.js"></script>
  <script src="assets/js/simplebar.js"></script>
  <script src="assets/libs/@simonwep/pickr/pickr.es5.min.js"></script>
  <script src="assets/libs/apexcharts/apexcharts.min.js"></script>
  <script src="assets/js/sales-dashboard.js"></script>
  <script src="assets/js/custom.js"></script>
  <script src="assets/js/custom-switcher.min.js"></script>

  <!-- Modal -->
  <div class="modal fade" id="modalConteudo" tabindex="-1" aria-labelledby="modalConteudo" data-bs-backdrop="static" data-bs-keyboard="false" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable">
      <div class="modal-content">
      </div>
    </div>
  </div>

  <script>
  $(document).ready(function() {
    $('.openModal').click(function() {
      var targetUrl = $(this).data('target');
      
      // Close sidebar if open (mobile)
      if ($('#sidebar').hasClass('open')) {
        $('#sidebar').removeClass('open');
        $('#sidebarOverlay').removeClass('active');
        $('body').removeClass('sidebar-open');
      }
      
      $.get(targetUrl, function(data) {
        $('#modalConteudo .modal-content').html(data);
        $('#modalConteudo').modal('show');
      });
    });
  });
  </script>

  <!-- Dashboard JavaScript -->
  <script>
    $(document).ready(function() {
        // Chart Data from PHP
        const dailyData7 = <?= json_encode($dailyData7) ?>;
        const dailyData30 = <?= json_encode($dailyData30) ?>;
        const todayData = <?= json_encode($todayData) ?>;
        const currentRevenue = <?= $currentRevenue ?>;
        
        console.log('Chart data loaded:', { dailyData7, dailyData30, todayData });
        
        // Theme Toggle
        const themeToggle = document.getElementById('themeToggle');
        const htmlElement = document.documentElement;
        const lightIcon = themeToggle.querySelector('[data-light]');
        const darkIcon = themeToggle.querySelector('[data-dark]');
        
        // Load saved theme
        const savedTheme = localStorage.getItem('theme') || 'light';
        htmlElement.setAttribute('data-theme-mode', savedTheme);
        updateThemeIcons(savedTheme);
        
        themeToggle.addEventListener('click', () => {
            const currentTheme = htmlElement.getAttribute('data-theme-mode');
            const newTheme = currentTheme === 'light' ? 'dark' : 'light';
            
            htmlElement.setAttribute('data-theme-mode', newTheme);
            localStorage.setItem('theme', newTheme);
            updateThemeIcons(newTheme);
        });
        
        function updateThemeIcons(theme) {
            if (theme === 'dark') {
                lightIcon.style.display = 'none';
                darkIcon.style.display = 'block';
            } else {
                lightIcon.style.display = 'block';
                darkIcon.style.display = 'none';
            }
        }
        
        // Values Toggle
        const toggleValuesBtn = document.getElementById('toggleValues');
        const eyeIcon = document.getElementById('eyeIcon');
        let valuesVisible = true;
        
        toggleValuesBtn.addEventListener('click', () => {
            valuesVisible = !valuesVisible;
            const financialValues = document.querySelectorAll('.financial-value');
            
            financialValues.forEach(value => {
                if (valuesVisible) {
                    value.classList.remove('value-blurred');
                } else {
                    value.classList.add('value-blurred');
                }
            });
            
            // Update eye icon
            if (valuesVisible) {
                eyeIcon.innerHTML = '<path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/>';
            } else {
                eyeIcon.innerHTML = '<path d="M17.94 17.94A10.07 10.07 0 0 1 12 20c-7 0-11-8-11-8a18.45 18.45 0 0 1 5.06-5.94M9.9 4.24A9.12 9.12 0 0 1 12 4c7 0 11 8 11 8a18.5 18.5 0 0 1-2.16 3.19m-6.72-1.07a3 3 0 1 1-4.24-4.24"/><line x1="1" y1="1" x2="23" y2="23"/>';
            }
        });

        // Solicitar Prêmio functionality
        document.getElementById('solicitarPremio').addEventListener('click', function(e) {
            e.preventDefault();
            if (currentRevenue < 10000) {
                showRevenueAlert();
            } else {
                // Redirect to prize request page or show success message
                showToast('Prêmio Solicitado', 'Sua solicitação foi enviada para análise!');
            }
        });
        
        // ApexCharts
        let chart;
        
        function createChart(data, period) {
            if (chart) {
                chart.destroy();
            }
            
            let categories = [];
            let series = [];
            
            console.log('Creating chart for period:', period, 'with data:', data);
            
            if (period === 'today') {
                // Hourly data for today
                const hours = Array.from({length: 24}, (_, i) => i);
                categories = hours.map(h => h.toString().padStart(2, '0') + ':00');
                series = hours.map(h => {
                    const hourData = data.find(d => parseInt(d.hour) === h);
                    return hourData ? parseFloat(hourData.total || 0) : 0;
                });
            } else {
                // Daily data
                const days = period === '7' ? 7 : 30;
                const today = new Date();
                const dateRange = [];
                
                for (let i = days - 1; i >= 0; i--) {
                    const date = new Date(today);
                    date.setDate(date.getDate() - i);
                    dateRange.push(date.toISOString().split('T')[0]);
                }
                
                categories = dateRange.map(date => {
                    const d = new Date(date);
                    return d.toLocaleDateString('pt-BR', { day: '2-digit', month: '2-digit' });
                });
                
                series = dateRange.map(date => {
                    const dayData = data.find(d => d.date === date);
                    return dayData ? parseFloat(dayData.total || 0) : 0;
                });
            }
            
            console.log('Chart categories:', categories);
            console.log('Chart series:', series);
            
            const currentTheme = htmlElement.getAttribute('data-theme-mode');
            const textColor = currentTheme === 'dark' ? '#94a3b8' : '#64748b';
            const borderColor = currentTheme === 'dark' ? 'rgba(255, 255, 255, 0.1)' : 'rgba(59, 130, 246, 0.1)';
            
            const options = {
                chart: {
                    type: 'area',
                    height: 360,
                    fontFamily: 'Inter, sans-serif',
                    toolbar: { show: false },
                    background: 'transparent'
                },
                series: [{
                    name: 'Valor (R$)',
                    data: series
                }],
                xaxis: {
                    categories: categories,
                    labels: {
                        style: {
                            colors: textColor,
                            fontSize: '12px'
                        }
                    },
                    axisBorder: { show: false },
                    axisTicks: { show: false }
                },
                yaxis: {
                    labels: {
                        style: {
                            colors: textColor,
                            fontSize: '12px'
                        },
                        formatter: function(value) {
                            return 'R$ ' + value.toLocaleString('pt-BR', {minimumFractionDigits: 0, maximumFractionDigits: 0});
                        }
                    }
                },
                fill: {
                    type: 'gradient',
                    gradient: {
                        shadeIntensity: 1,
                        opacityFrom: 0.7,
                        opacityTo: 0.1,
                        stops: [0, 100],
                        colorStops: [
                            {
                                offset: 0,
                                color: 'rgb(59, 130, 246)',
                                opacity: 0.6
                            },
                            {
                                offset: 100,
                                color: 'rgb(6, 182, 212)',
                                opacity: 0.1
                            }
                        ]
                    }
                },
                stroke: {
                    curve: 'smooth',
                    width: 3,
                    colors: ['rgb(59, 130, 246)']
                },
                grid: {
                    borderColor: borderColor,
                    strokeDashArray: 3,
                    xaxis: { lines: { show: false } }
                },
                dataLabels: { enabled: false },
                tooltip: {
                    theme: currentTheme,
                    style: {
                        fontSize: '12px',
                        fontFamily: 'Inter, sans-serif'
                    },
                    y: {
                        formatter: function(value) {
                            return 'R$ ' + value.toLocaleString('pt-BR', {minimumFractionDigits: 2});
                        }
                    }
                },
                noData: {
                    text: 'Nenhum dado disponível',
                    align: 'center',
                    verticalAlign: 'middle',
                    style: {
                        color: textColor,
                        fontSize: '14px',
                        fontFamily: 'Inter, sans-serif'
                    }
                }
            };
            
            chart = new ApexCharts(document.querySelector("#transactionChart"), options);
            chart.render();
        }
        
        // Initialize chart with 7 days data
        createChart(dailyData7, '7');
        
        // Chart filter buttons
        $('.filter-btn').click(function() {
            $('.filter-btn').removeClass('active');
            $(this).addClass('active');
            
            const period = $(this).data('period');
            let data;
            
            switch(period) {
                case '7':
                    data = dailyData7;
                    break;
                case '30':
                    data = dailyData30;
                    break;
                case 'today':
                    data = todayData;
                    break;
                default:
                    data = dailyData7;
            }
            
            console.log('Switching to period:', period, 'with data:', data);
            createChart(data, period);
        });
        
        // Submenu toggles
        $('#transferenciasMenu').click(function(e) {
            e.preventDefault();
            $(this).toggleClass('expanded');
            $('#transferenciasSubmenu').toggleClass('expanded');
        });
        
        $('#relatoriosMenu').click(function(e) {
            e.preventDefault();
            $(this).toggleClass('expanded');
            $('#relatoriosSubmenu').toggleClass('expanded');
        });
        
        $('#apiMenu').click(function(e) {
            e.preventDefault();
            $(this).toggleClass('expanded');
            $('#apiSubmenu').toggleClass('expanded');
        });
        
        // Sidebar Toggle (Mobile only) with body scroll fix
        $('#menuToggle').click(function() {
            $('#sidebar').addClass('open');
            $('#sidebarOverlay').addClass('active');
            $('body').addClass('sidebar-open');
        });
        
        $('#sidebarOverlay').click(function() {
            $('#sidebar').removeClass('open');
            $('#sidebarOverlay').removeClass('active');
            $('body').removeClass('sidebar-open');
        });
    });

    // Revenue Alert Functions
    function showRevenueAlert() {
        document.getElementById('alertOverlay').classList.add('show');
        document.getElementById('revenueAlert').classList.add('show');
    }

    function closeRevenueAlert() {
        document.getElementById('alertOverlay').classList.remove('show');
        document.getElementById('revenueAlert').classList.remove('show');
    }

    // Closand thelert when clicking overlay
    document.getElementById('alertOverlay').addEventListener('click', closeRevenueAlert);
  </script>

  <!-- Toast Container -->
  <div class="toast-container" id="toastContainer"></div>

  <script>
  function showToast(title, message, duration = 5000) {
    const toastContainer = document.getElementById('toastContainer');
    
    const toast = document.createElement('div');
    toast.className = 'toast';
    
    toast.innerHTML = `
      <div class="toast-content">
        <div class="toast-check">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" style="width: 18px; height: 18px;">
            <polyline points="20,6 9,17 4,12"/>
          </svg>
        </div>
        <div class="message">
          <div class="message-text text-1">${title}</div>
          <div class="message-text text-2">${message}</div>
        </div>
      </div>
      <button class="toast-close">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" style="width: 14px; height: 14px;">
          <line x1="18" y1="6" x2="6" y2="18"/>
          <line x1="6" y1="6" x2="18" y2="18"/>
        </svg>
      </button>
      <div class="progress"></div>
    `;
    
    toastContainer.appendChild(toast);
    
    // Show toast with proper timing
    setTimeout(() => {
      toast.classList.add('show');
      const progress = toast.querySelector('.progress');
      if (progress) {
        progress.classList.add('active');
      }
    }, 150);
    
    // Close toast function
    function closeToast() {
      toast.classList.remove('show');
      setTimeout(() => {
        if (toast.parentNode) {
          toast.parentNode.removeChild(toast);
        }
      }, 500);
    }
    
    // Auto closand thefter duration
    setTimeout(closeToast, duration);
    
    // Close button event
    const closeBtn = toast.querySelector('.toast-close');
    if (closeBtn) {
      closeBtn.addEventListener('click', closeToast);
    }
  }
  </script>

</body>
</html>
