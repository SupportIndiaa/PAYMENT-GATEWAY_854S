<?php
session_start();

// Função para carregar as variáveis do arquivo .env
function loadEnv($file) {
    if (!file_exists($file)) {
        throw new Exception("Arquivo .env não encontrado!");
    }

    $lines = file($file, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);

    foreach ($lines as $line) {
        if (strpos(trim($line), '#') === 0) {
            continue;
        }

        list($key, $value) = explode('=', $line, 2);
        $key = trim($key);
        $value = trim($value);

        putenv("$key=$value");
        $_ENV[$key] = $value;
    }
}

// Carregar as variáveis do arquivo .env
loadEnv('../../.env');  // Caminho do arquivo .env

// Função para conectar ao banco de dados
function connectDb() {
    // Obtendo as variáveis dand thembiente para a conexão com o banco
    $dbHost = getenv('DB_HOST');
    $dbName = getenv('DB_NAME');
    $dbUser = getenv('DB_USER');
    $dbPass = getenv('DB_PASS');

    try {
        // Conexão PDO com o banco de dados
        $pdo = new PDO("mysql:host=$dbHost;dbname=$dbName", $dbUser, $dbPass);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        return $pdo;
    } catch (PDOException $e) {
        die("Error ao conectar ao banco de dados: " . $e->getMessage());
    }
}

function sendPostbackRequest($postbackUrl, $transactionData) {
    // Inicializa o cURL
    $ch = curl_init();

    // Gera o token dand theutorização (exemplo, você pode ter quand thejustar conformand the sua lógica)
    $accessToken = "YOUR_ACCESS_TOKEN";  // Substitua com seu token gerado

    // Cria o comando curl completo (exemplo)
    $curlCommand = "curl --request POST \\\n";
    $curlCommand .= "--url '$postbackUrl' \\\n";
    $curlCommand .= "--header 'Authorization: Bearer $accessToken' \\\n";
    $curlCommand .= "--header 'Content-Type: application/json' \\\n";
    $curlCommand .= "--header 'User-Agent: insomnia/10.1.1' \\\n";
    $curlCommand .= "--data '" . json_encode($transactionData, JSON_PRETTY_PRINT) . "'\n";

    // Exibe o comando curl no log
    echo "Comando cURL:\n$curlCommand\n";

    // Definand thes opções do cURL
    curl_setopt($ch, CURLOPT_URL, $postbackUrl);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POST, true);
    
    // Envia os dados da transação (body da requisição)
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($transactionData));
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'Authorization: Bearer ' . $accessToken,
        'Content-Type: application/json',
        'User-Agent: insomnia/10.1.1',
    ]);

    // Executa a requisição
    $response = curl_exec($ch);

    // Verifica se houve erro na requisição cURL
    if (curl_errno($ch)) {
        echo 'Error no postback: ' . curl_error($ch);
    } else {
        echo 'Response do postback: ' . $response;
    }

    // Fecha a sessão cURL
    curl_close($ch);

    // Cria um nome dand therquivo dinâmico para o log (requisicao-01.txt, requisicao-02.txt, etc.)
    $logFile = 'requisicao-' . date('Y-m-d-H-i-s') . '.txt';  // Usa a data e hora para nomear o arquivo

    // Prepara o conteúdo para salvar no arquivo (dados enviados + resposta)
    $logData = "Comando cURL:\n" . $curlCommand . "\n";
    $logData .= "Dados Enviados:\n" . json_encode($transactionData, JSON_PRETTY_PRINT) . "\n\n";
    $logData .= "Response do Postback:\n" . $response . "\n\n";
    
    // Salva os dados no arquivo
    file_put_contents($logFile, $logData, FILE_APPEND);

    // Opcionalmente, exibe uma mensagem de sucesso
    echo "Dados salvos em $logFile";
}

