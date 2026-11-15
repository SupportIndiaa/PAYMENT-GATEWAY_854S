<html>
<head>
    <style>
        /* Animações */
        @keyframes float {
            0%, 100% { transform: translateY(0px); }
            50% { transform: translateY(-10px); }
        }
        
        @keyframes pulse {
            0%, 100% { transform: scale(1); }
            50% { transform: scale(1.05); }
        }
        
        @keyframes slideInUp {
            from {
                opacity: 0;
                transform: translateY(30px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
        
        @keyframes bounce {
            0%, 20%, 53%, 80%, 100% { transform: translateY(0); }
            40%, 43% { transform: translateY(-15px); }
            70% { transform: translateY(-8px); }
            90% { transform: translateY(-3px); }
        }
        
        /* Container principal */
        .manager-modal-overlay {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: rgba(0, 0, 0, 0.5);
            backdrop-filter: blur(8px);
            z-index: 1055;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }
        
        .manager-modal-container {
            background: linear-gradient(135deg, #06b6d4 0%, #0891b2 100%);
            border-radius: 20px;
            width: 100%;
            max-width: 420px;
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.2);
            overflow: hidden;
            position: relative;
            animation: slideInUp 0.5s ease-out;
        }
        
        /* Decoração de fundo */
        .manager-modal-container::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: linear-gradient(45deg, rgba(255,255,255,0.1) 0%, transparent 100%);
            pointer-events: none;
        }
        
        /* Header estilizado */
        .modal-header {
            padding: 24px 24px 16px;
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            border-bottom: 1px solid rgba(255, 255, 255, 0.2);
            position: relative;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }
        
        .modal-title {
            font-size: 18px;
            font-weight: 600;
            color: #2d3748;
            margin: 0;
            display: flex;
            align-items: center;
            gap: 10px;
        }
        
        .modal-title::before {
            content: "💬";
            font-size: 18px;
        }
        
        .btn-close {
            width: 36px;
            height: 36px;
            background: rgba(255, 255, 255, 0.9);
            border: none;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #64748b;
            cursor: pointer;
            transition: all 0.3s ease;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
        }
        
        .btn-close:hover {
            background: rgba(255, 255, 255, 1);
            transform: rotate(90deg) scale(1.1);
        }
        
        .btn-close::before {
            content: "✕";
            font-size: 16px;
            line-height: 1;
        }
        
        /* Body do modal */
        .modal-body {
            padding: 30px 24px 24px;
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
        }
        
        /* Card do perfil */
        .manager-profile-card {
            text-align: center;
            position: relative;
        }
        
        /* Avatar com efeitos - SEM bola verde */
        .manager-avatar {
            width: 120px;
            height: 120px;
            border-radius: 50%;
            margin: 0 auto 20px;
            position: relative;
            display: block;
            border: 4px solid rgba(6, 182, 212, 0.3);
            box-shadow: 0 8px 32px rgba(0, 0, 0, 0.15);
            animation: float 3s ease-in-out infinite;
            object-fit: cover;
        }
        
        .avatar-container {
            position: relative;
            display: inline-block;
            margin-bottom: 20px;
        }
        
        /* Status online - cores azul turquesa */
        .online-status {
            position: absolute;
            bottom: 8px;
            right: 8px;
            width: 24px;
            height: 24px;
            background: #06b6d4;
            border: 3px solid white;
            border-radius: 50%;
            animation: pulse 2s infinite;
        }
        
        .online-status::after {
            content: '';
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            width: 8px;
            height: 8px;
            background: white;
            border-radius: 50%;
        }
        
        /* Informações do gerente */
        .manager-name {
            font-size: 24px;
            font-weight: 700;
            color: #2d3748;
            margin: 0 0 8px 0;
            background: linear-gradient(135deg, #06b6d4, #0891b2);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }
        
        .manager-email {
            font-size: 14px;
            color: #64748b;
            margin: 0 0 8px 0;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 6px;
        }
        
        .manager-email::before {
            content: "📧";
            font-size: 14px;
        }
        
        .manager-role {
            display: inline-block;
            background: linear-gradient(135deg, #06b6d4, #0891b2);
            color: white;
            padding: 6px 16px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 600;
            margin-bottom: 20px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            box-shadow: 0 4px 12px rgba(6, 182, 212, 0.3);
        }
        
        /* Info cards */
        .info-cards {
            display: flex;
            gap: 12px;
            margin-bottom: 24px;
        }
        
        .info-card {
            flex: 1;
            background: rgba(255, 255, 255, 0.8);
            border: 1px solid rgba(6, 182, 212, 0.2);
            border-radius: 12px;
            padding: 12px 8px;
            text-align: center;
            backdrop-filter: blur(10px);
            transition: all 0.3s ease;
        }
        
        .info-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 20px rgba(6, 182, 212, 0.15);
            border-color: rgba(6, 182, 212, 0.3);
        }
        
        .info-card-icon {
            font-size: 20px;
            margin-bottom: 4px;
            display: block;
        }
        
        .info-card-label {
            font-size: 10px;
            color: #64748b;
            font-weight: 500;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        
        .info-card-value {
            font-size: 12px;
            color: #2d3748;
            font-weight: 600;
            margin-top: 2px;
        }
        
        /* Botão WhatsApp - cores azul turquesa */
        .whatsapp-button {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 12px;
            background: linear-gradient(135deg, #06b6d4, #0891b2);
            color: white;
            padding: 16px 24px;
            border-radius: 12px;
            text-decoration: none;
            font-weight: 600;
            font-size: 15px;
            transition: all 0.3s ease;
            box-shadow: 0 6px 20px rgba(6, 182, 212, 0.3);
            position: relative;
            overflow: hidden;
            border: none;
            cursor: pointer;
        }
        
        .whatsapp-button::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255,255,255,0.2), transparent);
            transition: left 0.5s;
        }
        
        .whatsapp-button:hover::before {
            left: 100%;
        }
        
        .whatsapp-button:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(6, 182, 212, 0.4);
            color: white;
            text-decoration: none;
            background: linear-gradient(135deg, #0891b2, #0e7490);
        }
        
        .whatsapp-icon {
            width: 24px;
            height: 24px;
            animation: bounce 2s infinite;
        }
        
        /* Responsivo */
        @media (max-width: 768px) {
            .manager-modal-overlay {
                padding: 16px;
            }
            
            .manager-modal-container {
                max-width: 100%;
                width: 100%;
                max-height: 95vh;
            }
            
            .modal-header {
                padding: 20px 20px 16px;
            }
            
            .modal-body {
                padding: 24px 20px 20px;
            }
            
            .modal-title {
                font-size: 16px;
            }
            
            .manager-avatar {
                width: 100px;
                height: 100px;
            }
            
            .manager-name {
                font-size: 22px;
            }
            
            .info-cards {
                gap: 8px;
            }
            
            .info-card {
                padding: 10px 6px;
            }
            
            .whatsapp-button {
                padding: 14px 20px;
                font-size: 14px;
            }
        }
        
        @media (max-width: 480px) {
            .modal-header {
                padding: 16px 16px 12px;
            }
            
            .modal-body {
                padding: 20px 16px 16px;
            }
            
            .manager-avatar {
                width: 90px;
                height: 90px;
            }
            
            .manager-name {
                font-size: 20px;
            }
        }
    </style>
</head>
<body>
    <div class="manager-modal-overlay">
        <div class="manager-modal-container">
            <div class="modal-header">
                <h6 class="modal-title" id="staticBackdropLabel4">Relationship Manager - <?php include '../get_site_name.php'; ?></h6>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="manager-profile-card">
                    <div class="avatar-container">
                        <img class="manager-avatar" src="https://st2.depositphotos.com/37227484/42763/i/450/depositphotos_427634378-stock-photo-blonde-business-woman-sitting-and.jpg" alt="João - Gerente">
                        <div class="online-status"></div>
                    </div>
                    
                    <h4 class="manager-name">Evylin</h4>
                    <p class="manager-email">gerente@propay.com</p>
                    <span class="manager-role">Relationship Manager</span>
                    
                    <div class="info-cards">
                        <div class="info-card">
                            <span class="info-card-icon">⚡</span>
                            <div class="info-card-label">Response</div>
                            <div class="info-card-value">Immediate</div>
                        </div>
                        <div class="info-card">
                            <span class="info-card-icon">🕒</span>
                            <div class="info-card-label">Timings</div>
                            <div class="info-card-value">24/7</div>
                        </div>
                        <div class="info-card">
                            <span class="info-card-icon">⭐</span>
                            <div class="info-card-label">Rating</div>
                            <div class="info-card-value">5.0</div>
                        </div>
                    </div>
                    
                    <a href="https://t.me/TOXIC_ADMINN" target="_blank" class="whatsapp-button">
                        <svg class="whatsapp-icon" viewBox="0 0 24 24" fill="currentColor">
                            <!-- Telegram Icon -->
                            <path d="M11.944 0A12 12 0 0 0 0 12a12 12 0 0 0 12 12 12 12 0 0 0 12-12A12 12 0 0 0 12 0a12 12 0 0 0-.056 0zm4.962 7.224c.1-.002.321.023.465.14a.506.506 0 0 1 .171.325c.016.093.036.306.02.472-.18 1.898-.962 6.502-1.36 8.627-.168.9-.499 1.201-.82 1.23-.696.065-1.225-.46-1.9-.902-1.056-.693-1.653-1.124-2.678-1.8-1.185-.78-.417-1.21.258-1.91.177-.184 3.247-2.977 3.307-3.23.007-.032.014-.15-.056-.212s-.174-.041-.249-.024c-.106.024-1.793 1.14-5.061 3.345-.48.33-.913.49-1.302.48-.428-.008-1.252-.241-1.865-.44-.752-.245-1.349-.374-1.297-.789.027-.216.325-.437.888-.662 3.498-1.524 5.83-2.529 6.998-3.014 3.332-1.386 4.025-1.627 4.476-1.635z"/>
                        </svg>
                        <span>@TOXIC_ADMINN</span>
                    </a>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
