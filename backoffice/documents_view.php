
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
$status_filter = "";
$document_type = "";
$documents = []; 

// Verificar se há busca
if (isset($_GET['search']) && !empty($_GET['search'])) {
    $search = trim($_GET['search']);
}

// Verificar filtro de status
if (isset($_GET['status']) && !empty($_GET['status'])) {
    $status_filter = trim($_GET['status']);
}

// Verificar filtro de tipo de documento
if (isset($_GET['document_type']) && !empty($_GET['document_type'])) {
    $document_type = trim($_GET['document_type']);
}

// Consultar documentos com filtros (ordenados por ID desc - mais recentes primeiro)
$query = "
    SELECT DISTINCT d.id AS document_unique_id, u.id AS user_id, u.name, u.username, d.document_id, d.file_path, d.approved 
    FROM users_documents d
    JOIN users u ON u.id = d.user_id
    WHERE 1=1
";

$params = [];

if (!empty($search)) {
    $query .= " AND u.username LIKE :search";
    $params[':search'] = "%$search%";
}

if (!empty($status_filter)) {
    if ($status_filter === 'approved') {
        $query .= " AND d.approved = 1";
    } elseif ($status_filter === 'pending') {
        $query .= " AND d.approved = 0";
    } elseif ($status_filter === 'rejected') {
        $query .= " AND d.approved = -1";
    }
}

if (!empty($document_type)) {
    $query .= " AND d.document_id = :document_type";
    $params[':document_type'] = $document_type;
}

$query .= " ORDER BY d.id DESC";

$stmt = $pdo->prepare($query);
foreach ($params as $key => $value) {
    $stmt->bindValue($key, $value);
}
$stmt->execute();

$documents = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Estatísticas para cards
$statsQuery = [
    'total' => "SELECT COUNT(DISTINCT d.id) FROM users_documents d",
    'pending' => "SELECT COUNT(DISTINCT d.id) FROM users_documents d WHERE d.approved = 0",
    'approved' => "SELECT COUNT(DISTINCT d.id) FROM users_documents d WHERE d.approved = 1",
    'rejected' => "SELECT COUNT(DISTINCT d.id) FROM users_documents d WHERE d.approved = -1"
];

$stats = [];
foreach ($statsQuery as $key => $sql) {
    $stmt = $pdo->prepare($sql);
    $stmt->execute();
    $stats[$key] = $stmt->fetchColumn();
}

// Função para determinar o tipo de documento com base no document_id
function getDocumentType($document_id) {
    switch ($document_id) {
        case 1: return "Frente";
        case 2: return "Verso";
        case 3: return "Segurando Documento";
        case 4: return "Contrato Social";
        default: return "Type Desconhecido";
    }
}

