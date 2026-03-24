<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>PCA Hybridization Portal - Bohol</title>
    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    
    <style>
        /* Modern Layout for PCA Landing Page */
        body {
            margin: 0;
            font-family: 'Inter', sans-serif;
            overflow-x: hidden;
        }

        .landing-page {
            display: flex;
            flex-direction: column;
            min-height: 100vh;
        }

        .landing-header {
            background-color: #0b9e4f;
            height: 80px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 0 40px;
            position: relative;
            overflow: hidden;
        }

        .header-left {
            display: flex;
            align-items: center;
            gap: 15px;
            z-index: 2;
        }

        .menu-icon {
            font-size: 2rem;
            color: white;
            cursor: pointer;
        }

        .header-logo {
            height: 45px;
        }

        .header-title {
            color: white;
            font-size: 1.25rem;
            font-weight: 700;
            letter-spacing: 0.5px;
        }

        .header-right {
            display: flex;
            align-items: center;
            gap: 30px;
            z-index: 2;
        }

        .header-right a {
            color: white;
            text-decoration: none;
            font-weight: 700;
            font-size: 1rem;
            text-transform: uppercase;
            transition: color 0.2s;
        }

        .header-right a:hover {
            color: #d4e122;
        }

        /* Yellow Slanted Shape Top */
        .header-shape {
            position: absolute;
            top: 0;
            right: 20%;
            width: 280px;
            height: 100%;
            background-color: #d4e122;
            transform: skewX(-35deg);
            z-index: 1;
        }

        .landing-main {
            flex: 1;
            background-image: url('{{ asset('images/CoconutBackground.png') }}');
            background-size: cover;
            background-position: center;
            background-repeat: no-repeat;
            position: relative;
            display: flex;
            justify-content: center;
            align-items: center;
        }

        /* Light overlay */
        .landing-main::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: rgba(255, 255, 255, 0.15);
        }

        .main-logo-container {
            z-index: 2;
            text-align: center;
        }

        .main-logo-container img {
            max-width: 500px;
            width: 90%;
            filter: drop-shadow(0 10px 20px rgba(0, 0, 0, 0.2));
        }

        .landing-footer {
            background-color: #0b9e4f;
            min-height: 120px;
            display: flex;
            justify-content: center;
            align-items: center;
            position: relative;
            overflow: hidden;
            padding: 20px;
        }

        .landing-footer h2 {
            color: white;
            font-size: 2rem;
            font-weight: 700;
            text-align: center;
            margin: 0;
            line-height: 1.2;
            z-index: 2;
        }

        /* Yellow Slanted Shape Bottom */
        .footer-shape {
            position: absolute;
            bottom: 0;
            right: -80px;
            width: 320px;
            height: 100%;
            background-color: #d4e122;
            transform: skewX(-35deg);
            z-index: 1;
        }

        @media (max-width: 992px) {
            .header-shape {
                right: 10%;
                width: 200px;
            }
            .landing-footer h2 {
                font-size: 1.5rem;
            }
        }

        @media (max-width: 768px) {
            .header-shape { display: none; }
            .header-title { display: none; }
            .landing-header { padding: 0 20px; }
            .main-logo-container img { max-width: 300px; }
            .landing-footer h2 { font-size: 1.1rem; }
            .footer-shape { right: -120px; width: 220px; }
        }
    </style>
</head>
<body>
    <div class="landing-page">
        <header class="landing-header">
            <div class="header-left">
                <i class="bi bi-list menu-icon"></i>
                <img src="{{ asset('images/PCA_Logo.png') }}" class="header-logo" alt="PCA Logo">
                <span class="header-title">PCA - BOHOL</span>
            </div>

            <div class="header-shape"></div>

            <div class="header-right">
                <a href="#">ABOUT US</a>
                <a href="/admin/login">LOGIN</a>
            </div>
        </header>

        <main class="landing-main">
            <div class="main-logo-container">
                <img src="{{ asset('images/PCA_DA_Logo.png') }}" alt="PCA and DA Logo">
            </div>
        </main>

        <footer class="landing-footer">
            <h2>Philippine Coconut Authority - Bohol<br>Hybridization Portal</h2>
            <div class="footer-shape"></div>
        </footer>
    </div>
</body>
</html>
