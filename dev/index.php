<!DOCTYPE html>
<html lang="pt-BR">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Documentation API PIX | RPay Solutions </title>
  <!-- Bootstrap CSS -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
  <style>
    :root {
      --primary-blue: #2563eb;
      --light-blue: #3b82f6;
      --lighter-blue: #93c5fd;
      --dark-gray: #1f2937;
      --medium-gray: #6b7280;
      --light-gray: #f3f4f6;
      --border-radius: 8px;
    }

    body {
      font-family: 'Segoe UI', system-ui, -apple-system, sans-serif;
      color: var(--dark-gray);
      background-color: white;
      line-height: 1.6;
    }

    /* Sidebar Moderna */
    .sidebar {
      width: 280px;
      background: white;
      border-right: 1px solid #e5e7eb;
      position: fixed;
      height: 100vh;
      padding: 2rem 1.5rem;
      overflow-y: auto;
      box-shadow: 0 1px 3px rgba(0, 0, 0, 0.05);
    }

    .sidebar-header {
      margin-bottom: 2rem;
      text-align: center;
    }

    .sidebar-header img {
      max-width: 160px;
      margin-bottom: 1rem;
    }

    .sidebar-header h4 {
      color: var(--primary-blue);
      font-weight: 600;
      font-size: 1.1rem;
    }

    .sidebar-nav {
      list-style: none;
      padding: 0;
    }

    .sidebar-nav li {
      margin-bottom: 0.5rem;
    }

    .sidebar-nav a {
      display: flex;
      align-items: center;
      padding: 0.75rem 1rem;
      color: var(--medium-gray);
      text-decoration: none;
      border-radius: var(--border-radius);
      transition: all 0.2s ease;
    }

    .sidebar-nav a:hover, .sidebar-nav a.active {
      background-color: var(--light-gray);
      color: var(--primary-blue);
    }

    .sidebar-nav i {
      margin-right: 0.75rem;
      width: 20px;
      text-align: center;
    }

    /* Conteúdo Principal */
    .main-content {
      margin-left: 280px;
      padding: 2rem 3rem;
    }

    /* Header */
    .doc-header {
      margin-bottom: 3rem;
      padding-bottom: 1.5rem;
      border-bottom: 1px solid #e5e7eb;
    }

    .doc-header h1 {
      font-weight: 700;
      color: var(--dark-gray);
      font-size: 2.25rem;
      margin-bottom: 0.75rem;
    }

    .doc-header p {
      color: var(--medium-gray);
      font-size: 1.1rem;
      max-width: 800px;
    }

    /* Seções */
    .section {
      margin-bottom: 3rem;
    }

    .section-title {
      font-weight: 600;
      color: var(--primary-blue);
      margin-bottom: 1.5rem;
      font-size: 1.5rem;
      position: relative;
      padding-bottom: 0.5rem;
    }

    .section-title::after {
      content: '';
      position: absolute;
      bottom: 0;
      left: 0;
      width: 50px;
      height: 3px;
      background: var(--primary-blue);
      border-radius: 3px;
    }

    /* Cards de Conteúdo */
    .content-card {
      background: white;
      border-radius: var(--border-radius);
      border: 1px solid #e5e7eb;
      padding: 1.75rem;
      margin-bottom: 2rem;
      box-shadow: 0 1px 3px rgba(0, 0, 0, 0.03);
    }

    /* Blocos de Código */
    .code-block {
      background-color: #f8fafc;
      border: 1px solid #e2e8f0;
      border-radius: var(--border-radius);
      padding: 1.25rem;
      font-family: 'SFMono-Regular', Menlo, Monaco, Consolas, monospace;
      font-size: 0.9rem;
      line-height: 1.6;
      color: #334155;
      overflow-x: auto;
      position: relative;
      white-space: pre;
    }

    .copy-btn {
      position: absolute;
      top: 0.5rem;
      right: 0.5rem;
      background: var(--light-blue);
      color: white;
      border: none;
      border-radius: 4px;
      padding: 0.25rem 0.75rem;
      font-size: 0.8rem;
      cursor: pointer;
      transition: background 0.2s;
    }

    .copy-btn:hover {
      background: var(--primary-blue);
    }

    /* Tabelas */
    .table {
      border-radius: var(--border-radius);
      overflow: hidden;
    }

    .table thead th {
      background-color: var(--primary-blue);
      color: white;
      font-weight: 500;
      border: none;
    }

    .table tbody tr:nth-child(even) {
      background-color: #f9fafb;
    }

    /* Alertas */
    .alert-custom {
      border-radius: var(--border-radius);
      border-left: 4px solid;
      padding: 1.25rem;
    }

    .alert-success {
      background-color: #f0fdf4;
      border-color: #16a34a;
      color: #166534;
    }

    .alert-danger {
      background-color: #fef2f2;
      border-color: #dc2626;
      color: #991b1b;
    }

    /* Botões */
    .btn-custom {
      background-color: var(--primary-blue);
      color: white;
      border: none;
      border-radius: var(--border-radius);
      padding: 0.5rem 1.25rem;
      font-weight: 500;
      transition: all 0.2s;
    }

    .btn-custom:hover {
      background-color: #1d4ed8;
      transform: translateY(-1px);
      box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
    }

    /* Footer */
    footer {
      text-align: center;
      padding: 2rem;
      margin-top: 3rem;
      color: var(--medium-gray);
      font-size: 0.9rem;
      border-top: 1px solid #e5e7eb;
    }

    /* Responsividade */
    @media (max-width: 992px) {
      .sidebar {
        width: 100%;
        position: relative;
        height: auto;
        border-right: none;
        border-bottom: 1px solid #e5e7eb;
      }
      .main-content {
        margin-left: 0;
      }
    }
  </style>
