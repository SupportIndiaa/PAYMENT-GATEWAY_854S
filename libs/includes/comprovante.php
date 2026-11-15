<?php
// Função para carregar as variáveis do arquivo .env
function loadEnv($file) {
    if (!file_exists($file)) {
        throw new Exception("Arquivo .env não encontrado!");
    }

    $lines = file($file, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);

    foreach ($lines as $line) {
        if (strpos(trim($line), '#') === 0) {
            continue;
        }

        list($key, $value) = explode('=', $line, 2);
        $key = trim($key);
        $value = trim($value);

        putenv("$key=$value");
        $_ENV[$key] = $value;
    }
}

// Função para conectar ao banco de dados
function connectDb() {
    $dbHost = getenv('DB_HOST');
    $dbName = getenv('DB_NAME');
    $dbUser = getenv('DB_USER');
    $dbPass = getenv('DB_PASS');

    try {
        // Conexão PDO com o banco de dados
        $pdo = new PDO("mysql:host=$dbHost;dbname=$dbName", $dbUser, $dbPass);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        return $pdo;
    } catch (PDOException $e) {
        die("Error ao conectar ao banco de dados: " . $e->getMessage());
    }
}

// Carrega as variáveis do arquivo .env
loadEnv('../../.env');

// Captura o ID da transação da URL
$transactionId = key($_GET); // Captura a chave da query string (o valor após o ?)

// Verifica se o ID foi fornecido
if ($transactionId) {
    // Conectar ao banco de dados
    $pdo = connectDb();

    // Preparar a consulta SQL para buscar a transação
    $stmt = $pdo->prepare("SELECT * FROM transactions WHERE id = :id LIMIT 1");
    $stmt->bindParam(':id', $transactionId);
    $stmt->execute();
    $transaction = $stmt->fetch(PDO::FETCH_ASSOC);

    // Caminho da logo
    $logoPath = 'https://hunterpayments.com.br/wp-content/uploads/2024/11/01-ICONE.png'; // Caminho fixo para o logo

    if ($transaction) {
        // Transação encontrada, exibe os dados
        echo '
        <html>
        <head></head>
        <body>
        <div class="modal-header">
            <h6 class="modal-title" id="staticBackdropLabel4">Comprovante de pagamento</h6>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="card custom-card border-0 shadow-none card-pattern-1">
            <div class="card-body p-50" style="padding:50px !important;z-index:2;">
                <div class="comprovante" id="comprovante">
                    <div class="p-3">
                        <div class="d-flex align-items-center mb-2 position-relative">
                            <!-- Logo corretamente posicionada -->
                            <img src="' . htmlspecialchars($logoPath) . '" style="width: 100px; margin: 0 auto; display:block;" alt="Logo">
                        </div>
                    </div>

                    <div class="p-3">
                        <div class="d-flex align-items-center">
                            <div class="flex-grow-1">
                                <strong class="fs-6">NOME:</strong>
                            </div>
                            <div class="ms-2 min-w-fit-content">
                                <span>' . htmlspecialchars($transaction['nome']) . '</span>
                            </div>
                        </div>
                    </div>
                    <hr class="border-white op-2 my-0">

                    <div class="p-3">
                        <div class="d-flex align-items-center">
                            <div class="flex-grow-1">
                                <strong class="fs-6">Chave PIX:</strong>
                            </div>
                            <div class="ms-2 min-w-fit-content">
                                <span>' . (!empty($transaction['document']) ? htmlspecialchars($transaction['document']) : 'Desconhecido') . '</span>
                            </div>
                        </div>
                    </div>
                    <hr class="border-white op-2 my-0">

                    <div class="p-3">
                        <div class="d-flex align-items-center">
                            <div class="flex-grow-1">
                                <strong class="fs-6">STATUS:</strong>
                            </div>
                            <div class="ms-2 min-w-fit-content">
                                <span>' . 'CONFIRMADO' . '</span>
                            </div>
                        </div>
                    </div>
                    <hr class="border-white op-2 my-0">

                    <div class="p-3">
                        <div class="d-flex align-items-center">
                            <div class="flex-grow-1">
                                <strong class="fs-6">DATA:</strong>
                            </div>
                            <div class="ms-2 min-w-fit-content">
                                <span>' . htmlspecialchars($transaction['created_at']) . '</span>
                            </div>
                        </div>
                    </div>
                    <hr class="border-white op-2 my-0">

                    <div class="p-3">
                        <div class="d-flex align-items-center">
                            <div class="flex-grow-1">
                                <strong class="fs-6">E2E ID:</strong>
                            </div>
                            <div class="ms-2 min-w-fit-content">
                                <span>' . htmlspecialchars($transaction['end2end']) . '</span>
                            </div>
                        </div>
                    </div>
                    <hr class="border-white op-2 my-0">

                    <div class="p-3 text-center">
                        <div class="d-flex align-items-center">
                            <div class="flex-grow-1">
                                <span style="font-size: 1.8rem !important;color: rgb(2, 153, 211);">R$ ' . number_format($transaction['amount'], 2, ',', '.') . '</span>
                            </div>
                        </div>
                    </div>
                </div>
                <p class="text-center">Autenticação: ' . htmlspecialchars($transaction['external_id']) . '</p>
            </div>
        </div>
        </body>
        </html>';
    } else {
        echo "Transação não encontrada.";
    }
} else {
    echo "ID de transação não fornecido.";
}
?>
