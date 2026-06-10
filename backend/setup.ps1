# Script de démarrage — API Véhicules de Sport (Symfony + API Platform)
$ErrorActionPreference = "Stop"

Write-Host "Demarrage des conteneurs Docker..." -ForegroundColor Cyan
docker compose up -d --build
if ($LASTEXITCODE -ne 0) { exit $LASTEXITCODE }

if (-not (Test-Path "vendor\autoload.php")) {
    Write-Host "Installation des dependances Composer (local)..." -ForegroundColor Cyan
    composer install --no-interaction
    if ($LASTEXITCODE -ne 0) { exit $LASTEXITCODE }
}

Write-Host "Installation des assets API Platform..." -ForegroundColor Cyan
docker compose exec php php bin/console assets:install public --no-interaction
if ($LASTEXITCODE -ne 0) { exit $LASTEXITCODE }

Write-Host "Execution des migrations..." -ForegroundColor Cyan
docker compose exec php php bin/console doctrine:migrations:migrate --no-interaction
if ($LASTEXITCODE -ne 0) { exit $LASTEXITCODE }

Write-Host "Chargement des donnees de demonstration..." -ForegroundColor Cyan
docker compose exec php php bin/console doctrine:fixtures:load --no-interaction
if ($LASTEXITCODE -ne 0) { exit $LASTEXITCODE }

Write-Host ""
Write-Host "Installation terminee !" -ForegroundColor Green
Write-Host "  API JSON      : http://localhost:8081/api"
Write-Host "  Marques       : http://localhost:8081/api/marques"
Write-Host "  Voitures      : http://localhost:8081/api/voitures"
Write-Host "  Documentation : http://localhost:8081/api/docs"