</head>

<body>
  <!-- Sidebar Moderna -->
  <div class="sidebar">
    <div class="sidebar-header">
      <img src="https://rpay.team/imagens/log.png" alt="rpay Logo">
      <h4>Documentation API PIX</h4>
    </div>
    <ul class="sidebar-nav">
      <li><a href="#gerar-pix" class="active"><i class="fas fa-qrcode"></i> Gerar PIX</a></li>
      <li><a href="#transferencia-pix"><i class="fas fa-exchange-alt"></i> Transferência PIX</a></li>
      <li><a href="#webhook-pagamento"><i class="fas fa-bell"></i> Webhook Pagamento</a></li>
      <li><a href="#webhook-transferencia"><i class="fas fa-bell"></i> Webhook Transferência</a></li>
    </ul>
  </div>

  <!-- Conteúdo Principal -->
  <div class="main-content">
    <!-- Seção Gerar PIX -->
    <section id="gerar-pix" class="section">
      <div class="doc-header">
        <h1>API - Gerar PIX</h1>
        <p>Gere QR Codes PIX dinâmicos para recebimento de pagamentos em sua aplicação.</p>
      </div>

      <div class="content-card">
        <h2 class="section-title">Introdução</h2>
        <p>Esta API permitand the geração de QR Codes PIX dinâmicos para recebimento de pagamentos. O QR Code gerado tem validade de 30 minutos e pode ser utilizado em qualquer aplicativo de pagamento que suporte PIX.</p>
      </div>

      <div class="content-card">
        <h2 class="section-title">Exemplo de Implementação</h2>
        <button class="copy-btn" onclick="copyToClipboard('code-gerar-pix')">Copy</button>
        <div class="code-block" id="code-gerar-pix">
$apiUrl = 'https://rpay.team/v3/pix/qrcode';
$postData = [
  'client_id' => 'seu_client_id',
  'client_secret' => 'seu_client_secret',
  'nome' => 'Nome do Cliente',
  'cpf' => 'Aadhaar/PAN do Cliente',
  'valor' => 100.00,
  'descricao' => 'Descrição do pagamento',
  'urlnoty' => 'https://seudominio.com/callback'
];

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $apiUrl);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($postData));
curl_setopt($ch, CURLOPT_HTTPHEADER, [
  'Content-Type: application/x-www-form-urlencoded',
]);

