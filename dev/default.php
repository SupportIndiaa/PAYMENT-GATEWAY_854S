<!DOCTYPE html>
<html lang="pt-BR">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Documentation: Gerar PIX</title> <!-- Bootstrap CSS -->
  <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet"> <!-- Estilos customizados -->
  <style>
    body {             background-color: #f4f8fb;             font-family: 'Arial', sans-serif;             color: #333;         }          header {             background: #ffffff;             border-bottom: 1px solid #e0e0e0;             padding: 20px 0;             text-align: center;         }          header h1 {             font-size: 2rem;             color: #4caf50;         }          header p {             font-size: 1.1rem;             color: #666;         }          .navbar-custom {             background-color: #4caf50;         }          .navbar-custom .navbar-nav .nav-link {             color: white;         }          .navbar-custom .navbar-nav .nav-link:hover {             color: #388e3c;         }          .section-title {             color: #4caf50;             margin-top: 40px;             margin-bottom: 20px;         }          .section-content {             background-color: #fff;             padding: 20px;             border-radius: 8px;             box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);         }          .img-fluid-custom {             max-height: 400px;             width: 100%;             object-fit: cover;             border-radius: 8px;             margin-top: 20px;         }          /* Estilo para o bloco de código */         .code-block {             background-color: #2d2d2d;             color: #f8f8f2;             padding: 15px;             border-radius: 8px;             border: 1px solid #444;             overflow-x: auto;             font-family: 'Courier New', monospace;             white-space: pre-wrap;             word-wrap: break-word;             font-size: 0.9rem;             line-height: 1.5;         }          .btn-custom {             background-color: #4caf50;             color: white;         }          .btn-custom:hover {             background-color: #388e3c;         }          footer {             background-color: #ffffff;             text-align: center;             padding: 20px;             border-top: 1px solid #e0e0e0;         }          footer a {             color: #4caf50;             text-decoration: none;         }          footer a:hover {             text-decoration: underline;         }          .table th {             background-color: #4caf50;             color: white;         }          .table-striped tbody tr:nth-child(odd) {             background-color: #f9f9f9;         }          .navbar-toggler-icon {             background-color: white;         }          .alert-custom {             padding: 15px;             margin-top: 20px;             border-radius: 5px;         }          .alert-success {             background-color: #d4edda;             border-color: #c3e6cb;             color: #155724;         }          .alert-danger {             background-color: #f8d7da;             border-color: #f5c6cb;             color: #721c24;         }          .section-content {             margin-bottom: 30px;         }          /* Sidebar */         .sidebar {             background-color: #2e3b4e;             color: #fff;             height: 100vh;             position: fixed;             top: 0;             left: 0;             width: 250px;             padding-top: 20px;         }          .sidebar a {             color: white;             text-decoration: none;             padding: 10px 15px;             display: block;         }          .sidebar a:hover {             background-color: #4caf50;         }          .main-content {             margin-left: 260px;             padding: 20px;         }          .navbar-toggler-icon {             background-color: white;         }          .navbar-custom {             box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);         }     
  </style>
</head>

<body>
  <!-- Sidebar -->
  <div class="sidebar">
    <h4 class="text-center text-white">Documentation PIX</h4>
    <ul class="list-unstyled">
      <li><a href="#introducao">Introdução - Gerar QRCode</a></li>
      <li><a href="#codigo">Exemplo de Código - Gerar QRCode</a></li>
      <li><a href="#parametros">Parâmetros - Gerar QRCode</a></li>
      <li><a href="#respostas">Responses da API - Gerar QRCode</a></li>
      
      <li><a href="#introducao1">Introdução - Transferência PIX</a></li>
      <li><a href="#codigo1">Exemplo de Código - Transferência PIX</a></li>
      <li><a href="#parametros1">Parâmetros - Transferência PIX</a></li>
      <li><a href="#respostas1">Responses da API - Transferência PIX</a></li>
      
      <li><a href="#webhook">WEBHOOK-Evento de Pagamento</a></li>
      <li><a href="#webhook1">WEBHOOK-Evento de Transferência</a></li>
    </ul>
  </div> <!-- Container principal -->
  <div class="main-content">
    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg navbar-custom"> <a class="navbar-brand text-white" href="#">Gerar PIX</a> </nav> <!-- Header -->
    <header>
      <h1>Documentation: Gerar PIX</h1>
      <p>Saiba como utilizar o script para gerar PIX em sua aplicação.</p>
    </header> <!-- Seção Introdução -->
    <section id="introducao">
      <h2 class="section-title">Introdução</h2>
      <div class="section-content">
        <p>Este script permitand the geração de um QR Code PIX utilizando um endpoint específico. É ideal para aplicações que precisam de integração com pagamentos via PIX.</p>
      </div>
    </section> <!-- Seção Exemplo de Código -->
    <section id="codigo">
      <h2 class="section-title">Exemplo de Código</h2>
      <div class="section-content"> <button class="btn btn-custom mb-3" onclick="copyToClipboard('code1')">Copy Código</button>
        <div class="code-block" id="code1"> &lt;?php<br> $apiUrl = 'https://rpay.team/v3/pix/qrcode';<br><br> $postData = [<br> 'client_id' => 'seu_client_id',<br> 'client_secret' => 'seu_client_secret',<br> 'nome' => 'Nome do Cliente',<br> 'cpf' => 'Aadhaar/PAN do Cliente',<br> 'valor' => 100.00,<br> 'descricao' => 'Descrição do pagamento',<br> 'urlnoty' => 'https://seuservidor.com/callback'<br> ];<br><br> $ch = curl_init();<br><br> curl_setopt($ch, CURLOPT_URL, $apiUrl);<br> curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);<br> curl_setopt($ch, CURLOPT_POST, true);<br> curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($postData));<br> curl_setopt($ch, CURLOPT_HTTPHEADER, [<br> 'Content-Type: application/x-www-form-urlencoded',<br> ]);<br><br> $response = curl_exec($ch);<br><br> if (curl_errno($ch)) {<br> echo 'Error cURL: ' . curl_error($ch);<br> } else {<br> echo 'Response do servidor: ' . $response;<br> }<br><br> curl_close($ch);<br> ?&gt; </div>
      </div>
    </section> <!-- Seção Parâmetros -->
    <section id="parametros">
      <h2 class="section-title">Parâmetros</h2>
      <div class="section-content">
        <table class="table table-striped table-bordered">
          <thead>
            <tr>
              <th>Parâmetro</th>
              <th>Descrição</th>
              <th>Exemplo</th>
            </tr>
          </thead>
          <tbody>
            <tr>
              <td>client_id</td>
              <td>Identificação do cliente na </td>
              <td>"seu_client_id"</td>
            </tr>
            <tr>
              <td>client_secret</td>
              <td>Chave secreta do cliente na </td>
              <td>"seu_client_secret"</td>
            </tr>
            <tr>
              <td>nome</td>
              <td>Nome do pagador.</td>
              <td>"João da Silva"</td>
            </tr>
            <tr>
              <td>cpf</td>
              <td>Aadhaar/PAN do pagador.</td>
              <td>"12345678901"</td>
            </tr>
            <tr>
              <td>valor</td>
              <td>Valor da transação.</td>
              <td>100.00</td>
            </tr>
            <tr>
              <td>descricao</td>
              <td>Descrição da transação.</td>
              <td>"Pagamento de serviços"</td>
            </tr>
            <tr>
              <td>urlnoty</td>
              <td>URL para receber notificações de callback.</td>
              <td>"https://seuservidor.com/callback"</td>
            </tr>
          </tbody>
        </table>
      </div>
    </section> <!-- Seção Responses da API -->
    <section id="respostas">
      <h2 class="section-title">Responses da API</h2>
      <div class="section-content">
        <div class="alert alert-success alert-custom"> <strong>Exemplo de Response 200:</strong>
          <div class="code-block"> {<br> "transactionId": "4392d1d7e408d3cec04fm1zf3gv7vkq1",<br> "external_id": "",<br> "status": "PENDING",<br> "amount": 15,<br> "calendar": {<br> "expiration": 3000,<br> "dueDate": "2024-10-07 04:41:05"<br> },<br> "debtor": {<br> "name": "Lucas Hernades",<br> "document": "12924586666"<br> },<br> "qrcode": "00020126850014br.gov.bcb.pix2563pix.voluti.com.br/qr/v3/at/6ed39bf2-bdc2-42b8-a95b-13d2212146b25204000053039865802BR5925BS PAYMENTS SOLUTIONS LTD6008SALVADOR62070503***63048D9B"<br> }<br> </div>
        </div>
        <div class="alert alert-danger alert-custom"> <strong>Exemplo de Response 401:</strong>
          <div class="code-block"> {<br> "statusCode": 401,<br> "message": "Error dand theutorização"<br> }<br> </div>
        </div>
      </div>
    </section>
  </div>
  
  
  <div class="main-content">
        <!-- Navbar -->
        <nav class="navbar navbar-expand-lg navbar-custom">
            <a class="navbar-brand text-white" href="#">Transferência PIX</a>
        </nav>

        <!-- Header -->
        <header>
            <h1>Documentation: Transferência PIX</h1>
            <p>Saiba como utilizar o script para realizar transferências via PIX em sua aplicação.</p>
        </header>

        <!-- Seção Introdução -->
        <section id="introducao1">
            <h2 class="section-title">Introdução</h2>
            <div class="section-content">
                <p>Este script permitand the realização de uma transferência PIX entre contas utilizando um endpoint específico. Ideal para sistemas que necessitam de integração com a API PIX para realizar pagamentos e transferências.</p>
            </div>
        </section>

        <!-- Seção Exemplo de Código -->
        <section id="codigo1">
            <h2 class="section-title">Exemplo de Código</h2>
            <div class="section-content">
                <button class="btn btn-custom mb-3" onclick="copyToClipboard('code1')">Copy Código</button>
                <div class="code-block" id="code1">
                    &lt;?php<br>
                    $apiUrl = 'https://rpay.team/v3/pix/payment';<br><br>

                    $postData = [<br>
                        'client_id' => 'rpay_4703432954',<br>
                        'client_secret' => '5969b5a5830625b35407e83bcb247ab7a7364480f522fafb5d8fde0232c95ff1',<br>
                        'nome' => 'cris',<br>
                        'cpf' => '11970142332',<br>
                        'valor' => 1.00,<br>
                        'chave_pix' => '11970142332',<br>
                        'urlnoty' => 'https://seudominio.com/callback'<br>
                    ];<br><br>

                    $ch = curl_init();<br><br>

                    curl_setopt($ch, CURLOPT_URL, $apiUrl);<br>
                    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);<br>
                    curl_setopt($ch, CURLOPT_POST, true);<br>
                    curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($postData));<br>
                    curl_setopt($ch, CURLOPT_HTTPHEADER, [<br>
                        'Content-Type: application/x-www-form-urlencoded',<br>
                    ]);<br><br>

                    $response = curl_exec($ch);<br><br>

                    if (curl_errno($ch)) {<br>
                        echo 'Error cURL: ' . curl_error($ch);<br>
                    } else {<br>
                        echo 'Response do servidor: ' . $response;<br>
                    }<br><br>

                    curl_close($ch);<br>
                    ?&gt;
                </div>
            </div>
        </section>

        <!-- Seção Parâmetros -->
        <section id="parametros1">
            <h2 class="section-title">Parâmetros</h2>
            <div class="section-content">
                <table class="table table-striped table-bordered">
                    <thead>
                        <tr>
                            <th>Parâmetro</th>
                            <th>Descrição</th>
                            <th>Exemplo</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>client_id</td>
                            <td>Identificação do cliente na </td>
                            <td>"seu_client_id"</td>
                        </tr>
                        <tr>
                            <td>client_secret</td>
                            <td>Chave secreta do cliente na </td>
                            <td>"seu_client_secret"</td>
                        </tr>
                        <tr>
                            <td>nome</td>
                            <td>Nome do pagador.</td>
                            <td>"cris"</td>
                        </tr>
                        <tr>
                            <td>cpf</td>
                            <td>Aadhaar/PAN do pagador.</td>
                            <td>"11970142332"</td>
                        </tr>
                        <tr>
                            <td>valor</td>
                            <td>Valor da transferência.</td>
                            <td>1.00</td>
                        </tr>
                        <tr>
                            <td>chave_pix</td>
                            <td>Chave PIX do destinatário.</td>
                            <td>"chave@pix.com"</td>
                        </tr>
                        <tr>
                            <td>urlnoty</td>
                            <td>URL para receber notificações de callback.</td>
                            <td>"https://seudominio.com/callback"</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </section>

        <!-- Seção Responses da API -->
        <section id="respostas1">
            <h2 class="section-title">Responses da API</h2>
            <div class="section-content">
                <div class="alert alert-success alert-custom">
                    <strong>Exemplo de Response 200:</strong>
                    <div class="code-block">[{"statusCode":200,"message":"Saque PIX processado com sucesso"}]</div>
                </div>
                <div class="alert alert-danger alert-custom">
                    <strong>Exemplo de Response 400:</strong>
                    <div class="code-block">{"statusCode":400,"message":"Saldo insuficiente para cobrir valor e taxas."}</div>
                </div>
            </div>
        </section>
        
        
        
        <section id="webhook">
            <h2 class="section-title">WEBHOOK-Evento de Pagamento</h2>
            <div class="section-content">
                <div class="alert alert-success alert-custom">
                    <strong>Será lhe enviado um alerta por meio de um Webhook assim que o pagamento for confirmado pelo nosso sistema, seguand thebaixo o modelo JSON do payload enviado:</strong>
                    <div class="code-block">{
    "requestBody": {
        "transactionType": "RECEIVEPIX",
        "transactionId": "c327ce8bee2a18565ec2m1zdu6px2keu",
        "external_id": "55aefd02e54e785fbb5a80faa19f8802",
        "amount": 15.00,
        "paymentType": "PIX",
        "status": "PAID",
        "dateApproval": "2024-10-07 16:07:10",
        "creditParty": {
            "name": "Lucas Hernadnes",
            "email": "contato@rpay.team",
            "taxId": "999999999"
        },
        "debitParty": {
            "bank": "RPAY SOLUCOES DE PAGAMENTOS LTDA",
            "taxId": "46872831000154"
        }
    }
}</div>
                </div>
            </div>
        </section>
        
        <section id="webhook1">
            <h2 class="section-title">WEBHOOK-Evento de Transferência</h2>
            <div class="section-content">
                <div class="alert alert-success alert-custom">
                    <strong>Será lhe enviado um alerta por meio de um Webhook assim que o pagamento for confirmado pelo nosso sistema, seguand thebaixo o modelo JSON do payload enviado:</strong>
                    <div class="code-block">{
    "transactionType": "PAYMENT",
    "transactionId": "798176179",
    "external_id": "ebceb2b835598ccad73ce42eb5etrh2m5",
    "amount": 1,
    "dateApproval": "2024-12-19 17:10:54",
    "statusCode": {
        "statusId": 1,
        "description": "Pagamento aprovado"
    }
}</div>
                </div>
            </div>
        </section>
    </div>
  <footer>
    <p>&copy; 2024 Documentation PIX. Desenvolvido com &hearts; para desenvolvedores. <br> <a href="#">Back ao topo</a></p>
  </footer> <!-- Bootstrap JS e dependências -->
  <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.2/dist/umd/popper.min.js"></script>
  <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script> <!-- Script de copiar código -->
  <script>
    function copyToClipboard(id) {             const codeBlock = document.getElementById(id).innerText;             navigator.clipboard.writeText(codeBlock).then(() => {                 alert('Código copiado para a área de transferência!');             }).catch(err => {                 alert('Error ao copiar código: ' + err);             });         }     
  </script>
</body>

</html>