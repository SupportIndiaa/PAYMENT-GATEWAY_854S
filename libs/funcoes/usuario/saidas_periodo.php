<?php

session_start();

use Dotenv\Dotenv;

require __DIR__ . '/../../../vendor/autoload.php';

// Carregar variáveis do .env
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

// Validar o parâmetro `data_inicial`
if (!$data_inicial) {
    jsonResponse(['error' => 'O parâmetro data_inicial é obrigatório.']);
}

$data_inicial = urldecode($data_inicial);

// Ajustar `data_final` para incluir o final do dia ou usar padrão
if (!$data_final) {
    $data_final = $data_inicial . ' 23:59:59'; // Final do mesmo dia
} else {
    $data_final = urldecode($data_final);
    // Adicionar um dia à data_final para garantir intervalo exclusivo
    $data_final = date('Y-m-d', strtotime($data_final . ' +1 day')) . ' 00:00:00';
}

// Validar formato das datas
if (!strtotime($data_inicial) || !strtotime($data_final)) {
    jsonResponse(['error' => 'Os parâmetros de data estão no formato incorreto.']);
}

// Ajustar a data para pegar também o **dia anterior** (caso o horário de data_inicial seja durantand the madrugada)
$data_anterior_inicio = date('Y-m-d 00:00:00', strtotime($data_inicial . ' -1 day'));

// Consulta para filtrar as transações de saída (saques) do dia atual e do dia anterior
$query = "
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
    AND type = 'WITHDRAW'
    ORDER BY t.created_at DESC
";

$stmt = $pdo->prepare($query);
$stmt->bindValue(':start', $data_anterior_inicio); // Data de início incluindo o dia anterior
$stmt->bindValue(':end', $data_final); // Data final ajustada
$stmt->bindValue(':user_id', $user_id, PDO::PARAM_INT);
$stmt->execute();

// Pegar os resultados da consulta
$saidas = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Calcular os totais
$totalSaidasPix = 0;
$totalTaxasSaidas = 0;
$quantidadeSaques = 0;

foreach ($saidas as &$saida) {
    $totalSaidasPix += $saida['valor'];
    $totalTaxasSaidas += $saida['taxa'];
    if ($saida['status'] == 1) {
        $quantidadeSaques++;
    }
    // Tornando o valor negativo para saídas
    $saida['valor'] = -abs($saida['valor']);
}

// Calcular os valores adicionais
$totalSacado = abs($totalSaidasPix); // O total sacado será o valor absoluto
$ticketMedio = ($quantidadeSaques > 0) ? $totalSacado / $quantidadeSaques : 0;

// Response no formato solicitado
$response = [
    'saidas' => $saidas,
    'totalTaxasSaidas' => $totalTaxasSaidas,
    'totalSaidasPix' => $totalSaidasPix,
    'quantidadeSaques' => $quantidadeSaques,
    'totalSacado' => number_format($totalSacado, 2, ',', '.'), // Formatar como valor monetário
    'ticketMedio' => number_format($ticketMedio, 2, ',', '.'), // Formatar como valor monetário
];

jsonResponse($response);
