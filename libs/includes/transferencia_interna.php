<div class="minimal-modal-overlay">
    <div class="minimal-modal-container">
        <!-- Header minimalista -->
        <div class="minimal-modal-header">
            <div class="header-content">
                <h2 class="modal-title">Transferência Interna</h2>
                <p class="modal-subtitle">Transfira para outro usuário da plataforma</p>
            </div>
            <button type="button" class="minimal-close-btn" data-bs-dismiss="modal" aria-label="Close">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                    <path d="M18 6L6 18M6 6L18 18"/>
                </svg>
            </button>
        </div>

        <!-- Conteúdo do modal -->
        <div class="minimal-modal-body">
            <form class="minimal-form">
                <!-- Usuário recebedor -->
                <div class="form-field">
                    <label class="field-label">Usuário recebedor *</label>
                    <div class="username-wrapper">
                        <span class="username-symbol">@</span>
                        <input 
                            type="text" 
                            class="minimal-input username-input" 
                            id="input-search" 
                            placeholder="usuario"
                        >
                    </div>
                </div>

                <!-- Valor e Descrição -->
                <div class="form-row">
                    <div class="form-field">
                        <label class="field-label">Valor *</label>
                        <div class="currency-wrapper">
                            <span class="currency-symbol">R$</span>
                            <input 
                                type="number" 
                                class="minimal-input currency-input" 
                                id="input-valor" 
                                placeholder="0,00"
                            >
                        </div>
                    </div>

                    <div class="form-field">
                        <label class="field-label">Descrição</label>
                        <textarea 
                            class="minimal-textarea" 
                            id="text-area" 
                            rows="1"
                            placeholder="Descrição da transferência (opcional)"
                        ></textarea>
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
                    Submit pagamento
                </button>
            </form>
        </div>
    </div>
</div>

