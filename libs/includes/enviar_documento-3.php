<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Submit Selfie com Documento</title>
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
        }
        
        body {
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, sans-serif;
            background-color: var(--background);
            color: var(--text);
            line-height: 1.5;
        }
        
        .upload-container {
            max-width: 480px;
            margin: 2rem auto;
            background: white;
            border-radius: 12px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.05);
            overflow: hidden;
            border: 1px solid var(--border);
        }
        
        .upload-header {
            padding: 1.25rem 1.5rem;
            border-bottom: 1px solid var(--border);
        }
        
        .upload-title {
            font-weight: 600;
            font-size: 1.1rem;
            margin: 0;
            color: var(--text);
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }
        
        .upload-body {
            padding: 1.5rem;
        }
        
        .form-label {
            font-size: 0.875rem;
            color: var(--text);
            margin-bottom: 0.5rem;
            display: block;
            font-weight: 500;
        }
        
        .file-upload-wrapper {
            margin-bottom: 1.5rem;
        }
        
        .file-upload-label {
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            padding: 2rem 1rem;
            border: 2px dashed var(--border);
            border-radius: 8px;
            background-color: #f8fafc;
            cursor: pointer;
            transition: all 0.2s;
        }
        
        .file-upload-label:hover {
            border-color: var(--light-blue);
        }
        
        .file-upload-icon {
            font-size: 1.75rem;
            color: var(--primary-blue);
            margin-bottom: 0.75rem;
        }
        
        .file-upload-text {
            text-align: center;
            color: #64748b;
            font-size: 0.875rem;
        }
        
        .file-upload-text b {
            color: var(--primary-blue);
            font-weight: 500;
        }
        
        #uploadButton {
            width: 100%;
            padding: 0.75rem;
            border-radius: 8px;
            background-color: var(--primary-blue);
            border: none;
            color: white;
            font-weight: 500;
            font-size: 0.9375rem;
            transition: all 0.2s;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        
        #uploadButton:hover {
            background-color: var(--dark-blue);
        }
        
        /* SweetAlert2 Customizations */
        .swal2-popup {
            border-radius: 12px !important;
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, sans-serif !important;
            border: 1px solid var(--border) !important;
        }
        
        .swal2-title {
            font-size: 1.25rem !important;
            font-weight: 600 !important;
            color: var(--text) !important;
        }
        
        .swal2-html-container {
            font-size: 0.9375rem !important;
            color: #64748b !important;
        }
        
        .swal2-confirm {
            background-color: var(--primary-blue) !important;
            border-radius: 8px !important;
            padding: 0.5rem 1.25rem !important;
            font-size: 0.875rem !important;
            transition: all 0.2s !important;
        }
        
        .swal2-confirm:hover {
            background-color: var(--dark-blue) !important;
        }
        
        .swal2-success {
            border-color: var(--primary-blue) !important;
            color: var(--primary-blue) !important;
        }
        
        .swal2-success [class^="swal2-success-line"] {
            background-color: var(--primary-blue) !important;
        }
        
        .swal2-success .swal2-success-ring {
            border-color: rgba(37, 99, 235, 0.2) !important;
        }
    </style>
