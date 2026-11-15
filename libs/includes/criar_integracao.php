<html>
<head>
    <style>
        /* Base minimalista otimizada */
        .minimal-modal-overlay {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: rgba(0, 0, 0, 0.4);
            backdrop-filter: blur(4px);
            z-index: 1055;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 16px;
        }
        
        .minimal-modal-container {
            background: white;
            border-radius: 16px;
            width: 100%;
            max-width: 480px;
            box-shadow: 0 8px 32px rgba(0, 0, 0, 0.12);
            overflow: hidden;
            max-height: 90vh;
            overflow-y: auto;
        }
        
        /* Header compacto */
        .modal-header {
            padding: 20px 24px 12px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            border-bottom: 1px solid #f1f5f9;
        }
        
        .modal-title {
            font-size: 18px;
            font-weight: 600;
            color: #1e293b;
            margin: 0;
            line-height: 1.2;
        }
        
        .btn-close {
            width: 32px;
            height: 32px;
            background: #f8fafc;
            border: 1px solid #e2e8f0;
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #64748b;
            cursor: pointer;
            transition: all 0.2s ease;
            flex-shrink: 0;
        }
        
        .btn-close:hover {
            background: #f1f5f9;
            color: #475569;
        }
        
        .btn-close::before {
            content: "✕";
            font-size: 14px;
            line-height: 1;
        }
        
        /* Body mais compacto */
        .modal-body {
            padding: 16px 24px 20px;
        }
        
        .form-label {
            font-size: 14px;
            font-weight: 500;
            color: #374151;
            margin: 0 0 6px 0;
            display: block;
        }
        
        .form-control {
            height: 40px;
            padding: 0 12px;
            border: 1px solid #d1d5db;
            border-radius: 8px;
            background: white;
            font-size: 14px;
            color: #1f2937;
            transition: all 0.2s ease;
            outline: none;
            font-weight: 400;
            width: 100%;
            box-sizing: border-box;
        }
        
        textarea.form-control {
            min-height: 70px;
            padding: 10px 12px;
            resize: vertical;
            font-family: inherit;
        }
        
        .form-control:focus {
            border-color: #06b6d4;
            box-shadow: 0 0 0 3px rgba(6, 182, 212, 0.1);
        }
        
        .form-control::placeholder {
            color: #9ca3af;
            font-weight: 400;
        }
        
        /* PIN inputs otimizados */
        .pin-container {
            display: flex;
            justify-content: center;
            gap: 8px;
            margin-top: 8px;
        }
        
        .box_code {
            width: 45px !important;
            height: 45px !important;
            border: 1px solid #d1d5db !important;
            border-radius: 8px !important;
            background: white !important;
            font-size: 16px !important;
            font-weight: 600 !important;
            color: #1f2937 !important;
            text-align: center !important;
            transition: all 0.2s ease !important;
            outline: none !important;
            margin: 0 !important;
        }
        
        .box_code:focus {
            border-color: #06b6d4 !important;
            box-shadow: 0 0 0 3px rgba(6, 182, 212, 0.1) !important;
            transform: scale(1.05) !important;
        }
        
        /* Checkbox com ícone */
        .form-check {
            display: flex;
            align-items: flex-start;
            gap: 10px;
            padding: 12px 16px;
            background: #f8fafc;
            border-radius: 8px;
            border: 1px solid #e2e8f0;
            margin-top: 16px;
        }
        
        .form-check-input {
            width: 18px;
            height: 18px;
            border: 1.5px solid #d1d5db;
            border-radius: 4px;
            background: white;
            cursor: pointer;
            flex-shrink: 0;
            margin-top: 1px;
            position: relative;
            appearance: none;
            -webkit-appearance: none;
        }
        
        .form-check-input:checked {
            background: #06b6d4;
            border-color: #06b6d4;
        }
        
        .form-check-input:checked::before {
            content: "✓";
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            color: white;
            font-size: 12px;
            font-weight: bold;
            line-height: 1;
        }
        
        .form-check-label {
            font-size: 13px;
            color: #4b5563;
            line-height: 1.4;
            cursor: pointer;
            user-select: none;
            margin: 0;
        }
        
        /* Botão otimizado */
        .btn.btn-success {
            height: 42px !important;
            padding: 0 24px !important;
            border: none !important;
            border-radius: 8px !important;
            background: #06b6d4 !important;
            color: white !important;
            font-size: 15px !important;
            font-weight: 600 !important;
            cursor: pointer !important;
            transition: all 0.2s ease !important;
            width: 100% !important;
            letter-spacing: 0.3px !important;
        }
        
        .btn.btn-success:hover:not(:disabled) {
            background: #0891b2 !important;
            transform: translateY(-1px) !important;
            box-shadow: 0 4px 12px rgba(6, 182, 212, 0.3) !important;
        }
        
        .btn.btn-success:disabled {
            background: #d1d5db !important;
            cursor: not-allowed !important;
            transform: none !important;
            box-shadow: none !important;
        }
        
        .modal-footer {
            padding: 12px 24px 20px;
            border-top: 1px solid #f1f5f9;
        }
        
        /* Spacing otimizado */
        .col-xl-12, .col-lg-12, .col-md-12, .col-sm-12 {
            margin-bottom: 16px;
        }
        
        .col-xl-12:last-child, .col-lg-12:last-child, .col-md-12:last-child, .col-sm-12:last-child {
            margin-bottom: 0;
        }
        
        .mt-3 {
            margin-top: 16px !important;
        }
        
        .row {
            margin: 0 !important;
        }
        
        /* Responsivo melhorado */
        @media (max-width: 768px) {
            .minimal-modal-overlay {
                padding: 12px;
            }
            
            .minimal-modal-container {
                max-width: 100%;
                width: 100%;
                max-height: 95vh;
            }
            
            .modal-header {
                padding: 16px 20px 10px;
            }
            
            .modal-body {
                padding: 12px 20px 16px;
            }
            
            .modal-footer {
                padding: 10px 20px 16px;
            }
            
            .modal-title {
                font-size: 17px;
            }
            
            .btn-close {
                width: 30px;
                height: 30px;
            }
            
            .form-control {
                height: 44px;
                font-size: 16px;
            }
            
            textarea.form-control {
                min-height: 80px;
            }
            
            .btn.btn-success {
                height: 46px !important;
                font-size: 16px !important;
            }
            
            .form-label {
                font-size: 14px;
            }
            
            /* PIN inputs mobile otimizado */
            .pin-container {
                gap: 6px;
                flex-wrap: wrap;
                justify-content: center;
            }
            
            .box_code {
                width: 42px !important;
                height: 42px !important;
                font-size: 18px !important;
            }
            
            .form-check {
                padding: 12px 14px;
            }
            
            .form-check-label {
                font-size: 14px;
            }
            
            .col-xl-12, .col-lg-12, .col-md-12, .col-sm-12 {
                margin-bottom: 14px;
            }
            
            .mt-3 {
                margin-top: 14px !important;
            }
        }
        
        @media (max-width: 480px) {
            .pin-container {
                gap: 4px;
            }
            
            .box_code {
                width: 38px !important;
                height: 38px !important;
                font-size: 16px !important;
            }
        }
    </style>
