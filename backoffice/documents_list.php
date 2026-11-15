
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

// Query para buscar todos os usuários com contagem de documentos para o modal de exportação
$usersQuery = "
    SELECT 
        u.id,
        u.name,
        u.username,
        COUNT(d.id) as document_count
    FROM users u
    LEFT JOIN users_documents d ON u.id = d.user_id
    GROUP BY u.id, u.name, u.username
    ORDER BY u.name ASC, u.username ASC
";

$usersStmt = $pdo->prepare($usersQuery);
$usersStmt->execute();
$usersForExport = $usersStmt->fetchAll(PDO::FETCH_ASSOC);

// Processar exportação quando solicitada
if (isset($_GET['export_user_id']) && is_numeric($_GET['export_user_id'])) {
    $export_user_id = (int)$_GET['export_user_id'];
    
    // Buscar documentos do usuário
    $exportDocQuery = "
        SELECT d.file_path, d.document_id, u.username, u.name
        FROM users_documents d
        JOIN users u ON u.id = d.user_id
        WHERE d.user_id = :user_id
        ORDER BY d.document_id ASC
    ";
    
    $exportDocStmt = $pdo->prepare($exportDocQuery);
    $exportDocStmt->bindValue(':user_id', $export_user_id, PDO::PARAM_INT);
    $exportDocStmt->execute();
    $userDocuments = $exportDocStmt->fetchAll(PDO::FETCH_ASSOC);
    
    if (empty($userDocuments)) {
        header('Content-Type: application/json');
        echo json_encode(['success' => false, 'message' => 'Usuário não possui documentos']);
        exit;
    }
    
    // Criar ZIP com os documentos
    $zip = new ZipArchive();
    $username = $userDocuments[0]['username'];
    $name = $userDocuments[0]['name'] ?: $username;
    $zipFileName = "documentos_" . preg_replace('/[^a-zA-Z0-9_-]/', '_', $username) . "_" . date('Y-m-d_H-i-s') . ".zip";
    $zipFilePath = sys_get_temp_dir() . DIRECTORY_SEPARATOR . $zipFileName;
    
    if ($zip->open($zipFilePath, ZipArchive::CREATE) === TRUE) {
        foreach ($userDocuments as $index => $doc) {
            $filePath = $doc['file_path'];
            
            // Ajustar caminho do arquivo
            if (file_exists($filePath)) {
                $fileName = "documento_" . getDocumentType($doc['document_id']) . "_" . ($index + 1) . "." . pathinfo($filePath, PATHINFO_EXTENSION);
                $zip->addFile($filePath, $fileName);
            }
        }
        
        $zip->close();
        
        // Submit arquivo para download
        if (file_exists($zipFilePath)) {
            header('Content-Type: application/zip');
            header('Content-Disposition: attachment; filename="' . $zipFileName . '"');
            header('Content-Length: ' . filesize($zipFilePath));
            header('Cache-Control: no-cache, must-revalidate');
            header('Pragma: no-cache');
            
            readfile($zipFilePath);
            unlink($zipFilePath); // Deletar arquivo temporário
            exit;
        }
    }
    
    header('Content-Type: application/json');
    echo json_encode(['success' => false, 'message' => 'Error ao criar arquivo ZIP']);
    exit;
}

