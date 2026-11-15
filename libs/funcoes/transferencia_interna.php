<?php
session_start();

// Função para carregar variáveis dand thembiente
function loadEnv() {
    $envFilePath = __DIR__ . '/../../.env';

    if (!file_exists($envFilePath)) {
        die("Arquivo .env não encontrado.");
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
    die("Connection failed: " . $mysqli->connect_error);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Obter dados do formulário (pode vir via FormData)
    $nome = isset($_POST['usuario']) ? $_POST['usuario'] : '';
    $valor = isset($_POST['valor']) ? str_replace(',', '.', $_POST['valor']) : 0.00; // Corrige vírgula para ponto
    $descricao = isset($_POST['descricao']) ? $_POST['descricao'] : '';
    $pin = isset($_POST['pin']) ? $_POST['pin'] : '';

    // Validação do valor
    if ($valor <= 0) {
        die(json_encode([ 
            "statusCode" => 400,
            "message" => "Valor inválido."
        ]));
    }

    // Verifica se o pin está configurado e se está correto
    $userId = $_SESSION['user_id']; // Pega o user_id da sessão

    $resultPin = $mysqli->query("SELECT pin FROM users WHERE id = '$userId'");
    if ($resultPin->num_rows > 0) {
        $user = $resultPin->fetch_assoc();
        if (empty($user['pin'])) {
            // Se o PIN não estiver configurado, solicita que o usuário ative o PIN
            echo json_encode([
                "statusCode" => 400,
                "message" => "PIN não configurado. Ative o PIN primeiro."
            ]);
            exit();
        }

        if ($user['pin'] !== $pin) {
            // Se o PIN estiver incorreto
            echo json_encode([
                "statusCode" => 401,
                "message" => "PIN incorreto."
            ]);
            exit();
        }
    } else {
        // Caso o usuário não seja encontrado na base de dados
        echo json_encode([
            "statusCode" => 404,
            "message" => "Usuário não encontrado."
        ]);
        exit();
    }

    // Obter as configurações de taxas da tabela config
    $result = $mysqli->query("SELECT tax_min, tax_cashin, tax_cashout, tax_internal FROM config WHERE id = 0 LIMIT 1");
    if ($result->num_rows > 0) {
        $config = $result->fetch_assoc();
        $tax_min = $config['tax_min']; // Valor mínimo da taxa
        $tax_cashin = $config['tax_cashin']; // Taxa do cashin (percentual)
        $tax_internal = $config['tax_internal']; // Verificar sand thes taxas internas estão habilitadas
    } else {
        echo json_encode([ 
            "statusCode" => 500,
            "message" => "Configuração não encontrada na tabela config."
        ]);
        exit();
    }

    // Calcular a taxa dand thecordo com o valor and the porcentagem configurada, se tax_internal for 1
    $taxaCalculada = 0;
    if ($tax_internal == 1) {
        $taxaCalculada = ($valor * $tax_cashin) / 100;

        // Sand the taxa calculada for menor que o valor mínimo, usa o tax_min
        if ($taxaCalculada < $tax_min) {
            $taxaCalculada = $tax_min;
        }
    }

    // Verificar se o usuário destinatário existe
    $resultUsuario = $mysqli->query("SELECT id, balance FROM users WHERE username = '$nome' LIMIT 1");

    if ($resultUsuario->num_rows > 0) {
        $userDestino = $resultUsuario->fetch_assoc();
        $userDestinoId = $userDestino['id'];
        $userDestinoBalance = $userDestino['balance'];

        // Verificar se o usuário de destino é o mesmo que o usuário logado
        if ($userId == $userDestinoId) {
            echo json_encode([
                "statusCode" => 401,
                "message" => "No é possível realizar essa operação com o próprio usuário."
            ]);
            exit();
        }
    } else {
        echo json_encode([
            "statusCode" => 404,
            "message" => "Usuário de destino não encontrado."
        ]);
        exit();
    }

    // Verificar saldo do usuário de origem (o que está logado)
    $resultBalance = $mysqli->query("SELECT balance FROM users WHERE id = '$userId'");
    if ($resultBalance->num_rows > 0) {
        $userOrigem = $resultBalance->fetch_assoc();
        $userOrigemBalance = $userOrigem['balance'];

        // Verifica se há saldo suficiente
        if ($userOrigemBalance < $valor) {
            echo json_encode([
                "statusCode" => 402,
                "message" => "Saldo insuficiente."
            ]);
            exit();
        }

        // Atualizar o saldo do usuário de origem e destino
        $newBalanceOrigem = $userOrigemBalance - $valor;
        $newBalanceDestino = $userDestinoBalance + $valor;

        // Transação de saldo
        $mysqli->begin_transaction();

        try {
            // Atualizar saldo do usuário de origem
            $stmtOrigem = $mysqli->prepare("UPDATE users SET balance = ? WHERE id = ?");
            $stmtOrigem->bind_param("di", $newBalanceOrigem, $userId);
            $stmtOrigem->execute();

            // Atualizar saldo do usuário de destino
            $stmtDestino = $mysqli->prepare("UPDATE users SET balance = ? WHERE id = ?");
            $stmtDestino->bind_param("di", $newBalanceDestino, $userDestinoId);
            $stmtDestino->execute();

            // Commit da transação
            $mysqli->commit();

            // Inserir a transação no banco de dados com status 'PAID' e external_id igual ao user_id do destino
            $id = uniqid(''); 
            $end2end = uniqid('');
            $externalId = $userDestinoId; // O user_id do usuário de destino

            // Adicionar o campo 'internal' (se necessário)
            $internal = 1;  // Definido conforme sua lógica de transação

            // Defina as variáveis para status e type
            $status = 'PAID';
            $type = 'INTERNAL_TRANSFER';

            // Preparar a consulta
            $stmtTransacao = $mysqli->prepare("INSERT INTO transactions (id, end2end, external_id, user_id, amount, created_at, status, type, nome, descricao, tax, internal) 
                                            VALUES (?, ?, ?, ?, ?, NOW(), ?, ?, ?, ?, ?, ?)");

            if (!$stmtTransacao) {
                die("Error na preparação da consulta: " . $mysqli->error);
            }

            // Passar os parâmetros corretamente no bind_param
            $stmtTransacao->bind_param("sssdisssddi", $id, $end2end, $externalId, $userId, $valor, $status, $type, $nome, $descricao, $taxaCalculada, $internal);

            // Executar a consulta
            $stmtTransacao->execute();

            
            echo json_encode([
                "statusCode" => 200,
                "message" => "Transferência realizada com sucesso"
            ]);
        } catch (Exception $e) {
            // Caso ocorra algum erro, faz rollback na transação
            $mysqli->rollback();
            echo json_encode([ 
                "statusCode" => 500,
                "message" => "Error ao realizar a transferência: " . $e->getMessage()
            ]);
        }
    } else {
        echo json_encode([ 
            "statusCode" => 500,
            "message" => "Error ao obter saldo do usuário de origem."
        ]);
    }

} else {
    echo json_encode([ 
        "statusCode" => 400,
        "message" => "Requisição inválida."
    ]);
}
