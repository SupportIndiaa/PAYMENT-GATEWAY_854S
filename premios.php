
<?php
session_start();

// Verificar se o usuário está logado
if (!isset($_SESSION['user_id'])) {
    header("Location: /login.php");
    exit();
}

$host = 'localhost';
$dbname = 'u321369575_Rgatepay77';
$username_db = 'u321369575_Rgatepay77';
$password_db = 'Rgatepay77';

$user_id = $_SESSION['user_id']; // Usar ID do usuário da sessão
$total_revenue = 0;

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $username_db, $password_db);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    // Buscar o faturamento TOTAL (sem filtro de data)
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
    $total_revenue = $result['total'] ?? 0;
    
    // Buscar cores do sistema
    $query = "SELECT primary_color, secondary_color FROM config LIMIT 1";
    $stmt = $pdo->prepare($query);
    $stmt->execute();
    $config = $stmt->fetch(PDO::FETCH_ASSOC);

    $primaryRgb = isset($config['primary_color']) ? $config['primary_color'] : '59, 130, 246';
    $secondaryRgb = isset($config['secondary_color']) ? $config['secondary_color'] : '16, 185, 129';
    
} catch(PDOException $e) {
    $total_revenue = 0;
    $primaryRgb = '59, 130, 246';
    $secondaryRgb = '16, 185, 129';
}

// Determinar meta baseado no faturamento atual
if ($total_revenue < 10000) {
    $current_goal = 10000;
} else {
    $current_goal = 100000;
}

