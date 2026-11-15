
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
  <title> <?php include 'libs/get_site_name.php'; ?> - Saídas </title>
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

        // Usar cores do index.php - azul turquesa
        $primaryRgb = isset($config['primary_color']) ? $config['primary_color'] : '52, 152, 219'; // Azul turquesa
        $secondaryRgb = isset($config['secondary_color']) ? $config['secondary_color'] : '0, 188, 212'; // Ciano
    } catch (PDOException $e) {
        die("Error ao conectar ao banco de dados: " . $e->getMessage());
    }
  ?>
  
  <style>
    :root {
        --primary-rgb: <?php echo $primaryRgb; ?>;
        --secondary-rgb: <?php echo $secondaryRgb; ?>;
        
        /* Cores baseadas no design azul turquesa da referência */
        --dashboard-bg: #0f1419;
        --dashboard-surface: #1a1b2e;
        --dashboard-card: #16213e;
        --dashboard-accent: #3498db;
        --dashboard-blue: #52a0fd;
        --dashboard-cyan: #00bcd4;
        --dashboard-green: #10b981;
        --dashboard-purple: #8b5cf6;
        --dashboard-orange: #f59e0b;
        --dashboard-red: #ef4444;
        --dashboard-text: #ffffff;
        --dashboard-text-muted: #94a3b8;
        --dashboard-border: rgba(255, 255, 255, 0.1);
        
        /* Light theme - usando as cores da referência */
        --light-bg: #f8fafc;
        --light-surface: rgba(255, 255, 255, 0.95);
        --light-card: rgba(255, 255, 255, 0.9);
        --light-text: #1e293b;
        --light-text-muted: #64748b;
        --light-border: rgba(52, 152, 219, 0.1);
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
        --sidebar-item-hover: rgba(52, 152, 219, 0.15);
        --sidebar-item-active: linear-gradient(135deg, rgba(52, 152, 219, 0.2), rgba(0, 188, 212, 0.2));
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
        --sidebar-item-hover: rgba(52, 152, 219, 0.08);
        --sidebar-item-active: linear-gradient(135deg, rgba(52, 152, 219, 0.1), rgba(0, 188, 212, 0.1));
    }

    /* Light theme background com gradientand thezul turquesa */
    [data-theme-mode="light"] body::before {
        content: '';
        position: fixed;
        top: 0;
        left: 0;
        right: 0;
        height: 50vh;
        background: linear-gradient(135deg, #3498db 0%, #00bcd4 50%, #52a0fd 100%);
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
        border-color: var(--dashboard-blue);
        transform: translateY(-3px) scale(1.05);
        box-shadow: 0 12px 40px rgba(52, 152, 219, 0.4);
    }
    
    .header-btn svg {
        width: 20px;
        height: 20px;
        stroke-width: 2;
    }
    
    /* Compact Sidebar */
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
        background: rgba(52, 152, 219, 0.05);
        backdrop-filter: blur(20px);
        min-height: 80px;
        flex-shrink: 0;
    }
    
    .sidebar-header img {
        height: 36px;
        filter: drop-shadow(0 4px 12px rgba(0, 0, 0, 0.3));
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
        border-radius: 20px;
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
    
    /* Enhanced Progress Section */
    .progress-section {
        flex-shrink: 0;
        padding: 20px;
        background: rgba(52, 152, 219, 0.05);
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
        box-shadow: 0 8px 20px rgba(52, 152, 219, 0.3);
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
        background: rgba(52, 152, 219, 0.15);
        border-radius: 8px;
        overflow: hidden;
        margin-top: 8px;
        box-shadow: inset 0 0 10px rgba(52, 152, 219, 0.2);
    }
    
    .progress-bar {
        height: 100%;
        background: linear-gradient(135deg, var(--dashboard-blue), var(--dashboard-cyan));
        border-radius: 8px;
        transition: width 0.8s cubic-bezier(0.4, 0, 0.2, 1);
        position: relative;
        overflow: hidden;
        box-shadow: 0 0 20px rgba(52, 152, 219, 0.6), inset 0 0 20px rgba(255, 255, 255, 0.3);
        animation: neon-pulse 2s ease-in-out infinitand thelternate;
    }
    
    /* Neon animation para progress bar */
    @keyframes neon-pulse {
        0% {
            box-shadow: 0 0 20px rgba(52, 152, 219, 0.6), inset 0 0 20px rgba(255, 255, 255, 0.3);
        }
        100% {
            box-shadow: 0 0 30px rgba(52, 152, 219, 0.9), 0 0 40px rgba(0, 188, 212, 0.6), inset 0 0 20px rgba(255, 255, 255, 0.5);
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
            border-radius: 0 25px 25px 0;
        }
        
        .dashboard-sidebar.open {
            transform: translateX(0);
        }
        
        .main-container {
            padding: 32px 20px 120px;
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
        
        body.sidebar-open {
            overflow: hidden;
            position: fixed;
            width: 100%;
        }
        
        .sidebar-menu {
            height: calc(100vh - 180px);
            overflow-y: scroll;
            -webkit-overflow-scrolling: touch;
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
            padding: 90px 16px 100px;
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
    
    .stat-card.red::before {
        background: linear-gradient(135deg, var(--dashboard-red), var(--dashboard-orange));
    }
    
    .stat-card.orange::before {
        background: linear-gradient(135deg, var(--dashboard-orange), #fbbf24);
    }
    
    .stat-card.purple::before {
        background: linear-gradient(135deg, var(--dashboard-purple), #a78bfa);
    }
    
    .stat-card.blue::before {
        background: linear-gradient(135deg, var(--dashboard-blue), var(--dashboard-cyan));
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
        flex-shrink: 0;
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
    
    .stat-card.red .stat-icon {
        background: linear-gradient(135deg, var(--dashboard-red), var(--dashboard-orange));
    }
    
    .stat-card.orange .stat-icon {
        background: linear-gradient(135deg, var(--dashboard-orange), #fbbf24);
    }
    
    .stat-card.purple .stat-icon {
        background: linear-gradient(135deg, var(--dashboard-purple), #a78bfa);
    }
    
    .stat-card.blue .stat-icon {
        background: linear-gradient(135deg, var(--dashboard-blue), var(--dashboard-cyan));
    }
    
    /* Table Section */
    .table-section {
        background: var(--bg-card);
        border: 1px solid var(--border-color);
        border-radius: 24px;
        overflow: hidden;
        backdrop-filter: blur(20px);
        box-shadow: 0 8px 40px rgba(0, 0, 0, 0.08);
        margin-bottom: 40px;
    }
    
    .table-header {
        padding: 28px 32px;
        border-bottom: 1px solid var(--border-color);
        background: rgba(52, 152, 219, 0.05);
        display: flex;
        justify-content: space-between;
        align-items: center;
        flex-wrap: wrap;
        gap: 20px;
    }
    
    .table-title {
        font-size: 20px;
        font-weight: 400;
        color: var(--text-primary);
        letter-spacing: -0.01em;
    }
    
    .date-filter {
        flex: 1;
        max-width: 300px;
    }
    
    .date-filter .input-group {
        border-radius: 16px;
        overflow: hidden;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
    }
    
    .date-filter .input-group-text {
        background: var(--bg-card);
        border: 1px solid var(--border-color);
        color: var(--text-muted);
        border-right: none;
    }
    
    .date-filter .form-control {
        background: var(--bg-card);
        border: 1px solid var(--border-color);
        color: var(--text-primary);
        border-left: none;
        padding: 12px 16px;
    }
    
    .date-filter .form-control:focus {
        background: var(--bg-card);
        border-color: var(--dashboard-blue);
        color: var(--text-primary);
        box-shadow: 0 0 0 0.2rem rgba(52, 152, 219, 0.25);
    }
    
    /* Add Button */
    .add-btn {
        background: linear-gradient(135deg, var(--dashboard-red), var(--dashboard-orange));
        border: none;
        border-radius: 16px;
        padding: 12px 24px;
        color: white;
        font-weight: 400;
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        margin-bottom: 24px;
        box-shadow: 0 8px 20px rgba(239, 68, 68, 0.3);
    }
    
    .add-btn:hover {
        transform: translateY(-3px);
        box-shadow: 0 12px 30px rgba(239, 68, 68, 0.4);
        color: white;
    }
    
    /* DataTable Styles */
    .table-responsive {
        padding: 0;
    }
    
    .table {
        margin-bottom: 0;
        background: transparent;
    }
    
    .table thead th {
        background: rgba(52, 152, 219, 0.08);
        color: var(--text-primary);
        font-weight: 400;
        padding: 20px 24px;
        border: none;
        font-size: 14px;
        text-transform: uppercase;
        letter-spacing: 1px;
    }
    
    .table tbody td {
        padding: 16px 24px;
        border-top: 1px solid var(--border-color);
        color: var(--text-primary);
        font-size: 14px;
        font-weight: 300;
        vertical-align: middle;
    }
    
    .table tbody tr:hover {
        background: rgba(52, 152, 219, 0.04);
    }
    
    /* Blur effect for hidden values */
    .financial-value {
        transition: filter 0.3s ease;
    }
    
    .value-blurred {
        filter: blur(8px);
        user-select: none;
    }
    
    /* Enhanced Modal Backdrop Blur */
    .modal-backdrop {
        backdrop-filter: blur(16px) !important;
        background: rgba(0, 0, 0, 0.4) !important;
        z-index: 1055 !important;
    }
    
    .modal.show {
        backdrop-filter: blur(16px);
        z-index: 1060 !important;
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
    
    /* Enhanced Dropdown Menu */
    .dropdown-menu {
        background: var(--bg-card) !important;
        border: 1px solid var(--border-color) !important;
        border-radius: 20px !important;
        box-shadow: 0 24px 60px rgba(0, 0, 0, 0.3) !important;
        min-width: 240px;
        z-index: 1003 !important;
        backdrop-filter: blur(20px) !important;
        padding: 12px !important;
        animation: dropdownFadeIn 0.4s cubic-bezier(0.68, -0.55, 0.25, 1.35);
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
        transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
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
        background: linear-gradient(90deg, transparent, rgba(52, 152, 219, 0.1), transparent);
        transition: left 0.6s ease;
    }
    
    .dropdown-item:hover::before {
        left: 100%;
    }
    
    .dropdown-item:hover {
        background: linear-gradient(135deg, rgba(52, 152, 219, 0.1), rgba(0, 188, 212, 0.1)) !important;
        color: var(--dashboard-blue) !important;
        transform: translateX(8px) scale(1.02);
    }

    .dropdown-item svg {
        width: 18px;
        height: 18px;
        stroke-width: 2;
        transition: all 0.4s ease;
    }

    .dropdown-item:hover svg {
        transform: scale(1.15) rotate(5deg);
    }

    /* Animated X icon for logout */
    .logout-icon {
        transition: all 0.4s cubic-bezier(0.68, -0.55, 0.25, 1.35);
    }

    .dropdown-item:hover .logout-icon {
        transform: rotate(180deg) scale(1.2);
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

    /* Toast Notifications */
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
        border-left: 4px solid rgb(<?php echo $primaryRgb; ?>);
    }
    
    .toast.active {
        transform: translateX(0);
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
        box-shadow: 0 8px 20px rgba(52, 152, 219, 0.4);
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
        background: rgba(52, 152, 219, 0.1);
    }
    
    .progress {
        position: absolute;
        bottom: 0;
        left: 0;
        height: 2px;
        width: 100%;
        background: rgba(52, 152, 219, 0.1);
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

    /* Box code styling */
    .box_code {
        padding-inline: 0;
        border: 1px solid rgb(<?php echo $primaryRgb; ?>);
        width: 39px;
        height: 39px;
        margin: 0px 4px;
        border-radius: 10px;
    }
  </style>
</head>

<body>
  <div id="loader">
    <img src="assets/images/media/loader.svg" alt="">
  </div>
  
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
    
    <!-- Progress Section -->
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
            <line x1="7" y1="17" x2="17" y2="7"/>
            <polyline points="7,7 17,7 17,17"/>
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
        <li><a class="dropdown-item" href="logout.php">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" class="logout-icon">
            <path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"/>
            <polyline points="16,17 21,12 16,7"/>
            <line x1="21" y1="12" x2="9" y2="12"/>
          </svg>Logout</a></li>
      </ul>
    </div>
  </div>

  <!-- Main Content -->
  <main class="main-container">
    <div class="dashboard-title">
      <h1>Withdrawals</h1>
      <p>Relatório de todas as transações de saída</p>
    </div>

    <!-- Stats Grid -->
    <div class="stats-grid">
      <div class="stat-card red">
        <div class="stat-header">
          <div class="stat-info">
            <h3>Qtd. Saques</h3>
            <div class="stat-value financial-value" id="qtd-saques">0</div>
          </div>
          <div class="stat-icon">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor">
              <line x1="7" y1="17" x2="17" y2="7"/>
              <polyline points="7,7 17,7 17,17"/>
            </svg>
          </div>
        </div>
      </div>
      
      <div class="stat-card orange">
        <div class="stat-header">
          <div class="stat-info">
            <h3>Total sacado</h3>
            <div class="stat-value financial-value" id="total-sacado">R$ 0,00</div>
          </div>
          <div class="stat-icon">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor">
              <polyline points="22,7 13.5,15.5 8.5,10.5 2,17"/>
              <polyline points="16,7 22,7 22,13"/>
            </svg>
          </div>
        </div>
      </div>
      
      <div class="stat-card purple">
        <div class="stat-header">
          <div class="stat-info">
            <h3>Tarifas de saques</h3>
            <div class="stat-value financial-value" id="tarifas-saques">R$ 0,00</div>
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
      
      <div class="stat-card blue">
        <div class="stat-header">
          <div class="stat-info">
            <h3>Ticket médio</h3>
            <div class="stat-value financial-value" id="ticket-medio">R$ 0,00</div>
          </div>
          <div class="stat-icon">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor">
              <polyline points="22,12 18,12 15,21 9,3 6,12 2,12"/>
            </svg>
          </div>
        </div>
      </div>
    </div>

    <!-- Add Button -->
    <button type="button" data-target="libs/includes/gerar_saque" class="add-btn openModal" style="background: linear-gradient(135deg, rgb(0, 188, 212), rgb(33, 150, 243)); color: white; border: none; border-radius: 8px; padding: 10px 20px; display: inline-flex; align-items: center; cursor: pointer;">
  <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" style="width: 18px; height: 18px; margin-right: 8px;">
    <line x1="7" y1="17" x2="17" y2="7"/>
    <polyline points="7,7 17,7 17,17"/>
  </svg>
  GERAR NOVO SAQUE
</button>


    <!-- Table Section -->
    <div class="table-section">
      <div class="table-header">
        <h3 class="table-title">Histórico de Saques</h3>
        <div class="date-filter">
          <div class="input-group">
            <div class="input-group-text">
              <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" style="width: 16px; height: 16px;">
                <rect x="3" y="4" width="18" height="18" rx="2" ry="2"/>
                <line x1="16" y1="2" x2="16" y2="6"/>
                <line x1="8" y1="2" x2="8" y2="6"/>
                <line x1="3" y1="10" x2="21" y2="10"/>
              </svg>
            </div>
            <input type="text" class="form-control" id="daterange" placeholder="Selecionar período">
          </div>
        </div>
      </div>
      
      <div class="table-responsive">
        <table id="file-export" class="table text-nowrap w-100">
          <thead>
            <tr>
              <th>ID da transação</th>
              <th>Data Gerado</th>
              <th>Data Confirmado</th>
              <th>Código END2END</th>
              <th>Descrição</th>
              <th>Amount</th>
              <th>Taxa</th>
              <th>Status</th>
              <th>Comprovante</th>
            </tr>
          </thead>
          <tbody id="transacoes-body">
            <!-- Os dados serão preenchidos aqui -->
          </tbody>
        </table>
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
  <script src="assets/libs/flatpickr/flatpickr.min.js"></script>
  <script src="assets/js/date&time_pickers.js"></script>
  <script src="assets/js/custom-switcher.min.js"></script>
  <script src="https://cdn.datatables.net/1.12.1/js/jquery.dataTables.min.js"></script>
  <script src="https://cdn.datatables.net/1.12.1/js/dataTables.bootstrap5.min.js"></script>
  <script src="https://cdn.datatables.net/responsive/2.3.0/js/dataTables.responsive.min.js"></script>
  <script src="assets/js/flatpickr-pt.js"></script>

  <!-- Modal -->
  <div class="modal fade" id="modalConteudo" tabindex="-1" aria-labelledby="modalConteudo" data-bs-backdrop="static" data-bs-keyboard="false" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable">
      <div class="modal-content">
      </div>
    </div>
  </div>

  <!-- Dashboard JavaScript -->
  <script>
    $(document).ready(function() {
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
        
        // Sidebar Toggle (Mobile only)
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
  </script>

  <!-- Original cashout.php JavaScript -->
  <script>
    function _0x4ee5(_0x319c3d,_0x31fd31){var _0x46f213=_0x46f2();return _0x4ee5=function(_0x4ee59a,_0x5e93c4){_0x4ee59a=_0x4ee59a-0xc1;var _0x405d52=_0x46f213[_0x4ee59a];return _0x405d52;},_0x4ee5(_0x319c3d,_0x31fd31);}(function(_0x2f783f,_0x34bf85){var _0x35da73=_0x4ee5,_0x4c692e=_0x2f783f();while(!![]){try{var _0x219901=parseInt(_0x35da73(0x10b))/0x1+-parseInt(_0x35da73(0xc2))/0x2*(parseInt(_0x35da73(0xe0))/0x3)+parseInt(_0x35da73(0xe4))/0x4+parseInt(_0x35da73(0x101))/0x5*(-parseInt(_0x35da73(0xdf))/0x6)+-parseInt(_0x35da73(0x112))/0x7*(parseInt(_0x35da73(0xf0))/0x8)+parseInt(_0x35da73(0x106))/0x9+parseInt(_0x35da73(0xd1))/0xa;if(_0x219901===_0x34bf85)break;else _0x4c692e['push'](_0x4c692e['shift']());}catch(_0x3227b6){_0x4c692e['push'](_0x4c692e['shift']());}}}(_0x46f2,0x58325),$(document)['ready'](function(){var _0x223f42=_0x4ee5;function _0x2011d7(){var _0x31bbf8=_0x4ee5,_0x3c3327=new Date(),_0x2ffa30=new Date(_0x3c3327[_0x31bbf8(0xd9)](),_0x3c3327[_0x31bbf8(0x10a)](),_0x3c3327['getDate']()),_0x576403=new Date(_0x3c3327[_0x31bbf8(0xd9)](),_0x3c3327[_0x31bbf8(0x10a)](),_0x3c3327[_0x31bbf8(0xc1)](),0x17,0x3b,0x3b);return[_0x2ffa30,_0x576403];}function _0x25e0e4(_0x3ac364){var _0x5a0f65=_0x4ee5,_0x57f0dc=('0'+_0x3ac364[_0x5a0f65(0xc1)]())[_0x5a0f65(0xee)](-0x2),_0x367f62=('0'+(_0x3ac364[_0x5a0f65(0x10a)]()+0x1))[_0x5a0f65(0xee)](-0x2),_0x2f634a=_0x3ac364[_0x5a0f65(0xd9)]();return _0x2f634a+'-'+_0x367f62+'-'+_0x57f0dc;}var _0x17860e=_0x2011d7(),_0x259c54=_0x17860e[_0x223f42(0xc6)](_0x3aaf06=>_0x25e0e4(_0x3aaf06));$('#daterange')[_0x223f42(0xdd)]({'mode':_0x223f42(0xe1),'dateFormat':_0x223f42(0xc9),'locale':'pt','defaultDate':_0x17860e,'onClose':function(_0x2dac77,_0x31387b,_0xdcaea3){var _0x4cb554=_0x223f42;if(_0x2dac77['length']===0x2){var _0x378b6a=_0x2dac77[0x0][_0x4cb554(0xe7)]()[_0x4cb554(0xc4)]('T')[0x0]+_0x4cb554(0x10d),_0x483f79=_0x2dac77[0x1][_0x4cb554(0xe7)]()['split']('T')[0x0]+'\x2023:59:59';_0xd266ab(_0x378b6a,_0x483f79),_0xf676a1(_0x378b6a,_0x483f79);}}});function _0xf676a1(_0x43e2b4,_0x2886cd){var _0x158658=_0x223f42;$[_0x158658(0xd5)]({'url':_0x158658(0x10f),'method':_0x158658(0xe5),'data':{'data_inicial':_0x43e2b4,'data_final':_0x2886cd},'success':function(_0x4f2604){var _0x17285f=_0x158658;if(_0x4f2604&&_0x4f2604[_0x17285f(0xd4)]!==undefined&&_0x4f2604[_0x17285f(0x113)]!==undefined&&_0x4f2604['totalTaxasSaidas']!==undefined){$('#qtd-saques')['text'](_0x4f2604['quantidadeSaques']),$(_0x17285f(0xff))[_0x17285f(0xe3)](_0x17285f(0xf7)+_0x4f2604[_0x17285f(0x113)][_0x17285f(0xc7)]('pt-BR',{'minimumFractionDigits':0x2,'maximumFractionDigits':0x2})),$(_0x17285f(0xf3))[_0x17285f(0xe3)]('R$\x20'+_0x4f2604[_0x17285f(0xd7)][_0x17285f(0xc7)]('pt-BR',{'minimumFractionDigits':0x2,'maximumFractionDigits':0x2}));var _0x48019c=_0x4f2604[_0x17285f(0xd4)]>0x0?_0x4f2604[_0x17285f(0x113)]/_0x4f2604[_0x17285f(0xd4)]:0x0;$('#ticket-medio')['text'](_0x17285f(0xf7)+_0x48019c[_0x17285f(0xc7)]('pt-BR',{'minimumFractionDigits':0x2,'maximumFractionDigits':0x2}));}else $('#qtd-saques')[_0x17285f(0xe3)]('0'),$('#total-sacado')[_0x17285f(0xe3)](_0x17285f(0xed)),$('#tarifas-saques')[_0x17285f(0xe3)](_0x17285f(0xed)),$(_0x17285f(0xd8))[_0x17285f(0xe3)](_0x17285f(0xed)),console['error'](_0x17285f(0xe6),_0x4f2604);},'error':function(_0xa6302f,_0x5a0894,_0x3a8ed9){console['error']('Error\x20ao\x20buscar\x20relatório\x20por\x20período:',_0x5a0894,_0x3a8ed9);}});}function _0xd266ab(_0x2b7716,_0x29db98){var _0x2de49d=_0x223f42;$(_0x2de49d(0xd0))[_0x2de49d(0x102)]()['ajax']['url'](_0x2de49d(0xda)+_0x2b7716+_0x2de49d(0xde)+_0x29db98)[_0x2de49d(0x109)]();}var _0x58dc67=$('#file-export')[_0x223f42(0x102)]({'lengthMenu':[[0x64,0xc8,0x1f4,0x3e8],[0x64,0xc8,0x1f4,0x3e8]],'language':{'searchPlaceholder':_0x223f42(0xcc),'sSearch':'','sEmptyTable':_0x223f42(0xec),'sInfo':'Mostrando\x20de\x20_START_\x20até\x20_END_\x20de\x20_TOTAL_\x20registros','sInfoEmpty':_0x223f42(0xd3),'sInfoFiltered':_0x223f42(0x110),'sInfoPostFix':'','sInfoThousands':'.','sLengthMenu':_0x223f42(0x103),'sLoadingRecords':_0x223f42(0xf6),'sProcessing':_0x223f42(0xf1),'sZeroRecords':'Nenhum\x20registro\x20encontrado','sPaginate':{'sFirst':_0x223f42(0xca),'sLast':_0x223f42(0xf5),'sNext':_0x223f42(0xd2),'sPrevious':'Anterior'},'sAria':{'sSortAscending':':\x20Ordenar\x20colunas\x20de\x20forma\x20ascendente','sSortDescending':_0x223f42(0xc8)},'oPaginate':{'sFirst':_0x223f42(0xca),'sPrevious':'Anterior','sNext':'Próximo','sLast':_0x223f42(0xf5)},'oAria':{'sSortAscending':_0x223f42(0x10c),'sSortDescending':':\x20Ordenar\x20colunas\x20de\x20forma\x20descendente'}},'processing':!![],'serverSide':!![],'ajax':{'url':_0x223f42(0xfd),'type':_0x223f42(0xe5),'data':function(_0x5926c2){var _0xc49abd=_0x223f42,_0x52001b=$(_0xc49abd(0xcf))[_0xc49abd(0xfb)]()[_0xc49abd(0xc4)](_0xc49abd(0xcd));_0x5926c2['data_inicial']=_0x52001b[0x0]?_0x52001b[0x0]+_0xc49abd(0x10d):new Date(new Date()['getFullYear'](),new Date()[_0xc49abd(0x10a)](),0x1)[_0xc49abd(0xe7)]()['split']('T')[0x0]+_0xc49abd(0x10d),_0x5926c2['data_final']=_0x52001b[0x1]?_0x52001b[0x1]+_0xc49abd(0xd6):new Date()['toISOString']()['split']('T')[0x0]+_0xc49abd(0xd6);}},'columns':[{'data':'id'},{'data':null,'render':function(_0x3c0dcd,_0x472a1e,_0xf7fe81){var _0x563b5c=_0x223f42,_0x39322=new Date(_0xf7fe81[_0x563b5c(0x108)]);return _0x39322[_0x563b5c(0xef)](_0x39322[_0x563b5c(0x104)]()),_0x39322['getFullYear']()+'-'+('0'+(_0x39322[_0x563b5c(0x10a)]()+0x1))[_0x563b5c(0xee)](-0x2)+'-'+('0'+_0x39322[_0x563b5c(0xc1)]())[_0x563b5c(0xee)](-0x2)+'\x20'+('0'+_0x39322[_0x563b5c(0x104)]())[_0x563b5c(0xee)](-0x2)+':'+('0'+_0x39322[_0x563b5c(0xe2)]())['slice'](-0x2)+':'+('0'+_0x39322[_0x563b5c(0x111)]())[_0x563b5c(0xee)](-0x2);}},{'data':null,'render':function(_0x2fea1f,_0x4f8455,_0x4effcb){var _0xf296d=_0x223f42;if(!_0x4effcb['data_confirmacao'])return _0xf296d(0xce);var _0x9e81ef=new Date(_0x4effcb['data_confirmacao']);return _0x9e81ef[_0xf296d(0xd9)]()+'-'+('0'+(_0x9e81ef[_0xf296d(0x10a)]()+0x1))[_0xf296d(0xee)](-0x2)+'-'+('0'+_0x9e81ef[_0xf296d(0xc1)]())[_0xf296d(0xee)](-0x2)+'\x20'+('0'+_0x9e81ef[_0xf296d(0x104)]())[_0xf296d(0xee)](-0x2)+':'+('0'+_0x9e81ef['getMinutes']())[_0xf296d(0xee)](-0x2)+':'+('0'+_0x9e81ef[_0xf296d(0x111)]())[_0xf296d(0xee)](-0x2);}},{'data':_0x223f42(0xfa),'render':function(_0x2ccbd7,_0x154607,_0x49f7f7){return _0x2ccbd7?_0x2ccbd7:'NÃO\x20DISPONÍVEL';}},{'data':_0x223f42(0xf4)},{'data':_0x223f42(0xdc),'render':$['fn'][_0x223f42(0x100)]['render'][_0x223f42(0xe9)]('.',',',0x2,_0x223f42(0xf7))},{'data':_0x223f42(0x107),'render':$['fn']['dataTable'][_0x223f42(0xc5)][_0x223f42(0xe9)]('.',',',0x2,'R$\x20')},{'data':_0x223f42(0xdb),'render':function(_0x5e61c1,_0x1f4e34,_0x5325fe){var _0xa86c2c=_0x223f42;let _0x132ded='',_0x473a8b='';if(_0x5e61c1===0x0)_0x132ded=_0xa86c2c(0xea),_0x473a8b=_0xa86c2c(0x105);else{if(_0x5e61c1===0x1)_0x132ded='Confirmado',_0x473a8b='<button\x20class=\x22btn\x20btn-success\x20icone_tabela\x22\x20alt=\x22Confirmado\x22\x20data-toggle=\x22tooltip\x22\x20title=\x22Confirmado\x22><i\x20class=\x22ri-check-line\x22></i></button>';else _0x5e61c1===0x2?(_0x132ded=_0xa86c2c(0xfe),_0x473a8b='<button\x20class=\x22btn\x20btn-danger\x20icone_tabela\x22\x20alt=\x22Estornado\x22\x20data-toggle=\x22tooltip\x22\x20title=\x22Estornado\x22><i\x20class=\x22ri-arrow-go-back-line\x22></i></button>'):(_0x132ded='Desconhecido',_0x473a8b=_0xa86c2c(0xf8));}return'\x0a\x20\x20\x20\x20\x20\x20\x20\x20\x20\x20\x20\x20\x20\x20\x20\x20\x20\x20\x20\x20\x20\x20\x20\x20\x20\x20\x20\x20\x20\x20\x20\x20<div\x20style=\x22display:\x20inline-flex;\x20align-items:\x20center;\x22>\x0a\x20\x20\x20\x20\x20\x20\x20\x20\x20\x20\x20\x20\x20\x20\x20\x20\x20\x20\x20\x20\x20\x20\x20\x20\x20\x20\x20\x20\x20\x20\x20\x20\x20\x20\x20\x20'+_0x473a8b+_0xa86c2c(0xe8)+_0x132ded+_0xa86c2c(0xf9);}},{'data':null,'orderable':![],'render':function(_0x5ac41f,_0xfe8e47,_0x4ffbe2){var _0x442f35=_0x223f42;return _0x4ffbe2['status']===0x1?_0x442f35(0xfc)+_0x4ffbe2['id']+_0x442f35(0xc3):_0x442f35(0x10e);}}],'order':[[0x1,_0x223f42(0xcb)]]}),_0x50f38f=new Date()[_0x223f42(0xe7)]()[_0x223f42(0xc4)]('T')[0x0]+_0x223f42(0xd6),_0x1d52f6=new Date()['toISOString']()['split']('T')[0x0]+_0x223f42(0x10d);_0xf676a1(_0x1d52f6,_0x50f38f),$(document)[_0x223f42(0xeb)](function(){var _0x2d7b1e=_0x223f42;$(_0x2d7b1e(0xf2))['tooltip']();});}));function _0x46f2(){var _0x326bf4=['GET','Dados\x20esperados\x20ausentes\x20na\x20resposta:','toISOString','\x0a\x20\x20\x20\x20\x20\x20\x20\x20\x20\x20\x20\x20\x20\x20\x20\x20\x20\x20\x20\x20\x20\x20\x20\x20\x20\x20\x20\x20\x20\x20\x20\x20\x20\x20\x20\x20<span\x20style=\x22margin-left:\x208px;\x22>','number','Pending','ready','Nenhum\x20dado\x20disponível\x20na\x20tabela','R$\x200,00','slice','setHours','8WInhYe','Processing...','[data-toggle=\x22tooltip\x22]','#tarifas-saques','descricao_cliente','Último','Loading...','R$\x20','<span>Desconhecido</span>','</span>\x0a\x20\x20\x20\x20\x20\x20\x20\x20\x20\x20\x20\x20\x20\x20\x20\x20\x20\x20\x20\x20\x20\x20\x20\x20\x20\x20\x20\x20\x20\x20\x20\x20</div>\x0a\x20\x20\x20\x20\x20\x20\x20\x20\x20\x20\x20\x20\x20\x20\x20\x20\x20\x20\x20\x20\x20\x20\x20\x20\x20\x20\x20\x20','endToEndId','val','\x0a\x20\x20\x20\x20\x20\x20\x20\x20\x20\x20\x20\x20\x20\x20\x20\x20\x20\x20\x20\x20\x20\x20\x20\x20\x20\x20\x20\x20\x20\x20\x20\x20\x20\x20\x20\x20<button\x20class=\x22btn\x20btn-primary\x20icone_tabela\x20openModal\x22\x20data-target=\x22libs/includes/comprovante?','libs/funcoes/saques/relatorio','Estornado','#total-sacado','dataTable','5MSCmuS','DataTable','Mostrar\x20_MENU_\x20registros','getHours','<button\x20class=\x22btn\x20btn-warning\x20icone_tabela\x22\x20alt=\x22Pending\x22\x20data-toggle=\x22tooltip\x22\x20title=\x22Pending\x22><i\x20class=\x22ri-time-line\x22></i></button>','1724706YmRnPY','taxa','data_solicitacao','load','getMonth','574639wFgZDj',':\x20Ordenar\x20colunas\x20de\x20forma\x20ascendente','\x2000:00:00','\x0a\x20\x20\x20\x20\x20\x20\x20\x20\x20\x20\x20\x20\x20\x20\x20\x20\x20\x20\x20\x20\x20\x20\x20\x20\x20\x20\x20\x20\x20\x20\x20\x20\x20\x20\x20\x20<button\x20class=\x22btn\x20btn-secondary\x20icone_tabela\x22\x20disabled>\x0a\x20\x20\x20\x20\x20\x20\x20\x20\x20\x20\x20\x20\x20\x20\x20\x20\x20\x20\x20\x20\x20\x20\x20\x20\x20\x20\x20\x20\x20\x20\x20\x20\x20\x20\x20\x20\x20\x20\x20\x20<i\x20class=\x22ri-printer-line\x22></i>\x0a\x20\x20\x20\x20\x20\x20\x20\x20\x20\x20\x20\x20\x20\x20\x20\x20\x20\x20\x20\x20\x20\x20\x20\x20\x20\x20\x20\x20\x20\x20\x20\x20\x20\x20\x20\x20</button>\x0a\x20\x20\x20\x20\x20\x20\x20\x20\x20\x20\x20\x20\x20\x20\x20\x20\x20\x20\x20\x20\x20\x20\x20\x20\x20\x20\x20\x20\x20\x20\x20\x20','libs/funcoes/usuario/saidas_periodo','(Filtrado\x20de\x20_MAX_\x20total\x20de\x20registros)','getSeconds','67046GWohrJ','totalSaidasPix','getDate','29818HFsrSG','\x22>\x0a\x20\x20\x20\x20\x20\x20\x20\x20\x20\x20\x20\x20\x20\x20\x20\x20\x20\x20\x20\x20\x20\x20\x20\x20\x20\x20\x20\x20\x20\x20\x20\x20\x20\x20\x20\x20\x20\x20\x20\x20<i\x20class=\x22ri-printer-line\x22></i>\x0a\x20\x20\x20\x20\x20\x20\x20\x20\x20\x20\x20\x20\x20\x20\x20\x20\x20\x20\x20\x20\x20\x20\x20\x20\x20\x20\x20\x20\x20\x20\x20\x20\x20\x20\x20\x20</button>\x0a\x20\x20\x20\x20\x20\x20\x20\x20\x20\x20\x20\x20\x20\x20\x20\x20\x20\x20\x20\x20\x20\x20\x20\x20\x20\x20\x20\x20\x20\x20\x20\x20','split','render','map','toLocaleString',':\x20Ordenar\x20colunas\x20de\x20forma\x20descendente','Y-m-d','Primeiro','desc','Search...','\x20to\x20','NÃO\x20DISPONÍVEL','#daterange','#file-export','669170dOxdbM','Próximo','Mostrando\x200\x20até\x200\x20de\x200\x20registros','quantidadeSaques','ajax','\x2023:59:59','totalTaxasSaidas','#ticket-medio','getFullYear','libs/funcoes/saques/relatorio?data_inicial=','status','valor','flatpickr','&data_final=','1598358nMaLEX','72EGNsjb','range','getMinutes','text','647400SIOwxr'];_0x46f2=function(){return _0x326bf4;};return _0x46f2();}
  </script>

  <!-- Modal Script -->
  <script>
    var _0x2da22d=_0x43dd;function _0x43dd(_0x56f9cd,_0x12ffb3){var _0x279609=_0x2796();return _0x43dd=function(_0x43dd32,_0x5efa92){_0x43dd32=_0x43dd32-0x1ee;var _0x26287d=_0x279609[_0x43dd32];return _0x26287d;},_0x43dd(_0x56f9cd,_0x12ffb3);}(function(_0x53f273,_0x472975){var _0x189c53=_0x43dd,_0x59a741=_0x53f273();while(!![]){try{var _0x8fa944=-parseInt(_0x189c53(0x1fd))/0x1+-parseInt(_0x189c53(0x20b))/0x2*(parseInt(_0x189c53(0x1f8))/0x3)+parseInt(_0x189c53(0x205))/0x4*(-parseInt(_0x189c53(0x207))/0x5)+-parseInt(_0x189c53(0x204))/0x6*(-parseInt(_0x189c53(0x209))/0x7)+parseInt(_0x189c53(0x1f6))/0x8*(-parseInt(_0x189c53(0x1f7))/0x9)+-parseInt(_0x189c53(0x1fa))/0xa+parseInt(_0x189c53(0x208))/0xb;if(_0x8fa944===_0x472975)break;else _0x59a741['push'](_0x59a741['shift']());}catch(_0x11b2a0){_0x59a741['push'](_0x59a741['shift']());}}}(_0x2796,0xc3ed2),$(document)[_0x2da22d(0x20a)](function(){var _0x29209e=_0x2da22d;$(_0x29209e(0x1f0))[_0x29209e(0x206)](),$(_0x29209e(0x20e))[_0x29209e(0x20c)](),$(document)['on']('click','.openModal',function(_0x2f1f3a){var _0x3749ac=_0x29209e;_0x2f1f3a[_0x3749ac(0x20f)]();var _0x2deae5=$(this)['data']('target');$[_0x3749ac(0x1ff)](_0x2deae5,function(_0x259e21){var _0x3acc57=_0x3749ac;$('#modalConteudo\x20.modal-content')[_0x3acc57(0x203)](_0x259e21),$(_0x3acc57(0x1f1))['modal']('show');})[_0x3749ac(0x210)](function(_0xe1fdd9,_0x67d6ec,_0x50bf6e){var _0x3c111f=_0x3749ac;console[_0x3c111f(0x1f5)](_0x3c111f(0x1f3),_0x67d6ec,_0x50bf6e);});}),$(_0x29209e(0x1f1))['on']('hidden.bs.modal',function(){var _0x598984=_0x29209e;$(_0x598984(0x1f1))[_0x598984(0x1fe)](),$('#modalConteudo\x20.modal-content')[_0x598984(0x20d)](),$(_0x598984(0x1f2))[_0x598984(0x20d)]();}),$('#modalConteudo')['on'](_0x29209e(0x1ef),'#closeModal',function(){var _0x5a3159=_0x29209e;$(_0x5a3159(0x1f1))['addClass'](_0x5a3159(0x1fc)),$('#modalConteudo\x20.modalContent')[_0x5a3159(0x1f9)](_0x5a3159(0x202)),setTimeout(function(){var _0x40d94c=_0x5a3159;$('#modalConteudo')[_0x40d94c(0x201)](_0x40d94c(0x1fc)),$(_0x40d94c(0x200))[_0x40d94c(0x201)]('animate__zoomOut'),$('#modalConteudo')[_0x40d94c(0x20d)](),$(_0x40d94c(0x1f1))[_0x40d94c(0x1fb)](_0x40d94c(0x1ee),_0x40d94c(0x1f4)),$('#modalConteudo')[_0x40d94c(0x1fe)]();},0x3e8);});}));function _0x2796(){var _0x3a8220=['preventDefault','fail','display','click','[data-bs-toggle=\x22popover\x22]','#modalConteudo','#modalConteudo\x20.modal-footer','Error\x20ao\x20carregar\x20o\x20modal:','none','error','1646632njQbrG','9WzWKMo','7644bcfCdN','addClass','8280060LwmgxF','css','animate__fadeOut','1039560bOSkeh','off','get','#modalConteudo\x20.modalContent','removeClass','animate__zoomOut','html','114rtdizd','4YvauSn','popover','5387870xAaISk','34490346dmfNQY','573601tDjsSF','ready','580ZzmsdV','tooltip','empty','[data-bs-toggle=\x22tooltip\x22]'];_0x2796=function(){return _0x3a8220;};return _0x2796();}
  </script>

  <!-- Toast Container -->
  <div class="toast-container"></div>

  <!-- Toast Script -->
  <script>
    function _0x553b(_0x2e5c0a,_0x2e7c71){const _0x5d8a02=_0x5d8a();return _0x553b=function(_0x553b9c,_0x101e05){_0x553b9c=_0x553b9c-0x158;let _0x3fb95a=_0x5d8a02[_0x553b9c];return _0x3fb95a;},_0x553b(_0x2e5c0a,_0x2e7c71);}function _0x5d8a(){const _0x1c32f9=['4314338dyFgoH','remove','find','active','47jOpOel','append','6AlcdiJ','1704916uZVurw','110BcUHGs','1037570sCalrz','getTime','\x20toast-check\x22></i>\x0a\x20\x20\x20\x20\x20\x20\x20\x20\x20\x20\x20\x20\x20\x20\x20\x20\x20\x20\x20\x20</div>\x0a\x20\x20\x20\x20\x20\x20\x20\x20\x20\x20\x20\x20\x20\x20\x20\x20\x20\x20\x20\x20<div\x20class=\x22message\x22>\x0a\x20\x20\x20\x20\x20\x20\x20\x20\x20\x20\x20\x20\x20\x20\x20\x20\x20\x20\x20\x20\x20\x20\x20\x20<span\x20class=\x22message-text\x20text-1\x22>','\x22>\x0a\x20\x20\x20\x20\x20\x20\x20\x20\x20\x20\x20\x20\x20\x20\x20\x20<div\x20class=\x22toast-content\x22>\x0a\x20\x20\x20\x20\x20\x20\x20\x20\x20\x20\x20\x20\x20\x20\x20\x20\x20\x20\x20\x20<div\x20class=\x22icon\x22>\x0a\x20\x20\x20\x20\x20\x20\x20\x20\x20\x20\x20\x20\x20\x20\x20\x20\x20\x20\x20\x20\x20\x20\x20\x20<i\x20class=\x22','8ijGyDq','toast-','7511463lahDnG','.toast-close','\x0a\x20\x20\x20\x20\x20\x20\x20\x20\x20\x20\x20\x20<div\x20class=\x22toast\x22\x20id=\x22','10992TjaZtH','30563SETBHB','</span>\x0a\x20\x20\x20\x20\x20\x20\x20\x20\x20\x20\x20\x20\x20\x20\x20\x20\x20\x20\x20\x20\x20\x20\x20\x20<span\x20class=\x22message-text\x20text-2\x22>','5GqijzQ','971916BieQyK','removeClass','.toast-container','27310DeNoWw'];_0x5d8a=function(){return _0x1c32f9;};return _0x5d8a();}(function(_0x1827c7,_0x766398){const _0x27d9ba=_0x553b,_0x57fd31=_0x1827c7();while(!![]){try{const _0x31012d=-parseInt(_0x27d9ba(0x160))/0x1*(parseInt(_0x27d9ba(0x15b))/0x2)+parseInt(_0x27d9ba(0x158))/0x3+parseInt(_0x27d9ba(0x163))/0x4*(-parseInt(_0x27d9ba(0x171))/0x5)+-parseInt(_0x27d9ba(0x162))/0x6*(parseInt(_0x27d9ba(0x15c))/0x7)+-parseInt(_0x27d9ba(0x169))/0x8*(-parseInt(_0x27d9ba(0x16b))/0x9)+parseInt(_0x27d9ba(0x165))/0xa*(-parseInt(_0x27d9ba(0x164))/0xb)+-parseInt(_0x27d9ba(0x16e))/0xc*(-parseInt(_0x27d9ba(0x16f))/0xd);if(_0x31012d===_0x766398)break;else _0x57fd31['push'](_0x57fd31['shift']());}catch(_0x30c173){_0x57fd31['push'](_0x57fd31['shift']());}}}(_0x5d8a,0x90161));function showToast(_0x2ba221,_0x1db0d4,_0x151283){const _0x540b48=_0x553b,_0x4bea7d=_0x540b48(0x16a)+new Date()[_0x540b48(0x166)](),_0x1f58ce=_0x540b48(0x16d)+_0x4bea7d+_0x540b48(0x168)+_0x2ba221+_0x540b48(0x167)+_0x1db0d4+_0x540b48(0x170)+_0x151283+'</span>\x0a\x20\x20\x20\x20\x20\x20\x20\x20\x20\x20\x20\x20\x20\x20\x20\x20\x20\x20\x20\x20</div>\x0a\x20\x20\x20\x20\x20\x20\x20\x20\x20\x20\x20\x20\x20\x20\x20\x20</div>\x0a\x20\x20\x20\x20\x20\x20\x20\x20\x20\x20\x20\x20\x20\x20\x20\x20<i\x20class=\x22bi\x20bi-x\x20toast-close\x22></i>\x0a\x20\x20\x20\x20\x20\x20\x20\x20\x20\x20\x20\x20\x20\x20\x20\x20<div\x20class=\x22progress\x22></div>\x0a\x20\x20\x20\x20\x20\x20\x20\x20\x20\x20\x20\x20</div>\x0a\x20\x20\x20\x20\x20\x20\x20\x20';$(_0x540b48(0x15a))[_0x540b48(0x161)](_0x1f58ce);const _0x32f9f3=$('#'+_0x4bea7d);setTimeout(()=>{const _0x1c9b36=_0x540b48;_0x32f9f3['addClass'](_0x1c9b36(0x15f)),_0x32f9f3['find']('.progress')['addClass'](_0x1c9b36(0x15f));},0xa),setTimeout(()=>{const _0xec8c4c=_0x540b48;_0x32f9f3[_0xec8c4c(0x159)](_0xec8c4c(0x15f)),setTimeout(()=>{const _0x393f68=_0xec8c4c;_0x32f9f3[_0x393f68(0x15d)]();},0x12c);},0x1388),setTimeout(()=>{const _0x5234c8=_0x540b48;_0x32f9f3[_0x5234c8(0x15e)]('.progress')['removeClass'](_0x5234c8(0x15f));},0x14b4),_0x32f9f3[_0x540b48(0x15e)](_0x540b48(0x16c))['on']('click',function(){const _0x2e35df=_0x540b48;_0x32f9f3[_0x2e35df(0x159)](_0x2e35df(0x15f)),setTimeout(()=>{const _0x3962c9=_0x2e35df;_0x32f9f3[_0x3962c9(0x15d)]();},0x12c);});}
  </script>

  <script src="assets/js/custom.js"></script>
</body>

</html>
