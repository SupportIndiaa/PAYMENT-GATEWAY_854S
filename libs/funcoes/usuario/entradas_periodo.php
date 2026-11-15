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

// Configurar o fuso horário
date_default_timezone_set('America/Sao_Paulo');

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

// Validar e processar o parâmetro `data_inicial` e `data_final`
if (!$data_inicial || !$data_final) {
    jsonResponse(['error' => 'Os parâmetros data_inicial e data_final são obrigatórios.']);
}

$data_inicial = urldecode($data_inicial);
$data_final = urldecode($data_final);

// Validar formato das datas
if (!strtotime($data_inicial) || !strtotime($data_final)) {
    jsonResponse(['error' => 'Os parâmetros de data estão no formato incorreto.']);
}

// Ajustar intervalo de datas
$horario_inicial = (strlen($data_inicial) > 10); // Verifica se há horário na data_inicial
$horario_final = (strlen($data_final) > 10);   // Verifica se há horário na data_final

if ($horario_inicial && $horario_final) {
    // Ajustar `data_inicial` para incluir o dia anterior às 00:00:00
    $data_anterior_inicio = date('Y-m-d 00:00:00', strtotime($data_inicial . ' -1 day'));
    $data_inicial = date('Y-m-d H:i:s', strtotime($data_inicial)); // Data inicial original com horário
} else {
    // Se horários não forem passados, usar apenas o dia inicial
    $data_anterior_inicio = date('Y-m-d 00:00:00', strtotime($data_inicial . ' -1 day'));
    $data_inicial .= ' 00:00:00';
    $data_final .= ' 23:59:59';
}

// Garantir intervalo de datas correto
$data_anterior_fim = date('Y-m-d 23:59:59', strtotime($data_anterior_inicio));

// Consulta para transações do intervalo total (dia anterior + atual)
$query = $pdo->prepare("
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
        u.name AS nome,
        u.cnpj AS cpf
    FROM transactions t
    JOIN users u ON u.id = t.user_id
    WHERE t.created_at BETWEEN :start AND :end
    AND t.user_id = :user_id
    AND t.status = 'PAID'
    AND (internal IS NULL OR internal != 1)
    AND type = 'DEPOSIT'
    ORDER BY t.created_at DESC
");
$query->bindValue(':start', $data_anterior_inicio);
$query->bindValue(':end', $data_final);
$query->bindValue(':user_id', $user_id, PDO::PARAM_INT);
$query->execute();

// Processar os resultados
$entradas = $query->fetchAll(PDO::FETCH_ASSOC);

// Variáveis de totalização
$totalPixEntradas = 0;
$totalTaxasEntradas = 0;
$depositosConfirmados = 0;
$depositosPendings = 0;

// Somar os dados de todas as entradas (dia atual + dia anterior)
foreach ($entradas as $entrada) {
    $totalPixEntradas += $entrada['valor'];
    $totalTaxasEntradas += $entrada['taxa'];
    if ($entrada['status'] == 1) {
        $depositosConfirmados++;
    } else {
        $depositosPendings++;
    }
}

// Response única com dados somados
$response = [
    'entradas' => $entradas,
    'totalPixEntradas' => $totalPixEntradas,
    'totalTaxasEntradas' => $totalTaxasEntradas,
    'depositosConfirmados' => $depositosConfirmados,
    'depositosPendings' => $depositosPendings,
    'quantidadeTransacoes' => count($entradas),
];

jsonResponse($response);
