<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Évaluation des Enseignants — Plateforme Officielle</title>
    <meta name="description" content="Plateforme institutionnelle d'évaluation des enseignements universitaires. Garantie de confidentialité et d'anonymat.">
    <meta name="robots" content="index, follow">

    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">

    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Inter', sans-serif;
            color: #1e293b;
            background: #ffffff;
            line-height: 1.5;
        }

        /* Typography */
        h1, h2, h3, h4, h5, h6 {
            font-weight: 700;
            letter-spacing: -0.02em;
        }

        /* Navigation */
        .navbar {
            background: rgba(255, 255, 255, 0.98);
            backdrop-filter: blur(10px);
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.05);
            padding: 1rem 0;
            transition: all 0.3s ease;
        }

        .navbar.scrolled {
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
        }

        .navbar-brand {
            font-weight: 800;
            font-size: 1.35rem;
            color: #0f172a;
            letter-spacing: -0.02em;
        }

        .navbar-brand span {
            color: #4361ee;
        }

        .nav-link {
            font-weight: 500;
            color: #475569;
            transition: color 0.2s ease;
            margin: 0 0.25rem;
        }

        .nav-link:hover {
            color: #4361ee;
        }

        .btn-login {
            background: #4361ee;
            color: white;
            padding: 0.5rem 1.5rem;
            border-radius: 10px;
            font-weight: 600;
            font-size: 0.875rem;
            transition: all 0.3s ease;
            border: none;
        }

        .btn-login:hover {
            background: #3451d1;
            transform: translateY(-1px);
            box-shadow: 0 4px 12px rgba(67, 97, 238, 0.3);
            color: white;
        }

        /* Hero Section */
        .hero {
            padding: 8rem 0 5rem 0;
            background: linear-gradient(135deg, #f8fafc 0%, #eef2ff 100%);
            position: relative;
            overflow: hidden;
        }

        .hero::before {
            content: '';
            position: absolute;
            top: 0;
            right: 0;
            width: 50%;
            height: 100%;
            background: radial-gradient(circle, rgba(67, 97, 238, 0.08) 0%, transparent 70%);
            pointer-events: none;
        }

        .hero-badge {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            background: rgba(67, 97, 238, 0.1);
            padding: 0.5rem 1rem;
            border-radius: 50px;
            font-size: 0.8rem;
            font-weight: 600;
            color: #4361ee;
            margin-bottom: 1.5rem;
        }

        .hero h1 {
            font-size: 3rem;
            font-weight: 800;
            color: #0f172a;
            margin-bottom: 1.5rem;
            line-height: 1.2;
        }

        .hero h1 .highlight {
            color: #4361ee;
            position: relative;
        }

        .hero .lead {
            font-size: 1.1rem;
            color: #475569;
            margin-bottom: 2rem;
            max-width: 90%;
        }

        .btn-primary-custom {
            background: #4361ee;
            color: white;
            padding: 0.875rem 2rem;
            border-radius: 12px;
            font-weight: 600;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            transition: all 0.3s ease;
            border: none;
        }

        .btn-primary-custom:hover {
            background: #3451d1;
            transform: translateY(-2px);
            box-shadow: 0 8px 20px rgba(67, 97, 238, 0.3);
            color: white;
        }

        .btn-outline-custom {
            background: transparent;
            color: #4361ee;
            padding: 0.875rem 2rem;
            border-radius: 12px;
            font-weight: 600;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            border: 1.5px solid #4361ee;
            transition: all 0.3s ease;
        }

        .btn-outline-custom:hover {
            background: #4361ee;
            color: white;
            transform: translateY(-2px);
        }

        /* Feature Cards */
        .section {
            padding: 5rem 0;
        }

        .section-title {
            text-align: center;
            margin-bottom: 3rem;
        }

        .section-tag {
            display: inline-block;
            background: rgba(67, 97, 238, 0.1);
            color: #4361ee;
            padding: 0.25rem 1rem;
            border-radius: 50px;
            font-size: 0.75rem;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 1px;
            margin-bottom: 1rem;
        }

        .section-title h2 {
            font-size: 2.25rem;
            color: #0f172a;
            margin-bottom: 1rem;
        }

        .section-title p {
            color: #64748b;
            max-width: 600px;
            margin: 0 auto;
        }

        .feature-card {
            background: white;
            border-radius: 20px;
            padding: 2rem;
            transition: all 0.3s ease;
            border: 1px solid #e2e8f0;
            height: 100%;
        }

        .feature-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 20px 35px -12px rgba(0, 0, 0, 0.1);
            border-color: #4361ee20;
        }

        .feature-icon {
            width: 60px;
            height: 60px;
            background: linear-gradient(135deg, #4361ee20, #4361ee10);
            border-radius: 15px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 1.5rem;
        }

        .feature-icon i {
            font-size: 1.75rem;
            color: #4361ee;
        }

        .feature-card h3 {
            font-size: 1.25rem;
            margin-bottom: 0.75rem;
            color: #0f172a;
        }

        .feature-card p {
            color: #64748b;
            font-size: 0.875rem;
            line-height: 1.6;
        }

        /* Stats Section */
        .stats-section {
            background: linear-gradient(135deg, #0f172a 0%, #1e293b 100%);
            padding: 4rem 0;
            color: white;
        }

        .stat-item {
            text-align: center;
            padding: 1rem;
        }

        .stat-number {
            font-size: 2.5rem;
            font-weight: 800;
            margin-bottom: 0.5rem;
        }

        .stat-label {
            font-size: 0.875rem;
            opacity: 0.8;
            text-transform: uppercase;
            letter-spacing: 1px;
        }

        /* Process Steps */
        .step-card {
            text-align: center;
            padding: 1.5rem;
        }

        .step-number {
            width: 70px;
            height: 70px;
            background: linear-gradient(135deg, #4361ee, #3451d1);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 1.5rem;
            font-size: 1.5rem;
            font-weight: 700;
            color: white;
            box-shadow: 0 10px 25px -5px rgba(67, 97, 238, 0.3);
        }

        .step-card h4 {
            font-size: 1.1rem;
            margin-bottom: 0.5rem;
            color: #0f172a;
        }

        .step-card p {
            color: #64748b;
            font-size: 0.875rem;
        }

        /* CTA Section */
        .cta-section {
            background: linear-gradient(135deg, #4361ee, #3451d1);
            padding: 5rem 0;
            color: white;
            text-align: center;
        }

        .cta-section h2 {
            font-size: 2rem;
            margin-bottom: 1rem;
        }

        .cta-section p {
            opacity: 0.9;
            margin-bottom: 2rem;
            font-size: 1rem;
        }

        .btn-cta {
            background: white;
            color: #4361ee;
            padding: 0.875rem 2rem;
            border-radius: 12px;
            font-weight: 600;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            transition: all 0.3s ease;
        }

        .btn-cta:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.2);
            color: #4361ee;
        }

        /* Footer */
        footer {
            background: #0f172a;
            padding: 3rem 0 2rem;
            color: #94a3b8;
        }

        footer h5 {
            color: white;
            font-size: 0.875rem;
            margin-bottom: 1rem;
            font-weight: 600;
        }

        footer ul {
            list-style: none;
            padding: 0;
        }

        footer ul li {
            margin-bottom: 0.5rem;
        }

        footer ul li a {
            color: #94a3b8;
            text-decoration: none;
            font-size: 0.875rem;
            transition: color 0.2s;
        }

        footer ul li a:hover {
            color: white;
        }

        .footer-bottom {
            border-top: 1px solid #1e293b;
            padding-top: 2rem;
            margin-top: 2rem;
            text-align: center;
            font-size: 0.75rem;
        }

        /* Responsive */
        @media (max-width: 768px) {
            .hero {
                padding: 6rem 0 3rem;
            }

            .hero h1 {
                font-size: 2rem;
            }

            .hero .lead {
                font-size: 1rem;
                max-width: 100%;
            }

            .section-title h2 {
                font-size: 1.75rem;
            }
        }

        /* Animations */
        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(30px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .animate-on-scroll {
            opacity: 0;
            transform: translateY(30px);
            transition: all 0.6s ease;
        }

        .animate-on-scroll.visible {
            opacity: 1;
            transform: translateY(0);
        }
    </style>
</head>
<body>

<!-- Navigation -->
<nav class="navbar navbar-expand-lg fixed-top">
    <div class="container">
        <a class="navbar-brand" href="/">
            Éval<span>ENS</span>
        </a>

        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
            <span class="navbar-toggler-icon"></span>
        </button>

        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav mx-auto">
                <li class="nav-item">
                    <a class="nav-link" href="#accueil">Accueil</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="#fonctionnalites">Fonctionnalités</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="#fonctionnement">Fonctionnement</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="#a-propos">À propos</a>
                </li>
            </ul>
            <a href="{{ route('login') }}" class="btn-login">
                <i class="bi bi-box-arrow-in-right"></i> Connexion
            </a>
        </div>
    </div>
</nav>

<!-- Hero Section -->
<section class="hero" id="accueil">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-lg-7">
                <div class="hero-badge">
                    <i class="bi bi-patch-check-fill"></i>
                    Plateforme officielle d'évaluation
                </div>
                <h1>
                    Améliorons ensemble la<br>
                    <span class="highlight">qualité de l'enseignement</span>
                </h1>
                <p class="lead">
                    Un outil institutionnel dédié à l'évaluation des enseignements,
                    garantissant confidentialité et transparence pour tous les acteurs
                    de la communauté universitaire.
                </p>
                <div class="d-flex gap-3 flex-wrap">
                    <a href="{{ route('login') }}" class="btn-primary-custom">
                        <i class="bi bi-arrow-right-circle"></i> Accéder à la plateforme
                    </a>
                    <a href="#fonctionnement" class="btn-outline-custom">
                        <i class="bi bi-info-circle"></i> En savoir plus
                    </a>
                </div>
            </div>
            <div class="col-lg-5 d-none d-lg-block">
                <img src="https://placehold.co/500x400/eef2ff/4361ee?text=Illustration" alt="Illustration" class="img-fluid" style="border-radius: 20px;">
            </div>
        </div>
    </div>
</section>

<!-- Features Section -->
<section class="section" id="fonctionnalites">
    <div class="container">
        <div class="section-title animate-on-scroll">
            <div class="section-tag">Pourquoi cette plateforme ?</div>
            <h2>Une solution complète pour l'évaluation pédagogique</h2>
            <p>Des outils pensés pour répondre aux besoins des établissements d'enseignement supérieur</p>
        </div>

        <div class="row g-4">
            <div class="col-md-6 col-lg-4 animate-on-scroll">
                <div class="feature-card">
                    <div class="feature-icon">
                        <i class="bi bi-shield-lock-fill"></i>
                    </div>
                    <h3>Confidentialité garantie</h3>
                    <p>Les évaluations sont strictement anonymes. Aucune information personnelle n'est associée aux réponses.</p>
                </div>
            </div>

            <div class="col-md-6 col-lg-4 animate-on-scroll">
                <div class="feature-card">
                    <div class="feature-icon">
                        <i class="bi bi-graph-up"></i>
                    </div>
                    <h3>Tableaux de bord dynamiques</h3>
                    <p>Visualisez les résultats agrégés avec des graphiques interactifs et des statistiques détaillées.</p>
                </div>
            </div>

            <div class="col-md-6 col-lg-4 animate-on-scroll">
                <div class="feature-card">
                    <div class="feature-icon">
                        <i class="bi bi-building"></i>
                    </div>
                    <h3>Multi-établissements</h3>
                    <p>Une solution centralisée permettant de gérer plusieurs universités ou campus indépendamment.</p>
                </div>
            </div>

            <div class="col-md-6 col-lg-4 animate-on-scroll">
                <div class="feature-card">
                    <div class="feature-icon">
                        <i class="bi bi-file-earmark-pdf-fill"></i>
                    </div>
                    <h3>Rapports personnalisables</h3>
                    <p>Générez des rapports PDF complets pour l'analyse et l'archivage des résultats.</p>
                </div>
            </div>

            <div class="col-md-6 col-lg-4 animate-on-scroll">
                <div class="feature-card">
                    <div class="feature-icon">
                        <i class="bi bi-calendar-check"></i>
                    </div>
                    <h3>Périodes d'évaluation</h3>
                    <p>Paramétrez facilement les campagnes d'évaluation avec des dates d'ouverture et de clôture.</p>
                </div>
            </div>

            <div class="col-md-6 col-lg-4 animate-on-scroll">
                <div class="feature-card">
                    <div class="feature-icon">
                        <i class="bi bi-database-check"></i>
                    </div>
                    <h3>Données sécurisées</h3>
                    <p>Architecture robuste respectant les normes de protection des données personnelles.</p>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- How it works -->
<section class="section bg-light" id="fonctionnement">
    <div class="container">
        <div class="section-title animate-on-scroll">
            <div class="section-tag">Comment ça fonctionne</div>
            <h2>Un processus simple et transparent</h2>
            <p>En quelques étapes, l'évaluation est accessible à tous les acteurs</p>
        </div>

        <div class="row">
            <div class="col-md-3 animate-on-scroll">
                <div class="step-card">
                    <div class="step-number">1</div>
                    <h4>Connexion sécurisée</h4>
                    <p>Accédez à la plateforme avec vos identifiants institutionnels.</p>
                </div>
            </div>

            <div class="col-md-3 animate-on-scroll">
                <div class="step-card">
                    <div class="step-number">2</div>
                    <h4>Accès aux formulaires</h4>
                    <p>Les étudiants accèdent aux questionnaires d'évaluation autorisés.</p>
                </div>
            </div>

            <div class="col-md-3 animate-on-scroll">
                <div class="step-card">
                    <div class="step-number">3</div>
                    <h4>Évaluation anonyme</h4>
                    <p>Remplissez le formulaire en toute confidentialité.</p>
                </div>
            </div>

            <div class="col-md-3 animate-on-scroll">
                <div class="step-card">
                    <div class="step-number">4</div>
                    <h4>Analyse des résultats</h4>
                    <p>Les résultats agrégés sont disponibles pour les responsables.</p>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Stats Section -->
<section class="stats-section">
    <div class="container">
        <div class="row">
            <div class="col-md-3 animate-on-scroll">
                <div class="stat-item">
                    <div class="stat-number">100%</div>
                    <div class="stat-label">Anonymat garanti</div>
                </div>
            </div>

            <div class="col-md-3 animate-on-scroll">
                <div class="stat-item">
                    <div class="stat-number">24/7</div>
                    <div class="stat-label">Disponibilité</div>
                </div>
            </div>

            <div class="col-md-3 animate-on-scroll">
                <div class="stat-item">
                    <div class="stat-number">Sécurisé</div>
                    <div class="stat-label">Données protégées</div>
                </div>
            </div>

            <div class="col-md-3 animate-on-scroll">
                <div class="stat-item">
                    <div class="stat-number">RGPD</div>
                    <div class="stat-label">Conformité</div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- About Section -->
<section class="section" id="a-propos">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-lg-6 animate-on-scroll">
                <div class="section-tag">À propos</div>
                <h2 style="font-size: 1.75rem; margin-bottom: 1rem;">Une plateforme au service de la qualité pédagogique</h2>
                <p style="color: #475569; margin-bottom: 1rem;">
                    Cette plateforme a été conçue pour répondre aux besoins des établissements
                    d'enseignement supérieur en matière d'évaluation des enseignements.
                </p>
                <p style="color: #475569;">
                    Elle permet de recueillir de manière systématique les avis des étudiants,
                    d'identifier les axes d'amélioration et de contribuer à l'amélioration continue
                    de la qualité pédagogique.
                </p>
            </div>
            <div class="col-lg-6 animate-on-scroll">
                <div class="bg-light p-4 rounded-4" style="background: #f1f5f9;">
                    <h4 style="color: #0f172a; margin-bottom: 1rem;">Charte de confidentialité</h4>
                    <ul style="color: #475569; list-style: none; padding-left: 0;">
                        <li class="mb-3"><i class="bi bi-check-circle-fill text-primary me-2"></i> Anonymat total des réponses</li>
                        <li class="mb-3"><i class="bi bi-check-circle-fill text-primary me-2"></i> Données non traçables individuellement</li>
                        <li class="mb-3"><i class="bi bi-check-circle-fill text-primary me-2"></i> Accès restreint aux résultats agrégés</li>
                        <li class="mb-3"><i class="bi bi-check-circle-fill text-primary me-2"></i> Conformité aux normes de protection des données</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- CTA Section -->
<section class="cta-section">
    <div class="container">
        <h2 class="animate-on-scroll">Prêt à contribuer à l'amélioration de la qualité pédagogique ?</h2>
        <p class="animate-on-scroll">Connectez-vous à la plateforme pour accéder à votre espace</p>
        <a href="{{ route('login') }}" class="btn-cta animate-on-scroll">
            <i class="bi bi-box-arrow-in-right"></i> Accéder à la plateforme
        </a>
    </div>
</section>

<!-- Footer -->
<footer>
    <div class="container">
        <div class="row">
            <div class="col-md-4 mb-4">
                <h5>ÉvalENS</h5>
                <p style="font-size: 0.875rem;">Plateforme institutionnelle d'évaluation des enseignements universitaires.</p>
            </div>
            <div class="col-md-2 mb-4">
                <h5>Liens</h5>
                <ul>
                    <li><a href="#accueil">Accueil</a></li>
                    <li><a href="#fonctionnalites">Fonctionnalités</a></li>
                    <li><a href="#fonctionnement">Fonctionnement</a></li>
                </ul>
            </div>
            <div class="col-md-3 mb-4">
                <h5>Légal</h5>
                <ul>
                    <li><a href="#">Confidentialité</a></li>
                    <li><a href="#">Mentions légales</a></li>
                    <li><a href="#">Charte d'utilisation</a></li>
                </ul>
            </div>
            <div class="col-md-3 mb-4">
                <h5>Contact</h5>
                <ul>
                    <li><a href="mailto:contact@example.com">contact@example.com</a></li>
                </ul>
            </div>
        </div>
        <div class="footer-bottom">
            <p>&copy; {{ date('Y') }} Plateforme d'Évaluation des Enseignements - Tous droits réservés</p>
        </div>
    </div>
</footer>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
    // Navbar scroll effect
    window.addEventListener('scroll', function() {
        const navbar = document.querySelector('.navbar');
        if (window.scrollY > 50) {
            navbar.classList.add('scrolled');
        } else {
            navbar.classList.remove('scrolled');
        }
    });

    // Scroll animations
    const observerOptions = {
        threshold: 0.1,
        rootMargin: '0px 0px -50px 0px'
    };

    const observer = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                entry.target.classList.add('visible');
                observer.unobserve(entry.target);
            }
        });
    }, observerOptions);

    document.querySelectorAll('.animate-on-scroll').forEach(el => {
        observer.observe(el);
    });

    // Smooth scroll for anchor links
    document.querySelectorAll('a[href^="#"]').forEach(anchor => {
        anchor.addEventListener('click', function(e) {
            const target = document.querySelector(this.getAttribute('href'));
            if (target) {
                e.preventDefault();
                target.scrollIntoView({
                    behavior: 'smooth',
                    block: 'start'
                });
            }
        });
    });
</script>
</body>
</html>
