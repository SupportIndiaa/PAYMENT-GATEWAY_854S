<?php
session_start();

// Função para carregar as variáveis dand thembiente
function loadEnv() {
    $envFilePath = __DIR__ . '/../../../.env';

    if (!file_exists($envFilePath)) {
        die("Arquivo .env não encontrado.");
    }

    $envVariables = parse_ini_file($envFilePath);

    foreach ($envVariables as $key => $value) {
        putenv("$key=$value");
    }
}

loadEnv();

// Conectando ao banco de dados
$mysqli = new mysqli(getenv('DB_HOST'), getenv('DB_USER'), getenv('DB_PASS'), getenv('DB_NAME'));

if ($mysqli->connect_error) {
    die("Conexão falhou: " . $mysqli->connect_error);
}

// Função para calcular a data inicial com base no período
function calculateStartDate($period) {
    $today = new DateTime();
    
    switch ($period) {
        case '7d':
            $interval = new DateInterval('P7D');
            break;
        case 'month':
            $interval = new DateInterval('P30D');
            break;
        case 'semester':
            $interval = new DateInterval('P6M');
            break;
        default:
            // Se o período não for reconhecido, retorna 30 dias por padrão
            $interval = new DateInterval('P30D');
            break;
    }

    $today->sub($interval);
    return $today->format('Y-m-d');
}

// Função para obter as transações do usuário com base no período
function getTransactionsByPeriod($userId, $period) {
    global $mysqli;

    // Calcula a data inicial com base no período fornecido
    $startDate = calculateStartDate($period);

    // Query para buscar as transações do usuário no período especificado
    $query = "
        SELECT amount, type, created_at, user_id, external_id 
        FROM transactions 
        WHERE (user_id = ? OR external_id = ?) 
        AND created_at >= ?
        AND status = 'PAID'
    ";
    $stmt = $mysqli->prepare($query);
    $stmt->bind_param("iis", $userId, $userId, $startDate);
    $stmt->execute();
    $result = $stmt->get_result();

    // Arrays para armazenar as transações
    $entradas = [];
    $saidas = [];

    // Iterando sobrand thes transações
    while ($row = $result->fetch_assoc()) {
        $amount = $row['amount'];
        $type = $row['type'];
        $date = date('Y-m-d', strtotime($row['created_at'])); // Formatar a data para yyyy-mm-dd
        $transactionUserId = $row['user_id'];
        $transactionExternalId = $row['external_id'];

        // Organizando as transações por tipo (entrada ou saída)
        if ($type == 'DEPOSIT') {
            $entradas[] = [
                'data' => $date,
                'valor' => $amount
            ];
        } elseif ($type == 'WITHDRAW') {
            $saidas[] = [
                'data' => $date,
                'valor' => $amount
            ];
        } elseif ($type == 'INTERNAL_TRANSFER') {
            // Para transações internas
            if ($transactionExternalId == $userId) {
                // Entrada: external_id é igual ao meu user_id
                $entradas[] = [
                    'data' => $date,
                    'valor' => $amount
                ];
            } elseif ($transactionUserId == $userId) {
                // Saída: user_id é igual ao meu user_id
                $saidas[] = [
                    'data' => $date,
                    'valor' => $amount
                ];
            }
        }
    }

    // Se não houver transações, preenche com a data atual e valor 0
    if (empty($entradas)) {
        $entradas[] = [
            'data' => date('Y-m-d'), // Data de hoje
            'valor' => 0
        ];
    }

    if (empty($saidas)) {
        $saidas[] = [
            'data' => date('Y-m-d'), // Data de hoje
            'valor' => 0
        ];
    }

    return [
        'entradas' => $entradas,
        'saidas' => $saidas
    ];
}

// Verificando o ID do usuário e o período
if (isset($_SESSION['user_id']) && isset($_GET['period'])) {
    $userId = intval($_SESSION['user_id']); // O ID do usuário vem da sessão
    $period = $_GET['period']; // O período é passado como parâmetro na URL

    // Buscando as transações com base no período
    $transactionData = getTransactionsByPeriod($userId, $period);

    // Retornando os dados em formato JSON
    echo json_encode($transactionData, JSON_PRETTY_PRINT);
} else {
    echo json_encode(["error" => "user_id e period são necessários."], JSON_PRETTY_PRINT);
}
?>
