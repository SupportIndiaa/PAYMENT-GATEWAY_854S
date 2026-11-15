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

$user_id = $_SESSION['user_id'];

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
        $data_final = $data_inicial . ' 23:59:59'; // Final do dia
    }
}

// Validar formato da data
if (!strtotime($data_inicial)) {
    jsonResponse(['error' => 'O parâmetro data_inicial está no formato incorreto.']);
}

// Criar o objeto DateTime com data_inicial
try {
    $startDate = new DateTime($data_inicial);
} catch (Exception $e) {
    jsonResponse(['error' => 'Data inicial inválida: ' . $e->getMessage()]);
}

// Criar o objeto DateTime com data_final
try {
    $endDate = new DateTime($data_final);
} catch (Exception $e) {
    jsonResponse(['error' => 'Data final inválida: ' . $e->getMessage()]);
}

// Garantir quand thes datas estejam no formato correto para a consulta
$data_inicial = $startDate->format('Y-m-d H:i:s');
$data_final = $endDate->format('Y-m-d H:i:s');

// Capturar o parâmetro de busca
$searchValue = $_GET['search']['value'] ?? '';

// Adicionar o filtro de busca na cláusula WHERE somente se searchValue não for vazio
$whereClause = "
    WHERE created_at BETWEEN :start AND :end 
      AND user_id = :user_id
      AND status IN ('PAID', 'REVERSED')
      AND type = 'DEPOSIT'
      AND (internal IS NULL OR internal != 1)
";

if (!empty($searchValue)) {
    $whereClause .= " AND descricao LIKE :searchValue";
}

// Consultar registros totais
$totalQuery = $pdo->prepare("
    SELECT COUNT(*) 
    FROM transactions 
    $whereClause
");
$totalQueryParams = [
    ':start' => $data_inicial,
    ':end' => $data_final,
    ':user_id' => $user_id,
];

if (!empty($searchValue)) {
    $totalQueryParams[':searchValue'] = '%' . $searchValue . '%';
}
$totalQuery->execute($totalQueryParams);
$recordsTotal = $totalQuery->fetchColumn();

// Consultar dados paginados
$dataQuery = $pdo->prepare("
    SELECT 
        id, 
        created_at, 
        confirmed_date, 
        end2end, 
        descricao,
        amount AS valor,
        tax AS taxa,
        status
    FROM transactions
    $whereClause
    ORDER BY created_at DESC
    LIMIT :offset, :limit
");

$dataQuery->bindValue(':start', $data_inicial);
$dataQuery->bindValue(':end', $data_final);
$dataQuery->bindValue(':user_id', $user_id, PDO::PARAM_INT);

if (!empty($searchValue)) {
    $dataQuery->bindValue(':searchValue', '%' . $searchValue . '%', PDO::PARAM_STR);
}

$dataQuery->bindValue(':offset', $start, PDO::PARAM_INT);
$dataQuery->bindValue(':limit', $length, PDO::PARAM_INT);

$dataQuery->execute();
$data = $dataQuery->fetchAll(PDO::FETCH_ASSOC);

// Formatar os dados para incluir informações extras
foreach ($data as &$row) {
    // Verificar and thelterar o status para 1 se for 'PAID'
    $row['status'] = ($row['status'] == 'PAID') ? 1 : 
                    (($row['status'] == 'REVERSED') ? 2 : 
                    (($row['status'] == 'PENDING') ? 0 : -1));

    // Formatar as datas
    $row['data_solicitacao'] = date('Y-m-d H:i:s', strtotime($row['created_at']));

    // Se confirmed_date for null, substituímos por uma data padrão
    if ($row['confirmed_date'] == null) {
        $row['data_confirmacao'] = '0000-00-00 00:00:00'; // Valor padrão
    } else {
        $row['data_confirmacao'] = date('Y-m-d H:i:s', strtotime($row['confirmed_date']));
    }
    
    // Alterar o pix_id para o ID da transação
    $row['pix_id'] = $row['id'];  
    unset($row['id']);  // Remover a coluna 'id' original

    // Ajustar os campos de descrição
    $row['descricao_cliente'] = $row['descricao'] ?? ''; // Caso tenha descrição ou não
    $row['endToEndId'] = $row['end2end'] ?? ''; // Caso tenha descrição ou não
    unset($row['descricao']);  // Remover a coluna 'descricao' original
}

// Response no formato DataTables
$response = [
    'draw' => $draw,
    'recordsTotal' => $recordsTotal,
    'recordsFiltered' => $recordsTotal,
    'data' => $data,
];

jsonResponse($response);
