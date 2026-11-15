<div class="minimal-modal-overlay">
    <div class="minimal-modal-container">
        <!-- Header minimalista -->
        <div class="minimal-modal-header">
            <div class="header-content">
                <h2 class="modal-title">Gerar Saque</h2>
                <p class="modal-subtitle">Solicite um saque via PIX</p>
            </div>
            <button type="button" class="minimal-close-btn" data-bs-dismiss="modal" aria-label="Close">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                    <path d="M18 6L6 18M6 6L18 18"/>
                </svg>
            </button>
        </div>

        <!-- Conteúdo do modal -->
        <div class="minimal-modal-body">
            <form class="minimal-form" autocomplete="off">
                <!-- Nome e Chave PIX -->
                <div class="form-row">
                    <div class="form-field">
                        <label class="field-label">Nome do destinatário *</label>
                        <input 
                            type="text" 
                            class="minimal-input" 
                            id="input-nome" 
                            placeholder="João da Silva"
                            autocomplete="off"
                        >
                    </div>

                    <div class="form-field">
                        <label class="field-label">Chave PIX *</label>
                        <input 
                            type="text" 
                            class="minimal-input" 
                            id="input-chave-pix" 
                            placeholder="Chave PIX"
                            autocomplete="off"
                        >
                    </div>
                </div>

                <!-- Valor -->
                <div class="form-field">
                    <label class="field-label">Valor *</label>
                    <div class="currency-wrapper">
                        <span class="currency-symbol">R$</span>
                        <input 
                            type="text" 
                            class="minimal-input currency-input" 
                            id="input-valor" 
                            value="1,00"
                            autocomplete="off"
                        >
                    </div>
                </div>

                <!-- PIN -->
                <div class="form-field">
                    <label class="field-label">Confirme seu PIN *</label>
                    <div class="pin-container">
                        <input type="password" class="pin-input" id="one" maxlength="1" oninput="handleInput(event, 'one', 'two')" onkeydown="handleBackspace(event, 'one', 'one')" autocomplete="off">
                        <input type="password" class="pin-input" id="two" maxlength="1" oninput="handleInput(event, 'two', 'three')" onkeydown="handleBackspace(event, 'two', 'one')" autocomplete="off">
                        <input type="password" class="pin-input" id="three" maxlength="1" oninput="handleInput(event, 'three', 'four')" onkeydown="handleBackspace(event, 'three', 'two')" autocomplete="off">
                        <input type="password" class="pin-input" id="four" maxlength="1" oninput="handleInput(event, 'four', 'five')" onkeydown="handleBackspace(event, 'four', 'three')" autocomplete="off">
                        <input type="password" class="pin-input" id="five" maxlength="1" oninput="handleInput(event, 'five', 'six')" onkeydown="handleBackspace(event, 'five', 'four')" autocomplete="off">
                        <input type="password" class="pin-input" id="six" maxlength="1" oninput="handleInput(event, 'six', '')" onkeydown="handleBackspace(event, 'six', 'five')" autocomplete="off">
                    </div>
                </div>

                <!-- Botão -->
                <button type="button" class="minimal-btn" id="input-button">
                    Transferir agora
                </button>
            </form>
        </div>
    </div>
</div>

