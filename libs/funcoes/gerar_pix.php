<?php
session_start();

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

$BSPAY_URL = getenv('BSPAY_URL');
$BSPAY_CLIENT_ID = getenv('BSPAY_CLIENT_ID');
$BSPAY_CLIENT_SECRET = getenv('BSPAY_CLIENT_SECRET');
$APP_URL = getenv('APP_URL');

// Configuração do banco de dados
$mysqli = new mysqli(getenv('DB_HOST'), getenv('DB_USER'), getenv('DB_PASS'), getenv('DB_NAME'));

if ($mysqli->connect_error) {
    http_response_code(500);
    echo json_encode([
        "statusCode" => 500,
        "message" => "Falha na conexão com o banco de dados: " . $mysqli->connect_error
    ]);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nome = isset($_POST['nome']) ? $_POST['nome'] : '';
    $cpf = isset($_POST['cpf']) ? $_POST['cpf'] : '';
    $valor = isset($_POST['valor']) ? str_replace(',', '.', $_POST['valor']) : 0.00; // Corrige vírgula para ponto
    $descricao = isset($_POST['descricao']) ? $_POST['descricao'] : '';

    if ($valor <= 0) {
        http_response_code(400);
        echo json_encode([
            "statusCode" => 400,
            "message" => "Valor inválido."
        ]);
        exit;
    }

    // Obter o user_id da sessão
    $userId = $_SESSION['user_id'];

    // Verificar se cash_in_active está ativo para o usuário
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

    // Obter as configurações de taxas da tabela config
    $result = $mysqli->query("SELECT tax_min, tax_cashin, tax_cashout FROM config WHERE id = 0 LIMIT 1");
    if ($result->num_rows > 0) {
        $config = $result->fetch_assoc();
        $tax_min = $config['tax_min']; // Valor mínimo da taxa
        $tax_cashin = $config['tax_cashin']; // Taxa do cashin (percentual)
    } else {
        http_response_code(500);
        echo json_encode([
            "statusCode" => 500,
            "message" => "Configuração não encontrada na tabela config."
        ]);
        exit;
    }

    // Calcular a taxa dand thecordo com o valor and the porcentagem configurada
    $taxaCalculada = ($valor * $tax_cashin) / 100;

    // Sand the taxa calculada for menor que o valor mínimo, usa o tax_min
    if ($taxaCalculada < $tax_min) {
        $taxaCalculada = $tax_min;
    }

    // Gerar o token dand thecesso via cURL
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

                    // Função para gerar um ID no formato "e76e996f57849498d0ddm4ebrz4y5g55"
                    function generateId() {
                        $id = bin2hex(random_bytes(16)); // Gera 32 caracteres hexadecimais
                        $randomPart = substr(str_shuffle('abcdefghijklmnopqrstuvwxyz0123456789'), 0, 6); // 6 caracteres aleatórios
                        return 'e' . substr($id, 0, 26) . $randomPart; // Combina o hex e o random
                    }

                    // Função para gerar um end2end no formato "E10573521202412071524aP3EdcJV8eP"
                    function generateEnd2End() {
                        $prefix = 'E'; // Prefixo fixo
                        $datePart = date('YmdHis'); // Data no formato ano, mês, dia, hora, minuto, segundo
                        $randomPart = substr(str_shuffle('ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789'), 0, 8); // 8 caracteres aleatórios
                        return $prefix . $datePart . $randomPart;
                    }

                    // Gerar os valores
                    $id = generateId();
                    $end2end = generateEnd2End();

                    // Exemplo de uso no seu código
                    $stmt = $mysqli->prepare("INSERT INTO transactions (id, end2end, external_id, amount, created_at, status, type, user_id, descricao, nome, document, tax) VALUES (?, ?, ?, ?, NOW(), 'PENDING', 'DEPOSIT', ?, ?, ?, ?, ?)");
                    $stmt->bind_param("sssdissss", $id, $end2end, $externalId, $valor, $userId, $descricao, $nome, $cpf, $taxaCalculada);

                    if ($stmt->execute()) {
                        http_response_code(200);
                        echo $qrcode;
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

            curl_close($curl);
        } else {
            http_response_code(500);
            echo json_encode([
                "statusCode" => 500,
                "message" => "Error ao obter token dand thecesso."
            ]);
        }
    }

    curl_close($ch);
} else {
    http_response_code(405);
    echo json_encode([
        "statusCode" => 405,
        "message" => "Método não permitido."
    ]);
}
