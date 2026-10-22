# SargazoQroo

**Español** · [English](README.en.md)

Aplicación web para el **pronóstico, monitoreo y simulación del arribo de sargazo** en las playas de Quintana Roo, México.

> **Aviso:** proyecto informativo y educativo. Los pronósticos se basan en datos históricos y en un modelo heurístico propio; no sustituyen la información oficial de SEMA-Q. Roo, la Semar u otras autoridades.

## Características

- **Dashboard** con pronóstico anual, variación respecto al año previo, playas monitoreadas, playas en alerta y temporada de sargazo.
- **Pronóstico mensual** por año (2020–2026) con toneladas estimadas, índice de afectación (0–100), nivel de riesgo y días estimados de arribo.
- **Mapa interactivo** de 18 playas agrupadas en 7 zonas/municipios, con nivel de afectación por playa.
- **Simulador de escenarios** con cinco variables: biomasa en el Atlántico, temperatura del mar, vientos alisios, corrientes/nutrientes y esfuerzo de limpieza.
- **Gráficas** comparativas de la serie histórica y de la distribución estimada por municipio.
- **API JSON** para consultar el pronóstico de un año y ejecutar simulaciones.

## Stack

| Capa | Tecnología |
| --- | --- |
| Backend | PHP 8.3, Laravel 13 |
| Frontend | Blade, Tailwind CSS 4, Bootstrap 5, Bootstrap Icons |
| Gráficas | ApexCharts |
| Bundler | Vite 8 |
| Base de datos | SQLite (por defecto), MySQL o PostgreSQL |
| Pruebas | PHPUnit 12 |
| Estilo | Laravel Pint |

## Requisitos

- PHP **8.3+** con las extensiones habituales de Laravel (`mbstring`, `openssl`, `pdo`, `tokenizer`, `xml`, `ctype`, `json`, `fileinfo`).
- [Composer](https://getcomposer.org/) 2.
- Node.js **^20.19** o **>=22.12** (requerido por Vite 8).
- SQLite, MySQL o PostgreSQL.

## Instalación

```bash
# 1. Clonar el repositorio
git clone https://github.com/chanchito-code/SargazoQroo.git
cd SargazoQroo

# 2. Dependencias de PHP
composer install

# 3. Configuración del entorno
cp .env.example .env        # Windows PowerShell: Copy-Item .env.example .env
php artisan key:generate

# 4. Base de datos SQLite (crear el archivo antes de migrar)
# Windows PowerShell:
New-Item -ItemType File -Path database/database.sqlite -Force
# Linux/macOS:
# touch database/database.sqlite
php artisan migrate

# 5. Dependencias y build del frontend
npm install
npm run build

# 6. Servidor de desarrollo
php artisan serve
```

También puedes ejecutar el asistente de instalación:

```bash
composer run setup
```

La aplicación quedará disponible en `http://127.0.0.1:8000`.

> Si usas MySQL o PostgreSQL, ajusta `DB_CONNECTION`, `DB_HOST`, `DB_PORT`, `DB_DATABASE`, `DB_USERNAME` y `DB_PASSWORD` en `.env` y ejecuta `php artisan migrate`.

## Uso

La pantalla principal se divide en cuatro secciones:

1. **Dashboard** — resumen de la temporada y del año seleccionado.
2. **Pronóstico** — selector de mes y año con el detalle por playa y las recomendaciones según el nivel de afectación.
3. **Mapa** — estado de cada playa y reparto estimado por municipio.
4. **Simulador** — ajuste de variables ambientales y de limpieza para comparar el escenario base contra el simulado.

## API

Las rutas son `web` (usan sesión y CSRF), por lo que las peticiones `POST` deben incluir la cookie de sesión y el token CSRF (`X-CSRF-TOKEN` o `_token`).

### `POST /api/prediccion`

Devuelve el pronóstico de los 12 meses de un año.

```json
{
  "anio": 2026
}
```

### `POST /api/simular`

Ejecuta una simulación para un mes/año. Las claves de `valores` son opcionales y toman el valor por defecto si se omiten.

```json
{
  "mes": 6,
  "anio": 2026,
  "valores": {
    "biomasa": 40,
    "temperatura": 28,
    "vientos": 20,
    "corrientes": 60,
    "limpieza": 50
  }
}
```

Rangos de las variables del simulador:

| Variable | Unidad | Rango |
| --- | --- | --- |
| `biomasa` | millones de toneladas | 0–80 |
| `temperatura` | °C | 24–32 |
| `vientos` | km/h | 5–45 |
| `corrientes` | índice 0–100 | 0–100 |
| `limpieza` | índice 0–100 | 0–100 |

## Datos

Los datos históricos viven en `app/Support/SargazoData.php`:

- Totales anuales de sargazo recolectado en Quintana Roo (2020–2026), con fuente y marca de dato oficial o estimado.
- Curvas estacionales de arribo mensual (normal y adelantada).
- Catálogo de playas (coordenadas para el mapa y sensibilidad) y zonas/municipios con su peso y sensibilidad.
- Niveles de afectación: sin presencia, bajo, moderado, alto y excesivo.

Fuentes citadas en el código: SEMA-Q. Roo, Semar y reportes oficiales. Los años 2022 y 2023 son estimaciones de cierre de temporada; 2026 es una proyección.

## Estructura del proyecto

```text
app/
├── Http/Controllers/          # HomeController y controladores de la API
├── Services/SargazoPredictor.php  # Modelo de pronóstico y simulación
└── Support/SargazoData.php    # Dataset histórico, playas, zonas y niveles
resources/
├── views/                     # Vistas Blade (dashboard, mapa, pronóstico, simulador)
└── js/                        # Lógica del frontend (mapa, gráficas, simulador)
routes/web.php                 # Rutas de la app y de la API
tests/                         # Pruebas de feature y unitarias
```

## Pruebas y estilo

```bash
php artisan test
vendor/bin/pint
```

## Licencia

MIT.
