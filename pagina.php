<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>TurquoisePay | Solução Completa em Pagamentos</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root {
            --turquoise: #40e0d0;
            --turquoise-dark: #20b2aa;
            --turquoise-light: #afeeee;
            --white: #ffffff;
            --gray-light: #f8f9fa;
            --gray: #6c757d;
            --gray-dark: #343a40;
            --shadow: 0 4px 20px rgba(0, 0, 0, 0.1);
        }
        
        body {
            font-family: 'Segoe UI', system-ui, -apple-system, sans-serif;
            color: var(--gray-dark);
            overflow-x: hidden;
        }
        
        /* Navbar */
        .navbar {
            background-color: var(--white);
            box-shadow: var(--shadow);
            padding: 15px 0;
        }
        
        .navbar-brand img {
            height: 40px;
        }
        
        .nav-link {
            color: var(--gray-dark) !important;
            font-weight: 500;
            margin: 0 10px;
            transition: all 0.3s;
        }
        
        .nav-link:hover {
            color: var(--turquoise) !important;
        }
        
        .btn-turquoise {
            background-color: var(--turquoise);
            color: white;
            border: none;
            border-radius: 30px;
            padding: 10px 25px;
            font-weight: 600;
            transition: all 0.3s;
        }
        
        .btn-turquoise:hover {
            background-color: var(--turquoise-dark);
            color: white;
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(64, 224, 208, 0.3);
        }
        
        /* Hero Section */
        .hero {
            background: linear-gradient(135deg, var(--turquoise-light), var(--turquoise));
            padding: 100px 0;
            color: white;
        }
        
        .hero h1 {
            font-weight: 700;
            font-size: 3rem;
            margin-bottom: 20px;
        }
        
        .hero p {
            font-size: 1.2rem;
            margin-bottom: 30px;
        }
        
        .hero-img {
            animation: float 3s ease-in-out infinite;
        }
        
        @keyframes float {
            0% { transform: translateY(0px); }
            50% { transform: translateY(-15px); }
            100% { transform: translateY(0px); }
        }
        
        /* Features */
        .features {
            padding: 80px 0;
            background-color: var(--white);
        }
        
        .section-title {
            color: var(--turquoise-dark);
            font-weight: 700;
            margin-bottom: 50px;
            position: relative;
        }
        
        .section-title:after {
            content: '';
            position: absolute;
            bottom: -15px;
            left: 50%;
            transform: translateX(-50%);
            width: 80px;
            height: 3px;
            background: var(--turquoise);
        }
        
        .feature-card {
            background: var(--white);
            border-radius: 10px;
            padding: 30px;
            margin-bottom: 30px;
            box-shadow: var(--shadow);
            transition: all 0.3s;
            border-top: 3px solid var(--turquoise);
        }
        
        .feature-card:hover {
            transform: translateY(-10px);
            box-shadow: 0 15px 30px rgba(0, 0, 0, 0.1);
        }
        
        .feature-icon {
            font-size: 2.5rem;
            color: var(--turquoise);
            margin-bottom: 20px;
        }
        
        /* How it works */
        .how-it-works {
            padding: 80px 0;
            background-color: var(--gray-light);
        }
        
        .step {
            position: relative;
            padding-left: 80px;
            margin-bottom: 40px;
        }
        
        .step-number {
            position: absolute;
            left: 0;
            top: 0;
            width: 60px;
            height: 60px;
            background: var(--turquoise);
            color: white;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.5rem;
            font-weight: 700;
        }
        
        /* Pricing */
        .pricing {
            padding: 80px 0;
            background-color: var(--white);
        }
        
        .pricing-card {
            background: var(--white);
            border-radius: 10px;
            padding: 40px 30px;
            margin-bottom: 30px;
            box-shadow: var(--shadow);
            transition: all 0.3s;
            border: 2px solid transparent;
        }
        
        .pricing-card:hover {
            transform: translateY(-10px);
            border-color: var(--turquoise);
        }
        
        .pricing-card.featured {
            border-color: var(--turquoise);
            position: relative;
        }
        
        .pricing-card.featured:before {
            content: 'Mais Popular';
            position: absolute;
            top: -12px;
            right: 30px;
            background: var(--turquoise);
            color: white;
            padding: 5px 15px;
            border-radius: 20px;
            font-size: 0.8rem;
            font-weight: 600;
        }
        
        .price {
            font-size: 2.5rem;
            font-weight: 700;
            color: var(--turquoise-dark);
        }
        
        .price small {
            font-size: 1rem;
            color: var(--gray);
        }
        
        /* Testimonials */
        .testimonials {
            padding: 80px 0;
            background-color: var(--gray-light);
        }
        
        .testimonial-card {
            background: var(--white);
            border-radius: 10px;
            padding: 30px;
            margin-bottom: 30px;
            box-shadow: var(--shadow);
        }
        
        .testimonial-img {
            width: 70px;
            height: 70px;
            border-radius: 50%;
            object-fit: cover;
            border: 3px solid var(--turquoise-light);
        }
        
        .testimonial-rating {
            color: #ffc107;
            margin-bottom: 15px;
        }
        
        /* CTA */
        .cta {
            padding: 80px 0;
            background: linear-gradient(135deg, var(--turquoise-dark), var(--turquoise));
            color: white;
        }
        
        /* Footer */
        .footer {
            background-color: var(--gray-dark);
            color: white;
            padding: 60px 0 20px;
        }
        
        .footer-logo img {
            height: 40px;
            margin-bottom: 20px;
        }
        
        .footer-links h5 {
            color: var(--turquoise);
            margin-bottom: 20px;
            font-weight: 600;
        }
        
        .footer-links ul {
            list-style: none;
            padding: 0;
        }
        
        .footer-links li {
            margin-bottom: 10px;
        }
        
        .footer-links a {
            color: var(--gray-light);
            text-decoration: none;
            transition: all 0.3s;
        }
        
        .footer-links a:hover {
            color: var(--turquoise);
            padding-left: 5px;
        }
        
        .social-icons a {
            display: inline-block;
            width: 40px;
            height: 40px;
            background: rgba(255, 255, 255, 0.1);
            color: white;
            border-radius: 50%;
            text-align: center;
            line-height: 40px;
            margin-right: 10px;
            transition: all 0.3s;
        }
        
        .social-icons a:hover {
            background: var(--turquoise);
            transform: translateY(-5px);
        }
        
        .copyright {
            border-top: 1px solid rgba(255, 255, 255, 0.1);
            padding-top: 20px;
            margin-top: 40px;
        }
    </style>
