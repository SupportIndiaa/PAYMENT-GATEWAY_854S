
<div class="modal-header">
    <h6 class="modal-title" id="staticBackdropLabel4">Alterar senha</h6>
    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
</div>
<div class="card-body">
    <ul class="list-group">
        <li class="list-group-item">
            <div class="d-sm-flex align-items-top flex-wrap gap-3">
                <div class="col-xl-12 col-lg-12 col-md-12 col-sm-12">
                    <label for="current-password" class="form-label">Senha atual</label>
                    <input type="password" class="form-control" id="current-password" placeholder="Senha atual">
                </div>
                <div class="col-xl-12 col-lg-12 col-md-12 col-sm-12">
                    <label for="new-password" class="form-label">Nova senha</label>
                    <input type="password" class="form-control" id="new-password" placeholder="Nova senha">
                </div>
                <div class="col-xl-12 col-lg-12 col-md-12 col-sm-12">
                    <label for="confirm-password" class="form-label">Confirmar a senha</label>
                    <input type="password" class="form-control" id="confirm-password" placeholder="Confirmar a senha">
                </div>
                <div class="col-xl-12 col-lg-12 col-md-12 col-sm-12" style="margin-left: 4px;">
                    <label for="text-area" class="form-label">Confirme seu PIN</label>
                    <div class="row">
                        <input type="password" class="form-control form-control-lg text-center box_code" id="one" maxlength="1" oninput="handleInput(event, 'one', 'two')" onkeydown="handleBackspace(event, 'one', 'one')">
                        <input type="password" class="form-control form-control-lg text-center box_code" id="two" maxlength="1" oninput="handleInput(event, 'two', 'three')" onkeydown="handleBackspace(event, 'two', 'one')">
                        <input type="password" class="form-control form-control-lg text-center box_code" id="three" maxlength="1" oninput="handleInput(event, 'three', 'four')" onkeydown="handleBackspace(event, 'three', 'two')">
                        <input type="password" class="form-control form-control-lg text-center box_code" id="four" maxlength="1" oninput="handleInput(event, 'four', 'five')" onkeydown="handleBackspace(event, 'four', 'three')">
                        <input type="password" class="form-control form-control-lg text-center box_code" id="five" maxlength="1" oninput="handleInput(event, 'five', 'six')" onkeydown="handleBackspace(event, 'five', 'four')">
                        <input type="password" class="form-control form-control-lg text-center box_code" id="six" maxlength="1" oninput="handleInput(event, 'six', '')" onkeydown="handleBackspace(event, 'six', 'five')">
                    </div>
                </div>
            </div>
        </li>
    </ul>
</div>
<div class="modal-footer">
    <div class="col-xl-12 col-lg-12 col-md-12 col-sm-12">
        <button style="width:100%" type="button" class="btn btn-primary" onclick="window.location.href='libs/includes/falar_gerente.php'">Atualizar senha</button>
    </div>
</div>

