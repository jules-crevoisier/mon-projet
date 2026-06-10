# API Véhicules de Sport — Symfony + API Platform

Backend REST pour gérer des **marques** et des **voitures de sport**, avec PostgreSQL et Docker.

## Prérequis

- [Docker Desktop](https://www.docker.com/products/docker-desktop/) démarré

## Démarrage rapide

```powershell
cd backend
.\setup.ps1
```

Ou manuellement :

```powershell
docker compose up -d --build
docker compose exec php composer install --no-interaction
docker compose exec php php bin/console doctrine:migrations:migrate --no-interaction
docker compose exec php php bin/console doctrine:fixtures:load --no-interaction
```

## URLs

| Ressource | URL |
|-----------|-----|
| Documentation Swagger | http://localhost:8081/api/docs |
| Liste des marques | http://localhost:8081/api/marques |
| Liste des voitures | http://localhost:8081/api/voitures |
| Entrée API (JSON-LD) | http://localhost:8081/api |

## Modèle de données

### Table `marque`

| Champ | Type | Description |
|-------|------|-------------|
| `nom` | string | Nom de la marque |
| `annee_creation` | int | Année de création |
| `pays_origine` | string | Pays d'origine |

### Table `voiture`

| Champ | Type | Description |
|-------|------|-------------|
| `modele` | string | Modèle du véhicule |
| `prix` | decimal | Prix en euros |
| `puissance` | int | Puissance en ch |
| `annee_sortie` | int | Année de sortie |
| `photo` | string (optionnel) | URL de la photo |
| `marque` | relation | Lien vers une marque |

## Exemples de requêtes

```bash
# Lister toutes les marques
curl http://localhost:8081/api/marques

# Lister toutes les voitures
curl http://localhost:8081/api/voitures

# Créer une marque
curl -X POST http://localhost:8081/api/marques \
  -H "Content-Type: application/json" \
  -d '{"nom":"Alpine","anneeCreation":1955,"paysOrigine":"France"}'
```

## Arrêter les conteneurs

```powershell
docker compose down
```
