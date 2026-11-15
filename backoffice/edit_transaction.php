
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

// Obter o ID da transação
if (!isset($_GET['id'])) {
    die("ID da transação não fornecido.");
}

$transaction_id = $_GET['id'];

// Consultar os dados da transação
$query = "SELECT * FROM transactions WHERE id = :id";
$stmt = $pdo->prepare($query);
$stmt->execute([':id' => $transaction_id]);
$transaction = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$transaction) {
    die("Transação não encontrada.");
}

$message = "";
$messageType = "";

// Atualizar os dados da transação se o formulário for enviado
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        if (isset($_POST['update'])) {
            $status = $_POST['status'];
            $descricao = $_POST['descricao'];
            $nome = $_POST['nome'];
            $tax = $_POST['tax'];
            $amount = $_POST['amount'];
            $external_id = $_POST['external_id'];

            $query = "UPDATE transactions 
                      SET status = :status, descricao = :descricao, nome = :nome, tax = :tax, amount = :amount, external_id = :external_id 
                      WHERE id = :id";
            $stmt = $pdo->prepare($query);
            $stmt->execute([
                ':status' => $status,
                ':descricao' => $descricao,
                ':nome' => $nome,
                ':tax' => $tax,
                ':amount' => $amount,
                ':external_id' => $external_id,
                ':id' => $transaction_id
            ]);

            // Atualizar o array da transação para refletir as alterações
            $transaction = array_merge($transaction, [
                'status' => $status,
                'descricao' => $descricao,
                'nome' => $nome,
                'tax' => $tax,
                'amount' => $amount,
                'external_id' => $external_id
            ]);

            $message = "Transação atualizada com sucesso!";
            $messageType = "success";
        }

        if (isset($_POST['cancel'])) {
            $query = "UPDATE transactions SET status = 'CANCELLED' WHERE id = :id";
            $stmt = $pdo->prepare($query);
            $stmt->execute([':id' => $transaction_id]);

            $transaction['status'] = 'CANCELLED';
            $message = "Transação cancelada com sucesso!";
            $messageType = "success";
        }

        if (isset($_POST['approve'])) {
            $query = "UPDATE transactions SET status = 'PAID' WHERE id = :id";
            $stmt = $pdo->prepare($query);
            $stmt->execute([':id' => $transaction_id]);

            $transaction['status'] = 'PAID';
            $message = "Transação aprovada com sucesso!";
            $messageType = "success";
        }

        if (isset($_POST['reverse']) && $transaction['status'] === 'PAID') {
            $query = "UPDATE transactions SET status = 'REVERSED' WHERE id = :id";
            $stmt = $pdo->prepare($query);
            $stmt->execute([':id' => $transaction_id]);

            // Atualizar o saldo do usuário
            $query = "UPDATE users SET balance = balance - :amount WHERE id = :user_id";
            $stmt = $pdo->prepare($query);
            $stmt->execute([':amount' => $transaction['amount'], ':user_id' => $transaction['user_id']]);

            $transaction['status'] = 'REVERSED';
            $message = "Transação estornada com sucesso!";
            $messageType = "success";
        }

        if (isset($_POST['delete'])) {
            $query = "DELETE FROM transactions WHERE id = :id";
            $stmt = $pdo->prepare($query);
            $stmt->execute([':id' => $transaction_id]);

            $message = "Transação deletada com sucesso!";
            $messageType = "success";
            
            // Redirect after deletion
            echo "<script>
                setTimeout(() => {
                    window.history.back();
                }, 2000);
            </script>";
        }
    } catch (Exception $e) {
        $message = "Error ao processar ação: " . $e->getMessage();
        $messageType = "error";
    }
}
?>
<!DOCTYPE html>
<html lang="pt-BR" dir="ltr" data-nav-layout="vertical" data-theme-mode="light" data-header-styles="light" data-menu-styles="light" data-toggled="close">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin - Editar Transação</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@200;300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    
    <style>
        :root {
            --primary-rgb: 59, 130, 246;
            --secondary-rgb: 16, 185, 129;
            
            /* Light Theme */
            --bg-primary: #f8fafc;
            --bg-secondary: rgba(255, 255, 255, 0.95);
            --bg-card: rgba(255, 255, 255, 0.95);
            --text-primary: #1e293b;
            --text-muted: #64748b;
            --border-color: rgba(59, 130, 246, 0.1);
            
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
            --bg-card: rgba(22, 33, 62, 0.95);
            --text-primary: #ffffff;
            --text-muted: #94a3b8;
            --border-color: rgba(255, 255, 255, 0.1);
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

        /* Modal Container */
        .modal-container {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: rgba(0, 0, 0, 0.6);
            backdrop-filter: blur(8px);
            z-index: 1000;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 1rem;
        }

        .modal {
            background: var(--bg-card);
            backdrop-filter: blur(20px);
            border-radius: 20px;
            padding: 2rem;
            max-width: 700px;
            width: 100%;
            max-height: 90vh;
            overflow-y: auto;
            box-shadow: var(--shadow-2xl);
            border: 1px solid var(--border-color);
            animation: modalSlideIn 0.4s cubic-bezier(0.4, 0, 0.2, 1);
            position: relative;
        }

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

        .modal-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 2rem;
            padding-bottom: 1rem;
            border-bottom: 1px solid var(--border-color);
        }

        .modal-title {
            font-size: 1.5rem;
            font-weight: 500;
            color: var(--text-primary);
            display: flex;
            align-items: center;
            gap: 0.75rem;
        }

        .modal-title i {
            color: rgb(var(--primary-rgb));
            font-size: 1.25rem;
        }

        .close-btn {
            background: none;
            border: none;
            font-size: 1.5rem;
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

        .form-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 1.5rem;
            margin-bottom: 2rem;
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

        .form-input, .form-select {
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

        .form-input:focus, .form-select:focus {
            outline: none;
            border-color: rgb(var(--primary-rgb));
            box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
            background: rgba(59, 130, 246, 0.08);
        }

        .form-input::placeholder {
            color: var(--text-muted);
        }

        .btn {
            padding: 0.875rem 1.5rem;
            border: none;
            border-radius: 12px;
            font-weight: 500;
            cursor: pointer;
            transition: all 0.3s ease;
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            font-size: 0.875rem;
            text-decoration: none;
            margin-right: 0.5rem;
            margin-bottom: 0.5rem;
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
            background: linear-gradient(135deg, #10b981, #059669);
            color: white;
        }

        .btn-success:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(16, 185, 129, 0.4);
        }

        .btn-warning {
            background: linear-gradient(135deg, #f59e0b, #d97706);
            color: white;
        }

        .btn-warning:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(245, 158, 11, 0.4);
        }

        .btn-danger {
            background: linear-gradient(135deg, #ef4444, #dc2626);
            color: white;
        }

        .btn-danger:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(239, 68, 68, 0.4);
        }

        .btn-secondary {
            background: var(--bg-card);
            color: var(--text-muted);
            border: 1px solid var(--border-color);
        }

        .btn-secondary:hover {
            background: rgba(59, 130, 246, 0.1);
            color: var(--text-primary);
        }

        .btn:disabled {
            opacity: 0.6;
            cursor: not-allowed;
            transform: none !important;
        }

        .modal-actions {
            display: flex;
            gap: 1rem;
            justify-content: flex-end;
            margin-top: 2rem;
            padding-top: 1rem;
            border-top: 1px solid var(--border-color);
            flex-wrap: wrap;
        }

        .action-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 1rem;
            margin-top: 2rem;
            padding-top: 1rem;
            border-top: 1px solid var(--border-color);
        }

        .info-note {
            background: rgba(245, 158, 11, 0.1);
            border: 1px solid rgba(245, 158, 11, 0.3);
            border-radius: 12px;
            padding: 1rem;
            margin: 1rem 0;
            color: var(--text-muted);
            font-size: 0.875rem;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .info-note i {
            color: #f59e0b;
            font-size: 1rem;
        }

        /* Fixed Popup Notification */
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

        /* MOBILE RESPONSIVE */
        @media (max-width: 768px) {
            .modal {
                margin: 1rem;
                max-width: calc(100vw - 2rem);
                padding: 1.5rem;
            }

            .form-grid {
                grid-template-columns: 1fr;
                gap: 1rem;
            }

            .modal-actions, .action-grid {
                grid-template-columns: 1fr;
                flex-direction: column;
            }

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
        }

        @media (max-width: 640px) {
            .modal {
                padding: 1rem;
            }

            .popup-notification {
                top: 0.5rem;
                right: 0.5rem;
                left: 0.5rem;
                padding: 1rem 1.5rem;
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
    <div class="modal-container">
        <div class="modal">
            <div class="modal-header">
                <h2 class="modal-title">
                    <i class="fas fa-edit"></i>
                    Editar Transação #<?php echo $transaction['id']; ?>
                </h2>
                <button class="close-btn" onclick="window.history.back()">
                    <i class="fas fa-times"></i>
                </button>
            </div>

            <form method="POST" id="editTransactionForm">
                <div class="form-grid">
                    <div class="form-group">
                        <label for="status" class="form-label">
                            <i class="fas fa-info-circle"></i> Status
                        </label>
                        <select name="status" id="status" class="form-select" required>
                            <option value="PENDING" <?php echo $transaction['status'] === 'PENDING' ? 'selected' : ''; ?>>PENDENTE</option>
                            <option value="PAID" <?php echo $transaction['status'] === 'PAID' ? 'selected' : ''; ?>>PAGO</option>
                            <option value="REVERSED" <?php echo $transaction['status'] === 'REVERSED' ? 'selected' : ''; ?>>ESTORNADO</option>
                            <option value="CANCELLED" <?php echo $transaction['status'] === 'CANCELLED' ? 'selected' : ''; ?>>CANCELADO</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="nome" class="form-label">
                            <i class="fas fa-user"></i> Nome
                        </label>
                        <input type="text" name="nome" id="nome" class="form-input" value="<?php echo htmlspecialchars($transaction['nome']); ?>" required>
                    </div>

                    <div class="form-group">
                        <label for="amount" class="form-label">
                            <i class="fas fa-dollar-sign"></i> Valor
                        </label>
                        <input type="number" step="0.01" name="amount" id="amount" class="form-input" value="<?php echo htmlspecialchars($transaction['amount']); ?>" required>
                    </div>

                    <div class="form-group">
                        <label for="tax" class="form-label">
                            <i class="fas fa-percentage"></i> Taxa
                        </label>
                        <input type="number" step="0.01" name="tax" id="tax" class="form-input" value="<?php echo htmlspecialchars($transaction['tax']); ?>" required>
                    </div>

                    <div class="form-group" style="grid-column: 1 / -1;">
                        <label for="descricao" class="form-label">
                            <i class="fas fa-comment"></i> Descrição
                        </label>
                        <input type="text" name="descricao" id="descricao" class="form-input" value="<?php echo htmlspecialchars($transaction['descricao']); ?>">
                    </div>

                    <div class="form-group" style="grid-column: 1 / -1;">
                        <label for="external_id" class="form-label">
                            <i class="fas fa-external-link-alt"></i> External ID
                        </label>
                        <input type="text" name="external_id" id="external_id" class="form-input" value="<?php echo htmlspecialchars($transaction['external_id']); ?>" required>
                    </div>
                </div>

                <div class="info-note">
                    <i class="fas fa-info-circle"></i>
                    Attention: Alterar o status aqui não envia webhook automaticamente.
                </div>

                <div class="modal-actions">
                    <button type="submit" name="update" class="btn btn-primary">
                        <i class="fas fa-save"></i>
                        Save Changes
                    </button>
                </div>
            </form>

            <!-- Action Buttons -->
            <div class="action-grid">
                <form method="POST" style="display: contents;">
                    <button type="submit" name="approve" class="btn btn-success" <?php echo $transaction['status'] === 'PAID' ? 'disabled' : ''; ?>>
                        <i class="fas fa-check"></i>
                        Aprovar
                    </button>
                </form>

                <form method="POST" style="display: contents;">
                    <button type="submit" name="reverse" class="btn btn-warning" <?php echo $transaction['status'] !== 'PAID' ? 'disabled' : ''; ?>>
                        <i class="fas fa-undo"></i>
                        Estornar
                    </button>
                </form>

                <form method="POST" style="display: contents;">
                    <button type="submit" name="cancel" class="btn btn-secondary">
                        <i class="fas fa-ban"></i>
                        Cancel
                    </button>
                </form>

                <form method="POST" style="display: contents;" onsubmit="return confirm('Tem certeza que deseja deletar esta transação?')">
                    <button type="submit" name="delete" class="btn btn-danger">
                        <i class="fas fa-trash"></i>
                        Deletar
                    </button>
                </form>
            </div>
        </div>
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
        // Popup functionality
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

        // Show popup if there's a message
        <?php if (!empty($message)): ?>
            showPopup(<?php echo json_encode($message); ?>, <?php echo json_encode($messageType); ?>);
        <?php endif; ?>

        // Keyboard shortcuts
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') {
                window.history.back();
            }
        });
    </script>
</body>
</html>