$response = curl_exec($ch);
curl_close($ch);
        </div>
      </div>

      <div class="content-card">
        <h2 class="section-title">Parâmetros</h2>
        <table class="table table-striped">
          <thead>
            <tr>
              <th>Parâmetro</th>
              <th>Type</th>
              <th>Descrição</th>
            </tr>
          </thead>
          <tbody>
            <tr>
              <td><code>client_id</code></td>
              <td>String</td>
              <td>Identificação única do cliente</td>
            </tr>
            <tr>
              <td><code>client_secret</code></td>
              <td>String</td>
              <td>Chave secreta para autenticação</td>
            </tr>
            <tr>
              <td><code>nome</code></td>
              <td>String</td>
              <td>Nome completo do pagador</td>
            </tr>
            <tr>
              <td><code>cpf</code></td>
              <td>String</td>
              <td>Aadhaar/PAN do pagador (apenas números)</td>
            </tr>
            <tr>
              <td><code>valor</code></td>
              <td>Decimal</td>
              <td>Valor da transação (ex: 100.00)</td>
            </tr>
            <tr>
              <td><code>descricao</code></td>
              <td>String</td>
              <td>Descrição do pagamento (máx. 50 caracteres)</td>
            </tr>
            <tr>
              <td><code>urlnoty</code></td>
              <td>String</td>
              <td>URL para receber notificações de pagamento</td>
            </tr>
          </tbody>
        </table>
      </div>

      <div class="content-card">
        <h2 class="section-title">Responses da API</h2>
        
        <div class="alert alert-success alert-custom">
          <strong>Success (200)</strong>
          <div class="code-block mt-2">
{
  "transactionId": "4392d1d7e408d3cec04fm1zf3gv7vkq1",
  "status": "PENDING",
  "amount": 15,
  "calendar": {
    "expiration": 3000,
    "dueDate": "2024-10-07 04:41:05"
  },
  "debtor": {
    "name": "Lucas Hernandes",
    "document": "80091794005"
  },
  "qrcode": "00020126850014br.gov.bcb.pix2563pix.voluti.com.br/qr/v3/at/6ed39bf2-bdc2-42b8-a95b-13d2212146b25204000053039865802BR5925RPAY PAYMENTS SOLUTIONS LTD6008SALVADOR62070503***63048D9B"
}
          </div>
        </div>

        <div class="alert alert-danger alert-custom mt-3">
          <strong>Error (401)</strong>
          <div class="code-block mt-2">
{
  "statusCode": 401,
  "message": "Error dand theutorização"
}
          </div>
        </div>
      </div>
    </section>

    <!-- Seção Transferência PIX -->
    <section id="transferencia-pix" class="section">
      <div class="doc-header">
        <h1>API - Transferência PIX</h1>
        <p>Realize transferências PIX entre contas de forma programática.</p>
      </div>

      <div class="content-card">
        <h2 class="section-title">Introdução</h2>
        <p>Esta API permite realizar transferências PIX para qualquer chave PIX cadastrada no sistema. As transferências são processadas em tempo real quando dentro do horário bancário.</p>
      </div>

      <div class="content-card">
        <h2 class="section-title">Exemplo de Implementação</h2>
        <button class="copy-btn" onclick="copyToClipboard('code-transferencia-pix')">Copy</button>
        <div class="code-block" id="code-transferencia-pix">
$apiUrl = 'https://rpay.team/v3/pix/payment';
$postData = [
  'client_id' => 'SEU CLIENT ID',
  'client_secret' => 'SEU CLIENT SECRET',
  'nome' => 'Nome do cliente',
  'cpf' => 'Cpf do cliente',
  'valor' => 1.00,
  'chave_pix' => 'Chave pix Cliente',
  'urlnoty' => 'https://seudominio.com/callback'
];

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $apiUrl);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($postData));
curl_setopt($ch, CURLOPT_HTTPHEADER, [
  'Content-Type: application/x-www-form-urlencoded',
]);