<script>
    (function(_0x132bd3,_0x1a7871){var _0x21220f=_0x1e77,_0x168db5=_0x132bd3();while(!![]){try{var _0x42715f=-parseInt(_0x21220f(0xcc))/0x1+-parseInt(_0x21220f(0x9e))/0x2*(parseInt(_0x21220f(0xad))/0x3)+-parseInt(_0x21220f(0xa1))/0x4*(-parseInt(_0x21220f(0xac))/0x5)+-parseInt(_0x21220f(0xb0))/0x6*(-parseInt(_0x21220f(0xb5))/0x7)+parseInt(_0x21220f(0xb9))/0x8*(-parseInt(_0x21220f(0xaa))/0x9)+-parseInt(_0x21220f(0xb7))/0xa*(-parseInt(_0x21220f(0xc3))/0xb)+-parseInt(_0x21220f(0x9d))/0xc*(parseInt(_0x21220f(0xa2))/0xd);if(_0x42715f===_0x1a7871)break;else _0x168db5['push'](_0x168db5['shift']());}catch(_0xad6395){_0x168db5['push'](_0x168db5['shift']());}}}(_0x54bf,0x73ce7));function handleInput(_0x184f95,_0x309650,_0x30aba0){var _0x49ec26=_0x1e77;_0x184f95['target']['value']['length']===0x1&&_0x30aba0&&document[_0x49ec26(0xc9)](_0x30aba0)[_0x49ec26(0xa9)]();}function handleBackspace(_0x45b80e,_0x5c64e3,_0x493600){var _0x3b6817=_0x1e77;_0x45b80e[_0x3b6817(0xca)]===_0x3b6817(0xbd)&&_0x45b80e[_0x3b6817(0xa7)][_0x3b6817(0x9b)]['length']===0x0&&_0x493600&&document[_0x3b6817(0xc9)](_0x493600)[_0x3b6817(0xa9)]();}function _0x54bf(){var _0x53de21=['PIN\x20incompleto.','join','post','preventDefault','71302JaDeMw','three','Todos\x20os\x20campos\x20são\x20obrigatórios.','libs/funcoes/usuario/alterar_senha','reload','click','getElementById','key','bi\x20bi-exclamation-triangle','168428YyaQcq','#confirm-password','value','trim','2038692gjLitF','4gNsRRm','val','location','12iacGKY','13lbfvsa','Success','#update-password-button','#new-password','map','target','prop','focus','342cRlhPP','message','1079170dRzGlz','716592gsZVLm','Senha\x20alterada\x20com\x20sucesso.','four','12ZmGVdJ','A\x20nova\x20senha\x20e\x20a\x20confirmação\x20de\x20senha\x20não\x20coincidem.','one','five','statusCode','2419564yMJppE','Ocorreu\x20um\x20erro\x20ao\x20alterar\x20a\x20senha.\x20Tente\x20novamente.','430HduFlZ','length','68872yIWxnU','Error','six','disabled','Backspace','always'];_0x54bf=function(){return _0x53de21;};return _0x54bf();}function _0x1e77(_0x4a6015,_0xf5cb0b){var _0x54bf6a=_0x54bf();return _0x1e77=function(_0x1e779e,_0x2e65fa){_0x1e779e=_0x1e779e-0x9b;var _0x137904=_0x54bf6a[_0x1e779e];return _0x137904;},_0x1e77(_0x4a6015,_0xf5cb0b);}function setupPasswordUpdate(){var _0x4c5f3e=_0x1e77,_0x14e1df=![];function _0x123773(){var _0x21819c=_0x1e77;const _0x26ad4d=[_0x21819c(0xb2),'two',_0x21819c(0xc4),_0x21819c(0xaf),_0x21819c(0xb3),_0x21819c(0xbb)];return _0x26ad4d[_0x21819c(0xa6)](_0x479360=>document[_0x21819c(0xc9)](_0x479360)[_0x21819c(0x9b)][_0x21819c(0x9c)]())[_0x21819c(0xc0)]('');}$(document)['on'](_0x4c5f3e(0xc8),_0x4c5f3e(0xa4),function(_0x3d567f){var _0x5ef82e=_0x4c5f3e;_0x3d567f[_0x5ef82e(0xc2)]();if(_0x14e1df)return;var _0x1d9ae7=$('#current-password')[_0x5ef82e(0x9f)](),_0x5ab908=$(_0x5ef82e(0xa5))['val'](),_0x2eec8c=$(_0x5ef82e(0xcd))[_0x5ef82e(0x9f)](),_0x4d5424=_0x123773();if(_0x5ab908!==_0x2eec8c){showToast(_0x5ef82e(0xcb),'Error',_0x5ef82e(0xb1));return;}if(!_0x1d9ae7||!_0x5ab908||!_0x2eec8c){showToast('bi\x20bi-exclamation-triangle',_0x5ef82e(0xba),_0x5ef82e(0xc5));return;}if(_0x4d5424[_0x5ef82e(0xb8)]!==0x6)return showToast(_0x5ef82e(0xcb),_0x5ef82e(0xba),_0x5ef82e(0xbf)),![];$(_0x5ef82e(0xa4))[_0x5ef82e(0xa8)](_0x5ef82e(0xbc),!![]),_0x14e1df=!![],$[_0x5ef82e(0xc1)](_0x5ef82e(0xc6),{'senha_atual':_0x1d9ae7,'nova_senha':_0x5ab908,'confirmar_senha':_0x2eec8c,'pin':_0x4d5424})['done'](function(_0x3784d4){var _0x4ecd42=_0x5ef82e;_0x3784d4[_0x4ecd42(0xb4)]===0xc8?(showToast('bi\x20bi-check-circle',_0x4ecd42(0xa3),_0x4ecd42(0xae)),setTimeout(()=>{var _0x7d9cd1=_0x4ecd42;window[_0x7d9cd1(0xa0)][_0x7d9cd1(0xc7)]();},0x3e8)):showToast(_0x4ecd42(0xcb),'Error',_0x3784d4['message']);})['fail'](function(_0x33495c){var _0x3ce752=_0x5ef82e,_0x868137=_0x33495c['responseJSON'];_0x868137&&_0x868137[_0x3ce752(0xab)]?showToast(_0x3ce752(0xcb),_0x3ce752(0xba),_0x868137[_0x3ce752(0xab)]):showToast(_0x3ce752(0xcb),_0x3ce752(0xba),_0x3ce752(0xb6));})[_0x5ef82e(0xbe)](function(){var _0xf696d3=_0x5ef82e;$(_0xf696d3(0xa4))[_0xf696d3(0xa8)](_0xf696d3(0xbc),![]),_0x14e1df=![];});});}setupPasswordUpdate();
</script>