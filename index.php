<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>AppCamiones - Gestión de Flotas y Rutas</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        :root {
            --primary-color: #3498db;
            --secondary-color: #2c3e50;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
            color: #333;
        }

        .hero-section {
            min-height: 100vh;
            display: flex;
            align-items: center;
            padding: 50px 0;
        }

        .presentation-column, .features-column {
            padding: 30px;
            border-radius: 15px;
            background: #ffffff;
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.1);
            height: 100%;
            transition: transform 0.3s ease;
        }

        .presentation-column:hover, .features-column:hover {
            transform: translateY(-10px);
        }

        .presentation-column h1 {
            font-size: 2.5rem;
            font-weight: 700;
            color: var(--primary-color);
            margin-bottom: 20px;
        }

        .presentation-column p {
            font-size: 1.1rem;
            line-height: 1.6;
            color: #555;
        }

        .features-column h2 {
            font-size: 2rem;
            font-weight: 600;
            color: var(--secondary-color);
            margin-bottom: 20px;
        }

        .features-column ul {
            list-style: none;
            padding: 0;
        }

        .features-column ul li {
            font-size: 1.1rem;
            margin-bottom: 15px;
            display: flex;
            align-items: center;
            color: #444;
        }

        .features-column ul li i {
            color: var(--primary-color);
            margin-right: 10px;
            font-size: 1.3rem;
        }

        .btn-try-now {
            background-color: var(--primary-color);
            color: white;
            font-weight: 600;
            padding: 12px 30px;
            border-radius: 25px;
            text-decoration: none;
            transition: background-color 0.3s ease;
            margin-top: 20px;
            display: inline-block;
        }

        .btn-try-now:hover {
            background-color: #2980b9;
            color: white;
        }

        .truck-icon {
            font-size: 4rem;
            color: var(--primary-color);
            margin-bottom: 20px;
        }

        @media (max-width: 768px) {
            .presentation-column, .features-column {
                margin-bottom: 30px;
            }

            .presentation-column h1 {
                font-size: 2rem;
            }

            .features-column h2 {
                font-size: 1.8rem;
            }
        }
    </style>
</head>
<body>
    <!-- Sección Principal -->
    <section class="hero-section">
        <div class="container">
            <div class="row">
                <!-- Columna de Presentación -->
                <div class="col-md-6">
                    <div class="presentation-column text-center text-md-start">
                        <i class="bi bi-truck truck-icon"></i>
                        <h1>AppCamiones</h1>
                        <p>
                            Bienvenido a <strong>AppCamiones</strong>, la solución definitiva para la gestión eficiente de flotas de camiones y rutas. Diseñada para administradores, supervisores, conductores e informáticos, nuestra aplicación te permite optimizar operaciones, gestionar incidencias y mantener el control total de tu flota en tiempo real.
                        </p>
                    </div>
                </div>

                <!-- Columna de Funcionalidades -->
                <div class="col-md-6">
                    <div class="features-column">
                        <h2>Funcionalidades Principales</h2>
                        <ul>
                            <li><i class="bi bi-truck-front"></i> Gestión de camiones y asignaciones.</li>
                            <li><i class="bi bi-signpost-2"></i> Planificación y seguimiento de rutas.</li>
                            <li><i class="bi bi-exclamation-triangle"></i> Reporte y gestión de incidencias.</li>
                            <li><i class="bi bi-people"></i> Administración de usuarios por roles.</li>
                            <li><i class="bi bi-speedometer2"></i> Panel de control personalizado.</li>
                        </ul>
                        <a href="views/auth/login.php" class="btn-try-now">Probar Ahora</a>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>