$response = curl_exec($ch);
curl_close($ch);
        </div>
      </div>

      <div class="content-card">
        <h2 class="section-title">Parâmetros</h2>
        <table class="table table-striped">
          <thead>
            <tr>
              <th>Parâmetro</th>
              <th>Type</th>
              <th>Descrição</th>
            </tr>
          </thead>
          <tbody>
            <tr>
              <td><code>client_id</code></td>
              <td>String</td>
              <td>Identificação única do cliente</td>
            </tr>
            <tr>
              <td><code>client_secret</code></td>
              <td>String</td>
              <td>Chave secreta para autenticação</td>
            </tr>
            <tr>
              <td><code>nome</code></td>
              <td>String</td>
              <td>Nome completo do pagador</td>
            </tr>
            <tr>
              <td><code>cpf</code></td>
              <td>String</td>
              <td>Aadhaar/PAN do pagador (apenas números)</td>
            </tr>
            <tr>
              <td><code>valor</code></td>
              <td>Decimal</td>
              <td>Valor da transferência (ex: 1.00)</td>
            </tr>
            <tr>
              <td><code>chave_pix</code></td>
              <td>String</td>
              <td>Chave PIX do destinatário (Aadhaar/PAN, email, telefone ou chavand theleatória)</td>
            </tr>
            <tr>
              <td><code>urlnoty</code></td>
              <td>String</td>
              <td>URL para receber notificações de transferência</td>
            </tr>
          </tbody>
        </table>
      </div>

      <div class="content-card">
        <h2 class="section-title">Responses da API</h2>
        
        <div class="alert alert-success alert-custom">
          <strong>Success (200)</strong>
          <div class="code-block mt-2">