$progress_percentage = min(($total_revenue / $current_goal) * 100, 100);
?>
<!DOCTYPE html>
<html lang="pt-BR" dir="ltr" data-nav-layout="vertical" data-theme-mode="light" data-header-styles="light" data-menu-styles="light" data-toggled="close">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Premiações - Sistema de Recompensas</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css" rel="stylesheet">
    
    <style>
        :root {
            --primary-rgb: <?php echo $primaryRgb; ?>;
            --secondary-rgb: <?php echo $secondaryRgb; ?>;
            
            /* Cores do dashboard baseadas no index.php */
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

        .container {
            display: flex;
            min-height: 100vh;
        }

        /* Sidebar - Estilo baseado no index.php */
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

        .sidebar-header img {
            height: 36px;
            filter: drop-shadow(0 4px 12px rgba(0, 0, 0, 0.3));
            opacity: 1;
        }

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

        .menu-item {
            display: flex;
            align-items: center;
            gap: 14px;
            padding: 12px 20px;
            color: var(--text-muted);
            text-decoration: none;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            margin: 2px 16px;
            border-radius: 25px;
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
            border-radius: 25px;
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

        .menu-item svg,
        .menu-item i {
            width: 18px;
            height: 18px;
            stroke-width: 2;
            z-index: 1;
            transition: all 0.3s ease;
            flex-shrink: 0;
        }

        .menu-item:hover svg,
        .menu-item:hover i,
        .menu-item.active svg,
        .menu-item.active i {
            color: var(--dashboard-blue);
            transform: scale(1.1);
        }

        .menu-item span {
            z-index: 1;
            transition: all 0.3s ease;
        }

        /* Progress Section igual ao index.php */
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

        .progress-icon svg,
        .progress-icon i {
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

        @keyframes neon-pulse {
            0% {
                box-shadow: 0 0 20px rgba(59, 130, 246, 0.6), inset 0 0 20px rgba(255, 255, 255, 0.3);
            }
            100% {
                box-shadow: 0 0 30px rgba(59, 130, 246, 0.9), 0 0 40px rgba(6, 182, 212, 0.6), inset 0 0 20px rgba(255, 255, 255, 0.5);
            }
        }

        /* Floating Header igual ao index.php */
        .floating-header-left {
            position: fixed;
            top: 24px;
            left: 24px;
            z-index: 999;
            display: none;
        }

        .header-btn {
            width: 40px;
            height: 40px;
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

        .header-btn:hover {
            color: white;
            background: linear-gradient(135deg, var(--dashboard-blue), var(--dashboard-cyan));
            border-color: var(--dashboard-cyan);
            transform: translateY(-3px) scale(1.05);
            box-shadow: 0 12px 40px rgba(59, 130, 246, 0.4);
        }

        .header-btn svg,
        .header-btn i {
            width: 16px;
            height: 16px;
            stroke-width: 2;
        }

        /* Main Content */
        .main-content {
            margin-left: 280px;
            flex: 1;
            min-height: 100vh;
            transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
        }

        .hero-section {
            background: rgba(255, 255, 255, 0.05);
            backdrop-filter: blur(20px);
            padding: 4rem 3rem 2rem;
            text-align: center;
            border-bottom: 1px solid var(--border-color);
        }

        .hero-title {
            font-size: 3rem;
            font-weight: 700;
            color: var(--text-primary);
            margin-bottom: 1rem;
            text-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
            animation: fadeInUp 0.8s ease;
        }

        .hero-subtitle {
            font-size: 1.25rem;
            color: var(--text-muted);
            font-weight: 400;
            max-width: 600px;
            margin: 0 auto;
            animation: fadeInUp 0.8s ease 0.2s both;
        }

        /* Stats Section - Design Melhorado */
        .stats-section {
            padding: 3rem;
            animation: fadeInUp 0.8s ease 0.4s both;
        }

        .stats-grid {
            display: grid;
            grid-template-columns: 1fr;
            gap: 2rem;
            margin-bottom: 3rem;
            max-width: 500px;
            margin-left: auto;
            margin-right: auto;
        }

        .stat-card {
            background: var(--bg-card);
            backdrop-filter: blur(20px);
            border-radius: 28px;
            padding: 3rem 2rem;
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.1);
            border: 1px solid var(--border-color);
            transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
            position: relative;
            overflow: hidden;
            text-align: center;
        }

        .stat-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 5px;
            background: linear-gradient(90deg, var(--dashboard-blue), var(--dashboard-cyan), var(--dashboard-green));
        }

        .stat-card:hover {
            transform: translateY(-12px) scale(1.02);
            box-shadow: 0 32px 64px rgba(0, 0, 0, 0.2);
        }

        .stat-icon-container {
            position: relative;
            display: inline-block;
            margin-bottom: 2rem;
        }

        .stat-icon-bg {
            width: 100px;
            height: 100px;
            border-radius: 50%;
            background: linear-gradient(135deg, var(--dashboard-blue), var(--dashboard-cyan));
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto;
            box-shadow: 0 20px 40px rgba(59, 130, 246, 0.4);
            animation: float 3s ease-in-out infinite;
        }

        .stat-icon-bg i {
            font-size: 3rem;
            color: white;
            animation: none;
        }

        .stat-number {
            display: block;
            font-size: 3.5rem;
            font-weight: 800;
            color: var(--text-primary);
            margin-bottom: 1rem;
            background: linear-gradient(135deg, var(--dashboard-blue), var(--dashboard-cyan));
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            text-shadow: none;
        }

        .stat-label {
            color: var(--text-muted);
            font-weight: 600;
            font-size: 1.1rem;
            text-transform: uppercase;
            letter-spacing: 2px;
        }

        /* Awards Section */
        .awards-section {
            padding: 4rem 3rem;
        }

        .section-title {
            text-align: center;
            font-size: 2.25rem;
            font-weight: 700;
            color: var(--text-primary);
            margin-bottom: 3rem;
            animation: fadeInUp 0.8s ease;
        }

        .awards-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(400px, 1fr));
            gap: 2rem;
            max-width: 1400px;
            margin: 0 auto;
        }

        .award-card {
            background: var(--bg-card);
            backdrop-filter: blur(20px);
            border-radius: 24px;
            padding: 2.5rem;
            box-shadow: 0 8px 32px rgba(0, 0, 0, 0.1);
            transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
            border: 1px solid var(--border-color);
            position: relative;
            overflow: hidden;
            animation: fadeInUp 0.8s ease;
        }

        .award-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 4px;
            background: linear-gradient(90deg, var(--dashboard-blue), var(--dashboard-cyan));
        }

        .award-card:hover {
            transform: translateY(-8px);
            box-shadow: 0 16px 48px rgba(0, 0, 0, 0.15);
        }

        .award-card.premium {
            background: var(--bg-card);
            border: 2px solid var(--dashboard-orange);
        }

        .award-card.premium::before {
            background: linear-gradient(90deg, var(--dashboard-orange), #d97706);
        }

        .premium-badge {
            position: absolute;
            top: 1.5rem;
            right: 1.5rem;
            background: linear-gradient(135deg, var(--dashboard-orange), #d97706);
            color: white;
            padding: 0.5rem 1rem;
            border-radius: 20px;
            font-size: 0.75rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            box-shadow: 0 4px 12px rgba(245, 158, 11, 0.3);
            animation: pulse 2s infinite;
        }

        .award-image {
            text-align: center;
            margin-bottom: 2rem;
            position: relative;
        }

        .award-image img {
            max-width: 180px;
            height: auto;
            border-radius: 16px;
            box-shadow: 0 8px 24px rgba(0, 0, 0, 0.1);
            transition: transform 0.3s ease;
        }

        .award-card:hover .award-image img {
            transform: scale(1.05);
        }

        .plaque-container {
            display: inline-block;
            position: relative;
        }

        .plaque {
            width: 180px;
            height: 120px;
            background: linear-gradient(135deg, #1f2937 0%, #374151 100%);
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 1rem;
            box-shadow: 0 12px 32px rgba(0, 0, 0, 0.3);
            border: 2px solid #6b7280;
            transition: transform 0.3s ease;
        }

        .award-card:hover .plaque {
            transform: rotateY(5deg) scale(1.05);
        }

        .plaque.gold {
            background: linear-gradient(135deg, #92400e 0%, #d97706 100%);
            border-color: var(--dashboard-orange);
        }

        .plaque-logo {
            width: 40px;
            height: 40px;
            object-fit: contain;
            border-radius: 8px;
        }

        .plaque-text {
            text-align: center;
            color: white;
        }

        .plaque-amount {
            display: block;
            font-size: 1.25rem;
            font-weight: 700;
            color: var(--dashboard-blue);
            text-shadow: 0 2px 4px rgba(0, 0, 0, 0.3);
        }

        .plaque.gold .plaque-amount {
            color: #fbbf24;
        }

        .plaque-subtitle {
            font-size: 0.65rem;
            opacity: 0.9;
            letter-spacing: 1px;
            margin-top: 2px;
        }

        .award-info h3 {
            font-size: 1.5rem;
            color: var(--text-primary);
            margin-bottom: 0.5rem;
            font-weight: 700;
        }

        .award-goal {
            display: flex;
            align-items: baseline;
            gap: 0.5rem;
            margin-bottom: 1rem;
        }

        .goal-amount {
            font-size: 2rem;
            font-weight: 800;
            color: var(--dashboard-blue);
        }

        .goal-label {
            color: var(--text-muted);
            font-size: 0.9rem;
            font-weight: 500;
        }

        .award-description {
            color: var(--text-muted);
            line-height: 1.6;
            margin-bottom: 1.5rem;
            font-weight: 400;
        }

        .progress-container {
            margin-bottom: 1rem;
        }

        .award-progress-bar {
            width: 100%;
            height: 8px;
            background: rgba(59, 130, 246, 0.15);
            border-radius: 8px;
            overflow: hidden;
            margin-bottom: 0.5rem;
        }

        .award-progress-fill {
            height: 100%;
            background: linear-gradient(90deg, var(--dashboard-blue) 0%, var(--dashboard-cyan) 100%);
            transition: width 0.5s ease;
            border-radius: 8px;
        }

        .progress-text {
            font-size: 0.875rem;
            color: var(--text-muted);
            font-weight: 500;
        }

        /* Ícones por cima dos cards */
        .award-icon {
            position: absolute;
            top: -20px;
            left: 50%;
            transform: translateX(-50%);
            width: 50px;
            height: 50px;
            background: linear-gradient(135deg, var(--dashboard-blue), var(--dashboard-cyan));
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 1.3rem;
            box-shadow: 0 8px 24px rgba(59, 130, 246, 0.4);
            animation: float 3s ease-in-out infinite;
            z-index: 10;
        }

        /* Info Section */
        .info-section {
            background: var(--bg-card);
            backdrop-filter: blur(20px);
            margin: 2rem 3rem;
            border-radius: 24px;
            padding: 3rem;
            border: 1px solid var(--border-color);
            animation: fadeInUp 0.8s ease 0.6s both;
        }

        .info-title {
            text-align: center;
            font-size: 2rem;
            font-weight: 700;
            color: var(--text-primary);
            margin-bottom: 2.5rem;
        }

        .info-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
            gap: 2rem;
        }

        .info-item {
            text-align: center;
            padding: 2rem;
            background: rgba(59, 130, 246, 0.05);
            border-radius: 16px;
            border: 1px solid var(--border-color);
            transition: transform 0.3s ease;
        }

        .info-item:hover {
            transform: translateY(-5px);
        }

        .info-icon {
            width: 60px;
            height: 60px;
            background: linear-gradient(135deg, var(--dashboard-blue), var(--dashboard-cyan));
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 1rem;
            color: white;
            font-size: 1.5rem;
            animation: pulse 2s infinite;
        }

        .info-item h4 {
            font-size: 1.25rem;
            color: var(--text-primary);
            margin-bottom: 0.75rem;
            font-weight: 600;
        }

        .info-item p {
            color: var(--text-muted);
            line-height: 1.6;
        }

        /* Popup */
        .popup-overlay {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.6);
            display: none;
            align-items: center;
            justify-content: center;
            z-index: 1000;
            backdrop-filter: blur(8px);
        }

        .popup {
            background: var(--bg-card);
            border-radius: 24px;
            padding: 3rem;
            max-width: 450px;
            width: 90%;
            text-align: center;
            box-shadow: 0 24px 64px rgba(0, 0, 0, 0.25);
            transform: scale(0.8);
            opacity: 0;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            border: 1px solid var(--border-color);
        }

        .popup.show {
            transform: scale(1);
            opacity: 1;
        }

        /* Logo limpa sem fundo */
        .popup-logo {
            width: 80px;
            height: 80px;
            border-radius: 12px;
            display: block;
            margin: 0 auto 1.5rem;
            object-fit: contain;
            /* Removido background e box-shadow */
        }

        .popup h3 {
            font-size: 1.5rem;
            color: var(--text-primary);
            margin-bottom: 1rem;
            font-weight: 700;
        }

        .popup p {
            color: var(--text-muted);
            line-height: 1.6;
            margin-bottom: 2rem;
            font-size: 1.1rem;
        }

        .popup-btn {
            background: linear-gradient(135deg, var(--dashboard-blue), var(--dashboard-cyan));
            color: white;
            border: none;
            padding: 1rem 2rem;
            border-radius: 12px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            font-size: 1rem;
            box-shadow: 0 4px 16px rgba(59, 130, 246, 0.3);
        }

        .popup-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(59, 130, 246, 0.4);
        }

        /* Sidebar Overlay para mobile */
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

        /* Animations */
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

        @keyframes bounce {
            0%, 20%, 50%, 80%, 100% {
                transform: translateY(0);
            }
            40% {
                transform: translateY(-10px);
            }
            60% {
                transform: translateY(-5px);
            }
        }

        @keyframes pulse {
            0% {
                transform: scale(1);
            }
            50% {
                transform: scale(1.05);
            }
            100% {
                transform: scale(1);
            }
        }

        @keyframes float {
            0%, 100% {
                transform: translateX(-50%) translateY(0);
            }
            50% {
                transform: translateX(-50%) translateY(-10px);
            }
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

            .main-content {
                margin-left: 0;
                padding: 32px 20px 120px;
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

            .awards-grid {
                grid-template-columns: 1fr !important;
                gap: 20px;
            }

            .hero-section {
                padding: 3rem 1.5rem 2rem;
            }

            .hero-title {
                font-size: 2.25rem;
            }

            .stats-section {
                padding: 3rem 1.5rem;
            }

            .awards-section {
                padding: 3rem 1.5rem;
            }

            .info-section {
                margin: 2rem 1.5rem;
                padding: 2rem;
            }
        }

        @media (max-width: 768px) {
            .dashboard-sidebar {
                width: 280px;
            }

            .floating-header-left {
                top: 16px;
                left: 16px;
            }

            .header-btn {
                width: 40px;
                height: 40px;
            }

            .awards-grid {
                grid-template-columns: 1fr;
                gap: 1.5rem;
            }
        }
    </style>
</head>
<body>
    <!-- Sidebar Overlay -->
    <div class="sidebar-overlay" id="sidebarOverlay"></div>

    <!-- Mobile Menu Toggle (Left) -->
    <div class="floating-header-left">
        <button class="header-btn" id="menuToggle">
            <i class="fas fa-bars"></i>
        </button>
    </div>

    <!-- Compact Sidebar -->
    <div class="dashboard-sidebar" id="sidebar">
        <div class="sidebar-header">
            <div class="d-flex align-items-center">
                <img src="teste.png" alt="logo">
            </div>
        </div>

        <nav class="sidebar-menu">
            <div class="menu-section">
                <a href="./" class="menu-item">
                    <i class="fas fa-home"></i>
                    <span>Dashboard</span>
                </a>

                <a href="premios.php" class="menu-item active">
                    <i class="fas fa-gift"></i>
                    <span>Rewards</span>
                </a>

                <a href="#" class="menu-item" onclick="showRequestPrizePopup()">
                    <i class="fas fa-star"></i>
                    <span>Solicitar Rewards</span>
                </a>

                <a href="#" class="menu-item" onclick="showNetworkPopup()">
                    <i class="fas fa-network-wired"></i>
                    <span>Network</span>
                </a>

                <a href="javascript:history.back()" class="menu-item">
                    <i class="fas fa-arrow-left"></i>
                    <span>Back</span>
                </a>
            </div>
        </nav>

        <!-- Progress Section igual ao index.php -->
        <div class="progress-section">
            <div class="progress-header">
                <div class="progress-icon">
                    <i class="fas fa-chart-line"></i>
                </div>
                <div class="progress-amount">
                    R$ <?= number_format($total_revenue, 2, ',', '.') ?> / <?= $current_goal >= 100000 ? '100K' : '10K' ?>
                </div>
            </div>
            <div class="progress-bar-container">
                <div class="progress-bar" style="width: <?= $progress_percentage ?>%"></div>
            </div>
        </div>
    </div>

    <!-- Main Content -->
    <div class="main-content" id="main-content">
        <!-- Hero Section -->
        <div class="hero-section">
            <h1 class="hero-title">Seus Rewards Exclusivos</h1>
            <p class="hero-subtitle">Alcance marcos incríveis e seja recompensado pelo seu extraordinário desempenho em vendas</p>
        </div>

        <!-- Stats Section -->
        <div class="stats-section">
            <div class="stats-grid">
                <div class="stat-card">
                    <div class="stat-icon-container">
                        <div class="stat-icon-bg">
                            <i class="fas fa-trophy"></i>
                        </div>
                    </div>
                    <span class="stat-number">R$ <?php echo number_format($total_revenue, 2, ',', '.'); ?></span>
                    <span class="stat-label">Faturamento Total</span>
                </div>
            </div>

            <!-- Awards Grid -->
            <div class="awards-grid">
                <!-- Prêmio 1 - Pulseira 10k -->
                <div class="award-card">
                    <div class="award-icon">
                        <i class="fas fa-gem"></i>
                    </div>
                    <div class="award-image">
                        <img src="img/pulseira.png" alt="Pulseira Exclusiva">
                    </div>
                    <div class="award-info">
                        <h3>Pulseira Exclusiva</h3>
                        <div class="award-goal">
                            <span class="goal-amount">R$ 10.000</span>
                            <span class="goal-label">em faturamento</span>
                        </div>
                        <p class="award-description">Uma pulseira exclusiva e elegante para celebrar sua primeira conquista. Design único e sofisticado para os pioneiros do sucesso.</p>
                        <div class="progress-container">
                            <div class="award-progress-bar">
                                <div class="award-progress-fill" style="width: <?php echo min(($total_revenue / 10000) * 100, 100); ?>%"></div>
                            </div>
                            <span class="progress-text">R$ <?php echo number_format($total_revenue, 2, ',', '.'); ?> de R$ 10.000 alcançados</span>
                        </div>
                    </div>
                </div>

                <!-- Prêmio 2 - Placa 100k -->
                <div class="award-card">
                    <div class="award-icon">
                        <i class="fas fa-award"></i>
                    </div>
                    <div class="award-image">
                        <div class="plaque-container">
                            <div class="plaque">
                                <img src="teste.png" alt="Logo" class="plaque-logo">
                                <div class="plaque-text">
                                    <span class="plaque-amount">100K</span>
                                    <span class="plaque-subtitle">FATURAMENTO</span>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="award-info">
                        <h3>Placa de Reconhecimento</h3>
                        <div class="award-goal">
                            <span class="goal-amount">R$ 100.000</span>
                            <span class="goal-label">em faturamento</span>
                        </div>
                        <p class="award-description">Uma placa de reconhecimento premium que representa seu crescimento excepcional. Ideal para exibir em seu escritório ou espaço de trabalho.</p>
                        <div class="progress-container">
                            <div class="award-progress-bar">
                                <div class="award-progress-fill" style="width: <?php echo min(($total_revenue / 100000) * 100, 100); ?>%"></div>
                            </div>
                            <span class="progress-text">R$ <?php echo number_format($total_revenue, 2, ',', '.'); ?> de R$ 100.000 alcançados</span>
                        </div>
                    </div>
                </div>

                <!-- Prêmio 3 - Placa 1M -->
                <div class="award-card premium">
                    <div class="premium-badge">Elite</div>
                    <div class="award-icon">
                        <i class="fas fa-crown"></i>
                    </div>
                    <div class="award-image">
                        <div class="plaque-container">
                            <div class="plaque gold">
                                <img src="teste.png" alt="Logo" class="plaque-logo">
                                <div class="plaque-text">
                                    <span class="plaque-amount">1M</span>
                                    <span class="plaque-subtitle">FATURAMENTO</span>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="award-info">
                        <h3>Placa Diamante Elite</h3>
                        <div class="award-goal">
                            <span class="goal-amount">R$ 1.000.000</span>
                            <span class="goal-label">em faturamento</span>
                        </div>
                        <p class="award-description">O prêmio máximo para verdadeiros líderes. Uma placa exclusiva dourada que simboliza excelência, dedicação e sucesso extraordinário.</p>
                        <div class="progress-container">
                            <div class="award-progress-bar">
                                <div class="award-progress-fill" style="width: <?php echo min(($total_revenue / 1000000) * 100, 100); ?>%"></div>
                            </div>
                            <span class="progress-text">R$ <?php echo number_format($total_revenue, 2, ',', '.'); ?> de R$ 1.000.000 alcançados</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Info Section -->
        <div class="info-section">
            <h2 class="info-title">Como Funciona o Sistema</h2>
            <div class="info-grid">
                <div class="info-item">
                    <div class="info-icon">
                        <i class="fas fa-chart-line"></i>
                    </div>
                    <h4>Monitore em Tempo Real</h4>
                    <p>Acompanhe seu progresso de vendas e veja em tempo real o quanto falta para conquistar seu próximo prêmio exclusivo.</p>
                </div>
                <div class="info-item">
                    <div class="info-icon">
                        <i class="fas fa-trophy"></i>
                    </div>
                    <h4>Conquiste Automaticamente</h4>
                    <p>Ao atingir cada marco de faturamento, você automaticamente se torna elegível para receber seu prêmio exclusivo.</p>
                </div>
                <div class="info-item">
                    <div class="info-icon">
                        <i class="fas fa-shipping-fast"></i>
                    </div>
                    <h4>Entrega Exclusiva</h4>
                    <p>Seus prêmios são cuidadosamente embalados e enviados diretamente para o endereço cadastrado em sua conta.</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Popup Solicitar Rewards -->
    <div id="request-prize-overlay" class="popup-overlay">
        <div class="popup">
            <img src="teste.png" alt="Logo" class="popup-logo">
            <h3>Insufficient Revenue</h3>
            <p>Sua conta ainda não possui o faturamento necessário para solicitar prêmios. Continue vendendo and thecompanhe seu progresso!</p>
            <button onclick="closeRequestPrizePopup()" class="popup-btn">Understood</button>
        </div>
    </div>

    <!-- Popup Network -->
    <div id="network-popup-overlay" class="popup-overlay">
        <div class="popup">
            <img src="teste.png" alt="Logo" class="popup-logo">
            <h3>Faça Parte da Nossa Network</h3>
            <p>Junte-sand theo nosso grupo exclusivo e tenha acesso a oportunidades incríveis, networking e muito mais!</p>
            <button onclick="joinNetwork()" class="popup-btn">Fazer Parte</button>
        </div>
    </div>

    <script>
        // Sidebar functions
        function toggleSidebar() {
            const sidebar = document.getElementById('sidebar');
            const overlay = document.getElementById('sidebarOverlay');

            sidebar.classList.toggle('open');
            overlay.classList.toggle('active');

            if (sidebar.classList.contains('open')) {
                document.body.classList.add('sidebar-open');
            } else {
                document.body.classList.remove('sidebar-open');
            }
        }

        // Menu toggle
        document.getElementById('menuToggle').addEventListener('click', toggleSidebar);

        // Close sidebar when clicking overlay
        document.getElementById('sidebarOverlay').addEventListener('click', function() {
            const sidebar = document.getElementById('sidebar');
            if (sidebar.classList.contains('open')) {
                toggleSidebar();
            }
        });

        // Popup Solicitar Rewards functions
        function showRequestPrizePopup() {
            const totalRevenue = <?php echo $total_revenue; ?>;
            
            // Close sidebar if open (mobile)
            const sidebar = document.getElementById('sidebar');
            if (sidebar.classList.contains('open')) {
                toggleSidebar();
            }

            if (totalRevenue >= 10000) {
                // Redirecionar para página solicitar.php
                window.location.href = 'solicitar.php';
            } else {
                // Mostrar popup de faturamento insuficiente
                document.getElementById('request-prize-overlay').style.display = 'flex';
                setTimeout(() => {
                    document.querySelector('#request-prize-overlay .popup').classList.add('show');
                }, 10);
            }
        }

        function closeRequestPrizePopup() {
            document.querySelector('#request-prize-overlay .popup').classList.remove('show');
            setTimeout(() => {
                document.getElementById('request-prize-overlay').style.display = 'none';
            }, 300);
        }

        // Popup Network functions
        function showNetworkPopup() {
            // Close sidebar if open (mobile)
            const sidebar = document.getElementById('sidebar');
            if (sidebar.classList.contains('open')) {
                toggleSidebar();
            }

            document.getElementById('network-popup-overlay').style.display = 'flex';
            setTimeout(() => {
                document.querySelector('#network-popup-overlay .popup').classList.add('show');
            }, 10);
        }

        function closeNetworkPopup() {
            document.querySelector('#network-popup-overlay .popup').classList.remove('show');
            setTimeout(() => {
                document.getElementById('network-popup-overlay').style.display = 'none';
            }, 300);
        }

        function joinNetwork() {
            // Aqui você podand thedicionar a lógica para entrar no grupo
            alert('Redirecionando para o grupo...');
            closeNetworkPopup();
        }

        // Close popups when clicking outside
        document.getElementById('request-prize-overlay').addEventListener('click', function(e) {
            if (e.target === this) {
                closeRequestPrizePopup();
            }
        });

        document.getElementById('network-popup-overlay').addEventListener('click', function(e) {
            if (e.target === this) {
                closeNetworkPopup();
            }
        });
    </script>
</body>
</html>
