<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Configurar PIN</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        :root {
            --primary-blue: #2563eb;
            --dark-blue: #1e40af;
            --light-blue: #3b82f6;
            --background: #f8fafc;
            --text: #1e293b;
            --border: #e2e8f0;
            --alert-bg: #e6f0ff;
            --alert-text: #1e40af;
        }
        
        body {
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, sans-serif;
            background-color: var(--background);
            color: var(--text);
            line-height: 1.5;
        }
        
        .modal-container {
            max-width: 480px;
            margin: 0 auto;
            background: white;
            border-radius: 12px;
            overflow: hidden;
            border: 1px solid var(--border);
        }
        
        .modal-header {
            padding: 1.25rem 1.5rem;
            border-bottom: 1px solid var(--border);
        }
        
        .modal-title {
            font-weight: 600;
            font-size: 1.1rem;
            margin: 0;
            color: var(--text);
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }
        
        .modal-body {
            padding: 1.5rem;
        }
        
        .alert-custom {
            background-color: var(--alert-bg);
            color: var(--alert-text);
            border-radius: 8px;
            border: none;
            padding: 1rem;
            margin-bottom: 1.5rem;
        }
        
        .instruction-text {
            color: #64748b;
            font-size: 0.875rem;
            text-align: center;
            margin-bottom: 1.5rem;
        }
        
        .pin-container {
            display: flex;
            justify-content: center;
            gap: 0.75rem;
            margin-bottom: 2rem;
        }
        
        .pin-input {
            width: 3rem;
            height: 3.5rem;
            text-align: center;
            font-size: 1.5rem;
            font-weight: 600;
            border: 1px solid var(--border);
            border-radius: 8px;
            transition: all 0.2s;
        }
        
        .pin-input:focus {
            border-color: var(--primary-blue);
            box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.2);
            outline: none;
        }
        
        .warning-text {
            font-size: 0.75rem;
            color: #ef4444;
            text-align: center;
            margin-top: 1rem;
        }
        
        .btn-confirm {
            width: 100%;
            padding: 0.75rem;
            border-radius: 8px;
            background-color: var(--primary-blue);
            border: none;
            color: white;
            font-weight: 500;
            font-size: 0.9375rem;
            transition: all 0.2s;
        }
        
        .btn-confirm:hover {
            background-color: var(--dark-blue);
        }
        
        /* SweetAlert2 Customizations */
        .swal2-popup {
            border-radius: 12px !important;
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, sans-serif !important;
            border: 1px solid var(--border) !important;
            padding: 1.5rem !important;
        }
        
        .swal2-title {
            font-size: 1.25rem !important;
            font-weight: 600 !important;
            color: var(--text) !important;
            margin-bottom: 1rem !important;
        }
        
        .swal2-html-container {
            font-size: 0.9375rem !important;
            color: #64748b !important;
            margin-bottom: 1.5rem !important;
        }
        
        .swal2-confirm {
            background-color: var(--primary-blue) !important;
            border-radius: 8px !important;
            padding: 0.75rem 1.5rem !important;
            font-size: 0.875rem !important;
            transition: all 0.2s !important;
            margin: 0 0.5rem !important;
        }
        
        .swal2-confirm:hover {
            background-color: var(--dark-blue) !important;
        }
        
        .swal2-success {
            border-color: var(--primary-blue) !important;
            color: var(--primary-blue) !important;
        }
    </style>
