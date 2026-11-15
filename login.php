
<!DOCTYPE html>
<html lang="pt" dir="ltr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>RPay - Login</title>
    <meta name="Description" content="Dashboard principal do gateway de pagamento RPay">
    <meta name="Author" content="Ellodev">
    <meta name="keywords" content="Gateway de pagamento">
    <link rel="icon" href="assets/images/brand-logos/favicon.ico" type="image/x-icon">
    
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- Font Awesome - Versão mais estável -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" integrity="sha512-1ycn6IcaQQ40/MKBW2W4Rhis/DbILU74C1vSrLJxCq57o941Ym01SwNsOMqvEBFlcgUa6xLiPY/NS5R+E6ztJQ==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    
    <style>
        :root {
            --primary: #17a2b8;
            --primary-dark: #138496;
            --primary-light: #b8e6ea;
            --primary-gradient: linear-gradient(135deg, #17a2b8 0%, #138496 100%);
            --white: #ffffff;
            --gray-50: #f8fafc;
            --gray-100: #f1f5f9;
            --gray-200: #e2e8f0;
            --gray-300: #cbd5e1;
            --gray-400: #94a3b8;
            --gray-500: #64748b;
            --gray-600: #475569;
            --gray-700: #334155;
            --gray-800: #1e293b;
            --gray-900: #0f172a;
            --dark-bg: #0f172a;
            --dark-card: #1e293b;
            --dark-text: #e2e8f0;
            --error: #ef4444;
            --success: #10b981;
            --warning: #f59e0b;
            --shadow-sm: 0 1px 2px 0 rgb(0 0 0 / 0.05);
            --shadow: 0 1px 3px 0 rgb(0 0 0 / 0.1), 0 1px 2px -1px rgb(0 0 0 / 0.1);
            --shadow-md: 0 4px 6px -1px rgb(0 0 0 / 0.1), 0 2px 4px -2px rgb(0 0 0 / 0.1);
            --shadow-lg: 0 10px 15px -3px rgb(0 0 0 / 0.1), 0 4px 6px -4px rgb(0 0 0 / 0.1);
            --shadow-xl: 0 20px 25px -5px rgb(0 0 0 / 0.1), 0 8px 10px -6px rgb(0 0 0 / 0.1);
            --shadow-2xl: 0 25px 50px -12px rgb(0 0 0 / 0.25);
        }
        
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        html, body {
            height: 100%;
            overflow-x: hidden;
        }
        
        body {
            font-family: 'Poppins', 'Inter', sans-serif;
            background: var(--gray-50);
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            padding: 16px;
            position: fixed;
            width: 100%;
        }
        
        /* Enhanced Background with Animated Gradients */
        body::before {
            content: '';
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: 
                radial-gradient(circland thet 20% 50%, rgba(23, 162, 184, 0.15) 0%, transparent 50%),
                radial-gradient(circland thet 80% 20%, rgba(19, 132, 150, 0.15) 0%, transparent 50%),
                radial-gradient(circland thet 40% 80%, rgba(184, 230, 234, 0.1) 0%, transparent 50%),
                linear-gradient(135deg, var(--gray-50) 0%, #f8fafc 100%);
            z-index: -2;
            animation: backgroundShift 20s ease-in-out infinite;
        }
        
        body.dark-mode {
            background: var(--dark-bg);
            color: var(--dark-text);
        }
        
        body.dark-mode::before {
            background: 
                radial-gradient(circland thet 20% 50%, rgba(23, 162, 184, 0.08) 0%, transparent 50%),
                radial-gradient(circland thet 80% 20%, rgba(19, 132, 150, 0.08) 0%, transparent 50%),
                radial-gradient(circland thet 40% 80%, rgba(184, 230, 234, 0.08) 0%, transparent 50%),
                linear-gradient(135deg, var(--dark-bg) 0%, #1e293b 100%);
        }
        
        @keyframes backgroundShift {
            0%, 100% { 
                transform: translateX(0) translateY(0);
            }
            25% { 
                transform: translateX(-5px) translateY(-10px);
            }
            50% { 
                transform: translateX(10px) translateY(5px);
            }
            75% { 
                transform: translateX(-8px) translateY(8px);
            }
        }
        
        /* Fixed Container Sizes */
        .login-container {
            width: 100%;
            z-index: 10;
            position: relative;
        }
        
        /* Desktop: Fixed width, Mobile: Full width with max constraints */
        .login-card {
            width: 100%;
            max-width: 480px; /* Fixed width for desktop */
            margin: 0 auto;
            border: none;
            border-radius: 24px;
            backdrop-filter: blur(20px);
            -webkit-backdrop-filter: blur(20px);
            background: rgba(255, 255, 255, 0.95);
            border: 1px solid rgba(255, 255, 255, 0.2);
            box-shadow: var(--shadow-2xl);
            overflow: hidden;
            position: relative;
            animation: cardEntrance 0.8s cubic-bezier(0.16, 1, 0.3, 1);
        }
        
        body.dark-mode .login-card {
            background: rgba(30, 41, 59, 0.95);
            border: 1px solid rgba(23, 162, 184, 0.1);
            box-shadow: 
                0 25px 50px -12px rgba(0, 0, 0, 0.4),
                0 0 0 1px rgba(23, 162, 184, 0.1);
        }
        
        /* Enhanced Header with Gradient */
        .card-header {
            background: var(--primary-gradient);
            color: white;
            text-align: center;
            padding: 32px 24px;
            border-bottom: none;
            position: relative;
            overflow: hidden;
        }
        
        .card-header::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.1), transparent);
            animation: shimmer 3s infinite;
        }
        
        .card-header .logo {
            width: auto;
            height: 60px;
            background: none;
            border-radius: 0;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 16px;
            overflow: hidden;
        }
        
        .card-header .logo img {
            width: auto;
            height: 60px;
            max-width: 200px;
            object-fit: contain;
            border-radius: 0;
        }
        
        .card-header h2 {
            font-weight: 600;
            margin: 0;
            font-size: 1.75rem;
            letter-spacing: -0.5px;
            text-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }
        
        .card-header p {
            margin: 8px 0 0 0;
            font-size: 0.9rem;
            opacity: 0.9;
        }
        
        .card-body {
            padding: 32px 24px;
            position: relative;
        }
        
        body.dark-mode .card-body {
            color: var(--dark-text);
        }
        
        /* Enhanced Form Elements */
        .form-label {
            font-weight: 500;
            margin-bottom: 8px;
            color: var(--gray-700);
            font-size: 0.9rem;
            display: block;
        }
        
        body.dark-mode .form-label {
            color: var(--gray-300);
        }
        
        .input-group {
            position: relative;
            margin-bottom: 8px;
        }
        
        .input-group-text {
            position: absolute;
            left: 12px;
            top: 50%;
            transform: translateY(-50%);
            background: none;
            border: none;
            color: var(--gray-400);
            font-size: 16px;
            z-index: 10;
            pointer-events: none;
        }
        
        .form-control {
            width: 100%;
            height: 48px;
            border-radius: 12px;
            border: 1px solid var(--gray-200);
            padding: 12px 16px 12px 44px;
            font-size: 0.95rem;
            background: rgba(255, 255, 255, 0.8);
            color: var(--gray-900);
            transition: all 0.3s cubic-bezier(0.16, 1, 0.3, 1);
            font-weight: 400;
        }
        
        body.dark-mode .form-control {
            background: rgba(255, 255, 255, 0.05);
            border-color: var(--gray-600);
            color: var(--dark-text);
        }
        
        .form-control:focus {
            border-color: var(--primary);
            box-shadow: 0 0 0 3px rgba(23, 162, 184, 0.1);
            background: rgba(255, 255, 255, 1);
            outline: none;
        }
        
        body.dark-mode .form-control:focus {
            background: rgba(255, 255, 255, 0.1);
            box-shadow: 0 0 0 3px rgba(23, 162, 184, 0.2);
        }
        
        /* Enhanced Button */
        .btn-login {
            background: var(--primary-gradient);
            border: none;
            color: white;
            padding: 14px 20px;
            font-size: 1rem;
            font-weight: 500;
            border-radius: 12px;
            width: 100%;
            margin-top: 16px;
            letter-spacing: 0.3px;
            position: relative;
            overflow: hidden;
            box-shadow: var(--shadow-md);
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            cursor: pointer;
            transition: all 0.3s ease;
        }
        
        .btn-login:hover {
            transform: translateY(-2px);
            box-shadow: var(--shadow-lg);
        }
        
        .btn-login:disabled {
            opacity: 0.7;
            cursor: not-allowed;
            transform: none;
        }
        
        /* Enhanced Links */
        .forgot-password {
            color: var(--primary);
            text-decoration: none;
            font-size: 0.85rem;
            font-weight: 500;
            transition: all 0.3s ease;
        }
        
        .forgot-password:hover {
            color: var(--primary-dark);
            text-decoration: underline;
        }
        
        body.dark-mode .forgot-password {
            color: var(--primary-light);
        }
        
        .register-link {
            color: var(--primary);
            font-weight: 500;
            text-decoration: none;
            transition: all 0.3s ease;
        }
        
        .register-link:hover {
            color: var(--primary-dark);
            text-decoration: underline;
        }
        
        /* Enhanced Password Toggle */
        .password-toggle {
            position: absolute;
            right: 12px;
            top: 50%;
            transform: translateY(-50%);
            background: none;
            border: none;
            color: var(--gray-400);
            cursor: pointer;
            padding: 8px;
            border-radius: 6px;
            transition: all 0.3s ease;
            z-index: 10;
        }
        
        .password-toggle:hover {
            background: rgba(23, 162, 184, 0.1);
            color: var(--primary);
        }
        
        /* Enhanced Theme Toggle */
        .theme-toggle {
            position: fixed;
            bottom: 24px;
            left: 24px;
            width: 48px;
            height: 48px;
            border-radius: 50%;
            background: var(--primary-gradient);
            color: white;
            border: none;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 18px;
            box-shadow: var(--shadow-lg);
            z-index: 1000;
            transition: all 0.4s cubic-bezier(0.16, 1, 0.3, 1);
        }
        
        .theme-toggle:hover {
            transform: scale(1.1);
            box-shadow: 0 8px 20px rgba(23, 162, 184, 0.4);
        }
        
        /* Toast Notifications with Colors */
        .toast-container {
            position: fixed;
            top: 20px;
            right: 20px;
            z-index: 9999;
            max-width: 360px;
            width: calc(100% - 40px);
        }
        
        .toast {
            border: none;
            border-radius: 12px;
            backdrop-filter: blur(20px);
            overflow: hidden;
            margin-bottom: 12px;
            background: rgba(255, 255, 255, 0.95);
            border: 1px solid rgba(255, 255, 255, 0.2);
            box-shadow: var(--shadow-lg);
            animation: toastSlideIn 0.5s cubic-bezier(0.16, 1, 0.3, 1) forwards;
            font-family: 'Poppins', sans-serif;
            position: relative;
        }
        
        body.dark-mode .toast {
            background: rgba(30, 41, 59, 0.95);
            border: 1px solid rgba(23, 162, 184, 0.1);
        }
        
        .toast.success {
            border-left: 4px solid var(--success);
            background: rgba(16, 185, 129, 0.1);
        }
        
        .toast.error {
            border-left: 4px solid var(--error);
            background: rgba(239, 68, 68, 0.1);
        }
        
        body.dark-mode .toast.success {
            background: rgba(16, 185, 129, 0.15);
        }
        
        body.dark-mode .toast.error {
            background: rgba(239, 68, 68, 0.15);
        }
        
        .toast-header {
            border-bottom: 1px solid rgba(0, 0, 0, 0.05);
            background: transparent;
            color: var(--gray-900);
            font-weight: 600;
            padding: 16px 20px 8px;
            font-size: 1rem;
        }
        
        body.dark-mode .toast-header {
            color: var(--dark-text);
            border-bottom-color: rgba(255, 255, 255, 0.1);
        }
        
        .toast-header .icon {
            margin-right: 12px;
            font-size: 1.2rem;
        }
        
        .toast-header .success {
            color: var(--success);
        }
        
        .toast-header .error {
            color: var(--error);
        }
        
        .toast-body {
            padding: 8px 20px 16px;
            color: var(--gray-700);
            font-size: 0.95rem;
            line-height: 1.6;
        }
        
        body.dark-mode .toast-body {
            color: var(--gray-300);
        }
        
        .progress-container {
            height: 4px;
            background: rgba(0, 0, 0, 0.05);
            overflow: hidden;
            position: absolute;
            bottom: 0;
            left: 0;
            right: 0;
        }
        
        body.dark-mode .progress-container {
            background: rgba(255, 255, 255, 0.1);
        }
        
        .progress-bar {
            height: 100%;
            width: 100%;
            animation: progressBar 5s linear forwards;
        }
        
        .toast.success .progress-bar {
            background: linear-gradient(90deg, var(--success), #059669);
        }
        
        .toast.error .progress-bar {
            background: linear-gradient(90deg, var(--error), #dc2626);
        }
        
        /* Keyframe Animations */
        @keyframes cardEntrance {
            0% {
                opacity: 0;
                transform: translateY(30px) scale(0.9);
            }
            100% {
                opacity: 1;
                transform: translateY(0) scale(1);
            }
        }
        
        @keyframes shimmer {
            0% { left: -100%; }
            100% { left: 100%; }
        }
        
        @keyframes toastSlideIn {
            0% {
                transform: translateX(100%);
                opacity: 0;
            }
            100% {
                transform: translateX(0);
                opacity: 1;
            }
        }
        
        @keyframes progressBar {
            0% { width: 100%; }
            100% { width: 0%; }
        }
        
        /* Mobile Responsive - Fixed smaller size */
        @media (max-width: 576px) {
            html, body {
                height: 100vh;
                overflow: hidden;
                position: fixed;
                width: 100%;
            }
            
            body {
                padding: 12px;
                align-items: flex-start;
                padding-top: 10vh;
            }
            
            .login-card {
                max-width: 100%;
                margin: 0;
            }
            
            .card-header {
                padding: 24px 20px;
            }
            
            .card-header h2 {
                font-size: 1.5rem;
            }
            
            .card-body {
                padding: 24px 20px;
            }
            
            .form-control {
                height: 44px;
                font-size: 16px; /* Prevent zoom on iOS */
            }
            
            .btn-login {
                padding: 12px 16px;
                font-size: 0.95rem;
            }
            
            .theme-toggle {
                width: 44px;
                height: 44px;
                bottom: 20px;
                left: 20px;
                font-size: 16px;
            }
            
            .toast-container {
                max-width: calc(100% - 24px);
                right: 12px;
                top: 12px;
            }
        }
        
        /* Extra small screens */
        @media (max-width: 400px) {
            body {
                padding-top: 5vh;
            }
            
            .card-header {
                padding: 20px 16px;
            }
            
            .card-body {
                padding: 20px 16px;
            }
            
            .card-header h2 {
                font-size: 1.4rem;
            }
        }
        
        /* Desktop - Ensure fixed size */
        @media (min-width: 768px) {
            .login-card {
                width: 480px; /* Fixed width for desktop */
                max-width: none;
            }
        }
        
        /* High contrast mode */
        @media (prefers-contrast: high) {
            .login-card {
                border: 2px solid var(--gray-300);
            }
            
            .form-control {
                border: 2px solid var(--gray-400);
            }
        }
        
        /* Reduced motion */
        @media (prefers-reduced-motion: reduce) {
            *,
            *::before,
            *::after {
                animation-duration: 0.01ms !important;
                animation-iteration-count: 1 !important;
                transition-duration: 0.01ms !important;
            }
    </style>
</head>
<body>
    <!-- Toast Notifications Container -->
    <div class="toast-container"></div>
    
    <!-- Theme Toggle Button -->
    <button class="theme-toggle" id="themeToggle" aria-label="Alternar tema">
        <i class="fas fa-moon"></i>
    </button>
    
    <!-- Main Login Container -->
    <div class="login-container">
        <div class="login-card">
            <div class="card-header">
                <div class="logo">
                    <img src="teste.png" alt="Logo RPay" onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';">
                    <i class="fas fa-university" style="display: none; font-size: 20px;"></i>
                </div>
                <h2>RPay 🚀</h2>
                <p>Access your RPay account.</p>
            </div>
            <div class="card-body">
                <form id="loginForm" novalidate>
                    <div class="mb-3">
                        <label for="usuario" class="form-label">
                            Username
                        </label>
                        <div class="input-group">
                            <i class="fas fa-user input-group-text"></i>
                            <input 
                                type="text" 
                                class="form-control" 
                                id="usuario" 
                                name="usuario" 
                                placeholder="Enter your username" 
                                required 
                                autocomplete="username"
                            >
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label for="senha" class="form-label">
                            Password
                        </label>
                        <div class="input-group">
                            <i class="fas fa-lock input-group-text"></i>
                            <input 
                                type="password" 
                                class="form-control" 
                                id="senha" 
                                name="senha" 
                                placeholder="Enter your password" 
                                required 
                                autocomplete="current-password"
                            >
                            <button 
                                type="button" 
                                class="password-toggle" 
                                id="togglePassword"
                                aria-label="Mostrar/ocultar senha"
                            >
                                <i class="fas fa-eye"></i>
                            </button>
                        </div>
                        <div class="text-end mt-2">
                            <a href="forgot_password.php" class="forgot-password">
                                Forgot Password?
                            </a>
                        </div>
                    </div>
                    
                    <button type="submit" class="btn btn-login" id="login-button">
                        <i class="fas fa-sign-in-alt"></i> 
                        Login
                    </button>
                    
                    <div class="text-center mt-4">
                        <p class="mb-0 text-muted" style="font-size: 0.9rem;">
                            Don't have an account? 
                            <a href="register.php" class="register-link">
                                Register
                            </a>
                        </p>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Bootstrap Bundle with Popper -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    
    <script>
        // Password toggle functionality
        document.getElementById('togglePassword').addEventListener('click', function() {
            const passwordInput = document.getElementById('senha');
            const icon = this.querySelector('i');
            
            if (passwordInput.type === 'password') {
                passwordInput.type = 'text';
                icon.classList.remove('fa-eye');
                icon.classList.add('fa-eye-slash');
            } else {
                passwordInput.type = 'password';
                icon.classList.remove('fa-eye-slash');
                icon.classList.add('fa-eye');
            }
        });
        
        // Theme toggle functionality
        document.getElementById('themeToggle').addEventListener('click', function() {
            document.body.classList.toggle('dark-mode');
            const icon = this.querySelector('i');
            
            if (document.body.classList.contains('dark-mode')) {
                icon.classList.remove('fa-moon');
                icon.classList.add('fa-sun');
                localStorage.setItem('theme', 'dark');
            } else {
                icon.classList.remove('fa-sun');
                icon.classList.add('fa-moon');
                localStorage.setItem('theme', 'light');
            }
        });
        
        // Initialize theme
        function initializeTheme() {
            const savedTheme = localStorage.getItem('theme');
            const systemDark = window.matchMedia('(prefers-color-scheme: dark)').matches;
            
            if (savedTheme === 'dark' || (!savedTheme && systemDark)) {
                document.body.classList.add('dark-mode');
                const themeToggle = document.querySelector('#themeToggle i');
                themeToggle.classList.remove('fa-moon');
                themeToggle.classList.add('fa-sun');
            }
        }
        
        initializeTheme();
        
        // Toast notification function with auto-close
        function showToast(type, title, message) {
            const toastContainer = document.querySelector('.toast-container');
            const toastId = 'toast-' + Date.now();
            
            let iconClass, toastClass;
            if (type === 'success') {
                iconClass = 'fas fa-check-circle success';
                toastClass = 'success';
            } else if (type === 'error') {
                iconClass = 'fas fa-exclamation-circle error';
                toastClass = 'error';
            } else if (type === 'warning') {
                iconClass = 'fas fa-exclamation-triangle warning';
                toastClass = 'warning';
            } else {
                iconClass = 'fas fa-info-circle info';
                toastClass = 'info';
            }
            
            const toastHTML = `
                <div class="toast ${toastClass}" id="${toastId}" role="alert" aria-live="assertive" aria-atomic="true">
                    <div class="toast-header">
                        <i class="${iconClass} icon"></i>
                        <strong class="me-auto">${title}</strong>
                        <button type="button" class="btn-close" data-bs-dismiss="toast" aria-label="Fechar notificação"></button>
                    </div>
                    <div class="toast-body">
                        ${message}
                    </div>
                    <div class="progress-container">
                        <div class="progress-bar"></div>
                    </div>
                </div>
            `;
            
            toastContainer.insertAdjacentHTML('beforeend', toastHTML);
            
            // Show the toast
            const toastElement = document.getElementById(toastId);
            const toast = new bootstrap.Toast(toastElement, {
                autohide: true,
                delay: 5000
            });
            toast.show();
            
            // Auto-closand thefter progress bar completes
            setTimeout(function() {
                toast.hide();
            }, 5000);
            
            // Remove toast after it's hidden
            toastElement.addEventListener('hidden.bs.toast', function() {
                toastElement.remove();
            });
        }
        
        // Form validation and submission
        $(document).ready(function() {
            $('#loginForm').on('submit', function(e) {
                e.preventDefault();
                
                const usuario = $('#usuario').val().trim();
                const senha = $('#senha').val();
                
                if (!usuario) {
                    showToast('error', 'Campo obrigatório', 'Por favor, digite seu usuário.');
                    return;
                }
                
                if (!senha) {
                    showToast('error', 'Campo obrigatório', 'Por favor, digite sua senha.');
                    return;
                }
                
                const button = $('#login-button');
                button.prop('disabled', true);
                button.html('<span class="spinner-border spinner-border-sm me-2"></span> Validando...');
                
                // AJAX call to login validation
                $.post('libs/funcoes/valida_login.php', {
                    'usuario': usuario,
                    'senha': senha
                })
                .done(function(response) {
                    try {
                        const data = JSON.parse(response);
                        
                        if (data.statusCode === 200) {
                            showToast('success', 'Login Aprovado', 'Redirecionando para o dashboard...');
                            button.html('<i class="fas fa-check me-2"></i> Login realizado');
                            
                            setTimeout(function() {
                                window.location.href = 'index.php';
                            }, 1500);
                        } else {
                            button.prop('disabled', false);
                            button.html('<i class="fas fa-sign-in-alt me-2"></i> Entrar');
                            showToast('error', 'Credenciais Inválidas', 'Usuário ou senha incorretos.');
                        }
                    } catch (e) {
                        button.prop('disabled', false);
                        button.html('<i class="fas fa-sign-in-alt me-2"></i> Entrar');
                        showToast('error', 'Error de Sistema', 'Ocorreu um erro ao processar o login.');
                    }
                })
                .fail(function() {
                    button.prop('disabled', false);
                    button.html('<i class="fas fa-sign-in-alt me-2"></i> Entrar');
                    showToast('error', 'Error de Conexão', 'Falha na conexão com o servidor.');
                });
            });
        });
        
        // Mobile optimization
        document.addEventListener('DOMContentLoaded', function() {
            // Prevent zoom on iOS
            if (/iPhone|iPad|iPod/i.test(navigator.userAgent)) {
                document.addEventListener('gesturestart', function(e) {
                    e.preventDefault();
                });
                
                // Disable double tap to zoom
                let lastTouchEnd = 0;
                document.addEventListener('touchend', function(event) {
                    const now = (new Date()).getTime();
                    if (now - lastTouchEnd <= 300) {
                        event.preventDefault();
                    }
                    lastTouchEnd = now;
                }, false);
            }
            
            // Auto-focus on desktop
            if (window.innerWidth > 768) {
                document.getElementById('usuario').focus();
            }
        });
    </script>
</body>
</html>