</head>
<body>
    <div class="minimal-modal-overlay">
        <div class="minimal-modal-container">
            <div class="modal-header">
                <h6 class="modal-title" id="staticBackdropLabel4">Gerar Chaves de Integração</h6>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="row gerar_qrcode">
                    <div class="col-xl-12 col-lg-12 col-md-12 col-sm-12">
                        <label for="input-nome" class="form-label">Nome do Projeto</label>
                        <input type="text" class="form-control" id="input-nome" placeholder="Nome do seu projeto.">
                    </div>
                    <div class="col-xl-12 col-lg-12 col-md-12 col-sm-12 mt-3">
                        <label for="input-nome" class="form-label">Detalhes do Projeto</label>
                        <textarea class="form-control" name="" id="" placeholder="Diga-nós um pouco mais sobre seu projeto..."></textarea>
                    </div>
                    <div class="col-xl-12 col-lg-12 col-md-12 col-sm-12">
                        <label for="input-nome" class="form-label">Domínio</label>
                        <input type="text" class="form-control" id="input-url" placeholder="https://seudominio.com.br">
                    </div>
                    <div class="col-xl-12 col-lg-12 col-md-12 col-sm-12 mt-3">
                        <label for="input-valor" class="form-label">    Para continuar, informe seu PIN de 6 dígitos</label>
                        <div class="pin-container">
                            <input type="password" class="form-control form-control-lg text-center box_code" id="one" maxlength="1" oninput="handleInput(event, 'one', 'two')" onkeydown="handleBackspace(event, 'one', 'one')">
                            <input type="password" class="form-control form-control-lg text-center box_code" id="two" maxlength="1" oninput="handleInput(event, 'two', 'three')" onkeydown="handleBackspace(event, 'two', 'one')">
                            <input type="password" class="form-control form-control-lg text-center box_code" id="three" maxlength="1" oninput="handleInput(event, 'three', 'four')" onkeydown="handleBackspace(event, 'three', 'two')">
                            <input type="password" class="form-control form-control-lg text-center box_code" id="four" maxlength="1" oninput="handleInput(event, 'four', 'five')" onkeydown="handleBackspace(event, 'four', 'three')">
                            <input type="password" class="form-control form-control-lg text-center box_code" id="five" maxlength="1" oninput="handleInput(event, 'five', 'six')" onkeydown="handleBackspace(event, 'five', 'four')">
                            <input type="password" class="form-control form-control-lg text-center box_code" id="six" maxlength="1" oninput="handleInput(event, 'six', '')" onkeydown="handleBackspace(event, 'six', 'five')">
                        </div>
                    </div>
                    <div class="form-check mt-3" style="margin-left:0px;">
                        <input class="form-check-input" type="checkbox" value="" id="flexCheckChecked" checked="">
                        <label class="form-check-label" for="flexCheckChecked">
                            Declaro que li e estou dand thecordo com os termos de uso
                        </label>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <div class="col-xl-12 col-lg-12 col-md-12 col-sm-12">
                    <input style="width:100%" type="button" class="form-control btn btn-success" id="input-button" value="Criar" disabled="">
                </div>
            </div>
        </div>
    </div>

    <script>
        const _0x1118fb=_0x5dfc;(function(_0xee64eb,_0x1c7b26){const _0x1011d5=_0x5dfc,_0xde8ecc=_0xee64eb();while(!![]){try{const _0x2de25e=-parseInt(_0x1011d5(0x1e3))/0x1*(-parseInt(_0x1011d5(0x1d2))/0x2)+parseInt(_0x1011d5(0x1d1))/0x3*(parseInt(_0x1011d5(0x1dd))/0x4)+parseInt(_0x1011d5(0x1d5))/0x5*(-parseInt(_0x1011d5(0x1d4))/0x6)+parseInt(_0x1011d5(0x1f2))/0x7*(-parseInt(_0x1011d5(0x1c3))/0x8)+-parseInt(_0x1011d5(0x1e7))/0x9+parseInt(_0x1011d5(0x1ee))/0xa*(-parseInt(_0x1011d5(0x1e0))/0xb)+parseInt(_0x1011d5(0x1b1))/0xc*(parseInt(_0x1011d5(0x1cb))/0xd);if(_0x2de25e===_0x1c7b26)break;else _0xde8ecc['push'](_0xde8ecc['shift']());}catch(_0x361b13){_0xde8ecc['push'](_0xde8ecc['shift']());}}}(_0x18b1,0x1ddd8));function _0x18b1(){const _0x57ff89=['1668Kcykcg','disabled','message','Chave\x20copiada','click','append','URL\x20incorreta.','input','clipboard','nome','querySelectorAll','Success','input-nome','.box_code','Armazene\x20suas\x20credenciais\x20que\x20aparecerá\x20apenas\x20uma\x20única\x20vez.<br><br>\x0a\x20\x20\x20\x20\x20\x20\x20\x20\x20\x20\x20\x20\x20\x20\x20\x20\x20\x20\x20\x20\x20\x20\x20\x20\x20\x20\x20\x20<strong>Client\x20ID:</strong>\x0a\x20\x20\x20\x20\x20\x20\x20\x20\x20\x20\x20\x20\x20\x20\x20\x20\x20\x20\x20\x20\x20\x20\x20\x20\x20\x20\x20\x20<button\x20onclick=\x22copyToClipboard(\x27','querySelector','input-button','location','212184mtbRZk','log','flexCheckChecked','.form-control,\x20.form-check-input','Backspace','fire','checked','focus','33007LQsaST','client_id','then','POST','replace','json','117246RmJXEF','138udQrjp','addEventListener','5142aLABql','1375lszZsS','nextElementSibling','input-url','\x27)\x22\x20class=\x22btn\x20btn-secondary-light\x20rounded-pill\x20btn-wave\x22>COPIAR\x20CLIENT\x20SECRET</button>','match','length','textarea','map','16unORrw','Error','value','279367kiIsMn','libs/funcoes/credenciais/gerar_chaves','test','501DQmHqy','dominio','Falha\x20ao\x20copiar','Success','509202SjGbQj','target','join','getElementById','\x27)\x22\x20class=\x22btn\x20btn-success-light\x20rounded-pill\x20btn-wave\x22>COPIAR\x20CLIENT\x20ID</button><br>\x0a\x20\x20\x20\x20\x20\x20\x20\x20\x20\x20\x20\x20\x20\x20\x20\x20\x20\x20\x20\x20\x20\x20\x20\x20\x20\x20\x20\x20<br><strong>Client\x20Secret:</strong>\x0a\x20\x20\x20\x20\x20\x20\x20\x20\x20\x20\x20\x20\x20\x20\x20\x20\x20\x20\x20\x20\x20\x20\x20\x20\x20\x20\x20\x20<button\x20onclick=\x22copyToClipboard(\x27','writeText','catch','30RAuvaL','descricao','Credenciais Atualizadas','Credenciais\x20Atualizadas','14IJzzUz','reload','keyup'];_0x18b1=function(){return _0x57ff89;};return _0x18b1();}function handleInput(_0x459e23,_0x3dd0be,_0x2fec97){const _0x504430=_0x5dfc;_0x459e23[_0x504430(0x1e8)][_0x504430(0x1df)][_0x504430(0x1da)]===0x1&&_0x2fec97&&_0x459e23[_0x504430(0x1e8)]['value'][_0x504430(0x1d9)](/^\d$/)&&document[_0x504430(0x1ea)](_0x2fec97)[_0x504430(0x1ca)]();}function handleBackspace(_0x65b2a8,_0x5b8640,_0x19c779){const _0x4b147f=_0x5dfc;_0x65b2a8['key']===_0x4b147f(0x1c7)&&_0x65b2a8[_0x4b147f(0x1e8)]['value'][_0x4b147f(0x1da)]===0x0&&_0x19c779&&document[_0x4b147f(0x1ea)](_0x19c779)[_0x4b147f(0x1ca)]();}document[_0x1118fb(0x1bb)](_0x1118fb(0x1c6))['forEach'](_0x16ebdc=>{const _0x2da678=_0x1118fb;_0x16ebdc[_0x2da678(0x1d3)](_0x2da678(0x1f4),checkForm),_0x16ebdc[_0x2da678(0x1d3)]('change',checkForm);}),document[_0x1118fb(0x1bb)](_0x1118fb(0x1be))['forEach'](_0xb4039=>{const _0x33be02=_0x1118fb;_0xb4039[_0x33be02(0x1d3)](_0x33be02(0x1b8),function(){const _0x3aa3df=_0x33be02;this[_0x3aa3df(0x1df)]=this[_0x3aa3df(0x1df)][_0x3aa3df(0x1cf)](/[^0-9]/g,''),handleInput(event,this['id'],this['nextElementSibling']?this[_0x3aa3df(0x1d6)]['id']:'');});});function checkForm(){const _0x379db5=_0x1118fb,_0x5eab36=document[_0x379db5(0x1ea)](_0x379db5(0x1bd))[_0x379db5(0x1df)],_0x4da53b=document['querySelector'](_0x379db5(0x1db))[_0x379db5(0x1df)],_0x470881=document['getElementById']('input-url')['value'],_0xf89bb1=[...document[_0x379db5(0x1bb)](_0x379db5(0x1be))]['every'](_0x4b2c65=>_0x4b2c65[_0x379db5(0x1df)]['trim']()[_0x379db5(0x1da)]===0x1),_0x573863=document[_0x379db5(0x1ea)](_0x379db5(0x1c5))['checked'];document['getElementById'](_0x379db5(0x1c1))[_0x379db5(0x1b2)]=!(_0x5eab36&&_0x4da53b&&_0xf89bb1&&_0x573863);}function validateURL(_0x435f38){const _0x5a5bad=_0x1118fb,_0x48a094=/^https:\/\/([\da-z\.-]+)\.([a-z\.]{2,6})([\/\w \.-]*)*\/?$/;return _0x48a094[_0x5a5bad(0x1e2)](_0x435f38);}document['getElementById'](_0x1118fb(0x1c1))[_0x1118fb(0x1d3)](_0x1118fb(0x1b5),function(){const _0x3a1662=_0x1118fb,_0x5a8dfc=document['getElementById'](_0x3a1662(0x1d7))[_0x3a1662(0x1df)],_0x2524ac=document[_0x3a1662(0x1ea)](_0x3a1662(0x1c5))[_0x3a1662(0x1c9)];if(!validateURL(_0x5a8dfc)){showToast(_0x3a1662(0x1f0),_0x3a1662(0x1de),_0x3a1662(0x1b7));return;}if(!_0x2524ac){showToast(_0x3a1662(0x1f0),_0x3a1662(0x1de),'Você\x20deve\x20concordar\x20com\x20os\x20termos.');return;}sendFormData();});function _0x5dfc(_0x2f7656,_0x45405e){const _0x18b1da=_0x18b1();return _0x18b1da[_0x2f7656-0x1b1];}function copyToClipboard(_0x78b98){const _0x5cc147=_0x1118fb;navigator[_0x5cc147(0x1b9)][_0x5cc147(0x1ec)](_0x78b98)[_0x5cc147(0x1cd)](function(){const _0x5672a5=_0x5cc147;showToast(_0x5672a5(0x1e6),_0x5672a5(0x1bc),_0x5672a5(0x1b4));},function(_0x3509aa){const _0x20a1b4=_0x5cc147;showToast('Chaves Geradas',_0x20a1b4(0x1de),_0x20a1b4(0x1e5));});}function sendFormData(){const _0x2fbbf6=_0x1118fb,_0xfc915a=new FormData();_0xfc915a[_0x2fbbf6(0x1b6)](_0x2fbbf6(0x1ba),document[_0x2fbbf6(0x1ea)](_0x2fbbf6(0x1bd))[_0x2fbbf6(0x1df)]),_0xfc915a[_0x2fbbf6(0x1b6)](_0x2fbbf6(0x1ef),document[_0x2fbbf6(0x1c0)](_0x2fbbf6(0x1db))[_0x2fbbf6(0x1df)]),_0xfc915a[_0x2fbbf6(0x1b6)](_0x2fbbf6(0x1e4),document[_0x2fbbf6(0x1ea)]('input-url')[_0x2fbbf6(0x1df)]);const _0x48e902=[...document[_0x2fbbf6(0x1bb)](_0x2fbbf6(0x1be))][_0x2fbbf6(0x1dc)](_0xe237e3=>_0xe237e3[_0x2fbbf6(0x1df)])[_0x2fbbf6(0x1e9)]('');_0xfc915a['append']('pin',_0x48e902),fetch(_0x2fbbf6(0x1e1),{'method':_0x2fbbf6(0x1ce),'body':_0xfc915a})[_0x2fbbf6(0x1cd)](_0x2c17d1=>_0x2c17d1[_0x2fbbf6(0x1d0)]())[_0x2fbbf6(0x1cd)](_0x55000c=>{const _0x1d41e8=_0x2fbbf6;_0x55000c['statusCode']===0xc8?(showToast(_0x1d41e8(0x1e6),_0x1d41e8(0x1bc),'Chaves\x20geradas.'),setTimeout(()=>{const _0x2db847=_0x1d41e8;Swal[_0x2db847(0x1c8)]({'title':_0x2db847(0x1f1),'html':_0x2db847(0x1bf)+_0x55000c[_0x2db847(0x1b3)][_0x2db847(0x1cc)]+_0x2db847(0x1eb)+_0x55000c['message']['client_secret']+_0x2db847(0x1d8),'icon':'success','confirmButtonText':'Confirmo\x20que\x20salvei\x20minhas\x20credenciais'})['then'](_0x4181c4=>{const _0x48a292=_0x2db847;_0x4181c4['isConfirmed']&&window[_0x48a292(0x1c2)][_0x48a292(0x1f3)]();});},0x3e8)):showToast('Error',_0x1d41e8(0x1de),_0x55000c['message']);})[_0x2fbbf6(0x1ed)](_0x5bcff2=>{const _0x2d0e36=_0x2fbbf6;console[_0x2d0e36(0x1c4)](_0x5bcff2),showToast('bi\x20bi-exclamation-circle',_0x2d0e36(0x1de),_0x5bcff2);});}
    </script>
</body>
</html>