// Processar as requisições AJAX (aprovar ou recusar documentos)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    header('Content-Type: application/json');

    $document_id = isset($_POST['document_id']) ? (int)$_POST['document_id'] : 0;
    $action = isset($_POST['action']) ? trim($_POST['action']) : '';

    if ($document_id > 0 && in_array($action, ['approve', 'reject'])) {
        try {
            if ($action === 'approve') {
                // Aprovar o documento
                $status = 1; // 1 = aprovado
                $stmt = $pdo->prepare("UPDATE users_documents SET approved = :status WHERE id = :document_id");
                $stmt->bindValue(':status', $status, PDO::PARAM_INT);
                $stmt->bindValue(':document_id', $document_id, PDO::PARAM_INT);
                $stmt->execute();
            
                // Buscar o user_id para realizar a verificação
                $stmt_document = $pdo->prepare("SELECT user_id FROM users_documents WHERE id = :document_id");
                $stmt_document->bindValue(':document_id', $document_id, PDO::PARAM_INT);
                $stmt_document->execute();
                $document = $stmt_document->fetch(PDO::FETCH_ASSOC);
            
                if ($document) {
                    // Verificar se todos os documentos 1-4 do usuário estão aprovados
                    $stmt_check = $pdo->prepare("
                        SELECT COUNT(*) 
                        FROM users_documents 
                        WHERE user_id = :user_id AND document_id IN (1, 2, 3, 4) AND approved = 1
                    ");
                    $stmt_check->bindValue(':user_id', $document['user_id'], PDO::PARAM_INT);
                    $stmt_check->execute();
                    $approved_documents = $stmt_check->fetchColumn();

                    // Se todos os documentos 1-4 estiverem aprovados
                    if ($approved_documents == 4) {
                        // Atualizar a coluna documents_checked para 1
                        $stmt_user = $pdo->prepare("UPDATE users SET documents_checked = 1 WHERE id = :user_id");
                        $stmt_user->bindValue(':user_id', $document['user_id'], PDO::PARAM_INT);
                        $stmt_user->execute();

                        echo json_encode([
                            'success' => true, 
                            'message' => 'Documento aprovado! Todos os documentos do usuário foram verificados.',
                            'type' => 'success'
                        ]);
                    } else {
                        echo json_encode([
                            'success' => true, 
                            'message' => 'Documento aprovado com sucesso!',
                            'type' => 'success'
                        ]);
                    }
                }
            
            } elseif ($action === 'reject') {
                // Recusar o documento (deletar)
                $stmt = $pdo->prepare("DELETE FROM users_documents WHERE id = :document_id");
                $stmt->bindValue(':document_id', $document_id, PDO::PARAM_INT);
                $stmt->execute();

                echo json_encode([
                    'success' => true, 
                    'message' => 'Documento recusado e removido com sucesso!',
                    'type' => 'error'
                ]);
            }
        } catch (PDOException $e) {
            echo json_encode([
                'success' => false, 
                'message' => 'Error ao processar a ação: ' . $e->getMessage(),
                'type' => 'error'
            ]);
        }
    } else {
        echo json_encode([
            'success' => false, 
            'message' => 'Dados inválidos fornecidos.',
            'type' => 'error'
        ]);
    }

    exit;
}

// Configuração de paginação
$limit = 10;
$page = isset($_GET['page']) ? max(1, (int)$_GET['page']) : 1;
$offset = ($page - 1) * $limit;

// Filtros
$search = isset($_GET['search']) ? trim($_GET['search']) : '';

// Query principal com paginação
$query = "
    SELECT DISTINCT d.id AS document_unique_id, u.id AS user_id, u.name, u.username, d.document_id, d.file_path, d.approved 
    FROM users_documents d
    JOIN users u ON u.id = d.user_id
    WHERE d.approved = 0
";

$conditions = [];
$params = [];

if (!empty($search)) {
    $conditions[] = "u.username LIKE :search";
    $params[':search'] = '%' . $search . '%';
}

if (!empty($conditions)) {
    $query .= " AND " . implode(" AND ", $conditions);
}

$query .= " ORDER BY d.id DESC LIMIT :limit OFFSET :offset";

$stmt = $pdo->prepare($query);
foreach ($params as $key => $value) {
    $stmt->bindValue($key, $value, PDO::PARAM_STR);
}
$stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
$stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
$stmt->execute();
$documents = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Total para paginação
$totalQuery = "
    SELECT COUNT(DISTINCT d.id) 
    FROM users_documents d
    JOIN users u ON u.id = d.user_id
    WHERE d.approved = 0
";
if (!empty($conditions)) {
    $totalQuery .= " AND " . implode(" AND ", $conditions);
}

