<?php
session_start();

use Dotenv\Dotenv;

require __DIR__ . '/../../../vendor/autoload.php';

$dotenv = Dotenv::createImmutable(__DIR__ . '/../../../');
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
    jsonResponse(['error' => 'Error ao conectar ao banco de dados: ' . $e->getMessage()]);
}

// Função para retornar a resposta JSON
function jsonResponse($data) {
    header('Content-Type: application/json');
    echo json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
    exit;
}

// Capturar e processar os parâmetros da URL
$data_inicial = $_GET['data_inicial'] ?? null;
$data_final = $_GET['data_final'] ?? null;
$draw = intval($_GET['draw'] ?? 1);
$start = intval($_GET['start'] ?? 0);
$length = intval($_GET['length'] ?? 10);

// Validar sessão do usuário
if (!isset($_SESSION['user_id'])) {
    jsonResponse(['error' => 'Usuário não autenticado.']);
}

$user_id = intval($_SESSION['user_id']);

// Verificar e processar o parâmetro `data_inicial`
if (!$data_inicial) {
    jsonResponse(['error' => 'O parâmetro data_inicial é obrigatório.']);
}

// Se o parâmetro data_inicial contiver "até", vamos separar as datas
if (strpos($data_inicial, 'até') !== false) {
    list($data_inicial, $data_final) = explode(' até ', $data_inicial);
} else {
    // Se não houver "até", definir o final do dia para a data final
    if (!$data_final) {
        $data_final = $data_inicial;
    }
}

// Criar objetos DateTime para validar and thejustar os intervalos
try {
    $startDate = new DateTime($data_inicial);
    $endDate = new DateTime($data_final);
    $endDate->modify('+1 day'); // Incluir o final do dia
} catch (Exception $e) {
    jsonResponse(['error' => 'Datas inválidas: ' . $e->getMessage()]);
}

// Garantir quand thes datas estejam no formato correto para a consulta
$data_inicial = $startDate->format('Y-m-d 00:00:00');
$data_final = $endDate->format('Y-m-d 00:00:00');

// Capturar o parâmetro de busca
$searchValue = $_GET['search']['value'] ?? '';

// Construir a cláusula WHERE com condições opcionais
$whereClause = "
    WHERE t.created_at >= :start AND t.created_at < :end 
      AND (t.user_id = :user_id OR t.external_id = :user_id)
      AND t.status = 'PAID'
      AND t.internal = 1
";

// Adicionar a busca no campo 'descricao' se houver um valor de busca
if (!empty($searchValue)) {
    $whereClause .= " AND t.descricao LIKE :searchValue";
}

// Consultar registros totais
$totalQuery = $pdo->prepare("
    SELECT COUNT(*) 
    FROM transactions t
    LEFT JOIN users u ON t.user_id = u.id
    LEFT JOIN users ue ON t.external_id = ue.id
    $whereClause
");

$totalQueryParams = [
    ':start' => $data_inicial,
    ':end' => $data_final,
    ':user_id' => $user_id,
];

if (!empty($searchValue)) {
    $totalQueryParams[':searchValue'] = '%' . $searchValue . '%'; // Adiciona o filtro LIKE
}

$totalQuery->execute($totalQueryParams);
$recordsTotal = $totalQuery->fetchColumn();

// Consultar dados paginados
$dataQuery = $pdo->prepare("
    SELECT 
        t.id, 
        t.created_at, 
        t.confirmed_date, 
        t.end2end, 
        t.descricao,
        t.amount AS valor,
        t.tax AS taxa,
        t.status,
        t.external_id,
        u.username AS usuario_origem,
        ue.username AS usuario_destino
    FROM transactions t
    LEFT JOIN users u ON t.user_id = u.id
    LEFT JOIN users ue ON t.external_id = ue.id
    $whereClause
    ORDER BY t.created_at DESC
    LIMIT :offset, :limit
");

$dataQuery->bindValue(':start', $data_inicial);
$dataQuery->bindValue(':end', $data_final);
$dataQuery->bindValue(':user_id', $user_id, PDO::PARAM_INT);

if (!empty($searchValue)) {
    $dataQuery->bindValue(':searchValue', '%' . $searchValue . '%', PDO::PARAM_STR); // Adiciona o filtro LIKE
}

$dataQuery->bindValue(':offset', $start, PDO::PARAM_INT);
$dataQuery->bindValue(':limit', $length, PDO::PARAM_INT);
$dataQuery->execute();
$data = $dataQuery->fetchAll(PDO::FETCH_ASSOC);

// Verificar e formatar os resultados
foreach ($data as &$row) {
    // Alterar status para 1 (pago) ou 0 (não pago)
    $row['status'] = ($row['status'] === 'PAID') ? 1 : 0;

    // Formatar as datas
    $row['data_solicitacao'] = date('Y-m-d H:i:s', strtotime($row['created_at']));
    $row['data_confirmacao'] = $row['created_at'] 
        ? date('Y-m-d H:i:s', strtotime($row['created_at'])) 
        : '0000-00-00 00:00:00';

    // Ajustar valores de `descricao` e `endToEndId`
    $row['descricao'] = $row['descricao'] ?? 'Nenhuma';
    $row['endToEndId'] = $row['end2end'] ?? '';

    // Adicionar usernames
    $row['usuario_id_usuario'] = $row['usuario_origem'] ?? '';
    $row['usuario_destino_usuario'] = $row['usuario_destino'] ?? '';

    // Calcular saldo atual e novo saldo (fixo como 0.00 para exemplo)
    $row['saldo_atual'] = "0.00"; 
    $row['novo_saldo'] = "0.00"; 

    // Remover campos desnecessários
    unset($row['external_id'], $row['usuario_origem'], $row['usuario_destino']);
}

// Response no formato DataTables
$response = [
    'draw' => $draw,
    'recordsTotal' => $recordsTotal,
    'recordsFiltered' => $recordsTotal, // ou você podand thejustar isso para refletir o número de registros filtrados
    'data' => $data,
];

jsonResponse($response);
