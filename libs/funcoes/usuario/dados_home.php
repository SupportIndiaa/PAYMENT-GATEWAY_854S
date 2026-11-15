<?php
session_start();

// Função para carregar as variáveis dand thembiente
function loadEnv() {
    $envFilePath = __DIR__ . '/../../../.env';

    if (!file_exists($envFilePath)) {
        die("Arquivo .env não encontrado.");
    }

    $envVariables = parse_ini_file($envFilePath);

    foreach ($envVariables as $key => $value) {
        putenv("$key=$value");
    }
}

loadEnv();

// Conectando ao banco de dados
$mysqli = new mysqli(getenv('DB_HOST'), getenv('DB_USER'), getenv('DB_PASS'), getenv('DB_NAME'));

if ($mysqli->connect_error) {
    die("Conexão falhou: " . $mysqli->connect_error);
}

// Função para obter os dados de transações do usuário
function getTransactionData($userId) {
    global $mysqli;

    // Inicializando as variáveis para armazenar as métricas totais
    $totalSaldo = 0;
    $totalFaturamento = 0;
    $totalTransacoes = 0;
    $totalEntradas = 0;
    $totalSaidas = 0;

    // Arrays para armazenar as transações
    $entradas = [];
    $saidas = [];

    // Query para buscar todas as transações do usuário
    $query = "
        SELECT amount, type, created_at, user_id, external_id 
        FROM transactions 
        WHERE (user_id = ? OR external_id = ?) 
        AND created_at IS NOT NULL
        AND status = 'PAID'
    ";
    $stmt = $mysqli->prepare($query);
    $stmt->bind_param("ii", $userId, $userId);
    $stmt->execute();
    $result = $stmt->get_result();

    // Iterando sobrand thes transações
    while ($row = $result->fetch_assoc()) {
        $amount = $row['amount'];
        $type = $row['type'];
        $date = date('Y-m-d', strtotime($row['created_at'])); // Formatar a data para yyyy-mm-dd
        $transactionUserId = $row['user_id'];
        $transactionExternalId = $row['external_id'];

        // Contabilizando entradas e saídas
        if ($type == 'DEPOSIT') {
            $totalEntradas += $amount;
            $totalFaturamento += $amount;
            $totalSaldo += $amount; // Adiciona ao saldo total
            $entradas[] = [
                'valor' => $amount,
                'data' => $date,
                'tipo' => 'entrada'
            ];
        } elseif ($type == 'WITHDRAW') {
            $totalSaidas += $amount;
            $totalSaldo -= $amount; // Subtrai do saldo total
            $saidas[] = [
                'valor' => -$amount,
                'data' => $date,
                'tipo' => 'saida'
            ];
        } elseif ($type == 'INTERNAL_TRANSFER') {
            // Verifica se é entrada ou saída para INTERNAL_TRANSFER
            if ($transactionExternalId == $userId) {
                // Entrada: external_id é igual ao user_id
                $totalEntradas += $amount;
                $totalFaturamento += $amount;
                $totalSaldo += $amount; // Adiciona ao saldo total
                $entradas[] = [
                    'valor' => $amount,
                    'data' => $date,
                    'tipo' => 'entrada'
                ];
            } elseif ($transactionUserId == $userId) {
                // Saída: user_id é igual ao user_id
                $totalSaidas += $amount;
                $totalSaldo -= $amount; // Subtrai do saldo total
                $saidas[] = [
                    'valor' => -$amount,
                    'data' => $date,
                    'tipo' => 'saida'
                ];
            }
        }

        // Incrementando o número total de transações
        $totalTransacoes++;
    }

    // Organizando as transações por data para retorno
    $saldoDiario = [];
    $faturamentoDiario = [];
    $quantidadeTransacoesDiaria = [];
    $ticketMedioDiario = [];

    // Agrupando por data
    foreach (array_merge($entradas, $saidas) as $transacao) {
        $date = $transacao['data'];

        // Organizar dados diários
        if (!isset($saldoDiario[$date])) {
            $saldoDiario[$date] = 0;
            $faturamentoDiario[$date] = 0;
            $quantidadeTransacoesDiaria[$date] = 0;
            $ticketMedioDiario[$date] = 0;  // Inicializa com 0
        }

        if ($transacao['tipo'] == 'entrada') {
            $saldoDiario[$date] += $transacao['valor'];
            $faturamentoDiario[$date] += $transacao['valor'];
        } else {
            $saldoDiario[$date] -= abs($transacao['valor']);
            $faturamentoDiario[$date] += abs($transacao['valor']);
        }

        // Incrementa o contador de transações diárias
        $quantidadeTransacoesDiaria[$date]++;

        // Atualiza a soma de depósitos e saques para o cálculo do ticket médio
        $ticketMedioDiario[$date] += $transacao['valor'];
    }

    // Calculando o ticket médio diário corretamente
    foreach ($ticketMedioDiario as $date => $totalValor) {
        $ticketMedioDiario[$date] = abs($totalValor) / $quantidadeTransacoesDiaria[$date];  // Divide pela quantidade total de transações (depositos + saques)
    }

    // Preparando os dados finais para resposta JSON
    $data = [
        "saldoTotal" => $totalSaldo,
        "faturamentoTotal" => $totalFaturamento,
        "quantidadeTotalTransacoes" => $totalTransacoes,
        "ticketMedio" => ($totalEntradas + $totalSaidas) / max($totalTransacoes, 1), // Evitar divisão por zero
        "saldoDiario" => $saldoDiario,
        "faturamentoDiario" => $faturamentoDiario,
        "quantidadeTransacoesDiaria" => $quantidadeTransacoesDiaria,
        "ticketMedioDiario" => $ticketMedioDiario,
        "entradas" => $entradas,
        "saidas" => $saidas
    ];

    return $data;
}

// Verificando o ID do usuário
if (isset($_SESSION['user_id'])) {
    $userId = intval($_SESSION['user_id']); // O ID do usuário vem da sessão

    // Buscando e retornando os dados das transações
    $transactionData = getTransactionData($userId);
    echo json_encode($transactionData, JSON_PRETTY_PRINT);
} else {
    echo json_encode(["error" => "user_id é necessário."], JSON_PRETTY_PRINT);
}
?>