$totalStmt = $pdo->prepare($totalQuery);
foreach ($params as $key => $value) {
    $totalStmt->bindValue($key, $value, PDO::PARAM_STR);
}
$totalStmt->execute();
$totalDocuments = $totalStmt->fetchColumn();
$totalPages = ceil($totalDocuments / $limit);

// Dados para exportação
$exportQuery = "
    SELECT DISTINCT d.id AS document_unique_id, u.id AS user_id, u.name, u.username, d.document_id, d.file_path, d.approved 
    FROM users_documents d
    JOIN users u ON u.id = d.user_id
    WHERE d.approved = 0
";
if (!empty($conditions)) {
    $exportQuery .= " AND " . implode(" AND ", $conditions);
}
$exportQuery .= " ORDER BY d.id DESC";

$exportStmt = $pdo->prepare($exportQuery);
foreach ($params as $key => $value) {
    $exportStmt->bindValue($key, $value, PDO::PARAM_STR);
}
$exportStmt->execute();
$exportDocuments = $exportStmt->fetchAll(PDO::FETCH_ASSOC);

// Estatísticas
$queryStats = [
    'total' => "SELECT COUNT(DISTINCT d.id) FROM users_documents d WHERE d.approved = 0",
    'pending' => "SELECT COUNT(DISTINCT d.id) FROM users_documents d WHERE d.approved = 0",
    'approved' => "SELECT COUNT(DISTINCT d.id) FROM users_documents d WHERE d.approved = 1",
    'users_pending' => "SELECT COUNT(DISTINCT u.id) FROM users u JOIN users_documents d ON u.id = d.user_id WHERE d.approved = 0"
];