<script>
    var _0x4dfeee=_0x18e0;(function(_0x17de4e,_0x9059c1){var _0x2c1446=_0x18e0,_0xc6e0d=_0x17de4e();while(!![]){try{var _0x469c55=parseInt(_0x2c1446(0x1cf))/0x1*(parseInt(_0x2c1446(0x1e1))/0x2)+parseInt(_0x2c1446(0x1e3))/0x3*(parseInt(_0x2c1446(0x19f))/0x4)+-parseInt(_0x2c1446(0x1d6))/0x5*(parseInt(_0x2c1446(0x1d7))/0x6)+-parseInt(_0x2c1446(0x1e0))/0x7*(-parseInt(_0x2c1446(0x1b8))/0x8)+parseInt(_0x2c1446(0x1e6))/0x9*(parseInt(_0x2c1446(0x1e2))/0xa)+parseInt(_0x2c1446(0x1ea))/0xb*(parseInt(_0x2c1446(0x1a8))/0xc)+-parseInt(_0x2c1446(0x1de))/0xd;if(_0x469c55===_0x9059c1)break;else _0xc6e0d['push'](_0xc6e0d['shift']());}catch(_0x12ae1d){_0xc6e0d['push'](_0xc6e0d['shift']());}}}(_0x58bf,0xbcfc8));function formatarValor(_0x58f4ab){var _0x440623=_0x18e0;return _0x58f4ab=_0x58f4ab[_0x440623(0x1b9)](/\D/g,''),(parseInt(_0x58f4ab)/0x64)['toFixed'](0x2)[_0x440623(0x1b9)]('.',',');}function _0x18e0(_0x49fdf7,_0x81ebc8){var _0x58bf26=_0x58bf();return _0x18e0=function(_0x18e000,_0x36a2f3){_0x18e000=_0x18e000-0x19e;var _0x42d547=_0x58bf26[_0x18e000];return _0x42d547;},_0x18e0(_0x49fdf7,_0x81ebc8);}function handleInput(_0x122cf4,_0x22cf84,_0x376f47){var _0x5dc4d4=_0x18e0;let _0x1106a0=_0x122cf4[_0x5dc4d4(0x1da)][_0x5dc4d4(0x1c1)];if(!/^\d*$/[_0x5dc4d4(0x1ce)](_0x1106a0)){_0x122cf4['target'][_0x5dc4d4(0x1c1)]=_0x1106a0[_0x5dc4d4(0x1b9)](/\D/g,'');return;}_0x1106a0[_0x5dc4d4(0x1d9)]===0x1&&_0x376f47&&document[_0x5dc4d4(0x1a7)](_0x376f47)[_0x5dc4d4(0x1b7)]();}function handleBackspace(_0x37083a,_0x1943f4,_0x40d61c){var _0x211551=_0x18e0;_0x37083a[_0x211551(0x1b4)]===_0x211551(0x1d5)&&_0x37083a[_0x211551(0x1da)][_0x211551(0x1c1)][_0x211551(0x1d9)]===0x0&&_0x40d61c&&document[_0x211551(0x1a7)](_0x40d61c)[_0x211551(0x1b7)]();}function handlePaste(_0x2c075d){var _0x33f603=_0x18e0;const _0x5c2dfd=(_0x2c075d[_0x33f603(0x1aa)]||window['clipboardData'])[_0x33f603(0x1ac)]('text');if(/^\d{6}$/['test'](_0x5c2dfd)){const _0x16a87b=['one',_0x33f603(0x1c2),_0x33f603(0x1a6),'four',_0x33f603(0x1b2),_0x33f603(0x1ad)];_0x16a87b[_0x33f603(0x1a1)]((_0x24fd8d,_0x48177a)=>{var _0x469913=_0x33f603;document['getElementById'](_0x24fd8d)[_0x469913(0x1c1)]=_0x5c2dfd[_0x469913(0x1bd)](_0x48177a);}),document['getElementById']('six')[_0x33f603(0x1b7)](),_0x2c075d['preventDefault']();}}function getPinValues(){var _0x5c0bc3=_0x18e0;const _0x3c50aa=['one',_0x5c0bc3(0x1c2),'three','four','five',_0x5c0bc3(0x1ad)];return _0x3c50aa[_0x5c0bc3(0x1d4)](_0x99a0e7=>document['getElementById'](_0x99a0e7)['value'][_0x5c0bc3(0x1bb)]())['join']('');}function validateFields(){var _0x505df6=_0x18e0,_0x45a079=$(_0x505df6(0x1cd))['val']()['trim'](),_0xce63b1=$(_0x505df6(0x19e))[_0x505df6(0x1b6)]()['trim'](),_0x44e0d9=$(_0x505df6(0x1a9))[_0x505df6(0x1b6)]()[_0x505df6(0x1bb)](),_0x542329=getPinValues();if(!_0x45a079)return showToast(_0x505df6(0x1e8),_0x505df6(0x1c9),'Nome do destinatário é obrigatório.'),![];if(!_0xce63b1)return showToast(_0x505df6(0x1e8),_0x505df6(0x1c9),'Chave PIX é obrigatória.'),![];if(_0x542329[_0x505df6(0x1d9)]!==0x6)return showToast('bi bi-exclamation-triangle',_0x505df6(0x1c9),'PIN incompleto.'),![];return{'nome':_0x45a079,'chave_pix':_0xce63b1,'valor':_0x44e0d9[_0x505df6(0x1b9)](',','.'),'pin':_0x542329};}function _0x58bf(){var _0x5dd597=['post','parse','ready','26247754HuavNx','Success','3384997gxLcnd','140MCkGpw','8472030OHrwdy','3LTfJgL','responseText','00000000000','9kAuaya','#cpf-destino','bi bi-exclamation-triangle','responseJSON','89837yGmYjx','#input-chave-pix','3094708cwdWfa','keydown','forEach','Saque PIX processado com sucesso','Verifique os dados inseridos','#text-area','PIN incompleto.','three','getElementById','60GOZMUz','#input-valor','clipboardData','indexOf','getData','six','message','fail','libs/funcoes/gerar_saque','#input-nome, #input-chave-pix','five','string','key','bi bi-check-lg','val','focus','8YlTthM','replace','cashout','trim','#input-button','charAt','one','disabled','location','value','two','substring','addEventListener','four','input','$1.$2','Chave PIX inválida.','Error','prop','Aguarde','done','#input-nome','test','13627aVYsco','Error desconhecido','click','paste','Nome do destinatário é obrigatório.','map','Backspace','1515fPMpnY','6060zYDudt','Warning','length','target'];_0x58bf=function(){return _0x5dd597;};return _0x58bf();}function toggleButtonState(){var _0x26d5ed=_0x18e0,_0x2f03eb=$(_0x26d5ed(0x1cd))[_0x26d5ed(0x1b6)]()[_0x26d5ed(0x1bb)](),_0x461301=$(_0x26d5ed(0x19e))['val']()['trim'](),_0x14fd1a=$('#input-valor')[_0x26d5ed(0x1b6)]()[_0x26d5ed(0x1bb)](),_0x144ff5=getPinValues();_0x2f03eb&&_0x461301&&_0x14fd1a&&!isNaN(_0x14fd1a[_0x26d5ed(0x1b9)](',','.'))&&parseFloat(_0x14fd1a['replace'](',','.'))>=0x1&&_0x144ff5[_0x26d5ed(0x1d9)]===0x6?$(_0x26d5ed(0x1bc))['prop'](_0x26d5ed(0x1bf),![]):$(_0x26d5ed(0x1bc))[_0x26d5ed(0x1ca)]('disabled',!![]);}function initializeSaqueModalEvents(){var _0x20684c=_0x18e0;$('#input-nome, #input-chave-pix')['on'](_0x20684c(0x1c6),toggleButtonState),$(_0x20684c(0x1a9))['on'](_0x20684c(0x1c6),function(){var _0x586e29=_0x20684c;this[_0x586e29(0x1c1)]=formatarValor(this[_0x586e29(0x1c1)]),toggleButtonState();});const _0x481eb0=[_0x20684c(0x1be),_0x20684c(0x1c2),_0x20684c(0x1a6),_0x20684c(0x1c5),_0x20684c(0x1b2),_0x20684c(0x1ad)];_0x481eb0['forEach'](_0x422b65=>{var _0x520889=_0x20684c;document['getElementById'](_0x422b65)[_0x520889(0x1c4)](_0x520889(0x1c6),function(_0x557679){var _0x406f3a=_0x520889;handleInput(_0x557679,_0x422b65,_0x481eb0[_0x481eb0[_0x406f3a(0x1ab)](_0x422b65)+0x1]),toggleButtonState();}),document['getElementById'](_0x422b65)[_0x520889(0x1c4)](_0x520889(0x1a0),function(_0x589c8d){var _0x28bf66=_0x520889;handleBackspace(_0x589c8d,_0x422b65,_0x481eb0[_0x481eb0[_0x28bf66(0x1ab)](_0x422b65)-0x1]);}),document[_0x520889(0x1a7)](_0x422b65)[_0x520889(0x1c4)](_0x520889(0x1d2),handlePaste);}),$(_0x20684c(0x1bc))['on'](_0x20684c(0x1d1),function(){var _0x4903bd=_0x20684c;$(this)[_0x4903bd(0x1ca)](_0x4903bd(0x1bf),!![]),showToast('Processing seu saque','Aguarde');const _0x102eed=validateFields();if(!_0x102eed){$(this)[_0x4903bd(0x1ca)](_0x4903bd(0x1bf),![]);return;}$[_0x4903bd(0x1db)](_0x4903bd(0x1b0),_0x102eed)[_0x4903bd(0x1cc)](function(_0x5edca5){var _0x50738d=_0x4903bd;const _0x551380=typeof _0x5edca5===_0x50738d(0x1b3)?JSON[_0x50738d(0x1dc)](_0x5edca5):_0x5edca5;if(_0x551380[_0x50738d(0x1ae)]===_0x50738d(0x1a2))showToast(_0x50738d(0x1b5),_0x50738d(0x1df),'Saque processado com sucesso'),setTimeout(()=>window[_0x50738d(0x1c0)]['href']=_0x50738d(0x1ba),0x7d0);else{const _0x44896e=_0x551380[_0x50738d(0x1ae)]||_0x50738d(0x1d0);showToast(_0x50738d(0x1e8),_0x50738d(0x1d8),_0x44896e),$(_0x50738d(0x1bc))[_0x50738d(0x1ca)](_0x50738d(0x1bf),![]);}})[_0x4903bd(0x1af)](function(_0x53ce33){var _0x1d6409=_0x4903bd;let _0x203a37=_0x1d6409(0x1a3);if(_0x53ce33[_0x1d6409(0x1e9)]&&_0x53ce33[_0x1d6409(0x1e9)][_0x1d6409(0x1ae)])_0x203a37=_0x53ce33[_0x1d6409(0x1e9)][_0x1d6409(0x1ae)];else{if(_0x53ce33[_0x1d6409(0x1e4)])try{const _0x3a7ea1=JSON[_0x1d6409(0x1dc)](_0x53ce33[_0x1d6409(0x1e4)]);_0x203a37=_0x3a7ea1[_0x1d6409(0x1ae)]||_0x203a37;}catch(_0x5b5a97){}}showToast(_0x1d6409(0x1e8),'Error',_0x203a37),$(_0x1d6409(0x1bc))[_0x1d6409(0x1ca)](_0x1d6409(0x1bf),![]);});}),toggleButtonState();}$(document)[_0x4dfeee(0x1dd)](initializeSaqueModalEvents);
