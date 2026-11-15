
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

// Configuração de paginação - LIMITADO A 10
$limit = 10;
$page = isset($_GET['page']) ? max(1, (int)$_GET['page']) : 1;
$offset = ($page - 1) * $limit;

// Filtros
$search = isset($_GET['search']) ? trim($_GET['search']) : '';
$statusFilter = isset($_GET['status']) ? trim($_GET['status']) : '';

// Query principal com LIMIT correto
$query = "SELECT t.id, t.user_id, t.amount, t.type, t.status, t.created_at, u.username, t.postback_url, t.document
          FROM transactions t 
          INNER JOIN users u ON t.user_id = u.id";

$conditions = [];
$params = [];

if (!empty($search)) {
    $conditions[] = "u.username LIKE :search";
    $params[':search'] = '%' . $search . '%';
}

if (!empty($statusFilter) && $statusFilter !== 'all') {
    $conditions[] = "t.status = :status";
    $params[':status'] = $statusFilter;
}

if (!empty($conditions)) {
    $query .= " WHERE " . implode(" AND ", $conditions);
}

$query .= " ORDER BY t.created_at DESC LIMIT :limit OFFSET :offset";

$stmt = $pdo->prepare($query);
foreach ($params as $key => $value) {
    $stmt->bindValue($key, $value, PDO::PARAM_STR);
}
$stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
$stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
$stmt->execute();
$transactions = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Total para paginação
$totalQuery = "SELECT COUNT(*) FROM transactions t INNER JOIN users u ON t.user_id = u.id";
if (!empty($conditions)) {
    $totalQuery .= " WHERE " . implode(" AND ", $conditions);
}

$totalStmt = $pdo->prepare($totalQuery);
foreach ($params as $key => $value) {
    $totalStmt->bindValue($key, $value, PDO::PARAM_STR);
}
$totalStmt->execute();
$totalTransactions = $totalStmt->fetchColumn();
$totalPages = ceil($totalTransactions / $limit);

// Dados para exportação (TODOS os dados filtrados)
$exportQuery = "SELECT t.id, u.username, t.amount, t.status, t.created_at 
                FROM transactions t INNER JOIN users u ON t.user_id = u.id";
if (!empty($conditions)) {
    $exportQuery .= " WHERE " . implode(" AND ", $conditions);
}
$exportQuery .= " ORDER BY t.created_at DESC";

$exportStmt = $pdo->prepare($exportQuery);
foreach ($params as $key => $value) {
    $exportStmt->bindValue($key, $value, PDO::PARAM_STR);
}
$exportStmt->execute();
$exportTransactions = $exportStmt->fetchAll(PDO::FETCH_ASSOC);

// Estatísticas
$queryStats = [
    'total' => "SELECT COUNT(*) FROM transactions",
    'amount' => "SELECT SUM(amount) FROM transactions WHERE status IN ('PAID', 'completed')",
    'pending' => "SELECT COUNT(*) FROM transactions WHERE status IN ('PENDING', 'pending')",
    'completed' => "SELECT COUNT(*) FROM transactions WHERE status IN ('PAID', 'completed')",
    'documents' => "SELECT COUNT(*) FROM users_documents WHERE approved = 0"
];

$stats = [];
foreach ($queryStats as $key => $sql) {
    $stmt = $pdo->prepare($sql);
    $stmt->execute();
    $stats[$key] = $stmt->fetchColumn();
}

