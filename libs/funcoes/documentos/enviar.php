<?php
// Iniciar a sessão
session_start();

function loadEnv() {
    // Caminho do arquivo .env
    $envFilePath = __DIR__ . '/../../../.env';

    // Verifica se o arquivo .env existe
    if (!file_exists($envFilePath)) {
        die("Arquivo .env não encontrado.");
    }

    // Carrega as variáveis do arquivo .env
    $envVariables = parse_ini_file($envFilePath);

    // Definand thes variáveis dand thembiente no PHP
    foreach ($envVariables as $key => $value) {
        putenv("$key=$value");
    }
}

// Carregar variáveis do arquivo .env
loadEnv();

// Função de conexão com o banco de dados
function connectDb() {
    try {
        // Usando as variáveis carregadas do .env
        $dsn = 'mysql:host=' . getenv('DB_HOST') . ';dbname=' . getenv('DB_NAME') . ';charset=utf8';
        $pdo = new PDO($dsn, getenv('DB_USER'), getenv('DB_PASS') ?? '');
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        return $pdo;
    } catch (PDOException $e) {
        die("Error na conexão com o banco de dados: " . $e->getMessage());
    }
}

// Conectar ao banco de dados
$pdo = connectDb();

// Verifica se o usuário está logado
if (!isset($_SESSION['user_id'])) {
    // Se não estiver logado, retorna um erro em JSON
    http_response_code(400); // Bad Request
    echo json_encode([
        'statusCode' => 400,
        'message' => 'Access denied. Por favor, faça login.'
    ]);
    exit;
}

$userId = $_SESSION['user_id'];

// Verifica se os parâmetros necessários foram enviados
if (isset($_FILES['formFile']) && isset($_POST['documentId'])) {
    $formFile = $_FILES['formFile'];
    $documentId = $_POST['documentId'];

    // Verifica se o arquivo foi carregado corretamente
    if ($formFile['error'] !== UPLOAD_ERR_OK) {
        // Retorna um erro em JSON
        http_response_code(400); // Bad Request
        echo json_encode([
            'statusCode' => 400,
            'message' => 'Error ao enviar o arquivo.'
        ]);
        exit;
    }

    // Caminho para salvar o arquivo fisicamente
    $uploadDir = __DIR__ . '/uploads/'; // Diretório de uploads (modifique conforme necessário)
    $fileExtension = pathinfo($formFile['name'], PATHINFO_EXTENSION); // Extensão do arquivo
    $fileBaseName = pathinfo($formFile['name'], PATHINFO_FILENAME); // Nome base do arquivo (sem extensão)

    // Adiciona números aleatórios ao nome do arquivo
    $randomNumber = random_int(100000, 999999); // Gera um número aleatório de 6 dígitos
    $uniqueFileName = $fileBaseName . '_' . $randomNumber . '.' . $fileExtension; // Nome único
    $filePath = $uploadDir . $uniqueFileName; // Caminho completo onde o arquivo será salvo

    // Verifica se o diretório de upload existe, se não, cria-o
    if (!is_dir($uploadDir)) {
        mkdir($uploadDir, 0777, true);
    }

    // Move o arquivo para o diretório de uploads
    if (move_uploaded_file($formFile['tmp_name'], $filePath)) {
        // Verifica se o documento já existe na tabela users_documents
        $stmt = $pdo->prepare("SELECT * FROM users_documents WHERE user_id = :user_id AND document_id = :document_id");
        $stmt->bindParam(':user_id', $userId);
        $stmt->bindParam(':document_id', $documentId);
        $stmt->execute();
        $existingDocument = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($existingDocument) {
            // Se o documento já existe, faz a atualização com o novo caminho
            $stmt = $pdo->prepare("UPDATE users_documents SET file_path = :file_path WHERE user_id = :user_id AND document_id = :document_id");
            $stmt->bindParam(':file_path', $filePath);
            $stmt->bindParam(':user_id', $userId);
            $stmt->bindParam(':document_id', $documentId);
            $stmt->execute();

            // Retorna sucesso em JSON
            http_response_code(200); // OK
            echo json_encode([
                'success' => true,
                'message' => 'Arquivo atualizado com sucesso!',
                'fileName' => $uniqueFileName // Retorna o nome do arquivo gerado
            ]);
        } else {
            // Se o documento não existe, faz a inserção do novo caminho
            $stmt = $pdo->prepare("INSERT INTO users_documents (user_id, document_id, file_path) VALUES (:user_id, :document_id, :file_path)");
            $stmt->bindParam(':user_id', $userId);
            $stmt->bindParam(':document_id', $documentId);
            $stmt->bindParam(':file_path', $filePath);
            $stmt->execute();

            // Retorna sucesso em JSON
            http_response_code(200); // OK
            echo json_encode([
                'success' => true,
                'message' => 'Arquivo salvo com sucesso!',
                'fileName' => $uniqueFileName // Retorna o nome do arquivo gerado
            ]);
        }
    } else {
        // Se não conseguiu mover o arquivo, retorna erro
        http_response_code(500); // Internal Server Errorr
        echo json_encode([
            'statusCode' => 500,
            'message' => 'Error ao mover o arquivo para o diretório.'
        ]);
    }
} else {
    // Caso os parâmetros não tenham sido enviados corretamente
    http_response_code(400); // Bad Request
    echo json_encode([
        'statusCode' => 400,
        'message' => 'Parâmetros insuficientes.'
    ]);
}
?>