// Modificação dentro da função updateTransactionAndBalance
function updateTransactionAndBalance($data) {
    // Conectar ao banco de dados
    $pdo = connectDb();

    // Consultar o valor dand thenti_fraude_min na configuração
    $stmtConfig = $pdo->prepare("SELECT anti_fraude_min FROM config LIMIT 1");
    $stmtConfig->execute();
    $config = $stmtConfig->fetch(PDO::FETCH_ASSOC);
    
    if ($config) {
        $antiFraudeMin = $config['anti_fraude_min'];  // O valor mínimo para anti-fraude
    } else {
        echo "Configuração dand thenti-fraude não encontrada.";
        return;
    }

    // Verificar se o corpo da requisição contém 'requestBody'
    if (isset($data['requestBody'])) {
        $transaction = $data['requestBody'];
        
        // Extrair o external_id, status e valor da transação
        $external_id = $transaction['external_id'];
        $status = isset($transaction['status']) ? $transaction['status'] : null;
        $transactionType = $transaction['transactionType'];  // Novo campo transactionType
        $amount = isset($transaction['amount']) ? $transaction['amount'] : 0;  // Valor da transação
        
        // Verificar se o valor da transação é maior ou igual ao anti_fraude_min
        if ($amount >= $antiFraudeMin) {
            // Atualizar o cash_out_active para 0 para o usuário da transação
            $stmtUser = $pdo->prepare("UPDATE users SET cash_out_active = 0 WHERE id = :user_id");
            $stmtUser->bindParam(':user_id', $_SESSION['user_id']);  // Usando o user_id da sessão
            $stmtUser->execute();
        }

        // Buscar a transação completa no banco de dados
        $stmt = $pdo->prepare("SELECT user_id, amount, tax, status, postback_url FROM transactions WHERE external_id = :external_id LIMIT 1");
        $stmt->bindParam(':external_id', $external_id);
        $stmt->execute();
        
        $transaction_data = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($transaction_data) {
            // Obter o user_id, amount, tax, status e postback_url da transação
            $user_id = $transaction_data['user_id'];
            $amount = $transaction_data['amount'];
            $tax = $transaction_data['tax'];
            $current_status = $transaction_data['status']; // Status atual da transação
            $postbackUrl = $transaction_data['postback_url']; // Obtém o postbackUrl da transação

            // Sand the transação já está com status PAID, não faz nada
            if ($current_status == 'PAID') {
                echo "Transação já está com status PAID. Nenhuma alteração será realizada.";
                return;  // Encerra a execução do script, sem fazer qualquer alteração
            }

            // Processamento para RECEIVEPIX
            if ($transactionType == 'RECEIVEPIX') {
                // Calcular o valor líquido após a taxa
                $netAmount = $amount - $tax; 
                
                // Atualizar a transação para PAID
                $stmtUpdateTransaction = $pdo->prepare("UPDATE transactions SET status = :status WHERE external_id = :external_id");
                $stmtUpdateTransaction->bindParam(':status', $status);
                $stmtUpdateTransaction->bindParam(':external_id', $external_id);

                if ($stmtUpdateTransaction->execute()) {
                    // Sand the transação for atualizada para PAID, atualizar o balance do usuário
                    if ($status == 'PAID') {
                        $confirmed_date = date('Y-m-d H:i:s');  // Data de confirmação

                        // Atualizar a transação com o status PAID e confirmed_date
                        $stmtUpdateConfirmedDate = $pdo->prepare("UPDATE transactions SET confirmed_date = :confirmed_date WHERE external_id = :external_id");
                        $stmtUpdateConfirmedDate->bindParam(':confirmed_date', $confirmed_date);
                        $stmtUpdateConfirmedDate->bindParam(':external_id', $external_id);
                        
                        if ($stmtUpdateConfirmedDate->execute()) {
                            // Atualizar o balance do usuário
                            $stmtBalance = $pdo->prepare("UPDATE users SET balance = balance + :netAmount WHERE id = :user_id");
                            $stmtBalance->bindParam(':netAmount', $netAmount);
                            $stmtBalance->bindParam(':user_id', $user_id);

                            if ($stmtBalance->execute()) {
                                echo "Status da transação atualizado, balance do usuário ajustado e confirmed_date registrado com sucesso!";
                                
                                // Submit notificação cURL para o Pushcut
                                $pushcutUrl = "https://api.pushcut.io/AfBV1Bg7JpqG90moX36I7/notifications/Minha%20Primeira%20Notifica%C3%A7%C3%A3o";
                                
                                // Prepara os dados dinâmicos da notificação
                                $pushcutData = [
                                    "text" => "Venda efetuada no valor de R$ " . number_format($netAmount, 2, ',', '.'),
                                    "title" => "Venda aprovada - R4 Intermediadora 🧡"
                                ];
                                
                                // Inicializa o cURL
                                $ch = curl_init();
                                curl_setopt($ch, CURLOPT_URL, $pushcutUrl);
                                curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                                curl_setopt($ch, CURLOPT_POST, true);
                                curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($pushcutData));
                                curl_setopt($ch, CURLOPT_HTTPHEADER, [
                                    'Content-Type: application/json',
                                    'User-Agent: insomnia/10.2.0'
                                ]);
                                
                                // Executa a requisição
                                $response = curl_exec($ch);
                                
                                // Verifica se houve erro
                                if (curl_errno($ch)) {
                                    echo "Error ao enviar notificação Pushcut: " . curl_error($ch);
                                } else {
                                    echo "Notificação enviada com sucesso: " . $response;
                                }
                                
                                // Fecha a sessão cURL
                                curl_close($ch);

                                // Submit o postback se existir o postbackUrl
                                if ($postbackUrl) {
                                    // Submit o postback com os dados da transação
                                    sendPostbackRequest($postbackUrl, $transaction);
                                }
                            } else {
                                echo "Error ao atualizar o balance do usuário.";
                            }
                        } else {
                            echo "Error ao atualizar a confirmed_date da transação.";
                        }
                    }
                } else {
                    echo "Error ao atualizar o status da transação.";
                }
            }

            // Processamento para PAYMENT
            if ($transactionType == 'PAYMENT' && isset($transaction['statusCode'])) {
                // Verificar se o statusCode.statusId é 1 (pagamento aprovado)
                $statusId = $transaction['statusCode']['statusId'];
                
                if ($statusId == 1) {
                    $confirmed_date = date('Y-m-d H:i:s');  // Data de confirmação
            
                    // Atualizar o status da transação para PAID
                    $stmtUpdateTransaction = $pdo->prepare("
                        UPDATE transactions 
                        SET status = 'PAID', confirmed_date = :confirmed_date 
                        WHERE external_id = :external_id
                    ");
                    $stmtUpdateTransaction->bindParam(':confirmed_date', $confirmed_date);
                    $stmtUpdateTransaction->bindParam(':external_id', $external_id);
            
                    // Executar a query e verificar o resultado
                    if ($stmtUpdateTransaction->execute()) {
                        echo "Status da transação atualizado para PAID.";

                        // Submit o postback se existir o postbackUrl
                        if ($postbackUrl) {
                            // Submit o postback com os dados da transação
                            sendPostbackRequest($postbackUrl, $transaction);
                        }
                    } else {
                        echo "Error ao atualizar o status da transação para PAID.";
                    }
                }
            }
            
        } else {
            echo "Transação não encontrada no banco de dados.";
        }
    } else {
        echo "Dados da transação não encontrados no corpo da requisição.";
    }
}

// Receber o corpo da requisição (JSON) e processá-lo
$body = file_get_contents("php://input");  // Recebe o JSON enviado no corpo
$data = json_decode($body, true);  // Decodifica o JSON para um array associativo PHP

// Chamar a função para atualizar a transação e balance do usuário
updateTransactionAndBalance($data);
?>
