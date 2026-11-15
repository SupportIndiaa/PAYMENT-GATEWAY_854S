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

loadEnv();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode([
        "statusCode" => 405,
        "message" => "Método não permitido. Utilize POST."
    ]);
    exit;
}

$BSPAY_URL = getenv('BSPAY_URL');
$BSPAY_CLIENT_ID = getenv('BSPAY_CLIENT_ID');
$BSPAY_CLIENT_SECRET = getenv('BSPAY_CLIENT_SECRET');
$APP_URL = getenv('APP_URL');

$mysqli = new mysqli(getenv('DB_HOST'), getenv('DB_USER'), getenv('DB_PASS'), getenv('DB_NAME'));

if ($mysqli->connect_error) {
    http_response_code(500);
    echo json_encode([
        "statusCode" => 500,
        "message" => "Falha na conexão com o banco de dados: " . $mysqli->connect_error
    ]);
    exit;
}

$nome = isset($_POST['nome']) ? $_POST['nome'] : '';
$cpf = isset($_POST['cpf']) ? $_POST['cpf'] : '';
$valor = isset($_POST['valor']) ? $_POST['valor'] : '';
$descricao = isset($_POST['descricao']) ? $_POST['descricao'] : '';
$urlnoty = isset($_POST['urlnoty']) ? $_POST['urlnoty'] : '';

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
$access_token = $userData['access_token'];

if ($valor <= 0) {
    http_response_code(400);
    echo json_encode([
        "statusCode" => 400,
        "message" => "Valor inválido."
    ]);
    exit;
}

$stmt = $mysqli->prepare("SELECT cash_in_active FROM users WHERE id = ? LIMIT 1");
$stmt->bind_param("i", $userId);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    $userConfig = $result->fetch_assoc();

    if ($userConfig['cash_in_active'] != 1) {
        http_response_code(403);
        echo json_encode([
            "statusCode" => 403,
            "message" => "Entre em contato com o suporte."
        ]);
        exit;
    }
} else {
    http_response_code(404);
    echo json_encode([
        "statusCode" => 404,
        "message" => "Usuário não encontrado."
    ]);
    exit;
}

$result = $mysqli->query("SELECT tax_min, tax_cashin, tax_cashout FROM config WHERE id = 0 LIMIT 1");
if ($result->num_rows > 0) {
    $config = $result->fetch_assoc();
    $tax_min = $config['tax_min'];
    $tax_cashin = $config['tax_cashin'];
} else {
    http_response_code(500);
    echo json_encode([
        "statusCode" => 500,
        "message" => "Configuração não encontrada na tabela config."
    ]);
    exit;
}

$taxaCalculada = ($valor * $tax_cashin) / 100;

if ($taxaCalculada < $tax_min) {
    $taxaCalculada = $tax_min;
}

$authorization = 'Basic ' . base64_encode($BSPAY_CLIENT_ID . ':' . $BSPAY_CLIENT_SECRET);

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $BSPAY_URL . 'oauth/token');
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    'Authorization: ' . $authorization,
    'Content-Type: application/x-www-form-urlencoded',
]);

$postData = [
    'grant_type' => 'client_credentials',
];

curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($postData));

$response = curl_exec($ch);

if (curl_errno($ch)) {
    http_response_code(500);
    echo json_encode([
        "statusCode" => 500,
        "message" => "Error cURL: " . curl_error($ch)
    ]);
    exit;
} else {
    $responseData = json_decode($response, true);

    if (isset($responseData['access_token'])) {
        $accessToken = $responseData['access_token'];
        $externalId = uniqid();

        $payload = [
            'amount' => $valor,
            'external_id' => $externalId,
            'payerQuestion' => $descricao,
            'payer' => [
                'name' => $nome,
                'document' => $cpf,
                'email' => "",
            ],
            'postbackUrl' => $APP_URL . "/libs/bspay/webhook"
        ];

        $curl = curl_init();
        curl_setopt_array($curl, array(
            CURLOPT_URL => $BSPAY_URL . 'pix/qrcode',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_POSTFIELDS => json_encode($payload),
            CURLOPT_HTTPHEADER => array(
                'Authorization: Bearer ' . $accessToken,
                'Content-Type: application/json'
            ),
        ));

        $response = curl_exec($curl);
        file_put_contents('daanrox.txt', "Response QRCODE: " . print_r($response, true) . PHP_EOL, FILE_APPEND);

        if (curl_errno($curl)) {
            http_response_code(500);
            echo json_encode([
                "statusCode" => 500,
                "message" => "Error ao gerar QR Code: " . curl_error($curl)
            ]);
            exit;
        } else {
            $responsePix = json_decode($response, true);

            if (isset($responsePix['qrcode'])) {
    $qrcode = $responsePix['qrcode'];

    // Funções para gerar IDs únicos
    function generateId() {
        $id = bin2hex(random_bytes(16));
        $randomPart = substr(str_shuffle('abcdefghijklmnopqrstuvwxyz0123456789'), 0, 6);
        return 'e' . substr($id, 0, 26) . $randomPart;
    }

    function generateEnd2End() {
        $prefix = 'E';
        $datePart = date('YmdHis');
        $randomPart = substr(str_shuffle('ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789'), 0, 8);
        return $prefix . $datePart . $randomPart;
    }
    
    $id = generateId();
    $end2end = generateEnd2End();

    $stmt = $mysqli->prepare("INSERT INTO transactions (id, end2end, external_id, amount, created_at, status, type, user_id, descricao, nome, document, tax, postback_url) VALUES (?, ?, ?, ?, NOW(), 'PENDING', 'DEPOSIT', ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("sssdisssss", $id, $end2end, $externalId, $valor, $userId, $descricao, $nome, $cpf, $taxaCalculada, $urlnoty);
    
    if ($stmt->execute()) {
        $sid = $responsePix['transactionId'];
        $cid = $responsePix['calendar']['dueDate'];
        $amount = "R$" . number_format($responsePix['amount'], 2, ',', '.');
        $checkout_qrcode = $responsePix['qrcode'];

        $checkout_url = "https://pix.cashpag.com/checkout-page/v3.html?sid={$sid}&cid={$cid}&amount={$amount}&qrcode={$checkout_qrcode}";
        
        $responsePix['checkout_url'] = $checkout_url;

        http_response_code(200);
        echo json_encode($responsePix, JSON_UNESCAPED_SLASHES);
    } else {
        http_response_code(500);
        echo json_encode([
            "statusCode" => 500,
            "message" => "Error ao salvar transação: " . $stmt->error
        ]);
    }

    $stmt->close();
} else {
    http_response_code(500);
    echo json_encode([
        "statusCode" => 500,
        "message" => "Error ao obter QR Code."
    ]);
}
        }
    } else {
        http_response_code(500);
        echo json_encode([
            "statusCode" => 500,
            "message" => "Error ao obter token dand thecesso."
        ]);
    }
}

curl_close($ch);

?>