<script>
    // ... keep existing code (all JavaScript functions)
    const _0x5c2937=_0x3fe2;function _0x3fe2(_0x2514f9,_0x745ede){const _0x56a376=_0x56a3();return _0x3fe2=function(_0x3fe27a,_0x43edb4){_0x3fe27a=_0x3fe27a-0xd1;let _0xe25602=_0x56a376[_0x3fe27a];return _0xe25602;},_0x3fe2(_0x2514f9,_0x745ede);}(function(_0x2a8652,_0x511089){const _0x214266=_0x3fe2,_0x54f519=_0x2a8652();while(!![]){try{const _0xc8a60c=parseInt(_0x214266(0xef))/0x1+parseInt(_0x214266(0xed))/0x2+-parseInt(_0x214266(0xd5))/0x3*(-parseInt(_0x214266(0xea))/0x4)+-parseInt(_0x214266(0xe3))/0x5+parseInt(_0x214266(0x10b))/0x6*(parseInt(_0x214266(0xdf))/0x7)+parseInt(_0x214266(0xd6))/0x8+-parseInt(_0x214266(0x10f))/0x9*(parseInt(_0x214266(0xd8))/0xa);if(_0xc8a60c===_0x511089)break;else _0x54f519['push'](_0x54f519['shift']());}catch(_0x43f849){_0x54f519['push'](_0x54f519['shift']());}}}(_0x56a3,0x33072));function _0x56a3(){const _0x48bad5=['six','1013667OcsEms','209672auovcm','prop','890sCfCkI','responseJSON','four','parse','done','addEventListener','#input-button','7tadUVT','forEach','responseText','#input-search','640220tAHhGW','trim','charAt','paste','test','Transferência sendo Realizada','Success','4HXFvss','Transferência sendo Realizada','PIN\x20incompleto.','741102uppXZu','three','361094GrnphY','one','disabled','message','libs/funcoes/transferencia_interna','preventDefault','input','getElementById','replace','Backspace','toFixed','five','click','location','Error','focus','getData','Error','target','indexOf','key','Valor\x20deve\x20ser\x20um\x20número\x20maior\x20ou\x20igual\x20a\x201.','value','two','val','Aguarde','length','clipboardData','1827564WxWCrR','#input-valor','map','string','107523NYgDLE','keydown','ready','Usuário\x20recebedor\x20é\x20obrigatório.'];_0x56a3=function(){return _0x48bad5;};return _0x56a3();}function formatarValor(_0x3c56bb){const _0x38404a=_0x3fe2;return _0x3c56bb=_0x3c56bb[_0x38404a(0xf7)](/\D/g,''),(parseInt(_0x3c56bb)/0x64)[_0x38404a(0xf9)](0x2);}function handleInput(_0x5258b6,_0x751327,_0x4e0205){const _0x59c36f=_0x3fe2;let _0x49560d=_0x5258b6[_0x59c36f(0x101)][_0x59c36f(0x105)];if(!/^\d*$/[_0x59c36f(0xe7)](_0x49560d)){_0x5258b6[_0x59c36f(0x101)]['value']=_0x49560d[_0x59c36f(0xf7)](/\D/g,'');return;}_0x49560d['length']===0x1&&_0x4e0205&&document[_0x59c36f(0xf6)](_0x4e0205)[_0x59c36f(0xfe)]();}function handleBackspace(_0xa2c4ac,_0x36e825,_0x44a9ae){const _0x4fd755=_0x3fe2;_0xa2c4ac[_0x4fd755(0x103)]===_0x4fd755(0xf8)&&_0xa2c4ac[_0x4fd755(0x101)][_0x4fd755(0x105)][_0x4fd755(0x109)]===0x0&&_0x44a9ae&&document[_0x4fd755(0xf6)](_0x44a9ae)['focus']();}function handlePaste(_0x14f846){const _0x1d462d=_0x3fe2,_0x5a6fb6=(_0x14f846[_0x1d462d(0x10a)]||window[_0x1d462d(0x10a)])[_0x1d462d(0xff)]('text');if(/^\d{6}$/[_0x1d462d(0xe7)](_0x5a6fb6)){const _0x4d5f93=[_0x1d462d(0xf0),_0x1d462d(0x106),'three',_0x1d462d(0xda),_0x1d462d(0xfa),'six'];_0x4d5f93[_0x1d462d(0xe0)]((_0x43e630,_0xd3767e)=>{const _0x11c814=_0x1d462d;document[_0x11c814(0xf6)](_0x43e630)[_0x11c814(0x105)]=_0x5a6fb6[_0x11c814(0xe5)](_0xd3767e);}),document[_0x1d462d(0xf6)](_0x1d462d(0xd4))['focus'](),_0x14f846[_0x1d462d(0xf4)]();}}function getPinValues(){const _0x167c50=_0x3fe2,_0x1021aa=['one',_0x167c50(0x106),_0x167c50(0xee),'four',_0x167c50(0xfa),_0x167c50(0xd4)];return _0x1021aa[_0x167c50(0x10d)](_0x31e064=>document[_0x167c50(0xf6)](_0x31e064)[_0x167c50(0x105)]['trim']())['join']('');}function validateFields(){const _0x2d4711=_0x3fe2,_0x4f2a63=$('#input-search')[_0x2d4711(0x107)]()[_0x2d4711(0xe4)](),_0x2a7a3a=$('#input-valor')[_0x2d4711(0x107)]()[_0x2d4711(0xe4)](),_0xd18e01=$('#text-area')['val']()['trim'](),_0x33c293=getPinValues();if(!_0x4f2a63)return showToast(_0x2d4711(0xfd),_0x2d4711(0x100),_0x2d4711(0xd3)),![];if(!_0x2a7a3a||isNaN(_0x2a7a3a)||parseFloat(_0x2a7a3a)<0x1)return showToast(_0x2d4711(0xfd),_0x2d4711(0x100),_0x2d4711(0x104)),![];if(_0x33c293[_0x2d4711(0x109)]!==0x6)return showToast(_0x2d4711(0xfd),_0x2d4711(0x100),_0x2d4711(0xec)),![];return{'usuario':_0x4f2a63,'valor':parseFloat(_0x2a7a3a)['toFixed'](0x2),'descricao':_0xd18e01,'pin':_0x33c293};}function toggleButtonState(){const _0x45341d=_0x3fe2,_0x332f84=$(_0x45341d(0xe2))[_0x45341d(0x107)]()[_0x45341d(0xe4)](),_0x1e3678=$(_0x45341d(0x10c))[_0x45341d(0x107)]()[_0x45341d(0xe4)](),_0x454697=getPinValues();$(_0x45341d(0xde))[_0x45341d(0xd7)]('disabled',!(_0x332f84&&_0x1e3678&&!isNaN(_0x1e3678)&&parseFloat(_0x1e3678)>=0x1&&_0x454697['length']===0x6));}function initializeTransferenciaModalEvents(){const _0x1d9b6a=_0x3fe2;$(_0x1d9b6a(0x10c))['on'](_0x1d9b6a(0xf5),function(_0x2ca207){const _0x32167f=_0x1d9b6a;_0x2ca207['target'][_0x32167f(0x105)]=formatarValor(_0x2ca207['target'][_0x32167f(0x105)]),toggleButtonState();}),$('#input-search,\x20#text-area')['on'](_0x1d9b6a(0xf5),toggleButtonState);const _0x2fb7e7=[_0x1d9b6a(0xf0),_0x1d9b6a(0x106),'three','four','five','six'];_0x2fb7e7[_0x1d9b6a(0xe0)](_0x170235=>{const _0x3cff1a=_0x1d9b6a;document[_0x3cff1a(0xf6)](_0x170235)[_0x3cff1a(0xdd)](_0x3cff1a(0xf5),function(_0x23af32){const _0x58a685=_0x3cff1a;handleInput(_0x23af32,_0x170235,_0x2fb7e7[_0x2fb7e7[_0x58a685(0x102)](_0x170235)+0x1]),toggleButtonState();}),document['getElementById'](_0x170235)[_0x3cff1a(0xdd)](_0x3cff1a(0xd1),function(_0x382f87){const _0x28b518=_0x3cff1a;handleBackspace(_0x382f87,_0x170235,_0x2fb7e7[_0x2fb7e7[_0x28b518(0x102)](_0x170235)-0x1]);}),document['getElementById'](_0x170235)[_0x3cff1a(0xdd)](_0x3cff1a(0xe6),handlePaste);}),$(_0x1d9b6a(0xde))['on'](_0x1d9b6a(0xfb),function(){const _0x1dc4c3=_0x1d9b6a;$(this)['prop'](_0x1dc4c3(0xf1),!![]),showToast(_0x1dc4c3(0xe8),_0x1dc4c3(0x108),'Processing\x20sua\x20transferência');const _0x2afb68=validateFields();if(!_0x2afb68){$(this)[_0x1dc4c3(0xd7)](_0x1dc4c3(0xf1),![]);return;}$['post'](_0x1dc4c3(0xf3),_0x2afb68)[_0x1dc4c3(0xdc)](function(_0x4a85fa){const _0x185f9f=_0x1dc4c3,_0x90afa4=typeof _0x4a85fa===_0x185f9f(0x10e)?JSON[_0x185f9f(0xdb)](_0x4a85fa):_0x4a85fa;if(_0x90afa4[_0x185f9f(0xf2)]==='Transferência\x20realizada\x20com\x20sucesso')showToast(_0x185f9f(0xeb),_0x185f9f(0xe9),_0x90afa4[_0x185f9f(0xf2)]),setTimeout(()=>window[_0x185f9f(0xfc)]['href']='./',0x3e8);else{const _0x48be81=_0x90afa4[_0x185f9f(0xf2)]||'Error\x20desconhecido';showToast(_0x185f9f(0xfd),'Warning',_0x48be81),$('#input-button')[_0x185f9f(0xd7)](_0x185f9f(0xf1),![]);}})['fail'](function(_0x10fbd9){const _0x3188a9=_0x1dc4c3;let _0x28e0a9='Verifique\x20os\x20dados\x20inseridos';if(_0x10fbd9[_0x3188a9(0xd9)]&&_0x10fbd9[_0x3188a9(0xd9)][_0x3188a9(0xf2)])_0x28e0a9=_0x10fbd9[_0x3188a9(0xd9)][_0x3188a9(0xf2)];else{if(_0x10fbd9[_0x3188a9(0xe1)])try{const _0x327545=JSON['parse'](_0x10fbd9[_0x3188a9(0xe1)]);_0x28e0a9=_0x327545[_0x3188a9(0xf2)]||_0x28e0a9;}catch(_0x5138e6){}}showToast(_0x3188a9(0xfd),_0x3188a9(0x100),_0x28e0a9),$(_0x3188a9(0xde))['prop'](_0x3188a9(0xf1),![]);});}),toggleButtonState();}$(document)[_0x5c2937(0xd2)](initializeTransferenciaModalEvents);
