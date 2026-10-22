# SargazoQroo

[Español](README.md) · **English**

Web application for **forecasting, monitoring and simulating sargassum landings** on the beaches of Quintana Roo, Mexico.

> **Disclaimer:** educational and informational project. Forecasts are based on historical data and a custom heuristic model; they do not replace official information from SEMA-Q. Roo, the Mexican Navy (Semar) or other authorities.

## Features

- **Dashboard** with the annual forecast, year-over-year variation, monitored beaches, beaches on alert and the sargassum season.
- **Monthly forecast** per year (2020–2026) with estimated tons, impact index (0–100), risk level and estimated arrival days.
- **Interactive map** of 18 beaches grouped into 7 zones/municipalities, with the impact level per beach.
- **Scenario simulator** with five variables: Atlantic biomass, sea temperature, trade winds, currents/nutrients and cleanup effort.
- **Charts** comparing the historical series and the estimated distribution by municipality.
- **JSON API** to query a year's forecast and run simulations.

## Stack

| Layer | Technology |
| --- | --- |
| Backend | PHP 8.3, Laravel 13 |
| Frontend | Blade, Tailwind CSS 4, Bootstrap 5, Bootstrap Icons |
| Charts | ApexCharts |
| Bundler | Vite 8 |
| Database | SQLite (default), MySQL or PostgreSQL |
| Testing | PHPUnit 12 |
| Code style | Laravel Pint |

## Requirements

- PHP **8.3+** with the usual Laravel extensions (`mbstring`, `openssl`, `pdo`, `tokenizer`, `xml`, `ctype`, `json`, `fileinfo`).
- [Composer](https://getcomposer.org/) 2.
- Node.js **^20.19** or **>=22.12** (required by Vite 8).
- SQLite, MySQL or PostgreSQL.

## Installation

```bash
# 1. Clone the repository
git clone https://github.com/chanchito-code/SargazoQroo.git
cd SargazoQroo

# 2. PHP dependencies
composer install

# 3. Environment configuration
cp .env.example .env        # Windows PowerShell: Copy-Item .env.example .env
php artisan key:generate

# 4. SQLite database (create the file before migrating)
# Windows PowerShell:
New-Item -ItemType File -Path database/database.sqlite -Force
# Linux/macOS:
# touch database/database.sqlite
php artisan migrate

# 5. Frontend dependencies and build
npm install
npm run build

# 6. Development server
php artisan serve
```

You can also run the installation helper:

```bash
composer run setup
```

The application will be available at `http://127.0.0.1:8000`.

> If you use MySQL or PostgreSQL, set `DB_CONNECTION`, `DB_HOST`, `DB_PORT`, `DB_DATABASE`, `DB_USERNAME` and `DB_PASSWORD` in `.env`, then run `php artisan migrate`.

## Usage

The main screen is divided into four sections:

1. **Dashboard** — season and selected-year summary.
2. **Forecast** — month and year selectors with per-beach details and recommendations based on the impact level.
3. **Map** — status of each beach and the estimated distribution by municipality.
4. **Simulator** — adjust environmental and cleanup variables to compare the baseline scenario against the simulated one.

## API

The routes are `web` routes (they use sessions and CSRF), so `POST` requests must include the session cookie and the CSRF token (`X-CSRF-TOKEN` or `_token`).

### `POST /api/prediccion`

Returns the forecast for all 12 months of a year.

```json
{
  "anio": 2026
}
```

### `POST /api/simular`

Runs a simulation for a given month/year. Keys inside `valores` are optional and fall back to their default value when omitted.

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

Simulator variable ranges:

| Variable | Unit | Range |
| --- | --- | --- |
| `biomasa` | million tons | 0–80 |
| `temperatura` | °C | 24–32 |
| `vientos` | km/h | 5–45 |
| `corrientes` | index 0–100 | 0–100 |
| `limpieza` | index 0–100 | 0–100 |

## Data

Historical data lives in `app/Support/SargazoData.php`:

- Annual totals of sargassum collected in Quintana Roo (2020–2026), with source and an official/estimated flag.
- Seasonal landing curves (normal and early).
- Beach catalog (map coordinates and sensitivity) and zones/municipalities with their weight and sensitivity.
- Impact levels: no presence, low, moderate, high and excessive.

Sources cited in the code: SEMA-Q. Roo, Semar and official reports. 2022 and 2023 are end-of-season estimates; 2026 is a projection.

## Project structure

```text
app/
├── Http/Controllers/          # HomeController and API controllers
├── Services/SargazoPredictor.php  # Forecast and simulation model
└── Support/SargazoData.php    # Historical dataset, beaches, zones and levels
resources/
├── views/                     # Blade views (dashboard, map, forecast, simulator)
└── js/                        # Frontend logic (map, charts, simulator)
routes/web.php                 # App and API routes
tests/                         # Feature and unit tests
```

## Tests and code style

```bash
php artisan test
vendor/bin/pint
```

## License

MIT.
