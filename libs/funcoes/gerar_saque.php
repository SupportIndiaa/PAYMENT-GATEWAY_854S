<?php
session_start();

/**
 * Função para carregar variáveis do arquivo .env
 */
function loadEnv() {
    $envFilePath = __DIR__ . '/../../.env';

    if (!file_exists($envFilePath)) {
        http_response_code(500);
        echo json_encode([
            "statusCode" => 500,
            "message" => "Arquivo .env não encontrado."
        ]);
        exit;
    }

    $envVariables = parse_ini_file($envFilePath);

    foreach ($envVariables as $key => $value) {
        putenv("$key=$value");
    }
}

/**
 * Função para gerar IDs únicos
 */
function generateId() {
    $id = bin2hex(random_bytes(16)); // Gera 32 caracteres hexadecimais
    $randomPart = substr(str_shuffle('abcdefghijklmnopqrstuvwxyz0123456789'), 0, 6); // 6 caracteres aleatórios
    return 'e' . substr($id, 0, 26) . $randomPart; // Combina o hex e o random
}

/**
 * Função para gerar um end2end no formato "E10573521202412071524aP3EdcJV8eP"
 */
function generateEnd2End() {
    $prefix = 'E'; // Prefixo fixo
    $datePart = date('YmdHis'); // Data no formato ano, mês, dia, hora, minuto, segundo
    $randomPart = substr(str_shuffle('ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789'), 0, 8); // 8 caracteres aleatórios
    return $prefix . $datePart . $randomPart;
}

/**
 * Função para retornar respostas em JSON
 */
function jsonResponse($statusCode, $message) {
    http_response_code($statusCode);
    if ($statusCode != 200) {
        echo json_encode([
            "statusCode" => $statusCode,
            "message" => $message
        ]);
    }
    exit;
}

/**
 * Função para obter token do BSPAY
 */
function getBsPayToken($url, $clientId, $clientSecret) {
    $authorization = 'Basic ' . base64_encode($clientId . ':' . $clientSecret);

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url . 'oauth/token');
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'Authorization: ' . $authorization,
        'Content-Type: application/x-www-form-urlencoded',
    ]);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query(['grant_type' => 'client_credentials']));

    $response = curl_exec($ch);

    if (curl_errno($ch)) {
        curl_close($ch);
        jsonResponse(500, "Error cURL: " . curl_error($ch));
    }

    $responseData = json_decode($response, true);
    curl_close($ch);

    if (!isset($responseData['access_token'])) {
        jsonResponse(500, "Error ao obter token dand thecesso.");
    }

    return $responseData['access_token'];
}

/**
 * Função para processar pagamento via PIX
 */
function processPixPayment($url, $accessToken, $payload) {
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url . 'pix/payment');
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($payload));
    curl_setopt($ch, CURLOPT_IPRESOLVE, CURL_IPRESOLVE_V4);
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'Authorization: Bearer ' . $accessToken,
        'Content-Type: application/json'
    ]);

    $response = curl_exec($ch);
    curl_close($ch);
    file_put_contents('daanrox.txt', "RESPOSTA SAQUE: " . print_r($response, true) . PHP_EOL, FILE_APPEND);
    return json_decode($response, true);
}

loadEnv();

// Carregar variáveis dand thembiente
$BSPAY_URL = getenv('BSPAY_URL');
$BSPAY_CLIENT_ID = getenv('BSPAY_CLIENT_ID');
$BSPAY_CLIENT_SECRET = getenv('BSPAY_CLIENT_SECRET');
$APP_URL = getenv('APP_URL');

// Configuração do banco de dados
$mysqli = new mysqli(getenv('DB_HOST'), getenv('DB_USER'), getenv('DB_PASS'), getenv('DB_NAME'));

if ($mysqli->connect_error) {
    jsonResponse(500, "Falha na conexão com o banco de dados: " . $mysqli->connect_error);
}

// Verifica se o método da requisição é POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    jsonResponse(405, "Método não permitido.");
}

