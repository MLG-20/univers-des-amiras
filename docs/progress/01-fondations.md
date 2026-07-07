# Phase 1 — Fondations : journal d'avancement

**Statut : terminée et validée** (voir checklist complète dans [docs/files/01-fondations.md](../files/01-fondations.md) section 7 — tous les points sont cochés).

## Ce qui a été construit

### Projet & versionning
- Projet Laravel 12 (PHP 8.4) initialisé avec `composer create-project`, Git dès le premier commit.
- Historique en 6 commits thématiques (init projet, auth client, rôles/guard admin, Filament, Sanctum/API, catalogue) — pas de commit unique "initial".
- Dépôt GitHub privé : https://github.com/MLG-20/univers-des-amiras
- CI GitHub Actions (`.github/workflows/ci.yml`) : Pint (style), migrations MySQL, suite de tests, à chaque push/PR sur `main`.

### Authentification — deux espaces totalement cloisonnés
- **Client** : Laravel Breeze (stack Blade + Alpine.js), modèle `App\Models\User`, guard `web`, table `users`. Vérification d'email obligatoire (`MustVerifyEmail`), reset de mot de passe, rate limiting (5 tentatives) et message d'échec générique déjà fournis par Breeze.
- **Admin** : modèle `App\Models\Admin` **dédié**, table `admins` séparée, guard `admin` séparé (`config/auth.php`). Panel géré par **Filament** (`/admin`), qui apporte nativement rate limiting et message d'échec générique sur le login. `Admin::canAccessPanel()` vérifie `is_active` + email vérifié avant d'accorder l'accès.
- **Rôles** : `spatie/laravel-permission`, rôles `admin`/`employee` sur le guard `admin` (extensible pour de futurs rôles employé sans réécrire l'auth).
- Pourquoi deux tables/guards distincts et pas un simple flag `role` sur `users` : exigence explicite du cahier des charges section 2.3 ("jamais le même espace que les comptes clients") — un compte client ne peut physiquement pas s'authentifier côté admin, ce n'est pas qu'un filtrage d'affichage.

### Catalogue (back-office)
- Migrations : `categories` (hiérarchique via `parent_id` auto-référencé), `products` (soft delete + `is_active`), `product_variants` (attributs flexibles en JSON, stock), `product_images`.
- Modèles dans `App\Models\Catalogue\*` (namespace dédié, pas dans `App\Models` en vrac).
- Back-office : ressources Filament (`app/Filament/Resources/Catalogue/`) — CRUD catégories/produits, variantes et images en repeaters imbriqués, upload d'image validé côté serveur (`->image()` = vérifie le type MIME réel, pas l'extension).
- `is_active` = désactivation réversible (retire du catalogue sans supprimer) ; `SoftDeletes` sur `products` = suppression réelle mais réversible en base.

### API (v1, Sanctum)
- Ajoutée en cours de Phase 1 suite à une demande explicite (pas dans le cahier des charges initial) : préparer une future app mobile.
- `POST /api/v1/login` (token Sanctum, mêmes protections que le login web : rate limiting, message générique), `GET/POST /api/v1/*` protégés par `auth:sanctum`.
- Catalogue en lecture seule : `GET /api/v1/categories`, `/api/v1/products`, `/api/v1/products/{id}` — ne renvoie que les éléments actifs, 404 sur inactif/supprimé (même logique anti-énumération que côté web).

## Décisions prises avec Ahmed (et pourquoi)
- **Filament plutôt que Blade fait main pour l'admin** : gain de temps majeur sur tout CRUD futur (Phase 6 stats/back-office avancé notamment), au prix d'une dépendance Livewire supplémentaire. Le site public reste Blade/Breeze (pas mélangé avec Filament).
- **MySQL en local** (pas SQLite) : identifiants dev fournis par Ahmed, cohérent avec la cible de prod. Les tests tournent aussi sur MySQL (base `univers_des_amiras_testing`) car `pdo_sqlite` n'est pas installé sur la machine de dev et l'installer nécessitait un accès sudo interactif non disponible.
- **Aucun secret commité** : `.env` exclu, `phpunit.xml` ne contient que `DB_CONNECTION`/`DB_DATABASE` — les identifiants viennent de `.env` local (non versionné) ou des `env:` du job CI.

## Comment vérifier
```bash
php artisan migrate:fresh --seed   # doit tourner sans erreur sur base vierge
php artisan test                   # 44 tests à la fin de cette phase
```
Compte admin de dev créé par le seeder : voir `ADMIN_SEED_EMAIL` / `ADMIN_SEED_PASSWORD` dans `.env`.