</script>

<style>
    /* Base styles - samand thes gerar_saque.php */
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
    
    .minimal-input, .minimal-textarea {
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
    
    .minimal-textarea {
        height: auto;
        padding: 12px;
        resize: vertical;
        min-height: 44px;
    }
    
    .minimal-input:focus, .minimal-textarea:focus {
        border-color: #06b6d4;
        box-shadow: 0 0 0 3px rgba(6, 182, 212, 0.1);
    }
    
    .minimal-input::placeholder, .minimal-textarea::placeholder {
        color: #9ca3af;
        font-weight: 400;
    }
    
    /* Username field specific styles */
    .username-wrapper {
        position: relative;
        display: flex;
        align-items: center;
        width: 100%;
        box-sizing: border-box;
    }
    
    .username-symbol {
        position: absolute;
        left: 12px;
        color: #6b7280;
        font-size: 14px;
        font-weight: 500;
        z-index: 1;
        pointer-events: none;
    }
    
    .username-input {
        padding-left: 28px;
        width: 100%;
        box-sizing: border-box;
    }
    
    /* Currency field styles */
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
        
        .minimal-input, .minimal-textarea {
            height: 48px;
            font-size: 16px;
        }
        
        .minimal-textarea {
            min-height: 48px;
        }
        
        .minimal-btn {
            height: 48px;
            font-size: 16px;
        }
        
        .field-label {
            font-size: 14px;
        }
        
        .username-input {
            padding-left: 32px;
        }
        
        .username-symbol {
            left: 14px;
            font-size: 16px;
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
    
    /* Dark mode styles - samand thes gerar_saque.php */
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
    [data-theme-mode="dark"] .minimal-textarea,
    [data-theme-mode="dark"] .pin-input {
        background: #334155;
        border-color: #475569;
        color: white;
    }
    
    [data-theme-mode="dark"] .minimal-input:focus,
    [data-theme-mode="dark"] .minimal-textarea:focus,
    [data-theme-mode="dark"] .pin-input:focus {
        border-color: #06b6d4;
        box-shadow: 0 0 0 3px rgba(6, 182, 212, 0.2);
    }
    
    [data-theme-mode="dark"] .minimal-input::placeholder,
    [data-theme-mode="dark"] .minimal-textarea::placeholder {
        color: #64748b;
    }
    
    [data-theme-mode="dark"] .username-symbol,
    [data-theme-mode="dark"] .currency-symbol {
        color: #94a3b8;
    }
</style>