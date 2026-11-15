
<?php
session_start();

include('libs/updateUserInfo.php');

if (!isset($_SESSION['user_id'])) {
    header("Location: /login.php");
    exit();
}

function loadEnv2($file = '.env') {
    if (file_exists($file)) {
        $lines = file($file, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);

        foreach ($lines as $line) {
            // Ignora comentários
            if (strpos(trim($line), '#') === 0) {
                continue;
            }

            list($key, $value) = explode('=', $line, 2);
            $key = trim($key);
            $value = trim($value);

            if (!empty($key) && !empty($value)) {
                $_ENV[$key] = $value;
            }
        }
    }
}

// Carregar as variáveis do .env
loadEnv2();

function connectDb2() {
    try {
        // Usando as variáveis carregadas do .env
        $dsn = 'mysql:host=' . $_ENV['DB_HOST'] . ';dbname=' . $_ENV['DB_NAME'] . ';charset=utf8';
        $pdo = new PDO($dsn, $_ENV['DB_USER'], $_ENV['DB_PASS'] ?? '');
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        return $pdo;
    } catch (PDOException $e) {
        die("Error na conexão com o banco de dados: " . $e->getMessage());
    }
}

?>
<!DOCTYPE html>
<html lang="pt-br" dir="ltr" data-nav-layout="vertical" data-theme-mode="light" data-header-styles="light" data-menu-styles="light" data-toggled="close">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0, user-scalable=no">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <title> <?php include 'libs/get_site_name.php'; ?> - Profile </title>
  <meta name="Description" content="Profile do usuário do gateway de pagamento <?php include 'libs/get_site_name.php'; ?>">
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
  
  <!-- Style Css -->
  <?php include 'libs/config_css.php'; ?>
  
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
  
  <!-- Gallery CSS -->
  <link rel="stylesheet" href="assets/libs/glightbox/css/glightbox.min.css">
  
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
    /* Profile Cards */
    .profile-card {
        background: var(--bg-card);
        border: 1px solid var(--border-color);
        border-radius: 24px;
        padding: 28px;
        margin-bottom: 28px;
        transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
        backdrop-filter: blur(20px);
        box-shadow: 0 8px 40px rgba(0, 0, 0, 0.08);
    }
    
    .profile-card:hover {
        transform: translateY(-8px);
        box-shadow: 0 20px 60px rgba(0, 0, 0, 0.15);
    }
    
    .profile-card-title {
        font-size: 20px;
        font-weight: 400;
        color: var(--text-primary);
        margin-bottom: 20px;
        padding-bottom: 16px;
        border-bottom: 1px solid var(--border-color);
        background: linear-gradient(135deg, var(--dashboard-blue), var(--dashboard-cyan));
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
    }
    
    .info-item {
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: 16px 0;
        border-bottom: 1px solid var(--border-color);
        transition: all 0.3s ease;
    }
    
    .info-item:last-child {
        border-bottom: none;
    }
    
    .info-item:hover {
        background: rgba(59, 130, 246, 0.05);
        border-radius: 12px;
        padding-left: 16px;
        padding-right: 16px;
        margin: 0 -16px;
    }
    
    .info-label {
        font-weight: 500;
        color: var(--text-primary);
        font-size: 14px;
    }
    
    .info-value {
        color: var(--text-muted);
        font-size: 14px;
        font-weight: 300;
    }
    
    /* Document Cards */
    .document-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
        gap: 24px;
        margin-top: 20px;
    }
    
    .document-card {
        background: var(--bg-card);
        border: 1px solid var(--border-color);
        border-radius: 20px;
        padding: 24px;
        text-align: center;
        transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
        position: relative;
        overflow: hidden;
    }
    
    .document-card:hover {
        transform: translateY(-8px) scale(1.02);
        box-shadow: 0 20px 60px rgba(0, 0, 0, 0.15);
    }
    
    .document-icon {
        width: 80px;
        height: 80px;
        margin: 0 auto 20px;
        border-radius: 20px;
        display: flex;
        align-items: center;
        justify-content: center;
        background: linear-gradient(135deg, var(--dashboard-blue), var(--dashboard-cyan));
        color: white;
        position: relative;
        overflow: hidden;
        box-shadow: 0 12px 30px rgba(59, 130, 246, 0.3);
    }
    
    .document-icon.approved {
        background: linear-gradient(135deg, var(--dashboard-green), #34d399);
        box-shadow: 0 12px 30px rgba(16, 185, 129, 0.3);
    }
    
    .document-icon.pending {
        background: linear-gradient(135deg, var(--dashboard-orange), #fbbf24);
        box-shadow: 0 12px 30px rgba(245, 158, 11, 0.3);
    }
    
    .document-icon.analysis {
        background: linear-gradient(135deg, var(--dashboard-purple), #a855f7);
        box-shadow: 0 12px 30px rgba(139, 92, 246, 0.3);
    }
    
    .document-icon svg {
        width: 32px;
        height: 32px;
        stroke-width: 2;
    }
    
    .document-icon::before {
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
    
    .document-title {
        font-size: 16px;
        font-weight: 500;
        color: var(--text-primary);
        margin-bottom: 8px;
    }
    
    .document-status {
        display: inline-block;
        padding: 6px 16px;
        border-radius: 20px;
        font-size: 12px;
        font-weight: 500;
        margin-bottom: 16px;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }
    
    .status-approved {
        background: rgba(16, 185, 129, 0.15);
        color: var(--dashboard-green);
    }
    
    .status-pending {
        background: rgba(245, 158, 11, 0.15);
        color: var(--dashboard-orange);
    }
    
    .status-analysis {
        background: rgba(139, 92, 246, 0.15);
        color: var(--dashboard-purple);
    }
    
    .document-btn {
        background: linear-gradient(135deg, var(--dashboard-blue), var(--dashboard-cyan));
        color: white;
        border: none;
        padding: 10px 24px;
        border-radius: 12px;
        font-size: 13px;
        font-weight: 500;
        cursor: pointer;
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        position: relative;
        overflow: hidden;
    }
    
    .document-btn::before {
        content: '';
        position: absolute;
        top: 0;
        left: -100%;
        width: 100%;
        height: 100%;
        background: linear-gradient(90deg, transparent, rgba(255,255,255,0.2), transparent);
        transition: left 0.6s ease;
    }
    
    .document-btn:hover::before {
        left: 100%;
    }
    
    .document-btn:hover {
        transform: translateY(-2px) scale(1.05);
        box-shadow: 0 8px 20px rgba(59, 130, 246, 0.4);
    }
    
    .security-btn {
        width: 100%;
        background: linear-gradient(135deg, var(--dashboard-blue), var(--dashboard-cyan));
        color: white;
        border: none;
        padding: 14px 24px;
        border-radius: 16px;
        font-size: 14px;
        font-weight: 500;
        cursor: pointer;
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        position: relative;
        overflow: hidden;
    }
    
    .security-btn::before {
        content: '';
        position: absolute;
        top: 0;
        left: -100%;
        width: 100%;
        height: 100%;
        background: linear-gradient(90deg, transparent, rgba(255,255,255,0.2), transparent);
        transition: left 0.6s ease;
    }
    
    .security-btn:hover::before {
        left: 100%;
    }
    
    .security-btn:hover {
        transform: translateY(-3px) scale(1.02);
        box-shadow: 0 12px 30px rgba(59, 130, 246, 0.4);
    }
    
    /* Sidebar Overlay for mobile sidebar */
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
        background: linear-gradient(90deg, transparent, rgba(59, 130, 246, 0.1), transparent);
        transition: left 0.6s ease;
    }
    
    .dropdown-item:hover::before {
        left: 100%;
    }
    
    .dropdown-item:hover {
        background: linear-gradient(135deg, rgba(59, 130, 246, 0.1), rgba(6, 182, 212, 0.1)) !important;
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
        box-shadow: 0 8px 20px rgba(59, 130, 246, 0.4);
        flex-shrink: 0;
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

    /* Mobile responsive for toasts and responsive grid */
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
        
        .document-grid {
            grid-template-columns: 1fr;
            gap: 20px;
        }
        
        .main-container {
            padding: 90px 16px 100px;
        }
        
        .profile-card {
            padding: 20px;
            margin-bottom: 20px;
        }
    }

    /* Custom styling for box codes */
    .box_code {
        padding-inline: 0;
        border: 1px solid rgba(<?php echo $primaryRgb; ?>, 0.5);
        width: 39px;
        height: 39px;
        margin: 0px 4px;
        border-radius: 10px;
        background: var(--bg-card);
        color: var(--text-primary);
        text-align: center;
        font-weight: 500;
    }

    .box_code:focus {
        border-color: rgba(<?php echo $primaryRgb; ?>, 1);
        box-shadow: 0 0 0 3px rgba(<?php echo $primaryRgb; ?>, 0.1);
        outline: none;
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
   
        
        <a href="profile.php" class="menu-item active">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor">
            <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/>
            <circle cx="12" cy="7" r="4"/>
          </svg>
          <span>Profile</span>
        </a>
      </div>
    </nav>
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
        <li><a class="dropdown-item openModal" data-target="libs/includes/solicitar_premio">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor">
            <path d="M6 9l6 6 6-6"/>
            <circle cx="12" cy="12" r="1"/>
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
  
  <!-- Main Content -->
  <main class="main-container">
    <div class="dashboard-title">
      <h1>Profile</h1>
      <p>Manage your personal information and documentation</p>
    </div>

    <div class="row">
      <!-- Personal Information Card -->
      <div class="col-lg-4 col-md-12">
        <div class="profile-card">
          <div class="profile-card-title">Personal Details</div>
          <div class="info-item">
            <span class="info-label">Full name:</span>
            <span class="info-value"><?php echo $_SESSION['name']; ?></span>
          </div>
          <div class="info-item">
            <span class="info-label">CNPJ:</span>
            <span class="info-value"><?php echo $_SESSION['cnpj']; ?></span>
          </div>
          <div class="info-item">
            <span class="info-label">Email Address:</span>
            <span class="info-value"><?php echo $_SESSION['email']; ?></span>
          </div>
          <div class="info-item">
            <span class="info-label">Celular:</span>
            <span class="info-value"><?php echo $_SESSION['telefone']; ?></span>
          </div>
        </div>

        <!-- Security Card -->
        <div class="profile-card">
          <div class="profile-card-title">Security da Conta</div>
          <button class="security-btn openModal" data-target="libs/includes/senha">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" style="width: 18px; height: 18px; margin-right: 8px;">
              <path d="M5 11m0 2a2 2 0 0 1 2 -2h10a2 2 0 0 1 2 2v6a2 2 0 0 1 -2 2h-10a2 2 0 0 1 -2 -2z"/>
              <path d="M12 16m-1 0a1 1 0 1 0 2 0a1 1 0 1 0 -2 0"/>
              <path d="M8 11v-5a4 4 0 0 1 8 0"/>
            </svg>
            Atualizar Senha
          </button>
        </div>
      </div>

      <!-- Documentation Section -->
      <div class="col-lg-8 col-md-12">
        <div class="profile-card">
          <div class="profile-card-title">Documentation</div>
          
          <div class="document-grid">
            <?php
            // Carregar variáveis do arquivo .env
            loadEnv2();

            // Conectar ao banco de dados
            $pdo = connectDb2();

            // Recupera o ID do usuário da sessão
            $userId = $_SESSION['user_id'];

            // Verifica o estado de documents_checked
            $stmt = $pdo->prepare("SELECT documents_checked FROM users WHERE id = :user_id");
            $stmt->bindParam(':user_id', $userId);
            $stmt->execute();
            $user = $stmt->fetch(PDO::FETCH_ASSOC);

            $documentsChecked = $user['documents_checked'];

            // Inicializa a variável $documentStatuses para evitar erros
            $documentStatuses = [];

            // Lista de documentos e seus nomes (sem Contrato Social)
            $documents = [
                1 => 'Frente do documento', 
                2 => 'Verso do documento', 
                3 => 'Selfie com o documento'
            ];

            // SVGs personalizados para cada documento
            $documentSVGs = [
                1 => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor"><path d="M3 4m0 3a3 3 0 0 1 3 -3h12a3 3 0 0 1 3 3v10a3 3 0 0 1 -3 3h-12a3 3 0 0 1 -3 -3z"/><path d="M9 10m-2 0a2 2 0 1 0 4 0a2 2 0 1 0 -4 0"/><path d="M15 8l2 0"/><path d="M15 12l2 0"/><path d="M7 16l10 0"/></svg>',
                2 => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor"><path d="M7 12h3v4h-3z"/><path d="M10 6h-6a1 1 0 0 0 -1 1v12a1 1 0 0 0 1 1h16a1 1 0 0 0 1 -1v-12a1 1 0 0 0 -1 -1h-6"/><path d="M10 3m0 1a1 1 0 0 1 1 -1h2a1 1 0 0 1 1 1v3a1 1 0 0 1 -1 1h-2a1 1 0 0 1 -1 -1z"/><path d="M14 16h2"/><path d="M14 12h4"/></svg>',
                3 => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor"><path d="M4 8v-2a2 2 0 0 1 2 -2h2"/><path d="M4 16v2a2 2 0 0 0 2 2h2"/><path d="M16 4h2a2 2 0 0 1 2 2v2"/><path d="M16 20h2a2 2 0 0 0 2 -2v-2"/><path d="M9 10l.01 0"/><path d="M15 10l.01 0"/><path d="M9.5 15a3.5 3.5 0 0 0 5 0"/></svg>'
            ];

            // Se documents_checked for 1, todos os documentos são aprovados
            if ($documentsChecked == 1) {
                foreach ($documents as $documentId => $documentName) {
                    $documentStatuses[$documentId] = [
                        'name' => $documentName,
                        'status' => 'Aprovado',
                        'show_button' => false
                    ];
                }
            } else {
                // Caso contrário, verificamos os documentos individualmente
                foreach ($documents as $documentId => $documentName) {
                    // Verifica se o documento está na tabela users_documents
                    $stmt = $pdo->prepare("SELECT * FROM users_documents WHERE user_id = :user_id AND document_id = :document_id");
                    $stmt->bindParam(':user_id', $userId);
                    $stmt->bindParam(':document_id', $documentId);
                    $stmt->execute();
                    $document = $stmt->fetch(PDO::FETCH_ASSOC);

                    if ($document) {
                        $status = 'Análise';
                        $showButton = false;
                    } else {
                        $status = 'Pending';
                        $showButton = true;
                    }

                    $documentStatuses[$documentId] = [
                        'name' => $documentName,
                        'status' => $status,
                        'show_button' => $showButton
                    ];
                }
            }

            // Exibe os documentos com base no status
            foreach ($documentStatuses as $documentId => $documentInfo) {
                $iconClass = 'document-icon';
                $statusClass = 'document-status';
                
                if ($documentInfo['status'] == 'Aprovado') {
                    $iconClass .= ' approved';
                    $statusClass .= ' status-approved';
                } elseif ($documentInfo['status'] == 'Análise') {
                    $iconClass .= ' analysis';
                    $statusClass .= ' status-analysis';
                } else {
                    $iconClass .= ' pending';
                    $statusClass .= ' status-pending';
                }
                
                echo '<div class="document-card">';
                echo '  <div class="' . $iconClass . '">';
                echo $documentSVGs[$documentId];
                echo '  </div>';
                echo '  <div class="document-title">' . $documentInfo['name'] . '</div>';
                echo '  <div class="' . $statusClass . '">' . $documentInfo['status'] . '</div>';
                
                if ($documentInfo['status'] == 'Pending' && $documentInfo['show_button']) {
                    $link = 'libs/includes/enviar_documento-' . $documentId;
                    echo '  <button class="document-btn openModal" data-target="' . $link . '">Submit</button>';
                }
                
                echo '</div>';
            }
            ?>
          </div>
        </div>
      </div>
    </div>
  </main>

  <!-- Modal -->
  <div class="modal fade" id="modalConteudo" tabindex="-1" aria-labelledby="modalConteudo" data-bs-backdrop="static" data-bs-keyboard="false" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable">
      <div class="modal-content">
      </div>
    </div>
  </div>

  <!-- Toast Container -->
  <div class="toast-container" id="toastContainer"></div>

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
  <script src="assets/js/custom-switcher.min.js"></script>
  <script src="assets/libs/glightbox/js/glightbox.min.js"></script>
  <script src="assets/js/custom.js"></script>

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

        // Modal functionality
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

    // Toast function
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
          toast.classList.add('active');
          const progress = toast.querySelector('.progress');
          if (progress) {
            progress.classList.add('active');
          }
        }, 150);
        
        // Close toast function
        function closeToast() {
          toast.classList.remove('active');
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