$stats = [];
foreach ($queryStats as $key => $sql) {
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
    <title>RPay - Aprovação de Documentos</title>
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

        .search-input {
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

        .search-input:focus {
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

        .badge-approved {
            background: rgba(16, 185, 129, 0.2);
            color: #10b981;
            border: 1px solid rgba(16, 185, 129, 0.3);
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
            flex-direction: column;
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
            justify-content: center;
            gap: 0.375rem;
            white-space: nowrap;
        }

        .btn-approve {
            background: linear-gradient(135deg, #10b981, #059669);
            color: white;
        }

        .btn-approve:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(16, 185, 129, 0.4);
        }

        .btn-reject {
            background: linear-gradient(135deg, #ef4444, #dc2626);
            color: white;
        }

        .btn-reject:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(239, 68, 68, 0.4);
        }

        .btn-view {
            background: linear-gradient(135deg, rgb(var(--primary-rgb)), rgb(var(--primary-rgb)));
            color: white;
        }

        .btn-view:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(59, 130, 246, 0.4);
            color: white;
            text-decoration: none;
        }

        .btn:disabled {
            opacity: 0.6;
            cursor: not-allowed;
            transform: none !important;
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

        /* Enhanced Pagination */
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

        /* Modal/Popup Styles */
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

        /* Fixed Popup Notification - Top Right */
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

        /* Estilos específicos para o modal de exportação */
        .export-modal {
            max-width: 600px;
            max-height: 80vh;
        }

        .export-search {
            margin-bottom: 1.5rem;
            padding-bottom: 1rem;
            border-bottom: 1px solid var(--border-color);
        }

        .export-users-container {
            max-height: 400px;
            overflow-y: auto;
            margin-bottom: 1rem;
        }

        .export-users-container::-webkit-scrollbar {
            width: 6px;
        }

        .export-users-container::-webkit-scrollbar-track {
            background: rgba(59, 130, 246, 0.1);
            border-radius: 3px;
        }

        .export-users-container::-webkit-scrollbar-thumb {
            background: rgb(var(--primary-rgb));
            border-radius: 3px;
        }

        .export-user-item {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 1rem;
            border: 1px solid var(--border-color);
            border-radius: 12px;
            margin-bottom: 0.75rem;
            transition: all 0.3s ease;
            background: rgba(59, 130, 246, 0.02);
        }

        .export-user-item:hover {
            background: rgba(59, 130, 246, 0.05);
            border-color: rgba(59, 130, 246, 0.3);
            transform: translateY(-2px);
            box-shadow: var(--shadow-md);
        }

        .export-user-info {
            display: flex;
            align-items: center;
            gap: 1rem;
            flex: 1;
        }

        .user-name {
            font-weight: 500;
            color: var(--text-primary);
            font-size: 1rem;
        }

        .user-username {
            color: var(--text-muted);
            font-size: 0.875rem;
            margin-bottom: 0.25rem;
        }

        .user-docs-count {
            display: flex;
            align-items: center;
            gap: 0.375rem;
            color: rgb(var(--primary-rgb));
            font-size: 0.75rem;
            font-weight: 500;
        }

        .export-user-btn {
            padding: 0.5rem 1rem;
            background: linear-gradient(135deg, rgb(var(--primary-rgb)), rgb(var(--primary-rgb)));
            border: none;
            border-radius: 8px;
            color: white;
            font-size: 0.875rem;
            font-weight: 400;
            cursor: pointer;
            transition: all 0.3s ease;
            display: flex;
            align-items: center;
            gap: 0.375rem;
            white-space: nowrap;
        }

        .export-user-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(59, 130, 246, 0.4);
        }

        .export-user-btn:disabled {
            opacity: 0.6;
            cursor: not-allowed;
            transform: none !important;
        }

        /* Modal sem documentos */
        .no-docs-modal {
            max-width: 400px;
            text-align: center;
        }

        .no-docs-content {
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 1rem;
            margin-bottom: 1.5rem;
        }

        .no-docs-icon {
            width: 80px;
            height: 80px;
            background: rgba(245, 158, 11, 0.2);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #f59e0b;
            font-size: 2.5rem;
        }

        .no-docs-text h4 {
            color: var(--text-primary);
            font-weight: 500;
            margin-bottom: 0.5rem;
        }

        .no-docs-text p {
            color: var(--text-muted);
            margin: 0;
        }

        .modal-actions {
            display: flex;
            justify-content: center;
            gap: 0.75rem;
        }

        /* Mobile Export Loading */
        .mobile-export-loading {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: rgba(0, 0, 0, 0.8);
            backdrop-filter: blur(8px);
            z-index: 9999;
            display: none;
            align-items: center;
            justify-content: center;
            flex-direction: column;
            color: white;
        }

        .mobile-export-loading.show {
            display: flex;
        }

        .mobile-export-spinner {
            width: 60px;
            height: 60px;
            border: 4px solid rgba(255, 255, 255, 0.3);
            border-top-color: rgb(var(--primary-rgb));
            border-radius: 50%;
            animation: spin 1s linear infinite;
            margin-bottom: 1rem;
        }

        .mobile-export-text {
            font-size: 1.1rem;
            font-weight: 500;
        }

        /* Animações específicas */
        @keyframes modalSlideIn {
            from {
                opacity: 0;
                transform: translateY(30px) scale(0.95);
            }
            to {
                opacity: 1;
                transform: translateY(0) scale(1);
            }
        }

        .modal-overlay.active .modal {
            animation: modalSlideIn 0.4s cubic-bezier(0.4, 0, 0.2, 1);
        }

        /* Estados de carregamento */
        .loading-state {
            opacity: 0.7;
            pointer-events: none;
        }

        .loading-state::after {
            content: '';
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            width: 20px;
            height: 20px;
            border: 2px solid rgba(59, 130, 246, 0.3);
            border-top-color: rgb(var(--primary-rgb));
            border-radius: 50%;
            animation: spin 1s linear infinite;
        }

        @keyframes spin {
            to { transform: translate(-50%, -50%) rotate(360deg); }
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

            .table-container {
                overflow-x: auto;
            }

            .table {
                min-width: 900px;
            }

            .action-buttons {
                flex-direction: row;
                flex-wrap: wrap;
                gap: 0.25rem;
            }

            .btn {
                font-size: 0.75rem;
                padding: 0.4rem 0.6rem;
            }

            /* Mobile popup positioning */
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

            .export-modal {
                margin: 1rem;
                max-width: calc(100vw - 2rem);
            }
            
            .export-user-item {
                flex-direction: column;
                gap: 1rem;
                text-align: center;
            }
            
            .export-user-info {
                justify-content: center;
            }
            
            .export-user-btn {
                align-self: stretch;
                justify-content: center;
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

            /* Extra small mobile popup */
            .popup-notification {
                top: 0.5rem;
                right: 0.5rem;
                left: 0.5rem;
                padding: 1rem 1.5rem;
            }

            .popup-content {
                gap: 0.75rem;
            }

            .popup-message {
                font-size: 0.9rem;
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
                            <i class="fas fa-clipboard-check"></i>
                        </div>
                        <div class="header-text">
                            <h1>Aprovação de Documentos</h1>
                            <p>Aprove ou recuse documentos enviados pelos usuários</p>
                        </div>
                    </div>
                    <div class="header-actions">
                        <button class="header-btn" onclick="openExportModal()">
                            <i class="fas fa-download"></i>
                            <span>Exportar</span>
                        </button>
                    </div>
                </div>
            </header>

            <!-- Stats Grid -->
            <section class="stats-grid">
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
                            <div class="stat-title">Total de Documentos</div>
                            <div class="stat-value"><?php echo number_format($stats['total'] + $stats['approved']); ?></div>
                            <div class="stat-change">
                                <i class="fas fa-file-alt"></i>
                                <span>Sistema completo</span>
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
                            <div class="stat-title">Users Pendings</div>
                            <div class="stat-value"><?php echo number_format($stats['users_pending']); ?></div>
                            <div class="stat-change">
                                <i class="fas fa-user-clock"></i>
                                <span>Com documentos</span>
                            </div>
                        </div>
                        <div class="stat-icon">
                            <i class="fas fa-user-clock"></i>
                        </div>
                    </div>
                </div>
            </section>

            <!-- Search Section -->
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
                            <th><i class="fas fa-tools"></i> Ações</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($documents)): ?>
                            <tr>
                                <td colspan="7" style="text-align: center; padding: 3rem;">
                                    <i class="fas fa-file-search" style="font-size: 3rem; color: var(--text-muted); margin-bottom: 1rem;"></i>
                                    <div style="color: var(--text-muted);">
                                        <?php if (!empty($search)): ?>
                                            Nenhum documento encontrado com os filtros aplicados.
                                        <?php else: ?>
                                            Nenhum documento pendente encontrado.
                                        <?php endif; ?>
                                    </div>
                                </td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($documents as $document): ?>
                                <tr id="doc-<?php echo htmlspecialchars($document['document_unique_id']); ?>">
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
                                        <a href="<?php echo adjustFilePath($document['file_path']); ?>" target="_blank" class="btn btn-view">
                                            <i class="fas fa-external-link-alt"></i>
                                            <span>Visualizar</span>
                                        </a>
                                    </td>
                                    <td>
                                        <span class="badge badge-pending">
                                            <i class="fas fa-clock"></i> Pending
                                        </span>
                                    </td>
                                    <td>
                                        <div class="action-buttons">
                                            <button onclick="handleDocumentAction(<?php echo $document['document_unique_id']; ?>, 'approve')" class="btn btn-approve">
                                                <i class="fas fa-check"></i>
                                                <span>Aprovar</span>
                                            </button>
                                            <button onclick="handleDocumentAction(<?php echo $document['document_unique_id']; ?>, 'reject')" class="btn btn-reject">
                                                <i class="fas fa-times"></i>
                                                <span>Recusar</span>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>

                <!-- Enhanced Pagination -->
                <?php if ($totalPages > 1): ?>
                    <div class="pagination-container">
                        <div class="pagination-info">
                            Mostrando <?php echo ($offset + 1); ?>-<?php echo min($offset + $limit, $totalDocuments); ?> de <?php echo $totalDocuments; ?> documentos
                            <?php if (!empty($search)): ?>
                                <span style="color: rgb(var(--primary-rgb));">(filtrado)</span>
                            <?php endif; ?>
                        </div>
                        
                        <ul class="pagination">
                            <!-- Primeira página -->
                            <?php if ($page > 1): ?>
                                <li class="page-item">
                                    <a class="page-link" href="?page=1&search=<?php echo urlencode($search); ?>" title="Primeira página">
                                        <i class="fas fa-angle-double-left"></i>
                                    </a>
                                </li>
                            <?php endif; ?>

                            <!-- Página anterior -->
                            <li class="page-item">
                                <a class="page-link <?php echo $page <= 1 ? 'disabled' : ''; ?>" 
                                   href="<?php echo $page > 1 ? '?page=' . ($page - 1) . '&search=' . urlencode($search) : '#'; ?>"
                                   title="Página anterior">
                                    <i class="fas fa-chevron-left"></i>
                                    <span class="d-none d-sm-inline">Anterior</span>
                                </a>
                            </li>

                            <!-- Páginas numeradas -->
                            <?php
                            $startPage = max(1, $page - 2);
                            $endPage = min($totalPages, $page + 2);
                            
                            for ($i = $startPage; $i <= $endPage; $i++):
                            ?>
                                <li class="page-item <?php echo $i === $page ? 'active' : ''; ?>">
                                    <a class="page-link" href="?page=<?php echo $i; ?>&search=<?php echo urlencode($search); ?>">
                                        <?php echo $i; ?>
                                    </a>
                                </li>
                            <?php endfor; ?>

                            <!-- Próxima página -->
                            <li class="page-item">
                                <a class="page-link <?php echo $page >= $totalPages ? 'disabled' : ''; ?>" 
                                   href="<?php echo $page < $totalPages ? '?page=' . ($page + 1) . '&search=' . urlencode($search) : '#'; ?>"
                                   title="Próxima página">
                                    <span class="d-none d-sm-inline">Próxima</span>
                                    <i class="fas fa-chevron-right"></i>
                                </a>
                            </li>

                            <!-- Última página -->
                            <?php if ($page < $totalPages): ?>
                                <li class="page-item">
                                    <a class="page-link" href="?page=<?php echo $totalPages; ?>&search=<?php echo urlencode($search); ?>" title="Última página">
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

    <!-- Modal de Exportação -->
    <div id="exportModal" class="modal-overlay">
        <div class="modal export-modal">
            <div class="modal-header">
                <h3 class="modal-title">
                    <i class="fas fa-download"></i>
                    Exportar Documentos
                </h3>
                <button class="close-btn" onclick="closeExportModal()">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            
            <div class="modal-body">
                <div class="export-search">
                    <div class="search-group">
                        <label class="search-label">
                            <i class="fas fa-search"></i>
                            Buscar usuário
                        </label>
                        <input 
                            type="text" 
                            id="exportSearchInput"
                            class="search-input" 
                            placeholder="Digite o nome ou username..." 
                            onkeyup="filterExportUsers()"
                        >
                    </div>
                </div>
                
                <div class="export-users-container">
                    <div class="export-users-list" id="exportUsersList">
                        <?php foreach ($usersForExport as $user): ?>
                            <div class="export-user-item" data-username="<?php echo strtolower(htmlspecialchars($user['username'])); ?>" data-name="<?php echo strtolower(htmlspecialchars($user['name'] ?: '')); ?>">
                                <div class="export-user-info">
                                    <div class="user-avatar">
                                        <?php echo strtoupper(substr($user['name'] ?: $user['username'], 0, 2)); ?>
                                    </div>
                                    <div class="user-details">
                                        <div class="user-name"><?php echo htmlspecialchars($user['name'] ?: $user['username']); ?></div>
                                        <div class="user-username">@<?php echo htmlspecialchars($user['username']); ?></div>
                                        <div class="user-docs-count">
                                            <i class="fas fa-file-alt"></i>
                                            <?php echo $user['document_count']; ?> documento(s)
                                        </div>
                                    </div>
                                </div>
                                <button class="export-user-btn" onclick="exportUserDocuments(<?php echo $user['id']; ?>, '<?php echo htmlspecialchars($user['username']); ?>', <?php echo $user['document_count']; ?>)">
                                    <i class="fas fa-download"></i>
                                    Exportar
                                </button>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal de confirmação de usuário sem documentos -->
    <div id="noDocumentsModal" class="modal-overlay">
        <div class="modal no-docs-modal">
            <div class="modal-header">
                <h3 class="modal-title">
                    <i class="fas fa-exclamation-triangle"></i>
                    Nenhum Documento
                </h3>
                <button class="close-btn" onclick="closeNoDocumentsModal()">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            
            <div class="modal-body">
                <div class="no-docs-content">
                    <div class="no-docs-icon">
                        <i class="fas fa-folder-open"></i>
                    </div>
                    <div class="no-docs-text">
                        <h4 id="noDocsUserName">Usuário</h4>
                        <p>Este usuário não possui nenhum documento para exportar.</p>
                    </div>
                </div>
                <div class="modal-actions">
                    <button class="btn btn-primary" onclick="closeNoDocumentsModal()">
                        <i class="fas fa-check"></i>
                        Understood
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Mobile Export Loading -->
    <div id="mobileExportLoading" class="mobile-export-loading">
        <div class="mobile-export-spinner"></div>
        <div class="mobile-export-text">Preparando download...</div>
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

        // Improved popup functionality
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

        // Document action handler
        async function handleDocumentAction(documentId, action) {
            const actionText = action === 'approve' ? 'aprovar' : 'recusar';
            
            if (action === 'reject' && !confirm(`Tem certeza que deseja ${actionText} este documento?`)) {
                return;
            }

            const row = document.getElementById(`doc-${documentId}`);
            const buttons = row.querySelectorAll('.btn');
            buttons.forEach(btn => btn.disabled = true);

            try {
                const formData = new FormData();
                formData.append('document_id', documentId);
                formData.append('action', action);

                const response = await fetch('', {
                    method: 'POST',
                    body: formData
                });

                const result = await response.json();

                if (result.success) {
                    showPopup(result.message, result.type);
                    
                    setTimeout(() => {
                        row.style.transition = 'all 0.5s ease';
                        row.style.opacity = '0';
                        row.style.transform = 'translateX(-100%)';
                        setTimeout(() => {
                            row.remove();
                            
                            const tbody = document.querySelector('.table tbody');
                            if (tbody.children.length === 0) {
                                tbody.innerHTML = `
                                    <tr>
                                        <td colspan="7" style="text-align: center; padding: 3rem;">
                                            <i class="fas fa-file-search" style="font-size: 3rem; color: var(--text-muted); margin-bottom: 1rem;"></i>
                                            <div style="color: var(--text-muted);">Nenhum documento pendente encontrado.</div>
                                        </td>
                                    </tr>
                                `;
                            }
                        }, 500);
                    }, 1000);
                } else {
                    showPopup(result.message, 'error');
                    buttons.forEach(btn => btn.disabled = false);
                }
            } catch (error) {
                showPopup('Error ao processar solicitação. Tente novamente.', 'error');
                buttons.forEach(btn => btn.disabled = false);
            }
        }

        // Função para abrir modal de exportação
        function openExportModal() {
            const modal = document.getElementById('exportModal');
            modal.classList.add('active');
            document.body.style.overflow = 'hidden';
            
            // Focar no campo de busca
            setTimeout(() => {
                document.getElementById('exportSearchInput').focus();
            }, 300);
        }

        // Função para fechar modal de exportação
        function closeExportModal() {
            const modal = document.getElementById('exportModal');
            modal.classList.remove('active');
            document.body.style.overflow = '';
            
            // Limpar busca
            document.getElementById('exportSearchInput').value = '';
            filterExportUsers();
        }

        // Função para filtrar usuários na busca
        function filterExportUsers() {
            const searchTerm = document.getElementById('exportSearchInput').value.toLowerCase();
            const userItems = document.querySelectorAll('.export-user-item');
            
            userItems.forEach(item => {
                const username = item.dataset.username;
                const name = item.dataset.name;
                
                if (username.includes(searchTerm) || name.includes(searchTerm)) {
                    item.style.display = 'flex';
                } else {
                    item.style.display = 'none';
                }
            });
        }

        // Detectar se é mobile
        function isMobile() {
            return /Android|webOS|iPhone|iPad|iPod|BlackBerry|IEMobile|Opera Mini/i.test(navigator.userAgent) || window.innerWidth <= 768;
        }

        // Improved export function with mobile support
        async function exportUserDocuments(userId, username, documentCount) {
            if (documentCount === 0) {
                showNoDocumentsModal(username);
                return;
            }
            
            // Mostrar loading específico para mobile
            if (isMobile()) {
                document.getElementById('mobileExportLoading').classList.add('show');
            }
            
            // Mostrar estado de carregamento no modal
            const modal = document.getElementById('exportModal');
            modal.classList.add('loading-state');
            
            try {
                if (isMobile()) {
                    // Para mobile, abrir em nova aba
                    const exportUrl = `${window.location.pathname}?export_user_id=${userId}`;
                    window.open(exportUrl, '_blank');
                    
                    setTimeout(() => {
                        document.getElementById('mobileExportLoading').classList.remove('show');
                        closeExportModal();
                        showPopup(`Download iniciado! Verifiquand the pasta de downloads para os documentos de ${username}.`, 'success', 8000);
                    }, 2000);
                } else {
                    // Para desktop, usar o método anterior
                    const link = document.createElement('a');
                    link.href = `?export_user_id=${userId}`;
                    link.download = '';
                    document.body.appendChild(link);
                    link.click();
                    document.body.removeChild(link);
                    
                    setTimeout(() => {
                        closeExportModal();
                        showPopup(`Documentos de ${username} exportados com sucesso!`, 'success', 6000);
                    }, 1000);
                }
                
            } catch (error) {
                console.error('Error na exportação:', error);
                showPopup('Error ao exportar documentos. Tente novamente.', 'error');
                if (isMobile()) {
                    document.getElementById('mobileExportLoading').classList.remove('show');
                }
            } finally {
                modal.classList.remove('loading-state');
            }
        }

        // Função para mostrar modal de usuário sem documentos
        function showNoDocumentsModal(username) {
            document.getElementById('noDocsUserName').textContent = username;
            const modal = document.getElementById('noDocumentsModal');
            modal.classList.add('active');
        }

        // Função para fechar modal de usuário sem documentos
        function closeNoDocumentsModal() {
            const modal = document.getElementById('noDocumentsModal');
            modal.classList.remove('active');
        }

        // Event listeners para fechar modais clicando fora
        document.getElementById('exportModal').addEventListener('click', function(e) {
            if (e.target === this) {
                closeExportModal();
            }
        });

        document.getElementById('noDocumentsModal').addEventListener('click', function(e) {
            if (e.target === this) {
                closeNoDocumentsModal();
            }
        });

        // Atalhos de teclado para os modais
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') {
                if (document.getElementById('exportModal').classList.contains('active')) {
                    closeExportModal();
                }
                if (document.getElementById('noDocumentsModal').classList.contains('active')) {
                    closeNoDocumentsModal();
                }
                if (document.getElementById('popup-notification').classList.contains('show')) {
                    document.getElementById('popup-notification').classList.remove('show');
                }
                if (document.getElementById('mobileExportLoading').classList.contains('show')) {
                    document.getElementById('mobileExportLoading').classList.remove('show');
                }
            }
        });

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
            if (e.ctrlKey && e.key === 'k') {
                e.preventDefault();
                document.querySelector('.search-input')?.focus();
            }
            
            if (e.key === 'Escape') {
                if (sidebar.classList.contains('open')) {
                    toggleSidebar();
                }
                if (document.getElementById('popup-notification').classList.contains('show')) {
                    document.getElementById('popup-notification').classList.remove('show');
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
