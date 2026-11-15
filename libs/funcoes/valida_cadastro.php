<?php
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

// Função para conectar ao banco de dados
function connectDb() {
    $dbHost = getenv('DB_HOST');
    $dbName = getenv('DB_NAME');
    $dbUser = getenv('DB_USER');
    $dbPass = getenv('DB_PASS');

    try {
        $pdo = new PDO("mysql:host=$dbHost;dbname=$dbName", $dbUser, $dbPass);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        return $pdo;
    } catch (PDOException $e) {
        http_response_code(500);
        echo "Error ao conectar ao banco de dados: " . $e->getMessage();
        exit();
    }
}

// Função para validar os dados antes de cadastrar
function validateCadastro($nome, $email, $telefone, $username, $cnpj, $senha, $confirmarSenha, $faturamento) {
    if (empty($nome) || empty($email) || empty($telefone) || empty($username) || empty($cnpj) || empty($senha) || empty($confirmarSenha)) {
        http_response_code(400);
        return "Error: Todos os campos são obrigatórios.";
    }

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        http_response_code(400);
        return "Error: O e-mail informado é inválido.";
    }

    if ($senha !== $confirmarSenha) {
        http_response_code(400);
        return "Error: A senha and the confirmação de senha não são iguais.";
    }

    $pdo = connectDb();

    // Verificar duplicidade de dados no banco
    $checks = [
        'email' => $email,
        'telefone' => $telefone,
        'username' => $username,
        'cnpj' => $cnpj
    ];

    foreach ($checks as $field => $value) {
        $stmt = $pdo->prepare("SELECT COUNT(*) FROM users WHERE $field = :value");
        $stmt->bindParam(':value', $value);
        $stmt->execute();

        if ($stmt->fetchColumn() > 0) {
            http_response_code(409);
            return "Error: O $field já está sendo utilizado.";
        }
    }

    // Geração do ID aleatório (8 dígitos)
    $id = random_int(10000000, 99999999); // Garante um número entre 10000000 e 99999999

    // Inserção no banco de dados
    $stmt = $pdo->prepare("
        INSERT INTO users (id, username, password, name, email, telefone, cnpj, faturamento) 
        VALUES (:id, :username, :password, :name, :email, :telefone, :cnpj, :faturamento)
    ");

    // Bind dos parâmetros
    $stmt->bindParam(':id', $id);
    $stmt->bindParam(':username', $username);
    $stmt->bindParam(':password', $senha); // Senha em texto simples (não recomendada)
    $stmt->bindParam(':name', $nome);
    $stmt->bindParam(':email', $email);
    $stmt->bindParam(':telefone', $telefone);
    $stmt->bindParam(':cnpj', $cnpj);
    $stmt->bindParam(':faturamento', $faturamento);

    try {
        if ($stmt->execute()) {
            http_response_code(201);
            echo json_encode([
                'status' => 'success',
                'message' => 'Cadastro realizado com sucesso'
            ]);
        } else {
            http_response_code(500);
            return "Error ao realizar o cadastro.";
        }
    } catch (PDOException $e) {
        http_response_code(500);
        return "Error ao realizar o cadastro: " . $e->getMessage();
    }
}

// Carregar as variáveis do arquivo .env
try {
    loadEnv('../../.env');
} catch (Exception $e) {
    http_response_code(500);
    echo $e->getMessage();
    exit();
}

// Receber os dados do corpo da requisição via POST
$data = json_decode(file_get_contents("php://input"), true);

if ($data === null) {
    http_response_code(400);
    echo "Error: Dados inválidos ou não enviados.";
    exit();
}

$nome = $data['nome'] ?? '';
$email = $data['email'] ?? '';
$telefone = $data['telefone'] ?? '';
$username = $data['usuario'] ?? '';
$cnpj = $data['cnpj'] ?? '';
$senha = $data['senha'] ?? '';
$confirmarSenha = $data['confirmar_senha'] ?? '';
$faturamento = $data['faturamento'] ?? '';

$response = validateCadastro($nome, $email, $telefone, $username, $cnpj, $senha, $confirmarSenha, $faturamento);

echo $response;