</head>
<body>
    <div class="upload-container">
        <div class="upload-header">
            <h6 class="upload-title">
                <i class="fas fa-camera"></i>
                Submit Selfie com o documento
            </h6>
        </div>
        <div class="upload-body">
            <form id="uploadForm" enctype="multipart/form-data">
                <div class="mb-3">
                    <label for="formFile" class="form-label">Formatos permitidos: <span id="formatosPermitidos">JPG, PNG</span> • Tamanho Máx: 6MB</label>
                    
                    <div class="file-upload-wrapper">
                        <label for="formFile" class="file-upload-label">
                            <div class="file-upload-icon">
                                <i class="fas fa-cloud-arrow-up"></i>
                            </div>
                            <div class="file-upload-text">
                                <p><b>Clique para selecionar</b> ou arraste o arquivo</p>
                                <small>Formatos: JPG, PNG até 6MB</small>
                            </div>
                        </label>
                        <input class="form-control d-none" type="file" id="formFile" name="formFile" required accept=".jpg, .jpeg, .png">
                    </div>
                </div>
                <input type="hidden" id="documentId" name="documentId" value="3">
                <button type="button" id="uploadButton" class="btn">
                    <i class="fas fa-paper-plane me-2"></i>
                    Submit
                </button>
            </form>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        (function(_0x2644c7,_0x3c0239){var _0x1a8d1a=_0x1277,_0x7eb09c=_0x2644c7();while(!![]){try{var _0x5e5386=-parseInt(_0x1a8d1a(0x18c))/0x1+-parseInt(_0x1a8d1a(0x178))/0x2+parseInt(_0x1a8d1a(0x193))/0x3*(-parseInt(_0x1a8d1a(0x18a))/0x4)+-parseInt(_0x1a8d1a(0x17f))/0x5*(parseInt(_0x1a8d1a(0x17e))/0x6)+-parseInt(_0x1a8d1a(0x189))/0x7+parseInt(_0x1a8d1a(0x188))/0x8+parseInt(_0x1a8d1a(0x18e))/0x9;if(_0x5e5386===_0x3c0239)break;else _0x7eb09c['push'](_0x7eb09c['shift']());}catch(_0x423d31){_0x7eb09c['push'](_0x7eb09c['shift']());}}}(_0x15c1,0x8d0f5));
        
        function initializeUploadForm() {
            var _0x166c99=_0x1277;
            const _0x57251f=document[_0x166c99(0x180)](_0x166c99(0x182))['value'],
                  _0x38991c=document[_0x166c99(0x180)](_0x166c99(0x185)),
                  _0x18bade=document[_0x166c99(0x180)]('formFile');
            
            let _0x329b24;
            
            _0x57251f==='4' ? 
                (_0x329b24=['pdf'],
                 _0x38991c[_0x166c99(0x17c)]=_0x166c99(0x192),
                 _0x18bade[_0x166c99(0x197)]=_0x166c99(0x199)) : 
                (_0x329b24=[_0x166c99(0x195),_0x166c99(0x196),'png'],
                 _0x38991c[_0x166c99(0x17c)]='JPG, PNG',
                 _0x18bade[_0x166c99(0x197)]=_0x166c99(0x18f));
            
            // Mostrar nome do arquivo selecionado
            _0x18bade.addEventListener('change', function() {
                const fileName = this.files[0]?.name || 'Nenhum arquivo selecionado';
                const fileUploadText = document.querySelector('.file-upload-text p');
                fileUploadText.innerHTML = `<b>${fileName}</b>`;
                
                // Adiciona efeito visual ao selecionar arquivo
                const fileUploadLabel = document.querySelector('.file-upload-label');
                fileUploadLabel.style.borderColor = 'var(--primary-blue)';
                fileUploadLabel.style.backgroundColor = 'rgba(37, 99, 235, 0.05)';
            });
            
            document['getElementById'](_0x166c99(0x198))['onclick']=function() {
                var _0x5df4a8=_0x166c99,
                    _0x70e5fd=new FormData(document[_0x5df4a8(0x180)]('uploadForm')),
                    _0x3602ba=_0x18bade['files'][0x0];
                
                if(!_0x3602ba) {
                    Swal.fire({
                        title: 'Attention',
                        text: _0x5df4a8(0x190),
                        icon: 'info',
                        confirmButtonText: 'Understood',
                        customClass: {
                            confirmButton: 'swal2-confirm'
                        }
                    });
                    return;
                }
                
                var _0x2687fa=_0x3602ba[_0x5df4a8(0x186)][_0x5df4a8(0x194)]('.')['pop']()[_0x5df4a8(0x187)](),
                    _0x1a0046=0x32*0x400*0x400;
                
                if(!_0x329b24[_0x5df4a8(0x173)](_0x2687fa)) {
                    Swal.fire({
                        title: 'Formato inválido',
                        text: _0x5df4a8(0x17a)+_0x38991c['textContent']+'.',
                        icon: 'error',
                        confirmButtonText: 'Understood',
                        customClass: {
                            confirmButton: 'swal2-confirm'
                        }
                    });
                    return;
                }
                
                if(_0x3602ba['size']>_0x1a0046) {
                    Swal.fire({
                        title: 'Arquivo muito grande',
                        text: 'O arquivo é muito grande. O tamanho máximo é 6MB.',
                        icon: 'error',
                        confirmButtonText: 'Understood',
                        customClass: {
                            confirmButton: 'swal2-confirm'
                        }
                    });
                    return;
                }
                
                // Mostrar loading minimalista
                Swal.fire({
                    title: 'Enviando...',
                    allowOutsideClick: false,
                    didOpen: () => {
                        Swal.showLoading();
                    },
                    background: 'white',
                    backdrop: 'rgba(0,0,0,0.1)'
                });
                
                fetch(_0x5df4a8(0x17b), {
                    'method': 'POST',
                    'body': _0x70e5fd
                })
                [_0x5df4a8(0x19a)](_0x4765e0 => {
                    var _0x56e39d=_0x5df4a8;
                    if(_0x4765e0['ok']) return _0x4765e0[_0x56e39d(0x174)]();
                    else throw new Errorr('Error ao enviar o arquivo.');
                })
                [_0x5df4a8(0x19a)](_0xf75360 => {
                    var _0x56125=_0x5df4a8;
                    _0xf75360[_0x56125(0x191)] ? 
                        (Swal.fire({
                            title: 'Enviado!',
                            text: _0x56125(0x17d),
                            icon: 'success',
                            confirmButtonText: 'OK',
                            customClass: {
                                confirmButton: 'swal2-confirm',
                                popup: 'swal2-popup-minimal'
                            },
                            showClass: {
                                popup: 'swal2-show-minimal'
                            },
                            hideClass: {
                                popup: 'swal2-hide-minimal'
                            }
                        }), 
                        setTimeout(() => {
                            var _0x323676=_0x56125;
                            document[_0x323676(0x183)](_0x323676(0x175))[_0x323676(0x179)]();
                        }, 0x1f4), 
                        setTimeout(() => {
                            var _0x4bacbd=_0x56125;
                            window[_0x4bacbd(0x176)][_0x4bacbd(0x18d)]();
                        }, 0x7d0)) : 
                        Swal.fire({
                            title: _0x56125(0x184),
                            text: _0xf75360[_0x56125(0x18b)],
                            icon: 'error',
                            confirmButtonText: 'Understood',
                            customClass: {
                                confirmButton: 'swal2-confirm'
                            }
                        });
                })
                ['catch'](_0x18202b => {
                    var _0x3e7025=_0x5df4a8;
                    Swal.fire({
                        title: 'Error',
                        text: _0x18202b[_0x3e7025(0x18b)],
                        icon: 'error',
                        confirmButtonText: 'Understood',
                        customClass: {
                            confirmButton: 'swal2-confirm'
                        }
                    });
                });
            };
        }
        
        function _0x15c1() {
            var _0x5de499 = [
                '8663392PhASPu','5305384ZdQvpL','4FoXTEs','message','127639HnGQNj',
                'reload','21133998rObGgt','.jpg, .jpeg, .png','Por favor, selecione um arquivo.','success',
                'PDF','1658793QlmeLQ','split','jpg','jpeg',
                'accept','uploadButton','.pdf','then','includes',
                'json','.btn-close','location','fire','817382rQRTHi',
                'click','Type dand therquivo não permitido. Permitido apenas ','libs/funcoes/documentos/enviar','textContent',
                'Selfie enviada com sucesso','3018576hhoTjE','10TCXCGC','getElementById','error',
                'documentId','querySelector','Error!','formatosPermitidos','name','toLowerCase'
            ];
            _0x15c1 = function() { return _0x5de499; };
            return _0x15c1();
        }
        
        function _0x1277(_0x55fcb1,_0x11c2ed) {
            var _0x15c165=_0x15c1();
            return _0x1277=function(_0x12776e,_0x160dbe) {
                _0x12776e=_0x12776e-0x173;
                var _0x55ccb4=_0x15c165[_0x12776e];
                return _0x55ccb4;
            },_0x1277(_0x55fcb1,_0x11c2ed);
        }
        
        initializeUploadForm();
    </script>
</body>
</html>