</head>
<body>
    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg sticky-top">
        <div class="container">
            <a class="navbar-brand" href="#">
                <img src="https://via.placeholder.com/150x50?text=TurquoisePay" alt="TurquoisePay">
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="#features">Recursos</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#how-it-works">Como Funciona</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#pricing">Preços</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#testimonials">Depoimentos</a>
                    </li>
                    <li class="nav-item ms-lg-3">
                        <a class="btn btn-turquoise" href="#contact">Começar Agora</a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <!-- Hero Section -->
    <section class="hero">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-lg-6">
                    <h1>Solução completa para seus pagamentos</h1>
                    <p>Integração simples, taxas competitivas e suporte excepcional. Tudo que você precisa para aceitar pagamentos online.</p>
                    <div class="d-flex gap-3">
                        <a href="#contact" class="btn btn-turquoise">Começar Agora</a>
                        <a href="#how-it-works" class="btn btn-outline-light">Saiba Mais</a>
                    </div>
                </div>
                <div class="col-lg-6">
                    <img src="https://via.placeholder.com/600x500/40e0d0/ffffff?text=Payment+Gateway" alt="Payment Gateway" class="img-fluid hero-img">
                </div>
            </div>
        </div>
    </section>

    <!-- Features -->
    <section id="features" class="features">
        <div class="container">
            <h2 class="text-center section-title">Nossos Recursos</h2>
            <div class="row">
                <div class="col-md-4">
                    <div class="feature-card">
                        <div class="feature-icon">
                            <i class="fas fa-bolt"></i>
                        </div>
                        <h3>Processamento Rápido</h3>
                        <p>Transactions processadas em tempo real com confirmação instantânea para melhor experiência do usuário.</p>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="feature-card">
                        <div class="feature-icon">
                            <i class="fas fa-lock"></i>
                        </div>
                        <h3>Security Máxima</h3>
                        <p>Tecnologia de criptografia avançada e conformidade PCI DSS para proteger todas as transações.</p>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="feature-card">
                        <div class="feature-icon">
                            <i class="fas fa-chart-line"></i>
                        </div>
                        <h3>Relatórios Avançados</h3>
                        <p>Painel completo com análises e relatórios detalhados para acompanhar seu desempenho financeiro.</p>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="feature-card">
                        <div class="feature-icon">
                            <i class="fas fa-mobile-alt"></i>
                        </div>
                        <h3>Checkout Mobile</h3>
                        <p>Experiência de pagamento otimizada para dispositivos móveis com alto índice de conversão.</p>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="feature-card">
                        <div class="feature-icon">
                            <i class="fas fa-random"></i>
                        </div>
                        <h3>Multiplos Métodos</h3>
                        <p>Aceite cartões, PIX, boleto e transferências bancárias em uma única integração.</p>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="feature-card">
                        <div class="feature-icon">
                            <i class="fas fa-headset"></i>
                        </div>
                        <h3>Suporte 24/7</h3>
                        <p>Nossa equipe está disponível 24 horas por dia, 7 dias por semana para tand thejudar.</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- How it works -->
    <section id="how-it-works" class="how-it-works">
        <div class="container">
            <h2 class="text-center section-title">Como Funciona</h2>
            <div class="row justify-content-center">
                <div class="col-lg-8">
                    <div class="step">
                        <div class="step-number">1</div>
                        <h3>Crie sua Conta</h3>
                        <p>Registre-se em minutos e tenha acesso imediato ao painel de controle. Nenhum custo inicial ou mensalidade fixa.</p>
                    </div>
                    <div class="step">
                        <div class="step-number">2</div>
                        <h3>Integre à sua Plataforma</h3>
                        <p>Utilize nossa API simples ou plugins prontos para WordPress, WooCommerce, Magento e outras plataformas.</p>
                    </div>
                    <div class="step">
                        <div class="step-number">3</div>
                        <h3>Comecand the Receber Pagamentos</h3>
                        <p>Pronto! Seus clientes já podem realizar pagamentos e você recebe os fundos em sua conta bancária.</p>
                    </div>
                </div>
            </div>
            <div class="text-center mt-5">
                <a href="#" class="btn btn-turquoise">Ver Documentation da API</a>
            </div>
        </div>
    </section>

    <!-- Pricing -->
    <section id="pricing" class="pricing">
        <div class="container">
            <h2 class="text-center section-title">Planos e Preços</h2>
            <p class="text-center mb-5">Escolha o plano perfeito para o seu negócio. Sem taxas ocultas ou surpresas.</p>
            <div class="row">
                <div class="col-md-4">
                    <div class="pricing-card text-center">
                        <h3>Start</h3>
                        <div class="price mt-3 mb-4">1.99% <small>+ R$0.25</small></div>
                        <ul class="list-unstyled">
                            <li class="mb-3"><i class="fas fa-check text-success me-2"></i> Até R$10.000/mês</li>
                            <li class="mb-3"><i class="fas fa-check text-success me-2"></i> Cartões de crédito e débito</li>
                            <li class="mb-3"><i class="fas fa-check text-success me-2"></i> PIX</li>
                            <li class="mb-3"><i class="fas fa-times text-danger me-2"></i> Boleto bancário</li>
                            <li class="mb-3"><i class="fas fa-times text-danger me-2"></i> Suporte prioritário</li>
                        </ul>
                        <a href="#" class="btn btn-outline-primary w-100">Escolher Plano</a>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="pricing-card text-center featured">
                        <h3>Pro</h3>
                        <div class="price mt-3 mb-4">1.49% <small>+ R$0.20</small></div>
                        <ul class="list-unstyled">
                            <li class="mb-3"><i class="fas fa-check text-success me-2"></i> Até R$50.000/mês</li>
                            <li class="mb-3"><i class="fas fa-check text-success me-2"></i> Cartões de crédito e débito</li>
                            <li class="mb-3"><i class="fas fa-check text-success me-2"></i> PIX</li>
                            <li class="mb-3"><i class="fas fa-check text-success me-2"></i> Boleto bancário</li>
                            <li class="mb-3"><i class="fas fa-times text-danger me-2"></i> Suporte prioritário</li>
                        </ul>
                        <a href="#" class="btn btn-turquoise w-100">Escolher Plano</a>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="pricing-card text-center">
                        <h3>Enterprise</h3>
                        <div class="price mt-3 mb-4">0.99% <small>+ R$0.15</small></div>
                        <ul class="list-unstyled">
                            <li class="mb-3"><i class="fas fa-check text-success me-2"></i> Volume ilimitado</li>
                            <li class="mb-3"><i class="fas fa-check text-success me-2"></i> Cartões de crédito e débito</li>
                            <li class="mb-3"><i class="fas fa-check text-success me-2"></i> PIX</li>
                            <li class="mb-3"><i class="fas fa-check text-success me-2"></i> Boleto bancário</li>
                            <li class="mb-3"><i class="fas fa-check text-success me-2"></i> Suporte prioritário 24/7</li>
                        </ul>
                        <a href="#" class="btn btn-outline-primary w-100">Fale Conosco</a>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Testimonials -->
    <section id="testimonials" class="testimonials">
        <div class="container">
            <h2 class="text-center section-title">O que dizem nossos clientes</h2>
            <div class="row">
                <div class="col-md-4">
                    <div class="testimonial-card">
                        <div class="d-flex align-items-center mb-4">
                            <img src="https://randomuser.me/api/portraits/women/32.jpg" alt="Cliente" class="testimonial-img me-3">
                            <div>
                                <h5 class="mb-0">Ana Carolina</h5>
                                <small>CEO - Loja Virtual</small>
                            </div>
                        </div>
                        <div class="testimonial-rating">
                            <i class="fas fa-star"></i>
                            <i class="fas fa-star"></i>
                            <i class="fas fa-star"></i>
                            <i class="fas fa-star"></i>
                            <i class="fas fa-star"></i>
                        </div>
                        <p>"Migrei minha loja para o TurquoisePay e nunca mais tive problemas com pagamentos. A taxa de conversão aumentou 30% e o suporte é excelente!"</p>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="testimonial-card">
                        <div class="d-flex align-items-center mb-4">
                            <img src="https://randomuser.me/api/portraits/men/45.jpg" alt="Cliente" class="testimonial-img me-3">
                            <div>
                                <h5 class="mb-0">Ricardo Almeida</h5>
                                <small>Fundador - SaaS Startup</small>
                            </div>
                        </div>
                        <div class="testimonial-rating">
                            <i class="fas fa-star"></i>
                            <i class="fas fa-star"></i>
                            <i class="fas fa-star"></i>
                            <i class="fas fa-star"></i>
                            <i class="fas fa-star"></i>
                        </div>
                        <p>"A API do TurquoisePay foi a mais fácil de integrar que já usei. Em poucas horas já estava recebendo pagamentos dand thessinaturas recorrentes."</p>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="testimonial-card">
                        <div class="d-flex align-items-center mb-4">
                            <img src="https://randomuser.me/api/portraits/women/68.jpg" alt="Cliente" class="testimonial-img me-3">
                            <div>
                                <h5 class="mb-0">Fernanda Souza</h5>
                                <small>Diretora - Agência Digital</small>
                            </div>
                        </div>
                        <div class="testimonial-rating">
                            <i class="fas fa-star"></i>
                            <i class="fas fa-star"></i>
                            <i class="fas fa-star"></i>
                            <i class="fas fa-star"></i>
                            <i class="fas fa-star-half-alt"></i>
                        </div>
                        <p>"Recomendo para todos meus clientes. As taxas são competitivas e o tempo de liberação dos recursos é o mais rápido do mercado."</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- CTA -->
    <section id="contact" class="cta">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-lg-8 text-center">
                    <h2 class="mb-4">Pronto para impulsionar suas vendas?</h2>
                    <p class="mb-5">Cadastre-sand thegora e comecand the receber pagamentos em minutos. Sem compromisso, sem cartão de crédito.</p>
                    <form class="row g-3 justify-content-center">
                        <div class="col-md-8">
                            <input type="email" class="form-control form-control-lg" placeholder="Seu melhor e-mail">
                        </div>
                        <div class="col-md-4">
                            <button type="submit" class="btn btn-light btn-lg w-100">Começar Agora</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer class="footer">
        <div class="container">
            <div class="row">
                <div class="col-lg-4 mb-5 mb-lg-0">
                    <div class="footer-logo">
                        <img src="https://via.placeholder.com/150x50/ffffff?text=TurquoisePay" alt="TurquoisePay">
                    </div>
                    <p class="mb-4">A solução completa para processamento de pagamentos online para negócios de todos os tamanhos.</p>
                    <div class="social-icons">
                        <a href="#"><i class="fab fa-facebook-f"></i></a>
                        <a href="#"><i class="fab fa-twitter"></i></a>
                        <a href="#"><i class="fab fa-instagram"></i></a>
                        <a href="#"><i class="fab fa-linkedin-in"></i></a>
                    </div>
                </div>
                <div class="col-lg-2 col-md-4 mb-4 mb-md-0">
                    <div class="footer-links">
                        <h5>Empresa</h5>
                        <ul>
                            <li><a href="#">Sobre Nós</a></li>
                            <li><a href="#">Carreiras</a></li>
                            <li><a href="#">Blog</a></li>
                            <li><a href="#">Imprensa</a></li>
                            <li><a href="#">Parceiros</a></li>
                        </ul>
                    </div>
                </div>
                <div class="col-lg-2 col-md-4 mb-4 mb-md-0">
                    <div class="footer-links">
                        <h5>Produtos</h5>
                        <ul>
                            <li><a href="#">Pagamentos Online</a></li>
                            <li><a href="#">Links de Pagamento</a></li>
                            <li><a href="#">Assinaturas</a></li>
                            <li><a href="#">Marketplaces</a></li>
                            <li><a href="#">Soluções Corporativas</a></li>
                        </ul>
                    </div>
                </div>
                <div class="col-lg-2 col-md-4 mb-4 mb-md-0">
                    <div class="footer-links">
                        <h5>Recursos</h5>
                        <ul>
                            <li><a href="#">Documentation</a></li>
                            <li><a href="#">API</a></li>
                            <li><a href="#">Plugins</a></li>
                            <li><a href="#">Status</a></li>
                            <li><a href="#">FAQ</a></li>
                        </ul>
                    </div>
                </div>
                <div class="col-lg-2 col-md-4">
                    <div class="footer-links">
                        <h5>Suporte</h5>
                        <ul>
                            <li><a href="#">Central de Ajuda</a></li>
                            <li><a href="#">Contact</a></li>
                            <li><a href="#">Security</a></li>
                            <li><a href="#">Termos</a></li>
                            <li><a href="#">Privacidade</a></li>
                        </ul>
                    </div>
                </div>
            </div>
            <div class="copyright text-center">
                <p class="mb-0">© 2023 TurquoisePay. Todos os direitos reservados.</p>
            </div>
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Smooth scrolling for anchor links
        document.querySelectorAll('a[href^="#"]').forEach(anchor => {
            anchor.addEventListener('click', function (e) {
                e.preventDefault();
                
                document.querySelector(this.getAttribute('href')).scrollIntoView({
                    behavior: 'smooth'
                });
            });
        });
    </script>
</body>
</html>