// Captura os dados do formulário
$nome = $_POST['nome'] ?? '';
$cpf = $_POST['cpf'] ?? '';
$chave_pix = $_POST['chave_pix'] ?? '';
$valor = isset($_POST['valor']) ? str_replace(',', '.', $_POST['valor']) : 0.00;
$descricao = $_POST['descricao'] ?? '';
$pin = $_POST['pin'] ?? '';

// Verificação de valor inválido
if ($valor <= 0) {
    jsonResponse(400, "Valor inválido.");
}

// Obter o ID do usuário da sessão
$userId = $_SESSION['user_id'] ?? null;

if (!$userId) {
    jsonResponse(401, "Usuário não autenticado.");
}

// Verificar configurações do usuário
$stmt = $mysqli->prepare("SELECT cash_in_active, pin, balance FROM users WHERE id = ? LIMIT 1");
$stmt->bind_param("i", $userId);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    jsonResponse(404, "Usuário não encontrado.");
}

$userConfig = $result->fetch_assoc();

if ($userConfig['cash_in_active'] != 1) {
    jsonResponse(403, "Entre em contato com o suporte.");
}

if (empty($userConfig['pin'])) {
    jsonResponse(400, "PIN não configurado. Ative seu PIN primeiro.");
}

if ($userConfig['pin'] !== $pin) {
    jsonResponse(400, "PIN incorreto.");
}

$balance = $userConfig['balance'];

// Obter configurações de taxas
$result = $mysqli->query("SELECT tax_min, tax_cashout FROM config WHERE id = 0 LIMIT 1");
if ($result->num_rows === 0) {
    jsonResponse(500, "Configuração não encontrada na tabela config.");
}

$config = $result->fetch_assoc();
$tax_min = $config['tax_min'];
$tax_cashout = $config['tax_cashout'];

// Calcular taxas
$taxaCalculada = max(($valor * $tax_cashout) / 100, $tax_min);
$valorTotal = $valor + $taxaCalculada;

if ($balance < $valorTotal) {
    jsonResponse(400, "Saldo insuficiente para cobrir valor e taxas.");
}

// Obter token BSPAY
$accessToken = getBsPayToken($BSPAY_URL, $BSPAY_CLIENT_ID, $BSPAY_CLIENT_SECRET);

// Preparar pagamento PIX
$externalId = generateId();
$end2end = generateEnd2End();
$payload = [
    'amount' => $valor,
    'external_id' => $externalId,
    'description' => $descricao,
    'creditParty' => [
        'name' => $nome,
        'keyType' => 'Aadhaar/PAN',
        'key' => $chave_pix,
        'taxId' => $cpf
    ],
    'postbackUrl' => $APP_URL . "/libs/bspay/webhook"
];

// Realizar pagamento via PIX
$responsePix = processPixPayment($BSPAY_URL, $accessToken, $payload);

// Verificar resposta da API PIX
if (isset($responsePix['statusCode']) && $responsePix['statusCode'] == 200) {
    // Atualizar saldo do usuário
    $newBalance = $balance - $valorTotal;
    $stmt = $mysqli->prepare("UPDATE users SET balance = ? WHERE id = ?");
    $stmt->bind_param("di", $newBalance, $userId);
    $stmt->execute();

    // Inserir transação no banco de dados
    $id = generateId();
    $stmt = $mysqli->prepare("INSERT INTO transactions (id, end2end, external_id, amount, created_at, status, type, user_id, descricao, nome, document, tax) VALUES (?, ?, ?, ?, NOW(), 'PENDING', 'WITHDRAW', ?, ?, ?, ?, ?)");
    $stmt->bind_param("sssdissss", $id, $end2end, $externalId, $valor, $userId, $descricao, $nome, $chave_pix, $taxaCalculada);
    $stmt->execute();

    // Retornar resposta de sucesso
    http_response_code(200);
    echo json_encode([ 
        "statusCode" => 200,
        "message" => $responsePix['message']
    ]);
}
?>
