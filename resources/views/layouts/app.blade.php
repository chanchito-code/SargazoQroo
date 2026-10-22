<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Predicción de Sargazo · Quintana Roo')</title>
    <meta name="description" content="Sistema interactivo de predicción de arribo de sargazo a las playas de Quintana Roo por mes, con mapa de playas, gráficas históricas y simulador de variables.">
    <link rel="icon" href="data:image/svg+xml,<svg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 100 100'><text y='.9em' font-size='90'>🌊</text></svg>">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body>
    <div class="backdrop-app" id="backdropApp"></div>

    <aside class="sidebar" id="sidebarApp">
        <div class="brand">
            <div class="brand-mark"><i class="bi bi-water"></i></div>
            <div class="brand-text">
                <strong>Sargazo Q. Roo</strong>
                <span>Pronóstico</span>
            </div>
        </div>

        <div class="nav-label">Secciones</div>
        <nav class="flex-column gap-1" id="navTabs">
            <button class="nav-tab active" data-tab="dashboard" type="button">
                <i class="bi bi-speedometer2"></i><span>Dashboard</span>
            </button>
            <button class="nav-tab" data-tab="pronostico" type="button">
                <i class="bi bi-calendar2-week"></i><span>Pronóstico por mes</span>
            </button>
            <button class="nav-tab" data-tab="mapa" type="button">
                <i class="bi bi-geo-alt-fill"></i><span>Mapa de playas</span>
            </button>
            <button class="nav-tab" data-tab="graficas" type="button">
                <i class="bi bi-bar-chart-line-fill"></i><span>Gráficas históricas</span>
            </button>
            <button class="nav-tab" data-tab="simulador" type="button">
                <i class="bi bi-sliders"></i><span>Simulador</span>
            </button>
        </nav>

        <div class="sidebar-foot">
            <strong>Fuentes:</strong> SEMA Quintana Roo, Semar, CONABIO (SATsum), UNAM-ICML y Red de
            Monitoreo del Sargazo.
        </div>
    </aside>

    <div class="main">
        <header class="topbar">
            <button class="sidebar-toggle" id="sidebarToggle" type="button" aria-label="Abrir menú">
                <i class="bi bi-list"></i>
            </button>
            <div>
                <h1 id="topbarTitulo">Panel general</h1>
                <div class="subtitle">Pronóstico de arribo de sargazo a las playas de Quintana Roo</div>
            </div>
            <div class="topbar-right">
                <span class="chip"><i class="bi bi-calendar-event"></i> Temporada: {{ $temporada['inicio'] }}
                    – {{ $temporada['fin'] }}</span>
                <span class="chip" id="chipMesActual"><i class="bi bi-moon-stars"></i>
                    {{ $dashboard['mes_nombre'] }} {{ $dashboard['anio'] }}</span>
                <span class="badge-nivel" id="badgeMesActual"
                    style="background: {{ $dashboard['nivel_actual']['bg'] }}; color: {{ $dashboard['nivel_actual']['color'] }}">
                    <span class="dot"></span> {{ $dashboard['nivel_actual']['label'] }}
                </span>
            </div>
        </header>

        <main class="content">
            @yield('content')
        </main>
    </div>

    <script>
        window.APP = @json($appData, 15);
    </script>
</body>

</html>
