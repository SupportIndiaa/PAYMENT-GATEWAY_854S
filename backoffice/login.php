<?php
session_start();

// Verificar se já está logado como admin
if (isset($_SESSION['admin_id'])) {
    header('Location: user_list.php');
    exit();
}

use Dotenv\Dotenv;
require __DIR__ . '/../vendor/autoload.php';

$dotenv = Dotenv::createImmutable(__DIR__ . '/../');
$dotenv->load();

$login_error = false;
$code_error = false;
$show_code_step = false;
$temp_admin_data = null;
$login_success = false;

// Conectar ao banco de dados
try {
    $dbHost = $_ENV['DB_HOST'];
    $dbName = $_ENV['DB_NAME'];
    $dbUser = $_ENV['DB_USER'];
    $dbPass = $_ENV['DB_PASS'];

    $dsn = "mysql:host=$dbHost;dbname=$dbName;charset=utf8";
    $pdo = new PDO($dsn, $dbUser, $dbPass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Error ao conectar ao banco de dados: " . $e->getMessage());
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    
    if (isset($_POST['username']) && isset($_POST['password']) && !isset($_POST['code'])) {
        // Primeira etapa - verificar usuário e senha
        $username = trim($_POST['username']);
        $password = trim($_POST['password']);

        $stmt = $pdo->prepare("SELECT id, nome, senha, codigo FROM adminuser WHERE nome = :username");
        $stmt->bindParam(':username', $username);
        $stmt->execute();
        $admin = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($admin && $password === $admin['senha']) {
            // Senha correta, mostrar etapa do código
            $show_code_step = true;
            $temp_admin_data = $admin;
        } else {
            $login_error = true;
        }
    } elseif (isset($_POST['code']) && isset($_POST['admin_id'])) {
        // Segunda etapa - verificar código
        $code = trim($_POST['code']);
        $admin_id = $_POST['admin_id'];

        $stmt = $pdo->prepare("SELECT id, nome, codigo FROM adminuser WHERE id = :admin_id");
        $stmt->bindParam(':admin_id', $admin_id);
        $stmt->execute();
        $admin = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($admin && $code === $admin['codigo']) {
            // Código correto, fazer login
            $_SESSION['admin_id'] = $admin['id'];
            $_SESSION['admin_name'] = $admin['nome'];
            $login_success = true;
            $show_code_step = true; // Manter popup aberto
        } else {
            $code_error = true;
            $show_code_step = true;
            $temp_admin_data = $admin;
        }
    }
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=no, maximum-scale=1.0">
    <title>RPay Administração</title>
    <link rel="icon" href="../img/favicon.png" type="image/png">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <style>
        :root {
            --primary-rgb: 59, 130, 246;
            --secondary-rgb: 16, 185, 129;
            
            --dashboard-bg: #0f1419;
            --dashboard-surface: #1a1b2e;
            --dashboard-card: #16213e;
            --dashboard-blue: #3b82f6;
            --dashboard-cyan: #06b6d4;
            --dashboard-green: #10b981;
            --dashboard-red: #ef4444;
            --dashboard-text: #ffffff;
            --dashboard-text-muted: #94a3b8;
            --dashboard-border: rgba(255, 255, 255, 0.1);
        }

        [data-theme-mode="dark"] {
            --bg-primary: var(--dashboard-bg);
            --bg-secondary: var(--dashboard-surface);
            --bg-card: var(--dashboard-card);
            --text-primary: var(--dashboard-text);
            --text-muted: var(--dashboard-text-muted);
            --border-color: var(--dashboard-border);
        }

        [data-theme-mode="light"] {
            --bg-primary: #f8fafc;
            --bg-secondary: rgba(255, 255, 255, 0.95);
            --bg-card: rgba(255, 255, 255, 0.9);
            --text-primary: #1e293b;
            --text-muted: #64748b;
            --border-color: rgba(59, 130, 246, 0.1);
        }

        [data-theme-mode="light"] body::before {
            content: '';
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            height: 50vh;
            background: linear-gradient(135deg, #3b82f6 0%, #06b6d4 50%, #0ea5e9 100%);
            border-radius: 0 0 50% 50% / 0 0 100px 100px;
            z-index: -1;
            pointer-events: none;
        }

        /* Background Map for Dark Theme */
        [data-theme-mode="dark"] body::before {
            content: '';
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: var(--dashboard-bg);
            z-index: -2;
        }

        .network-background {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            z-index: -1;
            opacity: 0;
            transition: opacity 0.5s ease;
            pointer-events: none;
        }

        [data-theme-mode="dark"] .network-background {
            opacity: 1;
        }

        .network-canvas {
            width: 100%;
            height: 100%;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        html {
            -webkit-text-size-adjust: 100%;
            -webkit-font-smoothing: antialiased;
            -moz-osx-font-smoothing: grayscale;
        }

        body {
            background: var(--bg-primary);
            color: var(--text-primary);
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Inter', sans-serif;
            font-weight: 400;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 1rem;
            width: 100%;
            overflow-x: hidden;
        }

        .login-container {
            background: var(--bg-card);
            backdrop-filter: blur(20px);
            border-radius: 20px;
            padding: 2rem;
            border: 1px solid var(--border-color);
            box-shadow: 0 8px 32px rgba(0, 0, 0, 0.1);
            width: 100%;
            max-width: 380px;
            position: relative;
            animation: fadeInUp 0.6s ease;
        }

        .login-header {
            text-align: center;
            margin-bottom: 1.5rem;
        }

        .logo {
            width: 60px;
            height: 60px;
            margin: 0 auto 1rem;
            display: block;
            border-radius: 12px;
            object-fit: contain;
        }

        .login-title {
            font-size: 1.5rem;
            font-weight: 600;
            color: var(--text-primary);
            margin-bottom: 0.25rem;
        }

        .login-subtitle {
            color: var(--text-muted);
            font-size: 0.875rem;
            font-weight: 400;
        }

        .form-group {
            margin-bottom: 1rem;
        }

        .form-label {
            display: block;
            font-size: 0.8rem;
            font-weight: 500;
            margin-bottom: 0.4rem;
            color: var(--text-primary);
        }

        .form-input {
            width: 100%;
            padding: 0.75rem 1rem;
            background: rgba(59, 130, 246, 0.05);
            border: 1px solid var(--border-color);
            border-radius: 10px;
            color: var(--text-primary);
            font-size: 0.9rem;
            font-weight: 400;
            transition: all 0.3s ease;
        }

        .form-input:focus {
            outline: none;
            border-color: var(--dashboard-blue);
            box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
        }

        .form-input::placeholder {
            color: var(--text-muted);
            font-weight: 400;
        }

        .login-button {
            width: 100%;
            padding: 0.75rem 1rem;
            background: linear-gradient(135deg, var(--dashboard-blue), var(--dashboard-cyan));
            border: none;
            border-radius: 10px;
            color: white;
            font-weight: 500;
            font-size: 0.9rem;
            cursor: pointer;
            transition: all 0.3s ease;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 0.5rem;
            margin-top: 1rem;
        }

        .login-button:hover:not(:disabled) {
            transform: translateY(-1px);
            box-shadow: 0 4px 12px rgba(59, 130, 246, 0.3);
        }

        .login-button:disabled {
            opacity: 0.7;
            cursor: not-allowed;
        }

        .theme-toggle {
            position: fixed;
            top: 20px;
            right: 20px;
            width: 44px;
            height: 44px;
            border-radius: 50%;
            background: var(--bg-card);
            border: 1px solid var(--border-color);
            display: flex;
            align-items: center;
            justify-content: center;
            color: var(--text-primary);
            cursor: pointer;
            transition: all 0.3s ease;
            backdrop-filter: blur(20px);
            z-index: 999;
        }

        .theme-toggle:hover {
            transform: translateY(-2px);
            background: linear-gradient(135deg, var(--dashboard-blue), var(--dashboard-cyan));
            color: white;
        }

        /* Toast Notifications */
        .toast {
            position: fixed;
            top: 20px;
            right: 80px;
            padding: 12px 16px;
            border-radius: 12px;
            color: white;
            font-weight: 500;
            font-size: 14px;
            z-index: 1001;
            transform: translateX(400px);
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            display: flex;
            align-items: center;
            gap: 8px;
            backdrop-filter: blur(20px);
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.25);
            max-width: 300px;
            border: 1px solid rgba(255, 255, 255, 0.1);
        }

        .toast.success {
            background: linear-gradient(135deg, #10b981, #059669);
        }

        .toast.error {
            background: linear-gradient(135deg, #ef4444, #dc2626);
        }

        .toast.show {
            transform: translateX(0);
        }

        /* Popup para código */
        .popup-overlay {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.6);
            display: none;
            align-items: center;
            justify-content: center;
            z-index: 1000;
            backdrop-filter: blur(8px);
            padding: 1rem;
        }

        .popup {
            background: var(--bg-card);
            border-radius: 16px;
            padding: 2rem;
            max-width: 340px;
            width: 100%;
            text-align: center;
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.25);
            transform: scale(0.9);
            opacity: 0;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            border: 1px solid var(--border-color);
        }

        .popup.show {
            transform: scale(1);
            opacity: 1;
        }

        .popup-logo {
            width: 50px;
            height: 50px;
            border-radius: 8px;
            display: block;
            margin: 0 auto 1rem;
            object-fit: contain;
        }

        .popup h3 {
            font-size: 1.25rem;
            color: var(--text-primary);
            margin-bottom: 0.5rem;
            font-weight: 600;
        }

        .popup p {
            color: var(--text-muted);
            line-height: 1.5;
            margin-bottom: 1.5rem;
            font-size: 0.875rem;
            font-weight: 400;
        }

        .loading-spinner {
            width: 16px;
            height: 16px;
            border: 2px solid rgba(255, 255, 255, 0.3);
            border-radius: 50%;
            border-top-color: white;
            animation: spin 1s ease-in-out infinite;
        }

        @keyframes spin {
            to {
                transform: rotate(360deg);
            }
        }

        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        @media (max-width: 480px) {
            body {
                padding: 1rem 0.5rem;
                min-height: 100vh;
                height: 100vh;
                overflow: hidden;
            }
            
            .login-container {
                padding: 1.5rem;
                border-radius: 16px;
                margin: 0;
                height: auto;
                max-height: calc(100vh - 2rem);
                overflow-y: auto;
            }
            
            .popup {
                padding: 1.5rem;
                margin: 0.5rem;
            }
            
            .theme-toggle {
                top: 16px;
                right: 16px;
                width: 40px;
                height: 40px;
            }

            .toast {
                right: 60px;
                max-width: 250px;
            }
        }
    </style>
</head>
<body>
    <!-- Network Background for Dark Theme -->
    <div class="network-background">
        <canvas class="network-canvas" id="networkCanvas"></canvas>
    </div>

    <!-- Theme Toggle -->
    <button class="theme-toggle" id="themeToggle">
        <i class="fas fa-moon" id="themeIcon"></i>
    </button>

    <!-- Login Container -->
    <div class="login-container">
        <div class="login-header">
            <img src="teste.png" alt="Logo" class="logo" id="mainLogo">
            <h1 class="login-title">Painel Admin</h1>
            <p class="login-subtitle">Acesse o sistema administrativo</p>
        </div>

        <!-- Etapa 1: Login -->
        <div class="login-step" id="loginStep" <?php echo $show_code_step ? 'style="display: none;"' : ''; ?>>
            <form method="post" action="" id="loginForm">
                <div class="form-group">
                    <label for="username" class="form-label">Usuário</label>
                    <input type="text" id="username" name="username" class="form-input" placeholder="Enter your username" required>
                </div>
                <div class="form-group">
                    <label for="password" class="form-label">Senha</label>
                    <input type="password" id="password" name="password" class="form-input" placeholder="Enter your password" required>
                </div>
                <button type="submit" class="login-button">
                    <i class="fas fa-sign-in-alt"></i>
                    <span>Entrar</span>
                </button>
            </form>
        </div>
    </div>

    <!-- Popup para Código -->
    <div id="codePopup" class="popup-overlay" <?php echo $show_code_step ? 'style="display: flex;"' : ''; ?>>
        <div class="popup <?php echo $show_code_step ? 'show' : ''; ?>">
            <img src="teste.png" alt="Logo" class="popup-logo" id="popupLogo">
            <h3>Código de Verificação</h3>
            <p>Digite o código dand thecesso para completar o login</p>
            
            <form method="post" action="" id="codeForm">
                <input type="hidden" name="admin_id" value="<?php echo $temp_admin_data['id'] ?? ''; ?>">
                <div class="form-group">
                    <input type="text" id="code" name="code" class="form-input" placeholder="Digite o código" required style="text-align: center; font-size: 1rem; letter-spacing: 1px;">
                </div>
                <button type="submit" class="login-button" id="verifyBtn">
                    <i class="fas fa-check" id="verifyIcon"></i>
                    <span id="verifyText">Verificar</span>
                </button>
            </form>
        </div>
    </div>

    <script>
        // Network Background Animation
        class NetworkBackground {
            constructor() {
                this.canvas = document.getElementById('networkCanvas');
                this.ctx = this.canvas.getContext('2d');
                this.nodes = [];
                this.connections = [];
                this.mousePosition = { x: 0, y: 0 };
                this.animationId = null;
                
                this.init();
                this.createNodes();
                this.bindEvents();
                this.animate();
            }
            
            init() {
                this.resizeCanvas();
            }
            
            resizeCanvas() {
                this.canvas.width = window.innerWidth;
                this.canvas.height = window.innerHeight;
            }
            
            createNodes() {
                const nodeCount = Math.floor((this.canvas.width * this.canvas.height) / 15000);
                this.nodes = [];
                
                for (let i = 0; i < nodeCount; i++) {
                    this.nodes.push({
                        x: Math.random() * this.canvas.width,
                        y: Math.random() * this.canvas.height,
                        vx: (Math.random() - 0.5) * 0.5,
                        vy: (Math.random() - 0.5) * 0.5,
                        radius: Math.random() * 2 + 1,
                        opacity: Math.random() * 0.5 + 0.2
                    });
                }
            }
            
            updateNodes() {
                this.nodes.forEach(node => {
                    node.x += node.vx;
                    node.y += node.vy;
                    
                    if (node.x < 0 || node.x > this.canvas.width) node.vx *= -1;
                    if (node.y < 0 || node.y > this.canvas.height) node.vy *= -1;
                    
                    node.x = Math.max(0, Math.min(this.canvas.width, node.x));
                    node.y = Math.max(0, Math.min(this.canvas.height, node.y));
                });
            }
            
            drawNodes() {
                this.nodes.forEach(node => {
                    this.ctx.beginPath();
                    this.ctx.arc(node.x, node.y, node.radius, 0, Math.PI * 2);
                    this.ctx.fillStyle = `rgba(59, 130, 246, ${node.opacity})`;
                    this.ctx.fill();
                });
            }
            
            drawConnections() {
                for (let i = 0; i < this.nodes.length; i++) {
                    for (let j = i + 1; j < this.nodes.length; j++) {
                        const node1 = this.nodes[i];
                        const node2 = this.nodes[j];
                        const distance = Math.sqrt(
                            Math.pow(node1.x - node2.x, 2) + Math.pow(node1.y - node2.y, 2)
                        );
                        
                        if (distance < 120) {
                            const opacity = (120 - distance) / 120 * 0.1;
                            this.ctx.beginPath();
                            this.ctx.moveTo(node1.x, node1.y);
                            this.ctx.lineTo(node2.x, node2.y);
                            this.ctx.strokeStyle = `rgba(59, 130, 246, ${opacity})`;
                            this.ctx.lineWidth = 0.5;
                            this.ctx.stroke();
                        }
                    }
                }
            }
            
            animate() {
                this.ctx.clearRect(0, 0, this.canvas.width, this.canvas.height);
                
                this.updateNodes();
                this.drawConnections();
                this.drawNodes();
                
                this.animationId = requestAnimationFrame(() => this.animate());
            }
            
            bindEvents() {
                window.addEventListener('resize', () => {
                    this.resizeCanvas();
                    this.createNodes();
                });
                
                window.addEventListener('mousemove', (e) => {
                    this.mousePosition.x = e.clientX;
                    this.mousePosition.y = e.clientY;
                });
            }
            
            destroy() {
                if (this.animationId) {
                    cancelAnimationFrame(this.animationId);
                }
            }
        }

        // Initialize network background
        let networkBg = null;
        
        function initNetworkBackground() {
            if (document.documentElement.getAttribute('data-theme-mode') === 'dark') {
                if (!networkBg) {
                    networkBg = new NetworkBackground();
                }
            } else {
                if (networkBg) {
                    networkBg.destroy();
                    networkBg = null;
                }
            }
        }

        // Theme Toggland thend Logo Management
        const themeToggle = document.getElementById('themeToggle');
        const themeIcon = document.getElementById('themeIcon');
        const mainLogo = document.getElementById('mainLogo');
        const popupLogo = document.getElementById('popupLogo');
        const htmlElement = document.documentElement;
        
        const savedTheme = localStorage.getItem('admin_theme') || 'light';
        htmlElement.setAttribute('data-theme-mode', savedTheme);
        updateThemeIcon(savedTheme);
        updateLogos(savedTheme);
        
        // Initialize network background based on current theme
        setTimeout(() => {
            initNetworkBackground();
        }, 100);
        
        themeToggle.addEventListener('click', () => {
            const currentTheme = htmlElement.getAttribute('data-theme-mode');
            const newTheme = currentTheme === 'light' ? 'dark' : 'light';
            
            htmlElement.setAttribute('data-theme-mode', newTheme);
            localStorage.setItem('admin_theme', newTheme);
            updateThemeIcon(newTheme);
            updateLogos(newTheme);
            
            // Update network background
            setTimeout(() => {
                initNetworkBackground();
            }, 100);
        });
        
        function updateThemeIcon(theme) {
            if (theme === 'dark') {
                themeIcon.className = 'fas fa-sun';
            } else {
                themeIcon.className = 'fas fa-moon';
            }
        }

        function updateLogos(theme) {
            if (theme === 'light') {
                mainLogo.src = '../teste.png';
                popupLogo.src = '../teste.png';
            } else {
                mainLogo.src = '../img/l.png';
                popupLogo.src = '../img/l.png';
            }
        }

        // Toast functions
        function showToast(message, type = 'success') {
            const toast = document.createElement('div');
            toast.className = `toast ${type}`;
            
            const icon = type === 'success' ? 'fa-check' : 'fa-times';
            toast.innerHTML = `<i class="fas ${icon}"></i> ${message}`;
            
            document.body.appendChild(toast);
            
            setTimeout(() => {
                toast.classList.add('show');
            }, 100);
            
            setTimeout(() => {
                toast.classList.remove('show');
                setTimeout(() => {
                    if (document.body.contains(toast)) {
                        document.body.removeChild(toast);
                    }
                }, 300);
            }, 3000);
        }

        // Form handling
        document.getElementById('codeForm').addEventListener('submit', function(e) {
            e.preventDefault();
            
            const verifyBtn = document.getElementById('verifyBtn');
            const verifyIcon = document.getElementById('verifyIcon');
            const verifyText = document.getElementById('verifyText');
            
            // Show loading state
            verifyBtn.disabled = true;
            verifyIcon.innerHTML = '<div class="loading-spinner"></div>';
            verifyText.textContent = 'Aguarde...';
            
            // Submit form after short delay
            setTimeout(() => {
                this.submit();
            }, 500);
        });

        // Auto focus
        document.addEventListener('DOMContentLoaded', function() {
            <?php if ($show_code_step): ?>
                document.getElementById('code').focus();
            <?php else: ?>
                document.getElementById('username').focus();
            <?php endif; ?>
            
            <?php if ($login_error): ?>
                showToast('Usuário ou senha incorretos', 'error');
            <?php endif; ?>
            
            <?php if ($code_error): ?>
                showToast('Código incorreto', 'error');
            <?php endif; ?>
            
            <?php if ($login_success): ?>
                showToast('Login autorizado! Redirecionando...');
                setTimeout(() => {
                    window.location.href = 'user_list.php';
                }, 1500);
            <?php endif; ?>
        });

        // Prevent zoom on mobile
        document.addEventListener('gesturestart', function (e) {
            e.preventDefault();
        });

        document.addEventListener('gesturechange', function (e) {
            e.preventDefault();
        });

        document.addEventListener('gestureend', function (e) {
            e.preventDefault();
        });
    </script>
</body>
</html>
