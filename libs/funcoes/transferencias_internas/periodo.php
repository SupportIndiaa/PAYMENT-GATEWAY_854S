<?php
session_start();

use Dotenv\Dotenv;

require __DIR__ . '/../../../vendor/autoload.php';

$dotenv = Dotenv::createImmutable(__DIR__ . '/../../../');
$dotenv->load();

try {
    // Obter variáveis do .env
    $dbHost = $_ENV['DB_HOST'];
    $dbName = $_ENV['DB_NAME'];
    $dbUser = $_ENV['DB_USER'];
    $dbPass = $_ENV['DB_PASS'];

    $dsn = "mysql:host=$dbHost;dbname=$dbName;charset=utf8";
    $pdo = new PDO($dsn, $dbUser, $dbPass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    jsonResponse(['error' => 'Error ao conectar ao banco de dados: ' . $e->getMessage()]);
}

// Função para retornar a resposta JSON
function jsonResponse($data) {
    header('Content-Type: application/json');
    echo json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
    exit;
}

// Capturar parâmetros da URL
$data_inicial = $_GET['data_inicial'] ?? null;
$data_final = $_GET['data_final'] ?? null;

// Validar sessão do usuário
if (!isset($_SESSION['user_id'])) {
    jsonResponse(['error' => 'Usuário não autenticado.']);
}

$user_id = $_SESSION['user_id'];

// Validar e processar os parâmetros `data_inicial` e `data_final`
if (!$data_inicial) {
    jsonResponse(['error' => 'O parâmetro data_inicial é obrigatório.']);
}

$data_inicial = urldecode($data_inicial);

// Ajustar o formato para incluir hora no início do dia
$data_inicial .= ' 00:00:00';

// Verificar se `data_final` foi passado, caso contrário usar o final do dia de `data_inicial`
if (!$data_final) {
    $data_final = substr($data_inicial, 0, 10) . ' 23:59:59';
} else {
    $data_final = urldecode($data_final . ' 23:59:59'); // Garantir o final do dia
}

// Validar formato de `data_inicial` e `data_final`
if (!strtotime($data_inicial) || !strtotime($data_final)) {
    jsonResponse(['error' => 'Os parâmetros de data estão no formato incorreto.']);
}

// Consultar todas as transações onde o user_id ou o external_id corresponda ao usuário
$queryTodasTransacoes = $pdo->prepare("
    SELECT 
        t.id AS pix_id,
        t.created_at AS data_solicitacao,
        t.confirmed_date AS data_confirmacao,
        t.end2end AS endToEndId,
        t.descricao AS descricao_cliente,
        t.amount AS valor,
        t.tax AS taxa,
        CASE 
            WHEN t.status = 'PAID' THEN 1 
            ELSE 0 
        END AS status,
        u.name AS nome_origem,
        ue.name AS nome_destino,
        t.user_id,
        t.external_id
    FROM transactions t
    LEFT JOIN users u ON u.id = t.user_id
    LEFT JOIN users ue ON ue.id = t.external_id
    WHERE t.created_at >= :start AND t.created_at <= :end
    AND (t.user_id = :user_id OR t.external_id = :user_id)
    AND internal = 1
    ORDER BY t.created_at DESC
");

$queryTodasTransacoes->bindValue(':start', $data_inicial);
$queryTodasTransacoes->bindValue(':end', $data_final);
$queryTodasTransacoes->bindValue(':user_id', $user_id, PDO::PARAM_INT);
$queryTodasTransacoes->execute();

$todasTransacoes = $queryTodasTransacoes->fetchAll(PDO::FETCH_ASSOC);

// Contar apenas as transações relevantes (onde user_id ou external_id é igual ao user_id da sessão)
$quantidadeTransacoes = count($todasTransacoes);

// Inicializar contadores
$totalTransferenciasRecebidas = 0;
$totalTransferenciasEnviadas = 0;
$transferenciasConfirmadasRecebidas = 0;
$transferenciasPendingsRecebidas = 0;
$transferenciasConfirmadasEnviadas = 0;
$transferenciasPendingsEnviadas = 0;

// Separar entradas (recebidas) e saídas (enviadas)
$entradasFormatted = [];
$saidasFormatted = [];

foreach ($todasTransacoes as $transacao) {
    // Entradas: external_id deve ser o ID do usuário
    if ($transacao['external_id'] == $user_id) {
        $entradasFormatted[] = [
            'valor' => $transacao['valor'],
            'data' => $transacao['data_solicitacao'],
            'usuario_origem' => $transacao['nome_origem'],
            'status' => $transacao['status']
        ];

        // Calcular totais para entradas
        $totalTransferenciasRecebidas += $transacao['valor'];
        if ($transacao['status'] == 1) {
            $transferenciasConfirmadasRecebidas++;
        } else {
            $transferenciasPendingsRecebidas++;
        }
    }

    // Saídas: user_id deve ser o ID do usuário
    if ($transacao['user_id'] == $user_id) {
        $saidasFormatted[] = [
            'valor' => $transacao['valor'],
            'data' => $transacao['data_solicitacao'],
            'usuario_destino' => $transacao['nome_destino'],
            'status' => $transacao['status']
        ];

        // Calcular totais para saídas
        $totalTransferenciasEnviadas += $transacao['valor'];
        if ($transacao['status'] == 1) {
            $transferenciasConfirmadasEnviadas++;
        } else {
            $transferenciasPendingsEnviadas++;
        }
    }
}

// Response final
$response = [
    'entradas' => $entradasFormatted,
    'saidas' => $saidasFormatted,
    'totalTransferenciasRecebidas' => $totalTransferenciasRecebidas,
    'transferenciasConfirmadasRecebidas' => $transferenciasConfirmadasRecebidas,
    'transferenciasPendingsRecebidas' => $transferenciasPendingsRecebidas,
    'totalTransferenciasEnviadas' => $totalTransferenciasEnviadas,
    'transferenciasConfirmadasEnviadas' => $transferenciasConfirmadasEnviadas,
    'transferenciasPendingsEnviadas' => $transferenciasPendingsEnviadas,
    'quantidadeTransacoes' => $quantidadeTransacoes,
];

jsonResponse($response);