// Configuração de cores
$query = "SELECT primary_color, secondary_color FROM config LIMIT 1";
$stmt = $pdo->prepare($query);
$stmt->execute();
$config = $stmt->fetch(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="pt-BR" dir="ltr" data-nav-layout="vertical" data-theme-mode="light" data-header-styles="light" data-menu-styles="light" data-toggled="close">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>RPay - Gestão de Transactions</title>
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
            /* DETALHE AZUL DE FUNDO */
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

        .nav-badge {
            background: #ef4444;
            color: white;
            border-radius: 12px;
            padding: 2px 8px;
            font-size: 11px;
            font-weight: 600;
            margin-left: auto;
            z-index: 1;
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

        .header-actions {
            display: flex;
            gap: 0.75rem;
            flex-shrink: 0;
        }

        .header-btn {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            padding: 0.75rem 1.5rem;
            background: var(--bg-card);
            border: 1px solid var(--border-color);
            border-radius: 12px;
            color: var(--text-primary);
            text-decoration: none;
            font-weight: 400;
            transition: all 0.3s ease;
            backdrop-filter: blur(10px);
            box-shadow: var(--shadow);
            cursor: pointer;
            white-space: nowrap;
        }

        .header-btn:hover {
            background: rgb(var(--primary-rgb));
            color: white;
            transform: translateY(-2px);
            box-shadow: var(--shadow-lg);
        }

        .header-btn.primary {
            background: linear-gradient(135deg, rgb(var(--primary-rgb)), rgb(var(--primary-rgb)));
            color: white;
            border-color: rgb(var(--primary-rgb));
        }

        .header-btn.primary:hover {
            transform: translateY(-2px) scale(1.02);
            box-shadow: 0 10px 25px rgba(59, 130, 246, 0.4);
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

        /* Search Section com filtros */
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
            display: flex;
            gap: 1rem;
            align-items: end;
            flex-wrap: wrap;
        }

        .search-group {
            display: flex;
            flex-direction: column;
            gap: 0.5rem;
            flex: 1;
            min-width: 200px;
        }

        .search-label {
            font-size: 0.875rem;
            font-weight: 400;
            color: var(--text-muted);
        }

        .search-input, .filter-select {
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

        .search-input:focus, .filter-select:focus {
            outline: none;
            border-color: rgb(var(--primary-rgb));
            box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
        }

        .search-input::placeholder {
            color: var(--text-muted);
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

        .badge-completed {
            background: rgba(16, 185, 129, 0.2);
            color: #10b981;
            border: 1px solid rgba(16, 185, 129, 0.3);
        }

        .badge-failed {
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
        .action-buttons {
            display: flex;
            gap: 0.5rem;
        }

        .btn {
            padding: 0.5rem 1rem;
            border: none;
            border-radius: 8px;
            font-size: 0.875rem;
            font-weight: 400;
            cursor: pointer;
            transition: all 0.3s ease;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 0.375rem;
        }

        .btn-edit {
            background: linear-gradient(135deg, #f59e0b, #f97316);
            color: white;
        }

        .btn-edit:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(245, 158, 11, 0.4);
        }

        .btn-delete {
            background: linear-gradient(135deg, #ef4444, #dc2626);
            color: white;
        }

        .btn-delete:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(239, 68, 68, 0.4);
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

        /* ENHANCED Pagination */
        .pagination-container {
            margin-top: 0;
            display: flex;
            justify-content: space-between;
            align-items: center;
            animation: fadeInUp 0.8s ease 0.8s both;
            flex-wrap: wrap;
            gap: 1rem;
            padding: 1.5rem 2rem;
            background: var(--bg-card);
            border-radius: 0 0 20px 20px;
            border-top: 1px solid var(--border-color);
        }

        .pagination-info {
            color: var(--text-muted);
            font-size: 0.875rem;
            font-weight: 300;
        }

        .pagination {
            display: flex;
            gap: 0.5rem;
            list-style: none;
            align-items: center;
        }

        .page-item {
            margin: 0;
        }

        .page-link {
            padding: 0.75rem 1rem;
            background: var(--bg-card);
            border: 1px solid var(--border-color);
            border-radius: 12px;
            color: var(--text-muted);
            text-decoration: none;
            transition: all 0.3s ease;
            backdrop-filter: blur(20px);
            display: flex;
            align-items: center;
            gap: 0.5rem;
            min-width: 44px;
            justify-content: center;
            font-weight: 300;
        }

        .page-link:hover:not(.disabled) {
            background: rgba(59, 130, 246, 0.1);
            color: var(--text-primary);
            transform: translateY(-2px);
        }

        .page-link.disabled {
            opacity: 0.5;
            cursor: not-allowed;
        }

        .page-item.active .page-link {
            background: linear-gradient(135deg, rgb(var(--primary-rgb)), rgb(var(--primary-rgb)));
            color: white;
            border-color: rgb(var(--primary-rgb));
        }

        /* MODAL/POPUP Styles for Export */
        .modal-overlay {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: rgba(0, 0, 0, 0.5);
            backdrop-filter: blur(8px);
            z-index: 1050;
            display: none;
            align-items: center;
            justify-content: center;
            padding: 1rem;
        }

        .modal-overlay.active {
            display: flex;
            animation: fadeIn 0.3s ease;
        }

        .modal {
            background: var(--bg-card);
            border-radius: 20px;
            padding: 1.5rem;
            max-width: 500px;
            width: 100%;
            max-height: 90vh;
            overflow-y: auto;
            box-shadow: var(--shadow-2xl);
            border: 1px solid var(--border-color);
            animation: slideInUp 0.3s ease;
            margin: 1rem;
        }

        .modal-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 1.5rem;
            padding-bottom: 1rem;
            border-bottom: 1px solid var(--border-color);
        }

        .modal-title {
            font-size: 1.25rem;
            font-weight: 500;
            color: var(--text-primary);
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .close-btn {
            background: none;
            border: none;
            font-size: 1.25rem;
            color: var(--text-muted);
            cursor: pointer;
            padding: 0.5rem;
            border-radius: 8px;
            transition: all 0.3s ease;
            width: 40px;
            height: 40px;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .close-btn:hover {
            background: rgba(239, 68, 68, 0.1);
            color: #ef4444;
        }

        .export-options {
            display: flex;
            flex-direction: column;
            gap: 0.75rem;
            margin-bottom: 1.5rem;
        }

        .export-option {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            padding: 0.75rem;
            background: rgba(59, 130, 246, 0.05);
            border: 1px solid var(--border-color);
            border-radius: 12px;
            cursor: pointer;
            transition: all 0.3s ease;
        }

        .export-option:hover {
            background: rgba(59, 130, 246, 0.1);
            border-color: rgb(var(--primary-rgb));
        }

        .export-option.selected {
            background: rgba(59, 130, 246, 0.2);
            border-color: rgb(var(--primary-rgb));
        }

        .export-actions {
            display: flex;
            gap: 0.75rem;
            justify-content: flex-end;
            flex-wrap: wrap;
        }

        .btn-modal {
            padding: 0.75rem 1rem;
            border: none;
            border-radius: 12px;
            font-weight: 400;
            cursor: pointer;
            transition: all 0.3s ease;
            display: flex;
            align-items: center;
            gap: 0.5rem;
            font-size: 0.875rem;
            flex: 1;
            justify-content: center;
            min-width: 120px;
        }

        .btn-cancel {
            background: var(--bg-card);
            color: var(--text-muted);
            border: 1px solid var(--border-color);
        }

        .btn-cancel:hover {
            background: rgba(239, 68, 68, 0.1);
            color: #ef4444;
        }

        .btn-export {
            background: linear-gradient(135deg, rgb(var(--primary-rgb)), rgb(var(--primary-rgb)));
            color: white;
        }

        .btn-export:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(59, 130, 246, 0.4);
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

        @keyframes fadeIn {
            from { opacity: 0; }
            to { opacity: 1; }
        }

        @keyframes slideInUp {
            from { 
                opacity: 0;
                transform: translateY(30px);
            }
            to { 
                opacity: 1;
                transform: translateY(0);
            }
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

            .header-actions {
                width: 100%;
                justify-content: center;
                gap: 0.5rem;
            }

            .header-btn {
                flex: 1;
                justify-content: center;
                padding: 0.75rem 1rem;
                font-size: 0.875rem;
            }

            .search-form {
                flex-direction: column;
                align-items: stretch;
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

            .pagination-container {
                flex-direction: column;
                align-items: center;
                gap: 1rem;
                padding: 1rem;
            }

            .pagination {
                justify-content: center;
                flex-wrap: wrap;
            }

            .modal {
                margin: 0.5rem;
                padding: 1rem;
                max-height: 95vh;
                border-radius: 16px;
            }

            .modal-title {
                font-size: 1.125rem;
            }

            .export-actions {
                flex-direction: column;
            }

            .btn-modal {
                min-width: auto;
            }

            .table-container {
                overflow-x: auto;
            }

            .table {
                min-width: 700px;
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

            .pagination {
                gap: 0.25rem;
            }

            .page-link {
                padding: 0.5rem 0.75rem;
                font-size: 0.875rem;
            }

            .modal {
                margin: 0.25rem;
                max-height: 98vh;
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

    <!-- Export Modal -->
    <div class="modal-overlay" id="exportModal">
        <div class="modal">
            <div class="modal-header">
                <h3 class="modal-title">
                    <i class="fas fa-download"></i>
                    Exportar Transactions
                </h3>
                <button class="close-btn" onclick="closeExportModal()">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            
            <div class="export-options">
                <div class="export-option selected" data-format="csv">
                    <div>
                        <i class="fas fa-file-csv" style="font-size: 1.25rem; color: #10b981;"></i>
                    </div>
                    <div>
                        <strong>CSV (Excel)</strong>
                        <div style="color: var(--text-muted); font-size: 0.8rem;">Arquivo compatível com Excel</div>
                    </div>
                </div>
                
                <div class="export-option" data-format="pdf">
                    <div>
                        <i class="fas fa-file-pdf" style="font-size: 1.25rem; color: #ef4444;"></i>
                    </div>
                    <div>
                        <strong>PDF</strong>
                        <div style="color: var(--text-muted); font-size: 0.8rem;">Documento para impressão</div>
                    </div>
                </div>
            </div>

            <div style="margin-bottom: 1.5rem; padding: 0.75rem; background: rgba(59, 130, 246, 0.1); border-radius: 12px; font-size: 0.875rem;">
                <div style="display: flex; align-items: center; gap: 0.5rem; margin-bottom: 0.5rem;">
                    <i class="fas fa-info-circle" style="color: rgb(var(--primary-rgb));"></i>
                    <strong>Dados incluídos:</strong>
                </div>
                <div style="color: var(--text-muted);">
                    ID, Usuário, Valor, Status, Data
                    <?php if (!empty($search) || !empty($statusFilter)): ?>
                    <br><small><i class="fas fa-filter"></i> Filtros aplicados mantidos</small>
                    <?php endif; ?>
                </div>
            </div>

            <div class="export-actions">
                <button class="btn-modal btn-cancel" onclick="closeExportModal()">
                    <i class="fas fa-times"></i>
                    Cancel
                </button>
                <button class="btn-modal btn-export" onclick="performExport()">
                    <i class="fas fa-download"></i>
                    Exportar <?php echo count($exportTransactions); ?>
                </button>
            </div>
        </div>
    </div>

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
                            <h1>Transactions</h1>
                            <p>Gerencie todas as transações do sistema</p>
                        </div>
                    </div>
                    <div class="header-actions">
                        <button class="header-btn" onclick="openExportModal()">
                            <i class="fas fa-download"></i>
                            <span>Exportar</span>
                        </button>
                        <a href="add_transaction.php" class="header-btn primary">
                            <i class="fas fa-plus"></i>
                            <span>Nova Transação</span>
                        </a>
                    </div>
                </div>
            </header>

            <!-- Stats Grid -->
            <section class="stats-grid">
                <div class="stat-card">
                    <div class="stat-header">
                        <div>
                            <div class="stat-title">Total de Transactions</div>
                            <div class="stat-value"><?php echo number_format($stats['total']); ?></div>
                            <div class="stat-change">
                                <i class="fas fa-arrow-up"></i>
                                <span>+18% este mês</span>
                            </div>
                        </div>
                        <div class="stat-icon">
                            <i class="fas fa-exchange-alt"></i>
                        </div>
                    </div>
                </div>

                <div class="stat-card">
                    <div class="stat-header">
                        <div>
                            <div class="stat-title">Total Movimentado</div>
                            <div class="stat-value">R$ <?php echo number_format($stats['amount'] ?: 0, 2, ',', '.'); ?></div>
                            <div class="stat-change">
                                <i class="fas fa-arrow-up"></i>
                                <span>+24% este mês</span>
                            </div>
                        </div>
                        <div class="stat-icon">
                            <i class="fas fa-chart-line"></i>
                        </div>
                    </div>
                </div>

                <div class="stat-card">
                    <div class="stat-header">
                        <div>
                            <div class="stat-title">Transactions Pendings</div>
                            <div class="stat-value"><?php echo number_format($stats['pending']); ?></div>
                            <div class="stat-change">
                                <i class="fas fa-clock"></i>
                                <span>Aguardando</span>
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
                            <div class="stat-title">Transactions Concluídas</div>
                            <div class="stat-value"><?php echo number_format($stats['completed']); ?></div>
                            <div class="stat-change">
                                <i class="fas fa-check"></i>
                                <span>+15% este mês</span>
                            </div>
                        </div>
                        <div class="stat-icon">
                            <i class="fas fa-check-circle"></i>
                        </div>
                    </div>
                </div>
            </section>

            <!-- Search Section com filtros -->
            <section class="search-section">
                <form method="get" action="" class="search-form">
                    <div class="search-group">
                        <label class="search-label">
                            <i class="fas fa-search"></i>
                            Buscar usuário
                        </label>
                        <input 
                            type="text" 
                            name="search" 
                            class="search-input" 
                            placeholder="Digite o nome do usuário..." 
                            value="<?php echo htmlspecialchars($search); ?>"
                        >
                    </div>
                    
                    <div class="search-group">
                        <label class="search-label">
                            <i class="fas fa-filter"></i>
                            Status
                        </label>
                        <select name="status" class="filter-select">
                            <option value="all" <?php echo $statusFilter === 'all' || empty($statusFilter) ? 'selected' : ''; ?>>
                                Todos
                            </option>
                            <option value="PENDING" <?php echo $statusFilter === 'PENDING' ? 'selected' : ''; ?>>
                                Pending
                            </option>
                            <option value="PAID" <?php echo $statusFilter === 'PAID' ? 'selected' : ''; ?>>
                                Success
                            </option>
                            <option value="completed" <?php echo $statusFilter === 'completed' ? 'selected' : ''; ?>>
                                Aprovado
                            </option>
                            <option value="failed" <?php echo $statusFilter === 'failed' ? 'selected' : ''; ?>>
                                Failed
                            </option>
                            <option value="cancelled" <?php echo $statusFilter === 'cancelled' ? 'selected' : ''; ?>>
                                Cancelado
                            </option>
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
                            <th><i class="fas fa-user"></i> Usuário</th>
                            <th><i class="fas fa-dollar-sign"></i> Valor</th>
                            <th><i class="fas fa-tag"></i> Type</th>
                            <th><i class="fas fa-info-circle"></i> Status</th>
                            <th><i class="fas fa-calendar"></i> Data</th>
                            <th><i class="fas fa-tools"></i> Ações</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($transactions)): ?>
                            <tr>
                                <td colspan="7" style="text-align: center; padding: 3rem;">
                                    <i class="fas fa-search" style="font-size: 3rem; color: var(--text-muted); margin-bottom: 1rem;"></i>
                                    <div style="color: var(--text-muted);">
                                        <?php if (!empty($search) || !empty($statusFilter)): ?>
                                            No transaction found com os filtros aplicados.
                                        <?php else: ?>
                                            No transactions found.
                                        <?php endif; ?>
                                    </div>
                                </td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($transactions as $transaction): ?>
                                <tr>
                                    <td><strong>#<?php echo $transaction['id']; ?></strong></td>
                                    <td>
                                        <div class="user-info">
                                            <div class="user-avatar">
                                                <?php echo strtoupper(substr($transaction['username'], 0, 2)); ?>
                                            </div>
                                            <div class="user-details">
                                                <strong><?php echo htmlspecialchars($transaction['username']); ?></strong>
                                            </div>
                                        </div>
                                    </td>
                                    <td><strong>R$ <?php echo number_format($transaction['amount'], 2, ',', '.'); ?></strong></td>
                                    <td><span class="badge badge-type"><?php echo htmlspecialchars($transaction['type']); ?></span></td>
                                    <td>
                                        <?php
                                        $status = strtolower($transaction['status']);
                                        $statusClass = 'badge-pending';
                                        $statusIcon = 'fas fa-clock';
                                        if ($status === 'completed' || $status === 'paid' || $status === 'aprovado') {
                                            $statusClass = 'badge-completed';
                                            $statusIcon = 'fas fa-check';
                                        } elseif ($status === 'failed' || $status === 'rejeitado' || $status === 'cancelled') {
                                            $statusClass = 'badge-failed';
                                            $statusIcon = 'fas fa-times';
                                        }
                                        ?>
                                        <span class="badge <?php echo $statusClass; ?>">
                                            <i class="<?php echo $statusIcon; ?>"></i>
                                            <?php echo htmlspecialchars($transaction['status']); ?>
                                        </span>
                                    </td>
                                    <td><?php echo date('d/m/Y H:i', strtotime($transaction['created_at'])); ?></td>
                                    <td>
                                        <div class="action-buttons">
                                            <a href="edit_transaction.php?id=<?php echo $transaction['id']; ?>" class="btn btn-edit">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <a href="delete_transaction.php?id=<?php echo $transaction['id']; ?>" class="btn btn-delete" onclick="return confirm('Tem certeza que deseja excluir esta transação?');">
                                                <i class="fas fa-trash"></i>
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>

                <!-- ENHANCED Pagination com navegação completa -->
                <?php if ($totalPages > 1): ?>
                    <div class="pagination-container">
                        <div class="pagination-info">
                            Mostrando <?php echo ($offset + 1); ?>-<?php echo min($offset + $limit, $totalTransactions); ?> de <?php echo $totalTransactions; ?> transações
                            <?php if (!empty($search) || !empty($statusFilter)): ?>
                                <span style="color: rgb(var(--primary-rgb));">(filtrado)</span>
                            <?php endif; ?>
                        </div>
                        
                        <ul class="pagination">
                            <!-- Primeira página -->
                            <?php if ($page > 1): ?>
                                <li class="page-item">
                                    <a class="page-link" href="?page=1&search=<?php echo urlencode($search); ?>&status=<?php echo urlencode($statusFilter); ?>" title="Primeira página">
                                        <i class="fas fa-angle-double-left"></i>
                                    </a>
                                </li>
                            <?php endif; ?>

                            <!-- Página anterior -->
                            <li class="page-item">
                                <a class="page-link <?php echo $page <= 1 ? 'disabled' : ''; ?>" 
                                   href="<?php echo $page > 1 ? '?page=' . ($page - 1) . '&search=' . urlencode($search) . '&status=' . urlencode($statusFilter) : '#'; ?>"
                                   title="Página anterior">
                                    <i class="fas fa-chevron-left"></i>
                                    <span class="d-none d-sm-inline">Anterior</span>
                                </a>
                            </li>

                            <!-- Páginas numeradas */
                            <?php
                            $startPage = max(1, $page - 2);
                            $endPage = min($totalPages, $page + 2);
                            
                            for ($i = $startPage; $i <= $endPage; $i++):
                            ?>
                                <li class="page-item <?php echo $i === $page ? 'active' : ''; ?>">
                                    <a class="page-link" href="?page=<?php echo $i; ?>&search=<?php echo urlencode($search); ?>&status=<?php echo urlencode($statusFilter); ?>">
                                        <?php echo $i; ?>
                                    </a>
                                </li>
                            <?php endfor; ?>

                            <!-- Próxima página -->
                            <li class="page-item">
                                <a class="page-link <?php echo $page >= $totalPages ? 'disabled' : ''; ?>" 
                                   href="<?php echo $page < $totalPages ? '?page=' . ($page + 1) . '&search=' . urlencode($search) . '&status=' . urlencode($statusFilter) : '#'; ?>"
                                   title="Próxima página">
                                    <span class="d-none d-sm-inline">Próxima</span>
                                    <i class="fas fa-chevron-right"></i>
                                </a>
                            </li>

                            <!-- Última página -->
                            <?php if ($page < $totalPages): ?>
                                <li class="page-item">
                                    <a class="page-link" href="?page=<?php echo $totalPages; ?>&search=<?php echo urlencode($search); ?>&status=<?php echo urlencode($statusFilter); ?>" title="Última página">
                                        <i class="fas fa-angle-double-right"></i>
                                    </a>
                                </li>
                            <?php endif; ?>
                        </ul>
                    </div>
                <?php endif; ?>
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

        // EXPORT MODAL FUNCTIONALITY
        let selectedFormat = 'csv';
        
        function openExportModal() {
            document.getElementById('exportModal').classList.add('active');
            document.body.style.overflow = 'hidden';
        }
        
        function closeExportModal() {
            document.getElementById('exportModal').classList.remove('active');
            document.body.style.overflow = '';
        }
        
        // Export option selection
        document.querySelectorAll('.export-option').forEach(option => {
            option.addEventListener('click', function() {
                document.querySelectorAll('.export-option').forEach(opt => opt.classList.remove('selected'));
                this.classList.add('selected');
                selectedFormat = this.dataset.format;
            });
        });
        
        function performExport() {
            const exportData = <?php echo json_encode($exportTransactions); ?>;
            
            if (selectedFormat === 'csv') {
                exportToCSV(exportData);
            } else if (selectedFormat === 'pdf') {
                exportToPDF(exportData);
            }
            
            closeExportModal();
        }
        
        function exportToCSV(data) {
            try {
                // Preparar cabeçalhos e dados
                const headers = ['ID', 'Usuário', 'Valor (R$)', 'Status', 'Data'];
                const csvContent = [
                    headers.join(','),
                    ...data.map(row => [
                        row.id,
                        `"${row.username.replace(/"/g, '""')}"`,
                        `"${parseFloat(row.amount).toFixed(2).replace('.', ',')}"`,
                        `"${row.status.replace(/"/g, '""')}"`,
                        `"${new Date(row.created_at).toLocaleDateString('pt-BR')}"`
                    ].join(','))
                ].join('\n');
                
                // Adicionar BOM para UTF-8
                const bom = '\uFEFF';
                const blob = new Blob([bom + csvContent], { type: 'text/csv;charset=utf-8;' });
                
                // Criar e disparar download
                const link = document.createElement('a');
                const url = URL.createObjectURL(blob);
                link.setAttribute('href', url);
                link.setAttribute('download', `transacoes_${new Date().toISOString().split('T')[0]}.csv`);
                link.style.visibility = 'hidden';
                document.body.appendChild(link);
                link.click();
                document.body.removeChild(link);
                URL.revokeObjectURL(url);
                
                console.log('CSV export successful');
            } catch (error) {
                console.error('Error ao exportar CSV:', error);
                alert('Error ao exportar arquivo CSV. Tente novamente.');
            }
        }
        
        function exportToPDF(data) {
            try {
                // Criar janela para impressão
                const printWindow = window.open('', '_blank');
                const content = `
                    <!DOCTYPE html>
                    <html lang="pt-BR">
                    <head>
                        <title>Relatório de Transactions</title>
                        <meta charset="UTF-8">
                        <style>
                            * { margin: 0; padding: 0; box-sizing: border-box; }
                            body { 
                                font-family: Arial, sans-serif; 
                                margin: 20px; 
                                color: #333;
                                line-height: 1.4;
                            }
                            h1 { 
                                color: #3B82F6; 
                                text-align: center; 
                                border-bottom: 2px solid #3B82F6;
                                padding-bottom: 10px;
                                margin-bottom: 20px;
                            }
                            .header-info {
                                text-align: center;
                                margin-bottom: 30px;
                                color: #666;
                                background: #f8f9fa;
                                padding: 15px;
                                border-radius: 8px;
                            }
                            table { 
                                width: 100%; 
                                border-collapse: collapse; 
                                margin-top: 20px; 
                                font-size: 12px;
                            }
                            th, td { 
                                border: 1px solid #ddd; 
                                padding: 8px 6px; 
                                text-align: left; 
                            }
                            th { 
                                background-color: #f8f9fa; 
                                font-weight: bold;
                                color: #333;
                                font-size: 11px;
                                text-transform: uppercase;
                            }
                            tr:nth-child(even) {
                                background-color: #f8f9fa;
                            }
                            .footer {
                                margin-top: 30px;
                                text-align: center;
                                color: #666;
                                font-size: 10px;
                                border-top: 1px solid #ddd;
                                padding-top: 15px;
                            }
                            @media print {
                                body { margin: 0; font-size: 12px; }
                                .footer { position: fixed; bottom: 0; width: 100%; }
                                @page { margin: 15mm; }
                            }
                        </style>
                    </head>
                    <body>
                        <h1>Relatório de Transactions</h1>
                        <div class="header-info">
                            <p><strong>Data de Geração:</strong> ${new Date().toLocaleDateString('pt-BR', { 
                                weekday: 'long', 
                                year: 'numeric', 
                                month: 'long', 
                                day: 'numeric',
                                hour: '2-digit',
                                minute: '2-digit'
                            })}</p>
                            <p><strong>Total de Registros:</strong> ${data.length}</p>
                            ${<?php echo json_encode(!empty($search) || !empty($statusFilter)); ?> ? '<p><strong>Filtros Aplicados:</strong> Yes</p>' : ''}
                        </div>
                        <table>
                            <thead>
                                <tr>
                                    <th style="width: 10%;">ID</th>
                                    <th style="width: 25%;">Usuário</th>
                                    <th style="width: 15%;">Valor (R$)</th>
                                    <th style="width: 15%;">Status</th>
                                    <th style="width: 15%;">Date</th>
                                </tr>
                            </thead>
                            <tbody>
                                ${data.map(row => `
                                    <tr>
                                        <td>#${row.id}</td>
                                        <td>${row.username}</td>
                                        <td>R$ ${parseFloat(row.amount).toFixed(2).replace('.', ',')}</td>
                                        <td>${row.status}</td>
                                        <td>${new Date(row.created_at).toLocaleDateString('pt-BR')}</td>
                                    </tr>
                                `).join('')}
                            </tbody>
                        </table>
                        <div class="footer">
                            <p>Relatório gerado automaticamente pelo sistema dand thedministração</p>
                        </div>
                    </body>
                    </html>
                `;
                
                printWindow.document.write(content);
                printWindow.document.close();
                printWindow.focus();
                
                // Aguardar carregamento e imprimir
                setTimeout(() => {
                    printWindow.print();
                    printWindow.close();
                }, 250);
                
                console.log('PDF export successful');
            } catch (error) {
                console.error('Error ao exportar PDF:', error);
                alert('Error ao exportar arquivo PDF. Tente novamente.');
            }
        }

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

        // Keyboard shortcuts
        document.addEventListener('keydown', function(e) {
            // Ctrl + K to focus search
            if (e.ctrlKey && e.key === 'k') {
                e.preventDefault();
                document.querySelector('.search-input')?.focus();
            }
            
            // Escape to close modals
            if (e.key === 'Escape') {
                if (sidebar.classList.contains('open')) {
                    toggleSidebar();
                }
                if (document.getElementById('exportModal').classList.contains('active')) {
                    closeExportModal();
                }
            }
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