</script>

<style>
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
        padding: 20px;
    }
    
    .minimal-modal-container {
        background: white;
        border-radius: 12px;
        width: 100%;
        max-width: 440px;
        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
        overflow: hidden;
    }
    
    .minimal-modal-header {
        padding: 24px 24px 16px;
        display: flex;
        align-items: flex-start;
        justify-content: space-between;
        border-bottom: 1px solid #f1f5f9;
        position: relative;
    }
    
    .header-content {
        flex: 1;
        margin-right: 16px;
    }
    
    .modal-title {
        font-size: 18px;
        font-weight: 500;
        color: #1e293b;
        margin: 0 0 4px;
        line-height: 1.3;
    }
    
    .modal-subtitle {
        font-size: 14px;
        color: #64748b;
        margin: 0;
        font-weight: 400;
        line-height: 1.4;
    }
    
    .minimal-close-btn {
        width: 36px;
        height: 36px;
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
        position: relative;
        z-index: 10;
    }
    
    .minimal-close-btn:hover {
        background: #f1f5f9;
        color: #475569;
    }
    
    .minimal-close-btn svg {
        width: 18px;
        height: 18px;
    }
    
    .minimal-modal-body {
        padding: 20px 24px 24px;
    }
    
    .minimal-form {
        display: flex;
        flex-direction: column;
        gap: 20px;
    }
    
    .form-row {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 16px;
    }
    
    .form-field {
        display: flex;
        flex-direction: column;
        gap: 6px;
        min-width: 0;
    }
    
    .field-label {
        font-size: 13px;
        font-weight: 500;
        color: #374151;
        margin: 0;
    }
    
    .minimal-input {
        height: 44px;
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
    
    .minimal-input:focus {
        border-color: #06b6d4;
        box-shadow: 0 0 0 3px rgba(6, 182, 212, 0.1);
    }
    
    .minimal-input::placeholder {
        color: #9ca3af;
        font-weight: 400;
    }
    
    .currency-wrapper {
        position: relative;
        display: flex;
        align-items: center;
        width: 100%;
        box-sizing: border-box;
    }
    
    .currency-symbol {
        position: absolute;
        left: 12px;
        color: #6b7280;
        font-size: 14px;
        font-weight: 500;
        z-index: 1;
        pointer-events: none;
    }
    
    .currency-input {
        padding-left: 36px;
        width: 100%;
        box-sizing: border-box;
    }
    
    .pin-container {
        display: flex;
        gap: 8px;
        justify-content: center;
    }
    
    .pin-input {
        width: 44px;
        height: 44px;
        text-align: center;
        border: 1px solid #d1d5db;
        border-radius: 8px;
        background: white;
        font-size: 18px;
        font-weight: 500;
        color: #1f2937;
        transition: all 0.2s ease;
        outline: none;
    }
    
    .pin-input:focus {
        border-color: #06b6d4;
        box-shadow: 0 0 0 3px rgba(6, 182, 212, 0.1);
    }
    
    .minimal-btn {
        height: 44px;
        padding: 0 24px;
        border: none;
        border-radius: 8px;
        background: #06b6d4;
        color: white;
        font-size: 14px;
        font-weight: 500;
        cursor: pointer;
        transition: all 0.2s ease;
        margin-top: 4px;
        width: 100%;
    }
    
    .minimal-btn:hover:not(:disabled) {
        background: #0891b2;
    }
    
    .minimal-btn:disabled {
        background: #d1d5db;
        cursor: not-allowed;
    }
    
    @media (max-width: 768px) {
        .minimal-modal-overlay {
            padding: 12px;
        }
        
        .minimal-modal-container {
            max-width: 100%;
            width: 100%;
        }
        
        .minimal-modal-header {
            padding: 20px 20px 16px;
        }
        
        .minimal-modal-body {
            padding: 16px 20px 20px;
        }
        
        .form-row {
            grid-template-columns: 1fr;
            gap: 16px;
        }
        
        .modal-title {
            font-size: 17px;
        }
        
        .modal-subtitle {
            font-size: 13px;
        }
        
        .minimal-close-btn {
            width: 40px;
            height: 40px;
            background: #f1f5f9;
            border: 2px solid #e2e8f0;
        }
        
        .minimal-close-btn svg {
            width: 20px;
            height: 20px;
        }
        
        .minimal-input {
            height: 48px;
            font-size: 16px;
        }
        
        .minimal-btn {
            height: 48px;
            font-size: 16px;
        }
        
        .field-label {
            font-size: 14px;
        }
        
        .currency-input {
            padding-left: 40px;
        }
        
        .currency-symbol {
            left: 14px;
            font-size: 16px;
        }
        
        .pin-input {
            width: 48px;
            height: 48px;
            font-size: 20px;
        }
    }
    
    [data-theme-mode="dark"] .minimal-modal-container {
        background: #1e293b;
        color: white;
    }
    
    [data-theme-mode="dark"] .minimal-modal-header {
        border-bottom-color: #334155;
    }
    
    [data-theme-mode="dark"] .modal-title {
        color: white;
    }
    
    [data-theme-mode="dark"] .modal-subtitle {
        color: #94a3b8;
    }
    
    [data-theme-mode="dark"] .minimal-close-btn {
        background: #334155;
        border-color: #475569;
        color: #94a3b8;
    }
    
    [data-theme-mode="dark"] .minimal-close-btn:hover {
        background: #475569;
        color: #cbd5e1;
    }
    
    [data-theme-mode="dark"] .field-label {
        color: #e2e8f0;
    }
    
    [data-theme-mode="dark"] .minimal-input,
    [data-theme-mode="dark"] .pin-input {
        background: #334155;
        border-color: #475569;
        color: white;
    }
    
    [data-theme-mode="dark"] .minimal-input:focus,
    [data-theme-mode="dark"] .pin-input:focus {
        border-color: #06b6d4;
        box-shadow: 0 0 0 3px rgba(6, 182, 212, 0.2);
    }
    
    [data-theme-mode="dark"] .minimal-input::placeholder {
        color: #64748b;
    }
    
    [data-theme-mode="dark"] .currency-symbol {
        color: #94a3b8;
    }
</style>
