<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PIXUP BR - Resetar Senha</title>
    <link id="style" href="assets/libs/bootstrap/css/bootstrap.min.css" rel="stylesheet">
    <link href="assets/css/styles.css" rel="stylesheet">
</head>

<body style="background-color: #121111;">
    <div class="row authentication mx-0">
        <div class="col-xxl-12 col-xl-12 col-lg-12">
            <div class="row justify-content-center align-items-center h-100">
                <div class="col-xxl-8 col-xl-9 col-lg-6 col-md-6 col-sm-8 col-12">
                    <div class="card custom-card shadow-none my-4">
                        <div class="card-body p-4">
                            <div class="mb-3 d-flex justify-content-center">
                                <a href="./">
                                    <img src="assets/images/brand-logos/desktop-logo.png" alt="logo" class="authentication-brand desktop-logo">
                                </a>
                            </div>
                            <p class="h5 mb-2">Recuperação de senha</p>
                            <div class="row gy-3">
                                <div class="col-xl-12">
                                    <div class="position-relative">
                                        <input type="text" class="form-control form-control-lg" id="user_forgot" placeholder="Informe seu usuário ou email">
                                    </div>
                                </div>
                            </div>
                            <div class="d-grid mt-4">
                                <!-- Link para o WhatsApp com a mensagem pronta -->
                                <a id="confirmar" class="btn btn-lg btn-primary" href="#" target="_blank">
                                    Recuperar Senha
                                </a>
                            </div>
                            <div class="text-center">
                                <p class="text-muted mt-3">Lembrou da senha? <a href="login.php" class="text-primary">Login</a></p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="assets/libs/bootstrap/js/bootstrap.bundle.min.js"></script>
    <script>
        document.getElementById('confirmar').addEventListener('click', function (e) {
            e.preventDefault();
            var userInput = document.getElementById('user_forgot').value;
            if (userInput) {
                var mensagem = "Olá%20gerente,%20redefina%20minha%20senha%20por%20favor.%20Usuário%20ou%20Email Address:%20" + encodeURIComponent(userInput);
                var whatsappLink = "https://wa.me/?text=" + mensagem;
                window.location.href = whatsappLink;
            } else {
                alert("Por favor, insira seu usuário ou email.");
            }
        });
    </script>
</body>

</html>