[{
  "statusCode": 200,
  "message": "Saque PIX processado com sucesso"
}]
          </div>
        </div>

        <div class="alert alert-danger alert-custom mt-3">
          <strong>Error (400)</strong>
          <div class="code-block mt-2">
{
  "statusCode": 400,
  "message": "Saldo insuficiente para cobrir valor e taxas."
}
          </div>
        </div>
      </div>
    </section>

    <!-- Seção Webhook Pagamento -->
    <section id="webhook-pagamento" class="section">
      <div class="doc-header">
        <h1>Webhook - Pagamento Recebido</h1>
        <p>Receba notificações em tempo real quando um pagamento PIX for confirmado.</p>
      </div>

      <div class="content-card">
        <h2 class="section-title">Estrutura do Webhook</h2>
        <p>Quando um pagamento PIX for confirmado, nosso sistema enviará uma requisição POST para o <code>urlnoty</code> informado com os seguintes dados:</p>
        
        <button class="copy-btn" onclick="copyToClipboard('code-webhook-pagamento')">Copy</button>
        <div class="code-block" id="code-webhook-pagamento">
{
  "requestBody": {
    "transactionType": "RECEIVEPIX",
    "transactionId": "c327ce8bee2a18565ec2m1zdu6px2keu",
    "external_id": "55aefd02e54e785fbb5a80faa19f8802",
    "amount": 15.00,
    "paymentType": "PIX",
    "status": "PAID",
    "dateApproval": "2024-10-07 16:07:10",
    "creditParty": {
      "name": "Lucas Hernandes",
      "email": "contato@rpay.team",
      "taxId": "999999999"
    },
    "debitParty": {
      "bank": "RPAY SOLUCOES DE PAGAMENTOS LTDA",
      "taxId": "46872831000154"
    }
  }
}
        </div>
      </div>

      <div class="content-card">
        <h2 class="section-title">Campos do Webhook</h2>
        <table class="table table-striped">
          <thead>
            <tr>
              <th>Campo</th>
              <th>Type</th>
              <th>Descrição</th>
            </tr>
          </thead>
          <tbody>
            <tr>
              <td><code>transactionType</code></td>
              <td>String</td>
              <td>Type de transação (RECEIVEPIX para pagamentos recebidos)</td>
            </tr>
            <tr>
              <td><code>transactionId</code></td>
              <td>String</td>
              <td>ID único da transação em nosso sistema</td>
            </tr>
            <tr>
              <td><code>external_id</code></td>
              <td>String</td>
              <td>ID externo (opcional) que você pode usar para referência</td>
            </tr>
            <tr>
              <td><code>amount</code></td>
              <td>Decimal</td>
              <td>Valor do pagamento</td>
            </tr>
            <tr>
              <td><code>status</code></td>
              <td>String</td>
              <td>Status do pagamento (PAID para confirmado)</td>
            </tr>
            <tr>
              <td><code>dateApproval</code></td>
              <td>String</td>
              <td>Data e hora da confirmação do pagamento</td>
            </tr>
          </tbody>
        </table>
      </div>
    </section>

    <!-- Seção Webhook Transferência -->
    <section id="webhook-transferencia" class="section">
      <div class="doc-header">
        <h1>Webhook - Transferência Concluída</h1>
        <p>Receba notificações quando uma transferência PIX for concluída.</p>
      </div>

      <div class="content-card">
        <h2 class="section-title">Estrutura do Webhook</h2>
        <p>Quando uma transferência PIX for concluída, nosso sistema enviará uma requisição POST para o <code>urlnoty</code> informado com os seguintes dados:</p>
        
        <button class="copy-btn" onclick="copyToClipboard('code-webhook-transferencia')">Copy</button>
        <div class="code-block" id="code-webhook-transferencia">
{
  "transactionType": "PAYMENT",
  "transactionId": "798176179",
  "external_id": "ebceb2b835598ccad73ce42eb5etrh2m5",
  "amount": 1,
  "dateApproval": "2024-12-19 17:10:54",
  "statusCode": {
    "statusId": 1,
    "description": "Pagamento aprovado"
  }
}
        </div>
      </div>

      <div class="content-card">
        <h2 class="section-title">Campos do Webhook</h2>
        <table class="table table-striped">
          <thead>
            <tr>
              <th>Campo</th>
              <th>Type</th>
              <th>Descrição</th>
            </tr>
          </thead>
          <tbody>
            <tr>
              <td><code>transactionType</code></td>
              <td>String</td>
              <td>Type de transação (PAYMENT para transferências enviadas)</td>
            </tr>
            <tr>
              <td><code>transactionId</code></td>
              <td>String</td>
              <td>ID único da transação em nosso sistema</td>
            </tr>
            <tr>
              <td><code>external_id</code></td>
              <td>String</td>
              <td>ID externo (opcional) que você pode usar para referência</td>
            </tr>
            <tr>
              <td><code>amount</code></td>
              <td>Decimal</td>
              <td>Valor da transferência</td>
            </tr>
            <tr>
              <td><code>dateApproval</code></td>
              <td>String</td>
              <td>Data e hora da conclusão da transferência</td>
            </tr>
            <tr>
              <td><code>statusCode</code></td>
              <td>Object</td>
              <td>Contém o status e descrição da transferência</td>
            </tr>
          </tbody>
        </table>
      </div>
    </section>

    <footer>
      <p>&copy; 2024 RPay Solutions - Todos os direitos reservados</p>
      <p class="mt-2"><small>Documentation da API PIX - Versão 3.0</small></p>
    </footer>
  </div>

  <!-- Bootstrap JS -->
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
  <script>
    function copyToClipboard(id) {
      const codeBlock = document.getElementById(id).textContent;
      navigator.clipboard.writeText(codeBlock).then(() => {
        alert('Código copiado para a área de transferência!');
      }).catch(err => {
        alert('Error ao copiar código: ' + err);
      });
    }
  </script>
</body>

</html>