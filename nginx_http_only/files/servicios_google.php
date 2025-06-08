<?php
// servicios_google_completo.php
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Todos los Servicios de Google</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            padding: 20px;
        }
        
        .container {
            max-width: 1400px;
            margin: 0 auto;
        }
        
        .header {
            text-align: center;
            color: white;
            margin-bottom: 40px;
        }
        
        .header h1 {
            font-size: 3rem;
            margin-bottom: 10px;
            text-shadow: 2px 2px 4px rgba(0,0,0,0.3);
        }
        
        .header p {
            font-size: 1.3rem;
            opacity: 0.9;
            margin-bottom: 30px;
        }
        
        .search-container {
            position: relative;
            max-width: 600px;
            margin: 0 auto 40px;
        }
        
        .search-box {
            width: 100%;
            padding: 15px 50px 15px 20px;
            font-size: 18px;
            border: none;
            border-radius: 50px;
            background: white;
            box-shadow: 0 10px 25px rgba(0,0,0,0.1);
            outline: none;
            transition: all 0.3s ease;
        }
        
        .search-box:focus {
            box-shadow: 0 15px 35px rgba(0,0,0,0.15);
            transform: translateY(-2px);
        }
        
        .search-icon {
            position: absolute;
            right: 20px;
            top: 50%;
            transform: translateY(-50%);
            color: #666;
            font-size: 20px;
        }
        
        .services-count {
            text-align: center;
            color: white;
            font-size: 1.1rem;
            margin-bottom: 30px;
            opacity: 0.9;
        }
        
        .services-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(220px, 1fr));
            gap: 25px;
            padding: 20px 0;
        }
        
        .service-card {
            background: white;
            border-radius: 15px;
            padding: 25px;
            box-shadow: 0 8px 20px rgba(0,0,0,0.1);
            transition: all 0.3s ease;
            text-decoration: none;
            color: inherit;
            display: block;
            position: relative;
            overflow: hidden;
        }
        
        .service-card:hover {
            transform: translateY(-8px);
            box-shadow: 0 15px 35px rgba(0,0,0,0.15);
            text-decoration: none;
            color: inherit;
        }
        
        .service-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 4px;
            background: linear-gradient(90deg, #4285f4, #ea4335, #fbbc05, #34a853);
            opacity: 0;
            transition: opacity 0.3s ease;
        }
        
        .service-card:hover::before {
            opacity: 1;
        }
        
        .service-icon {
            width: 48px;
            height: 48px;
            margin: 0 auto 15px;
            background-size: contain;
            background-repeat: no-repeat;
            background-position: center;
            border-radius: 8px;
        }
        
        .service-name {
            font-size: 1.2rem;
            font-weight: 600;
            color: #333;
            margin-bottom: 8px;
            text-align: center;
        }
        
        .service-description {
            font-size: 0.85rem;
            color: #666;
            text-align: center;
            line-height: 1.4;
        }
        
        .no-results {
            text-align: center;
            color: white;
            font-size: 1.3rem;
            margin-top: 50px;
            opacity: 0.8;
        }
        
        .hidden {
            display: none !important;
        }
        
        /* Iconos usando emojis y colores de Google */
        .default-icon {
            background: linear-gradient(135deg, #4285f4, #34a853);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 24px;
            color: white;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>Servicios de Google</h1>
            <p>Accede a todos los servicios y herramientas de Google</p>
            
            <div class="search-container">
                <input 
                    type="text" 
                    class="search-box" 
                    id="searchInput" 
                    placeholder="Buscar servicios de Google..."
                    autocomplete="off"
                >
                <span class="search-icon">🔍</span>
            </div>
        </div>
        
        <div class="services-count" id="servicesCount"></div>
        
        <div class="services-grid" id="servicesGrid">
            <?php
            // Array completo con todos los servicios de Google
            $servicios = [
                // Servicios principales
                ['name' => 'Google Search', 'url' => 'https://google.com', 'description' => 'Motor de búsqueda web', 'icon' => '🔍'],
                ['name' => 'Gmail', 'url' => 'https://gmail.com', 'description' => 'Correo electrónico', 'icon' => '📧'],
                ['name' => 'Google Drive', 'url' => 'https://drive.google.com', 'description' => 'Almacenamiento en la nube', 'icon' => '💾'],
                ['name' => 'Google Docs', 'url' => 'https://docs.google.com', 'description' => 'Procesador de texto', 'icon' => '📄'],
                ['name' => 'Google Sheets', 'url' => 'https://sheets.google.com', 'description' => 'Hojas de cálculo', 'icon' => '📊'],
                ['name' => 'Google Slides', 'url' => 'https://slides.google.com', 'description' => 'Presentaciones', 'icon' => '📽️'],
                ['name' => 'Google Forms', 'url' => 'https://forms.google.com', 'description' => 'Formularios y encuestas', 'icon' => '📋'],
                ['name' => 'Google Calendar', 'url' => 'https://calendar.google.com', 'description' => 'Calendario y eventos', 'icon' => '📅'],
                ['name' => 'Google Photos', 'url' => 'https://photos.google.com', 'description' => 'Almacenamiento de fotos', 'icon' => '📸'],
                ['name' => 'YouTube', 'url' => 'https://youtube.com', 'description' => 'Plataforma de videos', 'icon' => '📺'],
                
                // Mapas y ubicación
                ['name' => 'Google Maps', 'url' => 'https://maps.google.com', 'description' => 'Mapas y navegación', 'icon' => '🗺️'],
                ['name' => 'Google Earth', 'url' => 'https://earth.google.com', 'description' => 'Explorador del planeta', 'icon' => '🌍'],
                ['name' => 'Google Street View', 'url' => 'https://streetview.google.com', 'description' => 'Vistas panorámicas', 'icon' => '📍'],
                
                // Comunicación y redes
                ['name' => 'Google Meet', 'url' => 'https://meet.google.com', 'description' => 'Videoconferencias', 'icon' => '📹'],
                ['name' => 'Google Chat', 'url' => 'https://chat.google.com', 'description' => 'Mensajería empresarial', 'icon' => '💬'],
                ['name' => 'Google Contacts', 'url' => 'https://contacts.google.com', 'description' => 'Gestión de contactos', 'icon' => '👥'],
                ['name' => 'Google Groups', 'url' => 'https://groups.google.com', 'description' => 'Grupos de discusión', 'icon' => '👫'],
                
                // Herramientas y utilidades
                ['name' => 'Google Translate', 'url' => 'https://translate.google.com', 'description' => 'Traductor de idiomas', 'icon' => '🌐'],
                ['name' => 'Google Lens', 'url' => 'https://lens.google.com', 'description' => 'Búsqueda visual', 'icon' => '👁️'],
                ['name' => 'Google Keep', 'url' => 'https://keep.google.com', 'description' => 'Notas y recordatorios', 'icon' => '📝'],
                ['name' => 'Google Scholar', 'url' => 'https://scholar.google.com', 'description' => 'Búsqueda académica', 'icon' => '🎓'],
                ['name' => 'Google Alerts', 'url' => 'https://alerts.google.com', 'description' => 'Alertas de búsqueda', 'icon' => '🔔'],
                ['name' => 'Google Trends', 'url' => 'https://trends.google.com', 'description' => 'Tendencias de búsqueda', 'icon' => '📈'],
                
                // Desarrolladores y negocios
                ['name' => 'Google Cloud', 'url' => 'https://cloud.google.com', 'description' => 'Servicios en la nube', 'icon' => '☁️'],
                ['name' => 'Google Analytics', 'url' => 'https://analytics.google.com', 'description' => 'Análisis web', 'icon' => '📊'],
                ['name' => 'Google Ads', 'url' => 'https://ads.google.com', 'description' => 'Publicidad online', 'icon' => '📢'],
                ['name' => 'Google AdSense', 'url' => 'https://adsense.google.com', 'description' => 'Monetización web', 'icon' => '💰'],
                ['name' => 'Google My Business', 'url' => 'https://business.google.com', 'description' => 'Perfil de negocio', 'icon' => '🏢'],
                ['name' => 'Google Workspace', 'url' => 'https://workspace.google.com', 'description' => 'Suite empresarial', 'icon' => '💼'],
                
                // Entretenimiento y medios
                ['name' => 'Google Play Store', 'url' => 'https://play.google.com', 'description' => 'Tienda de aplicaciones', 'icon' => '🎮'],
                ['name' => 'Google Play Music', 'url' => 'https://music.google.com', 'description' => 'Música streaming', 'icon' => '🎵'],
                ['name' => 'Google Play Movies', 'url' => 'https://movies.google.com', 'description' => 'Películas y series', 'icon' => '🎬'],
                ['name' => 'Google Play Books', 'url' => 'https://books.google.com', 'description' => 'Libros digitales', 'icon' => '📚'],
                ['name' => 'YouTube Music', 'url' => 'https://music.youtube.com', 'description' => 'Música en YouTube', 'icon' => '🎼'],
                ['name' => 'YouTube TV', 'url' => 'https://tv.youtube.com', 'description' => 'Televisión streaming', 'icon' => '📺'],
                
                // Herramientas especializadas
                ['name' => 'Google Fonts', 'url' => 'https://fonts.google.com', 'description' => 'Fuentes web gratuitas', 'icon' => '🔤'],
                ['name' => 'Google Sites', 'url' => 'https://sites.google.com', 'description' => 'Creador de sitios web', 'icon' => '🌐'],
                ['name' => 'Google Drawings', 'url' => 'https://drawings.google.com', 'description' => 'Editor de dibujos', 'icon' => '✏️'],
                ['name' => 'Google Jamboard', 'url' => 'https://jamboard.google.com', 'description' => 'Pizarra digital', 'icon' => '🖼️'],
                
                // Servicios móviles y dispositivos
                ['name' => 'Google Assistant', 'url' => 'https://assistant.google.com', 'description' => 'Asistente virtual', 'icon' => '🤖'],
                ['name' => 'Google Home', 'url' => 'https://home.google.com', 'description' => 'Hogar inteligente', 'icon' => '🏠'],
                ['name' => 'Google Pay', 'url' => 'https://pay.google.com', 'description' => 'Pagos digitales', 'icon' => '💳'],
                ['name' => 'Google Wallet', 'url' => 'https://wallet.google.com', 'description' => 'Cartera digital', 'icon' => '👛'],
                
                // Educación y aprendizaje
                ['name' => 'Google for Education', 'url' => 'https://edu.google.com', 'description' => 'Herramientas educativas', 'icon' => '🎒'],
                ['name' => 'Google Classroom', 'url' => 'https://classroom.google.com', 'description' => 'Aula virtual', 'icon' => '🏫'],
                ['name' => 'Google Arts & Culture', 'url' => 'https://artsandculture.google.com', 'description' => 'Arte y cultura', 'icon' => '🎨'],
                
                // Herramientas adicionales
                ['name' => 'Google News', 'url' => 'https://news.google.com', 'description' => 'Agregador de noticias', 'icon' => '📰'],
                ['name' => 'Google Finance', 'url' => 'https://finance.google.com', 'description' => 'Información financiera', 'icon' => '💹'],
                ['name' => 'Google Shopping', 'url' => 'https://shopping.google.com', 'description' => 'Comparador de precios', 'icon' => '🛒'],
                ['name' => 'Google Travel', 'url' => 'https://travel.google.com', 'description' => 'Planificador de viajes', 'icon' => '✈️'],
                ['name' => 'Google Podcasts', 'url' => 'https://podcasts.google.com', 'description' => 'Reproductor de podcasts', 'icon' => '🎙️'],
                
                // Servicios técnicos
                ['name' => 'Google Domains', 'url' => 'https://domains.google.com', 'description' => 'Registro de dominios', 'icon' => '🌍'],
                ['name' => 'Google PageSpeed', 'url' => 'https://pagespeed.web.dev', 'description' => 'Análisis de velocidad web', 'icon' => '⚡'],
                ['name' => 'Google Tag Manager', 'url' => 'https://tagmanager.google.com', 'description' => 'Gestión de etiquetas', 'icon' => '🏷️'],
                ['name' => 'Google Search Console', 'url' => 'https://search.google.com/search-console', 'description' => 'Herramientas para webmasters', 'icon' => '🔧'],
                
                // Servicios adicionales
                ['name' => 'Google One', 'url' => 'https://one.google.com', 'description' => 'Almacenamiento premium', 'icon' => '☁️'],
                ['name' => 'Google Fit', 'url' => 'https://fit.google.com', 'description' => 'Seguimiento de actividad', 'icon' => '🏃'],
                ['name' => 'Google Messages', 'url' => 'https://messages.google.com', 'description' => 'Mensajería SMS', 'icon' => '💬'],
                ['name' => 'Google Duo', 'url' => 'https://duo.google.com', 'description' => 'Videollamadas', 'icon' => '📞']
            ];
            
            // Ordenar servicios alfabéticamente
            usort($servicios, function($a, $b) {
                return strcmp($a['name'], $b['name']);
            });
            
            // Generar las tarjetas de servicios
            foreach ($servicios as $index => $servicio) {
                $searchTerms = strtolower($servicio['name'] . ' ' . $servicio['description']);
                
                echo '<a href="' . $servicio['url'] . '" target="_blank" class="service-card" data-search="' . $searchTerms . '">';
                echo '<div class="service-icon default-icon">' . $servicio['icon'] . '</div>';
                echo '<h3 class="service-name">' . $servicio['name'] . '</h3>';
                echo '<p class="service-description">' . $servicio['description'] . '</p>';
                echo '</a>';
            }
            ?>
        </div>
        
        <div class="no-results hidden" id="noResults">
            <h2>No se encontraron servicios</h2>
            <p>Intenta con diferentes términos de búsqueda</p>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const searchInput = document.getElementById('searchInput');
            const servicesGrid = document.getElementById('servicesGrid');
            const servicesCount = document.getElementById('servicesCount');
            const noResults = document.getElementById('noResults');
            const serviceCards = document.querySelectorAll('.service-card');
            
            function updateServicesCount() {
                const visibleCards = document.querySelectorAll('.service-card:not(.hidden)');
                const total = serviceCards.length;
                const visible = visibleCards.length;
                
                if (searchInput.value.trim() === '') {
                    servicesCount.textContent = `Mostrando ${total} servicios de Google`;
                } else {
                    servicesCount.textContent = `Mostrando ${visible} de ${total} servicios`;
                }
            }
            
            function filterServices() {
                const searchTerm = searchInput.value.toLowerCase().trim();
                let visibleCount = 0;
                
                serviceCards.forEach(card => {
                    const searchData = card.getAttribute('data-search');
                    
                    if (searchData.includes(searchTerm)) {
                        card.classList.remove('hidden');
                        visibleCount++;
                    } else {
                        card.classList.add('hidden');
                    }
                });
                
                // Mostrar/ocultar mensaje de no resultados
                if (visibleCount === 0 && searchTerm !== '') {
                    noResults.classList.remove('hidden');
                    servicesGrid.style.display = 'none';
                } else {
                    noResults.classList.add('hidden');
                    servicesGrid.style.display = 'grid';
                }
                
                updateServicesCount();
            }
            
            // Filtrar servicios mientras se escribe
            searchInput.addEventListener('input', filterServices);
            
            // Inicializar contador
            updateServicesCount();
            
            // Enfocar en el campo de búsqueda al cargar
            searchInput.focus();
        });
    </script>
</body>
</html>