// Função para ajustar o caminho do arquivo
function adjustFilePath($filePath) {
    $adjustedPath = preg_replace('/^.*?[\\\\\/]libs/', '', $filePath);
    return '/libs' . $adjustedPath;
}
?>
<!DOCTYPE html>
<html lang="pt-BR" dir="ltr" data-nav-layout="vertical" data-theme-mode="light" data-header-styles="light" data-menu-styles="light" data-toggled="close">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>RPay - Visualizar Todos os Documentos</title>
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

        /* Search Section */
        .search-section {
            background: var(--bg-card);
            backdrop-filter: blur(20px);
            border-radius: 20px;
            padding: 1.5rem;
            margin-bottom: 2rem;
            box-shadow: var(--shadow-lg);
            border: 1px solid var(--border-color);
            animation: fadeInUp 0.8s ease 0.4s both;
        }

        .search-form {
            display: grid;
            grid-template-columns: 2fr 1fr 1fr auto;
            gap: 1rem;
            align-items: end;
        }

        .search-group {
            display: flex;
            flex-direction: column;
            gap: 0.5rem;
        }

        .search-label {
            font-size: 0.875rem;
            font-weight: 400;
            color: var(--text-muted);
        }

        .search-input, .search-select {
            padding: 0.75rem 1rem;
            background: rgba(59, 130, 246, 0.05);
            border: 1px solid var(--border-color);
            border-radius: 12px;
            color: var(--text-primary);
            font-size: 1rem;
            font-weight: 300;
            transition: all 0.3s ease;
            min-width: 0;
        }

        .search-input:focus, .search-select:focus {
            outline: none;
            border-color: rgb(var(--primary-rgb));
            box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
        }

        .search-input::placeholder {
            color: var(--text-muted);
        }

        .search-select option {
            background: var(--bg-card);
            color: var(--text-primary);
        }

        .search-btn {
            padding: 0.75rem 1.5rem;
            background: linear-gradient(135deg, rgb(var(--primary-rgb)), rgb(var(--primary-rgb)));
            border: none;
            border-radius: 12px;
            color: white;
            font-weight: 400;
            cursor: pointer;
            transition: all 0.3s ease;
            display: flex;
            align-items: center;
            gap: 0.5rem;
            height: fit-content;
            white-space: nowrap;
        }

        .search-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(59, 130, 246, 0.4);
        }

        /* Table Container */
        .table-container {
            background: var(--bg-card);
            backdrop-filter: blur(20px);
            border-radius: 20px;
            overflow: hidden;
            border: 1px solid var(--border-color);
            box-shadow: var(--shadow-xl);
            animation: fadeInUp 0.8s ease 0.6s both;
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

        /* Status Badges */
        .badge {
            padding: 0.375rem 0.75rem;
            border-radius: 20px;
            font-size: 0.75rem;
            font-weight: 400;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            display: inline-flex;
            align-items: center;
            gap: 0.375rem;
        }

        .badge-pending {
            background: rgba(245, 158, 11, 0.2);
            color: #f59e0b;
            border: 1px solid rgba(245, 158, 11, 0.3);
        }

        .badge-approved {
            background: rgba(16, 185, 129, 0.2);
            color: #10b981;
            border: 1px solid rgba(16, 185, 129, 0.3);
        }

        .badge-rejected {
            background: rgba(239, 68, 68, 0.2);
            color: #ef4444;
            border: 1px solid rgba(239, 68, 68, 0.3);
        }

        .badge-type {
            background: rgba(59, 130, 246, 0.2);
            color: rgb(var(--primary-rgb));
            border: 1px solid rgba(59, 130, 246, 0.3);
        }

        /* Action Buttons */
        .btn-view {
            background: linear-gradient(135deg, rgb(var(--primary-rgb)), rgb(var(--primary-rgb)));
            color: white;
            text-decoration: none;
            padding: 0.5rem 1rem;
            border-radius: 8px;
            font-size: 0.875rem;
            font-weight: 400;
            cursor: pointer;
            transition: all 0.3s ease;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 0.375rem;
            white-space: nowrap;
        }

        .btn-view:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(59, 130, 246, 0.4);
            color: white;
            text-decoration: none;
        }

        /* User Info */
        .user-info {
            display: flex;
            align-items: center;
            gap: 0.75rem;
        }

        .user-avatar {
            width: 40px;
            height: 40px;
            background: linear-gradient(135deg, rgb(var(--primary-rgb)), rgb(var(--secondary-rgb)));
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-weight: 500;
            font-size: 0.875rem;
        }

        .user-details strong {
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

            .search-form {
                grid-template-columns: 1fr;
                gap: 1rem;
            }

            .search-group {
                min-width: auto;
                flex: none;
            }

            .search-btn {
                align-self: stretch;
                justify-content: center;
            }

            .stats-grid {
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

            .stat-card {
                padding: 1rem;
            }

            .search-section {
                padding: 1rem;
            }
        }

        /* Prevent horizontal scroll */
        body, html {
            overflow-x: hidden;
            max-width: 100%;
        }

        .table-container::-webkit-scrollbar {
            height: 6px;
        }

        .table-container::-webkit-scrollbar-track {
            background: rgba(59, 130, 246, 0.1);
            border-radius: 3px;
        }

        .table-container::-webkit-scrollbar-thumb {
            background: rgb(var(--primary-rgb));
            border-radius: 3px;
        }

        .table-container::-webkit-scrollbar-thumb:hover {
            background: rgba(59, 130, 246, 0.8);
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
                            <i class="fas fa-folder-open"></i>
                        </div>
                        <div class="header-text">
                            <h1>Todos os Documentos</h1>
                            <p>Visualize todos os documentos do sistema com filtros avançados</p>
                        </div>
                    </div>
                </div>
            </header>

            <!-- Stats Grid -->
            <section class="stats-grid">
                <div class="stat-card">
                    <div class="stat-header">
                        <div>
                            <div class="stat-title">Total de Documentos</div>
                            <div class="stat-value"><?php echo number_format($stats['total']); ?></div>
                            <div class="stat-change">
                                <i class="fas fa-file-alt"></i>
                                <span>No sistema</span>
                            </div>
                        </div>
                        <div class="stat-icon">
                            <i class="fas fa-file-alt"></i>
                        </div>
                    </div>
                </div>

                <div class="stat-card">
                    <div class="stat-header">
                        <div>
                            <div class="stat-title">Documentos Pendings</div>
                            <div class="stat-value"><?php echo number_format($stats['pending']); ?></div>
                            <div class="stat-change">
                                <i class="fas fa-clock"></i>
                                <span>Aguardando análise</span>
                            </div>
                        </div>
                        <div class="stat-icon">
                            <i class="fas fa-clock"></i>
                        </div>
                    </div>
                </div>

                <div class="stat-card">
                    <div class="stat-header">
                        <div>
                            <div class="stat-title">Documentos Aprovados</div>
                            <div class="stat-value"><?php echo number_format($stats['approved']); ?></div>
                            <div class="stat-change">
                                <i class="fas fa-check"></i>
                                <span>Total aprovados</span>
                            </div>
                        </div>
                        <div class="stat-icon">
                            <i class="fas fa-check-circle"></i>
                        </div>
                    </div>
                </div>

                <div class="stat-card">
                    <div class="stat-header">
                        <div>
                            <div class="stat-title">Documentos Rejeitados</div>
                            <div class="stat-value"><?php echo number_format($stats['rejected']); ?></div>
                            <div class="stat-change">
                                <i class="fas fa-times"></i>
                                <span>Total rejeitados</span>
                            </div>
                        </div>
                        <div class="stat-icon">
                            <i class="fas fa-times-circle"></i>
                        </div>
                    </div>
                </div>
            </section>

            <!-- Search Section -->
            <section class="search-section">
                <form method="GET" action="" class="search-form">
                    <div class="search-group">
                        <label class="search-label">
                            <i class="fas fa-search"></i>
                            Buscar por Username
                        </label>
                        <input 
                            type="text" 
                            name="search" 
                            class="search-input" 
                            placeholder="Digite o username..." 
                            value="<?php echo htmlspecialchars($search); ?>"
                        >
                    </div>

                    <div class="search-group">
                        <label class="search-label">
                            <i class="fas fa-filter"></i>
                            Status
                        </label>
                        <select name="status" class="search-select">
                            <option value="">Todos</option>
                            <option value="pending" <?php echo $status_filter === 'pending' ? 'selected' : ''; ?>>Pendings</option>
                            <option value="approved" <?php echo $status_filter === 'approved' ? 'selected' : ''; ?>>Aprovados</option>
                            <option value="rejected" <?php echo $status_filter === 'rejected' ? 'selected' : ''; ?>>Rejeitados</option>
                        </select>
                    </div>

                    <div class="search-group">
                        <label class="search-label">
                            <i class="fas fa-file-alt"></i>
                            Type de Documento
                        </label>
                        <select name="document_type" class="search-select">
                            <option value="">Todos</option>
                            <option value="1" <?php echo $document_type === '1' ? 'selected' : ''; ?>>Frente</option>
                            <option value="2" <?php echo $document_type === '2' ? 'selected' : ''; ?>>Verso</option>
                            <option value="3" <?php echo $document_type === '3' ? 'selected' : ''; ?>>Segurando Documento</option>
                            <option value="4" <?php echo $document_type === '4' ? 'selected' : ''; ?>>Contrato Social</option>
                        </select>
                    </div>
                    
                    <button type="submit" class="search-btn">
                        <i class="fas fa-search"></i>
                        <span>Filtrar</span>
                    </button>
                </form>
            </section>

            <!-- Table Container -->
            <section class="table-container">
                <table class="table">
                    <thead>
                        <tr>
                            <th><i class="fas fa-hashtag"></i> ID</th>
                            <th><i class="fas fa-user"></i> Full Name</th>
                            <th><i class="fas fa-at"></i> Username</th>
                            <th><i class="fas fa-file-alt"></i> Type</th>
                            <th><i class="fas fa-eye"></i> Documento</th>
                            <th><i class="fas fa-info-circle"></i> Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($documents)): ?>
                            <tr>
                                <td colspan="6" style="text-align: center; padding: 3rem;">
                                    <i class="fas fa-file-search" style="font-size: 3rem; color: var(--text-muted); margin-bottom: 1rem;"></i>
                                    <div style="color: var(--text-muted);">
                                        <?php if (!empty($search) || !empty($status_filter) || !empty($document_type)): ?>
                                            Nenhum documento encontrado com os filtros aplicados.
                                        <?php else: ?>
                                            Nenhum documento encontrado no sistema.
                                        <?php endif; ?>
                                    </div>
                                </td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($documents as $document): ?>
                                <tr>
                                    <td><strong>#<?php echo $document['document_unique_id']; ?></strong></td>
                                    <td>
                                        <div class="user-info">
                                            <div class="user-avatar">
                                                <?php echo strtoupper(substr($document['name'] ?: $document['username'], 0, 2)); ?>
                                            </div>
                                            <div class="user-details">
                                                <strong><?php echo htmlspecialchars($document['name'] ?: $document['username']); ?></strong>
                                            </div>
                                        </div>
                                    </td>
                                    <td><?php echo htmlspecialchars($document['username']); ?></td>
                                    <td>
                                        <span class="badge badge-type">
                                            <?php echo getDocumentType($document['document_id']); ?>
                                        </span>
                                    </td>
                                    <td>
                                        <a href="<?php echo adjustFilePath($document['file_path']); ?>" target="_blank" class="btn-view">
                                            <i class="fas fa-external-link-alt"></i>
                                            <span>Visualizar</span>
                                        </a>
                                    </td>
                                    <td>
                                        <?php if ($document['approved'] == 1): ?>
                                            <span class="badge badge-approved">
                                                <i class="fas fa-check"></i> Aprovado
                                            </span>
                                        <?php elseif ($document['approved'] == -1): ?>
                                            <span class="badge badge-rejected">
                                                <i class="fas fa-times"></i> Rejeitado
                                            </span>
                                        <?php else: ?>
                                            <span class="badge badge-pending">
                                                <i class="fas fa-clock"></i> Pending
                                            </span>
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

        // Enhanced hover effects
        document.querySelectorAll('.stat-card').forEach(card => {
            card.addEventListener('mouseenter', function() {
                this.style.transform = 'translateY(-8px) scale(1.02)';
                const icon = this.querySelector('.stat-icon');
                if (icon) icon.style.transform = 'scale(1.1) rotate(5deg)';
            });
            
            card.addEventListener('mouseleave', function() {
                this.style.transform = 'translateY(0) scale(1)';
                const icon = this.querySelector('.stat-icon');
                if (icon) icon.style.transform = 'scale(1) rotate(0deg)';
            });
        });

        // Initializand thenimations
        document.addEventListener('DOMContentLoaded', function() {
            const observer = new IntersectionObserver((entries) => {
                entries.forEach(entry => {
                    if (entry.isIntersecting) {
                        entry.target.style.opacity = '1';
                        entry.target.style.transform = 'translateY(0)';
                    }
                });
            });

            document.querySelectorAll('.stat-card, .table-container, .search-section').forEach(el => {
                el.style.opacity = '0';
                el.style.transform = 'translateY(20px)';
                el.style.transition = 'all 0.6s ease';
                observer.observe(el);
            });
        });
    </script>
</body>
</html>
