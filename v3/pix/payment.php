<?php

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

function generateId() {
    $id = bin2hex(random_bytes(16)); // Gera 32 caracteres hexadecimais
    $randomPart = substr(str_shuffle('abcdefghijklmnopqrstuvwxyz0123456789'), 0, 6); // 6 caracteres aleatórios
    return 'e' . substr($id, 0, 26) . $randomPart; // Combina o hex e o random
}

function generateEnd2End() {
    $prefix = 'E'; // Prefixo fixo
    $datePart = date('YmdHis'); // Data no formato ano, mês, dia, hora, minuto, segundo
    $randomPart = substr(str_shuffle('ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789'), 0, 8); // 8 caracteres aleatórios
    return $prefix . $datePart . $randomPart;
}

function jsonResponse($statusCode, $message) {
    file_put_contents('daanrox.txt', "JSONRESPONSE: " . print_r($message, true) . PHP_EOL, FILE_APPEND);
    http_response_code($statusCode);
    if ($statusCode != 200) {
        echo json_encode([
            "statusCode" => $statusCode,
            "message" => $message
        ]);
    }
    exit;
}

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
    file_put_contents('daanrox.txt', "Response TOKEN: " . print_r($responseData, true) . PHP_EOL, FILE_APPEND);
    return $responseData['access_token'];
}

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
    
    file_put_contents('daanrox.txt', "Response PAYMENT: " . print_r($response, true) . PHP_EOL, FILE_APPEND);

    return json_decode($response, true);
}

loadEnv();

$BSPAY_URL = getenv('BSPAY_URL');
$BSPAY_CLIENT_ID = getenv('BSPAY_CLIENT_ID');
$BSPAY_CLIENT_SECRET = getenv('BSPAY_CLIENT_SECRET');
$APP_URL = getenv('APP_URL');

$mysqli = new mysqli(getenv('DB_HOST'), getenv('DB_USER'), getenv('DB_PASS'), getenv('DB_NAME'));

if ($mysqli->connect_error) {
    jsonResponse(500, "Falha na conexão com o banco de dados: " . $mysqli->connect_error);
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    jsonResponse(405, "Método não permitido.");
}

$nome = $_POST['nome'] ?? '';
$cpf = $_POST['cpf'] ?? '';
$chave_pix = $_POST['chave_pix'] ?? '';
$valor = isset($_POST['valor']) ? str_replace(',', '.', $_POST['valor']) : 0.00;
$descricao = $_POST['descricao'] ?? '';
$urlnoty = isset($_POST['urlnoty']) ? $_POST['urlnoty'] : '';

if ($valor <= 0) {
    jsonResponse(400, "Valor inválido.");
}

$client_id = isset($_POST['client_id']) ? $_POST['client_id'] : '';
$client_secret = isset($_POST['client_secret']) ? $_POST['client_secret'] : '';

if (empty($client_id) || empty($client_secret)) {
    http_response_code(400);
    echo json_encode([
        "statusCode" => 400,
        "message" => "client_id e client_secret são obrigatórios."
    ]);
    exit;
}

$stmt = $mysqli->prepare("SELECT user_id, access_token FROM users_integration_keys WHERE client_id = ? AND client_secret = ? LIMIT 1");
$stmt->bind_param("ss", $client_id, $client_secret);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows == 0) {
    http_response_code(401);
    echo json_encode([
        "statusCode" => 401,
        "message" => "client_id ou client_secret inválidos."
    ]);
    exit;
}

$userData = $result->fetch_assoc();
$userId = $userData['user_id'];

if (!$userId) {
    jsonResponse(401, "Usuário não autenticado.");
}

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

$balance = $userConfig['balance'];

$result = $mysqli->query("SELECT tax_min, tax_cashout FROM config WHERE id = 0 LIMIT 1");
if ($result->num_rows === 0) {
    jsonResponse(500, "Configuração não encontrada na tabela config.");
}

$config = $result->fetch_assoc();
$tax_min = $config['tax_min'];
$tax_cashout = $config['tax_cashout'];

$taxaCalculada = max(($valor * $tax_cashout) / 100, $tax_min);
$valorTotal = $valor + $taxaCalculada;

if ($balance < $valorTotal) {
    jsonResponse(400, "Saldo insuficiente para cobrir valor e taxas.");
}

$accessToken = getBsPayToken($BSPAY_URL, $BSPAY_CLIENT_ID, $BSPAY_CLIENT_SECRET);

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

$responsePix = processPixPayment($BSPAY_URL, $accessToken, $payload);

if (isset($responsePix['statusCode']) && $responsePix['statusCode'] == 200) {
    $newBalance = $balance - $valorTotal;
    $stmt = $mysqli->prepare("UPDATE users SET balance = ? WHERE id = ?");
    $stmt->bind_param("di", $newBalance, $userId);
    $stmt->execute();
    
    $id = generateId();
    $stmt = $mysqli->prepare("INSERT INTO transactions (id, end2end, external_id, amount, created_at, status, type, user_id, descricao, nome, document, tax, postback_url) VALUES (?, ?, ?, ?, NOW(), 'PENDING', 'WITHDRAW', ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("sssdisssss", $id, $end2end, $externalId, $valor, $userId, $descricao, $nome, $chave_pix, $taxaCalculada, $urlnoty);
    $stmt->execute();
    
    http_response_code(200);
    echo json_encode([$responsePix]);
}
?>
