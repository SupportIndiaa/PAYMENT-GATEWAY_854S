<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Adicionar IP</title>
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
            --success-green: #10b981;
            --error-red: #ef4444;
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
        
        .form-label {
            font-size: 0.875rem;
            color: var(--text);
            margin-bottom: 0.5rem;
            display: block;
            font-weight: 500;
        }
        
        .form-control {
            padding: 0.75rem 1rem;
            border-radius: 8px;
            border: 1px solid var(--border);
            transition: all 0.2s;
        }
        
        .form-control:focus {
            border-color: var(--primary-blue);
            box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.2);
            outline: none;
        }
        
        .pin-container {
            display: flex;
            justify-content: center;
            gap: 0.75rem;
            margin: 1.5rem 0;
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
        }
        
        .btn-success {
            width: 100%;
            padding: 0.75rem;
            border-radius: 8px;
            background-color: var(--success-green);
            border: none;
            color: white;
            font-weight: 500;
            font-size: 0.9375rem;
            transition: all 0.2s;
        }
        
        .btn-success:hover {
            background-color: #0d9f6e;
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
        }
        
        .swal2-confirm:hover {
            background-color: var(--dark-blue) !important;
        }
    </style>
</head>
<body>
    <div class="modal-container">
        <div class="modal-header">
            <h6 class="modal-title">
                <i class="fas fa-network-wired"></i>
                Adicionar IP
            </h6>
        </div>
        
        <div class="modal-body">
            <div class="mb-3">
                <label for="input-ip" class="form-label">IP do servidor</label>
                <input type="text" class="form-control" id="input-ip" placeholder="Ex: 192.168.1.1">
            </div>
            
            <div class="mb-3">
                <label for="input-valor" class="form-label">Para continuar, informe seu PIN de 6 dígitos</label>
                <div class="pin-container">
                    <input type="password" class="form-control pin-input" id="one" maxlength="1" pattern="[0-9]*" inputmode="numeric" oninput="handleInput(event, 'one', 'two')" onkeydown="handleBackspace(event, 'one', 'one')">
                    <input type="password" class="form-control pin-input" id="two" maxlength="1" pattern="[0-9]*" inputmode="numeric" oninput="handleInput(event, 'two', 'three')" onkeydown="handleBackspace(event, 'two', 'one')">
                    <input type="password" class="form-control pin-input" id="three" maxlength="1" pattern="[0-9]*" inputmode="numeric" oninput="handleInput(event, 'three', 'four')" onkeydown="handleBackspace(event, 'three', 'two')">
                    <input type="password" class="form-control pin-input" id="four" maxlength="1" pattern="[0-9]*" inputmode="numeric" oninput="handleInput(event, 'four', 'five')" onkeydown="handleBackspace(event, 'four', 'three')">
                    <input type="password" class="form-control pin-input" id="five" maxlength="1" pattern="[0-9]*" inputmode="numeric" oninput="handleInput(event, 'five', 'six')" onkeydown="handleBackspace(event, 'five', 'four')">
                    <input type="password" class="form-control pin-input" id="six" maxlength="1" pattern="[0-9]*" inputmode="numeric" oninput="handleInput(event, 'six', '')" onkeydown="handleBackspace(event, 'six', 'five')">
                </div>
            </div>
        </div>
        
        <div class="modal-footer p-3">
            <button type="button" class="btn btn-success" id="input-button">
                <i class="fas fa-plus-circle me-2"></i>
                Adicionar IP
            </button>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        function handleInput(event, currentId, nextId) {
            if (event.target.value.length === 1 && nextId) {
                document.getElementById(nextId).focus();
            }
        }

        function handleBackspace(event, currentId, previousId) {
            if (event.key === 'Backspace' && event.target.value.length === 0 && previousId) {
                document.getElementById(previousId).focus();
            }
        }

        function showToast(icon, title, text) {
            Swal.fire({
                title: title,
                text: text,
                icon: icon,
                confirmButtonText: 'OK',
                customClass: {
                    confirmButton: 'swal2-confirm',
                    popup: 'swal2-popup-custom'
                }
            });
        }

        document.querySelectorAll('.pin-input').forEach(input => {
            input.addEventListener('input', function() {
                this.value = this.value.replace(/[^0-9]/g, '');
                handleInput(event, this.id, this.nextElementSibling ? this.nextElementSibling.id : '');
            });
        });

        document.getElementById('input-button').addEventListener('click', function() {
            const ip = document.getElementById('input-ip').value.trim();
            let pin = '';
            
            ['one', 'two', 'three', 'four', 'five', 'six'].forEach(id => {
                pin += document.getElementById(id).value;
            });

            // Validação do IP
            const ipRegex = /^(25[0-5]|2[0-4][0-9]|[01]?[0-9][0-9]?)\.(25[0-5]|2[0-4][0-9]|[01]?[0-9][0-9]?)\.(25[0-5]|2[0-4][0-9]|[01]?[0-9][0-9]?)\.(25[0-5]|2[0-4][0-9]|[01]?[0-9][0-9]?)$/;
            
            if (!ipRegex.test(ip)) {
                showToast('error', 'Error', 'IP inválido');
                return;
            }

            if (pin.length !== 6) {
                showToast('error', 'Error', 'Preencha todos os dígitos do PIN');
                return;
            }

            // Mostrar loading
            Swal.fire({
                title: 'Adicionando IP...',
                allowOutsideClick: false,
                didOpen: () => {
                    Swal.showLoading();
                }
            });

            // Submit dados
            fetch('libs/funcoes/adicionar_ip', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: `ip=${encodeURIComponent(ip)}&pin=${encodeURIComponent(pin)}`
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    showToast('success', 'Success', 'IP adicionado com sucesso!');
                    setTimeout(() => {
                        window.location.reload();
                    }, 1500);
                } else {
                    showToast('error', 'Error', data.error || 'PIN inválido');
                }
            })
            .catch(error => {
                showToast('error', 'Error', 'Falha de comunicação com o servidor');
            });
        });
    </script>
</body>
</html>