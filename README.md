# Gestion des véhicules de sport

Application Vue 3 réalisée pour le TP WR406D du 10 juin 2026.

[![Deploy to Render](https://render.com/images/deploy-to-render-button.svg)](https://render.com/deploy?repo=https://github.com/jules-crevoisier/mon-projet)

## Fonctionnalités

- CRUD des voitures de sport
- CRUD des marques
- Relation voiture / marque via une liste de sélection
- Recherche, filtre par marque et tri des voitures
- Statistiques rapides
- Connexion au backend Symfony/API Platform via `VITE_API_URL`

## Lancer le site

```powershell
npm install
npm run dev
```

Le site est disponible sur `http://127.0.0.1:5173`.

En local, Vite proxyfie `/api` vers `http://localhost:8081`.

## Vérifier la compilation

```powershell
npm run build
```

## Déploiement Render

Le fichier [render.yaml](C:/Users/srkod/Desktop/406d/render.yaml) prépare trois ressources:

- `wr406d-sportcars-front`: site Vue statique
- `wr406d-sportcars-api`: backend Symfony/API Platform en Docker
- `wr406d-sportcars-db`: base PostgreSQL

Étapes:

1. Pousser le projet sur GitHub.
2. Dans Render, choisir **New +** puis **Blueprint**.
3. Sélectionner le dépôt GitHub.
4. Laisser Render lire `render.yaml` et lancer le déploiement.

URLs prévues:

- Front: `https://wr406d-sportcars-front.onrender.com`
- API: `https://wr406d-sportcars-api.onrender.com/api`