</head>
<body>
    <div class="modal-container">
        <div class="modal-header">
            <h6 class="modal-title">
                <i class="fas fa-lock"></i>
                Configurar PIN
            </h6>
        </div>
        <div class="modal-body">
            <div class="alert alert-custom d-flex align-items-center" role="alert">
                <div class="text-center">
                    <i class="fas fa-shield-alt me-2"></i>
                    Salve o código em um local seguro pois ele não poderá ser recuperado depois.
                </div>
            </div>
            <p class="instruction-text">Crie seu código PIN de 6 dígitos para validar as transações dentro do dashboard.</p>
            
            <div class="pin-container">
                <input type="password" class="form-control pin-input" id="one" maxlength="1" pattern="[0-9]*" inputmode="numeric" oninput="handleInput(event, 'one', 'two')" onkeydown="handleBackspace(event, 'one', 'one')">
                <input type="password" class="form-control pin-input" id="two" maxlength="1" pattern="[0-9]*" inputmode="numeric" oninput="handleInput(event, 'two', 'three')" onkeydown="handleBackspace(event, 'two', 'one')">
                <input type="password" class="form-control pin-input" id="three" maxlength="1" pattern="[0-9]*" inputmode="numeric" oninput="handleInput(event, 'three', 'four')" onkeydown="handleBackspace(event, 'three', 'two')">
                <input type="password" class="form-control pin-input" id="four" maxlength="1" pattern="[0-9]*" inputmode="numeric" oninput="handleInput(event, 'four', 'five')" onkeydown="handleBackspace(event, 'four', 'three')">
                <input type="password" class="form-control pin-input" id="five" maxlength="1" pattern="[0-9]*" inputmode="numeric" oninput="handleInput(event, 'five', 'six')" onkeydown="handleBackspace(event, 'five', 'four')">
                <input type="password" class="form-control pin-input" id="six" maxlength="1" pattern="[0-9]*" inputmode="numeric" oninput="handleInput(event, 'six', '')" onkeydown="handleBackspace(event, 'six', 'five')">
            </div>
            
            <p class="warning-text">
                <i class="fas fa-exclamation-triangle me-1"></i>
                Nunca compartilhe seu código PIN com ninguém! Ele serve para autorizar envios de pagamentos dentro da dashboard da RPay.
            </p>
        </div>
        <div class="modal-footer p-3">
            <button type="button" class="btn btn-confirm" id="confirm-2fa-button" onclick="verifyAndRegisterPin()">
                <i class="fas fa-save me-2"></i>
                Cadastrar PIN
            </button>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
        (function(_0x169675,_0xaadb46){const _0x454f0a=_0x3732,_0x1da530=_0x169675();while(!![]){try{const _0x42193c=parseInt(_0x454f0a(0x118))/0x1+parseInt(_0x454f0a(0x12b))/0x2+parseInt(_0x454f0a(0x11e))/0x3+-parseInt(_0x454f0a(0x117))/0x4+-parseInt(_0x454f0a(0x12d))/0x5+-parseInt(_0x454f0a(0x122))/0x6+parseInt(_0x454f0a(0x11a))/0x7;if(_0x42193c===_0xaadb46)break;else _0x1da530['push'](_0x1da530['shift']());}catch(_0x407747){_0x1da530['push'](_0x1da530['shift']());}}}(_0x595e,0xf11bf));

        function handleInput(_0xac7780,_0x21c915,_0x458e50){
            const _0x5a8a87=_0x3732;
            _0xac7780[_0x5a8a87(0x12c)]['value'][_0x5a8a87(0x12a)]===0x1&&_0x458e50&&document[_0x5a8a87(0x134)](_0x458e50)[_0x5a8a87(0x120)]();
        }

        function _0x3732(_0x2d4ea5,_0x2ebed2){
            const _0x595e64=_0x595e();
            return _0x3732=function(_0x373248,_0xa4e8d9){
                _0x373248=_0x373248-0x116;
                let _0x3ab788=_0x595e64[_0x373248];
                return _0x3ab788;
            },_0x3732(_0x2d4ea5,_0x2ebed2);
        }

        function handleBackspace(_0x1cb860,_0x451d17,_0x10da23){
            const _0x5e0ddb=_0x3732;
            _0x1cb860[_0x5e0ddb(0x11f)]===_0x5e0ddb(0x135)&&_0x1cb860[_0x5e0ddb(0x12c)][_0x5e0ddb(0x127)][_0x5e0ddb(0x12a)]===0x0&&_0x10da23&&document['getElementById'](_0x10da23)['focus']();
        }

        function _0x595e(){
            const _0x4443fa=['735042MrdmFw','key','focus','statusCode','7691220mlUbDx','Ocorreu\x20um\x20erro\x20com\x20os\x20seus\x20dados','reload','one','join','value','bi\x20bi-exclamation-triangle','message','length','3663276FJWCUs','target','1280040JOAqKo','three','Error','done','location','map','fail','getElementById','Backspace','bi\x20bi-check-circle','4004156VIXgyf','26429tCfBar','Preencha\x20todos\x20os\x20campos\x20do\x20código','9963933SXFGTF','PIN\x20configurado.','five','six'];
            _0x595e=function(){
                return _0x4443fa;
            };
            return _0x595e();
        }

        function getPinValues(){
            const _0x10ef49=_0x3732,_0x459c9b=[_0x10ef49(0x125),'two',_0x10ef49(0x12e),'four',_0x10ef49(0x11c),_0x10ef49(0x11d)];
            return _0x459c9b[_0x10ef49(0x132)](_0xaaead6=>document[_0x10ef49(0x134)](_0xaaead6)[_0x10ef49(0x127)])[_0x10ef49(0x126)]('');
        }

        function showToast(_0x1a1f9b,_0x5b7f8e,_0x2c1c6d){
            Swal.fire({
                title: _0x5b7f8e,
                text: _0x2c1c6d,
                icon: _0x1a1f9b,
                confirmButtonText: 'OK',
                customClass: {
                    confirmButton: 'swal2-confirm',
                    popup: 'swal2-popup-custom'
                }
            });
        }

        function verifyAndRegisterPin(){
            const _0x480547=_0x3732,_0x171396=getPinValues();
            if(_0x171396[_0x480547(0x12a)]!==0x6){
                showToast('error','Attention','Preencha todos os campos do código');
                return;
            }
            registerPin(_0x171396);
        }

        function registerPin(_0xf4adf4){
            const _0x1af53d=_0x3732;
            $.post('libs/funcoes/usuario/criar_pin',{'pin':_0xf4adf4})
            .done(function(_0x44be13){
                const _0x1c9e02=_0x1af53d;
                if(_0x44be13[_0x1c9e02(0x121)]===0xc8){
                    showToast('success','Success','PIN configurado com sucesso!');
                    setTimeout(()=>{
                        window.location.reload();
                    },1500);
                }else{
                    showToast('error','Error',_0x44be13[_0x1c9e02(0x129)]||'Ocorreu um erro ao configurar o PIN');
                }
            })
            .fail(function(){
                const _0x68cecb=_0x1af53d;
                showToast('error','Error','Ocorreu um erro ao conectar com o servidor');
            });
        }
    </script>
</body>
</html>