# ÉvalENS — Plateforme d'évaluation des enseignements

Plateforme web de gestion et d'évaluation des enseignements pour établissements universitaires multi-sites. Développée avec **Laravel 13**, **Laravel Fortify** et **Bootstrap 5**.

---

## Sommaire

- [Présentation](#présentation)
- [Stack technique](#stack-technique)
- [Installation](#installation)
- [Architecture des rôles](#architecture-des-rôles)
- [Structure de la base de données](#structure-de-la-base-de-données)
- [Fonctionnalités par rôle](#fonctionnalités-par-rôle)
- [Routes](#routes)
- [Structure des fichiers](#structure-des-fichiers)

---

## Présentation

ÉvalENS permet à une université de :

- Gérer ses **sites / annexes** (campus physiques) et leurs gestionnaires
- Permettre aux **étudiants** d'évaluer les enseignants en fin de période
- Donner aux **enseignants** une vue de leurs résultats et évolutions
- Offrir aux **gestionnaires d'annexe** un tableau de bord et des **emplois du temps hebdomadaires**
- Fournir à l'**administrateur université** une vue consolidée par annexe
- Laisser le **SuperAdmin** gérer le référentiel d'universités et valider les inscriptions

---

## Stack technique

| Composant | Version / Détail |
|---|---|
| PHP | 8.2+ |
| Laravel | 13.x |
| Laravel Fortify | Authentification (register, login, 2FA) |
| Base de données | MariaDB 10.4 / MySQL 8 |
| Frontend | Bootstrap 5, LineIcons, Flaticon |
| JavaScript | Vanilla JS (aucune dépendance jQuery obligatoire) |

---

## Installation

```bash
# 1. Cloner le dépôt
git clone <url-du-repo> evalens
cd evalens

# 2. Installer les dépendances PHP
composer install

# 3. Copier et configurer l'environnement
cp .env.example .env
php artisan key:generate

# 4. Configurer la base de données dans .env
# DB_DATABASE=evalens
# DB_USERNAME=root
# DB_PASSWORD=

# 5. Exécuter les migrations
php artisan migrate

# 6. (Optionnel) Vider les caches
php artisan view:clear
php artisan route:clear
php artisan config:clear

# 7. Lancer le serveur
php artisan serve
```

---

## Architecture des rôles

```
SuperAdmin
 └── gère le référentiel d'universités (university_references)
 └── valide / rejette les inscriptions des directeurs

Directeur (Admin Université)
 └── gère les annexes de son université
 └── crée les gestionnaires d'annexe
 └── consulte tous les étudiants et enseignants groupés par annexe

Gestionnaire d'annexe
 └── voit uniquement les membres de son annexe
 └── établit les emplois du temps hebdomadaires
 └── publie / dépublie les emplois du temps

Enseignant
 └── consulte ses résultats d'évaluation
 └── voit son emploi du temps (via son annexe)

Étudiant
 └── soumet ses évaluations
 └── consulte l'emploi du temps publié de son annexe
```

---

## Structure de la base de données

### Tables principales

#### `university_references`
Référentiel des universités géré par le SuperAdmin.

| Colonne | Type | Description |
|---|---|---|
| id | bigint | Clé primaire |
| nom | string (unique) | Nom officiel de l'université |
| acronyme | string(20) nullable | Ex : UGB, UCAD |

#### `universities`
Instances d'université créées par les directeurs.

| Colonne | Type | Description |
|---|---|---|
| id | bigint | Clé primaire |
| nom | string | Nom (issu du référentiel) |
| acronyme | string nullable | Acronyme (issu du référentiel) |
| email | string nullable | Email institutionnel |
| telephone | string nullable | Téléphone |
| site_web | string nullable | URL du site web |
| statut | enum | `pending`, `active`, `rejected` |
| directeur_id | FK users | Directeur propriétaire |
| motif_rejet | text nullable | Motif si rejeté par SuperAdmin |
| validee_at | timestamp nullable | Date de validation |
| validee_par | FK users nullable | SuperAdmin ayant validé |

#### `annexes`
Sites physiques d'une université.

| Colonne | Type | Description |
|---|---|---|
| id | bigint | Clé primaire |
| university_id | FK universities | Université parente |
| nom | string | Nom du site |
| adresse | string nullable | Adresse physique |
| ville | string nullable | Ville |
| pays | string nullable | Pays |
| email | string nullable | Email du site |
| telephone | string nullable | Téléphone du site |

#### `users`
Tous les utilisateurs de la plateforme.

| Colonne | Type | Description |
|---|---|---|
| id | bigint | Clé primaire |
| prenom | string | Prénom |
| nom | string | Nom de famille |
| name | string | Prénom + Nom (calculé) |
| email | string (unique) | Adresse e-mail |
| role | enum | `superadmin`, `directeur`, `gestionnaire`, `enseignant`, `etudiant` |
| university_id | FK universities nullable | Université de rattachement |
| annexe_id | FK annexes nullable | Annexe de rattachement |
| password | string | Mot de passe hashé |

#### `emplois_du_temps`
Un emploi du temps par semaine par annexe.

| Colonne | Type | Description |
|---|---|---|
| id | bigint | Clé primaire |
| annexe_id | FK annexes | Annexe concernée |
| semaine | date | Lundi de la semaine |
| statut | enum | `brouillon`, `publie` |
| UNIQUE | (annexe_id, semaine) | Une seule entrée par semaine par annexe |

#### `creneaux`
Créneaux horaires d'un emploi du temps.

| Colonne | Type | Description |
|---|---|---|
| id | bigint | Clé primaire |
| emploi_du_temps_id | FK emplois_du_temps | Emploi du temps parent |
| jour | tinyint | 1=Lundi, 2=Mardi, …, 6=Samedi |
| heure_debut | time | Heure de début |
| heure_fin | time | Heure de fin |
| matiere | string | Intitulé de la matière |
| enseignant_id | FK users nullable | Enseignant assigné |
| salle | string nullable | Salle / amphi |
| type_cours | enum | `cours`, `td`, `tp`, `examen` |

---

## Fonctionnalités par rôle

### SuperAdmin (`/superadmin`)

- **Tableau de bord** — vue d'ensemble de la plateforme
- **Référentiel universités** — CRUD complet (nom + acronyme), recherche en temps réel
- **Inscriptions** — liste des directeurs en attente, approuver / rejeter avec motif
- **Historique** — inscriptions traitées
- **Utilisateurs** — liste globale
- **Statistiques** — indicateurs clés
- **Rapports** — exports
- **Logs** — journal d'activité

### Directeur / Admin Université (`/adminuniversity`)

- **Tableau de bord** — stats de l'université
- **Étudiants** — liste groupée par annexe, filtre par annexe + recherche, ajout / modification / suppression, import CSV
- **Enseignants** — même structure que les étudiants
- **Annexes** — CRUD des sites (nom, localisation, contact), création et suppression de gestionnaires par annexe
- **Périodes d'évaluation** — CRUD des périodes (nom, date début/fin)
- **Formations & UE** — CRUD
- **Questionnaires** — configuration
- **Rapports** — exports PDF

### Gestionnaire d'annexe (`/gestionnaire`)

Accès **strictement limité à son annexe** (middleware `EnsureIsGestionnaire`).

- **Tableau de bord** — compteurs étudiants / enseignants, membres récents avec liens vers les listes
- **Étudiants** — liste paginée avec recherche (prénom, nom, email)
- **Enseignants** — liste paginée avec recherche
- **Emplois du temps** :
  - Liste des semaines créées (statut, nombre de créneaux, actions)
  - Édition d'une semaine : grille Lundi → Samedi, ajout de créneaux par jour
  - Chaque créneau : horaire, type (Cours/TD/TP/Examen), matière, enseignant, salle
  - Publication / dépublication (rend visible ou invisible aux étudiants)

### Enseignant (`/teacher`)

- **Tableau de bord** — résumé des évaluations reçues
- **Mes résultats** — scores par critère
- **Évolution** — courbe de progression
- **Commentaires** — retours qualitatifs anonymisés
- **Mon rapport PDF** — export

### Étudiant (`/student`)

- **Tableau de bord** — évaluations en attente
- **Mes évaluations** — liste des formulaires disponibles
- **Formulaire d'évaluation** — soumission avec scores par critère, sauvegarde brouillon
- **Historique** — évaluations déjà soumises
- **Emploi du temps** — vue lecture seule de l'EDT publié de son annexe, navigation semaine précédente / actuelle / suivante

---

## Routes

Organisées par `Route::controller()`, une section par contrôleur.

### DirectorOnboardingController — `/director`

| Méthode | URI | Action |
|---|---|---|
| GET | `/director/register-university` | Formulaire inscription université |
| POST | `/director/register-university` | Soumettre l'inscription |
| GET | `/director/pending` | Page d'attente de validation |

### SuperAdminController — `/superadmin`

| Méthode | URI | Action |
|---|---|---|
| GET | `/superadmin` | Dashboard |
| GET/POST | `/superadmin/inscriptions` | Gestion inscriptions |
| POST | `/superadmin/inscriptions/{id}/approuver` | Approuver |
| POST | `/superadmin/inscriptions/{id}/rejeter` | Rejeter |
| GET/POST/PUT/DELETE | `/superadmin/universites` | CRUD référentiel |
| GET | `/superadmin/utilisateurs` | Liste utilisateurs |
| GET/POST | `/superadmin/criteres` | Critères d'évaluation |
| GET | `/superadmin/statistiques` | Stats |
| GET/POST | `/superadmin/rapports` | Rapports |
| GET | `/superadmin/logs` | Logs |

### AdminUniversityController — `/adminuniversity`

| Méthode | URI | Action |
|---|---|---|
| GET | `/adminuniversity` | Dashboard |
| GET/POST/PUT/DELETE | `/adminuniversity/etudiants` | CRUD étudiants |
| POST | `/adminuniversity/etudiants/import` | Import CSV |
| GET/POST/PUT/DELETE | `/adminuniversity/enseignants` | CRUD enseignants |
| GET/POST/PUT/DELETE | `/adminuniversity/annexes` | CRUD annexes |
| POST | `/adminuniversity/annexes/{id}/gestionnaire` | Créer gestionnaire |
| DELETE | `/adminuniversity/annexes/{id}/gestionnaire` | Retirer gestionnaire |
| GET/POST/PUT/DELETE | `/adminuniversity/periodes` | CRUD périodes |
| GET/POST/PUT/DELETE | `/adminuniversity/formations` | CRUD formations |
| GET/POST | `/adminuniversity/questionnaires` | Questionnaires |
| GET/POST | `/adminuniversity/rapports` | Rapports |

### GestionnaireController — `/gestionnaire`

| Méthode | URI | Action |
|---|---|---|
| GET | `/gestionnaire` | Dashboard |
| GET | `/gestionnaire/etudiants` | Liste étudiants de l'annexe |
| GET | `/gestionnaire/enseignants` | Liste enseignants de l'annexe |
| GET | `/gestionnaire/emplois-du-temps` | Liste des semaines |
| POST | `/gestionnaire/emplois-du-temps` | Créer une semaine |
| GET | `/gestionnaire/emplois-du-temps/{id}` | Éditer une semaine |
| POST | `/gestionnaire/emplois-du-temps/{id}/publier` | Publier / dépublier |
| DELETE | `/gestionnaire/emplois-du-temps/{id}` | Supprimer |
| POST | `/gestionnaire/emplois-du-temps/{id}/creneaux` | Ajouter un créneau |
| DELETE | `/gestionnaire/creneaux/{id}` | Supprimer un créneau |

### TeacherController — `/teacher`

| Méthode | URI | Action |
|---|---|---|
| GET | `/teacher` | Dashboard |
| GET | `/teacher/resultats` | Résultats |
| GET | `/teacher/evolution` | Évolution |
| GET | `/teacher/commentaires` | Commentaires |
| GET/POST | `/teacher/rapport` | Rapport PDF |

### StudentController — `/student`

| Méthode | URI | Action |
|---|---|---|
| GET | `/student` | Dashboard |
| GET | `/student/evaluations` | Liste évaluations |
| GET | `/student/evaluation/{token}` | Formulaire |
| POST | `/student/evaluation/{token}/soumettre` | Soumettre |
| POST | `/student/evaluation/{token}/brouillon` | Sauvegarder brouillon |
| GET | `/student/historique` | Historique |
| GET | `/student/emploi-du-temps` | Emploi du temps |

---

## Structure des fichiers

```
app/
├── Actions/Fortify/
│   └── CreateNewUser.php          # Inscription : valide et persiste university_id + annexe_id
├── Http/
│   ├── Controllers/
│   │   ├── SuperAdminController.php
│   │   ├── DirectorOnboardingController.php
│   │   ├── AdminUniversityController.php
│   │   ├── GestionnaireController.php
│   │   ├── TeacherController.php
│   │   └── StudentController.php
│   └── Middleware/
│       ├── EnsureIsGestionnaire.php   # role=gestionnaire + annexe_id requis
│       └── CheckDirectorOnboarding.php
├── Models/
│   ├── User.php                   # isGestionnaire(), dashboardRoute(), annexe()
│   ├── University.php
│   ├── UniversityReference.php    # Référentiel SuperAdmin
│   ├── Annexe.php                 # gestionnaire(), users(), university()
│   ├── EmploiDuTemps.php          # creneaux(), isPublie()
│   └── Creneau.php                # jourLabel(), typeColor()
└── Providers/
    └── FortifyServiceProvider.php # Redirections post-login/register par rôle

database/migrations/
├── ..._create_users_table.php
├── ..._create_universities_table.php
├── 2026_05_26_..._create_university_references_table.php
├── 2026_05_28_..._create_annexes_table.php
├── 2026_05_28_..._restructure_location_to_annexes.php
└── 2026_05_29_..._create_emplois_du_temps_tables.php

resources/views/
├── layouts/
│   ├── app.blade.php              # Sidebar dynamique par rôle
│   └── auth.blade.php
├── auth/
│   ├── login.blade.php
│   ├── register.blade.php         # Sélection université → annexe (JS)
│   └── choose-profile.blade.php
├── SuperAdmin/
│   ├── dashboard.blade.php
│   ├── inscriptions.blade.php
│   └── universites.blade.php
├── director/
│   └── register-university.blade.php
├── adminuniversity/
│   ├── dashboard.blade.php
│   ├── etudiants.blade.php        # Groupé par annexe
│   ├── enseignants.blade.php      # Groupé par annexe
│   └── annexes.blade.php
├── gestionnaire/
│   ├── dashboard.blade.php
│   ├── etudiants.blade.php
│   ├── enseignants.blade.php
│   ├── emplois-du-temps.blade.php # Liste des semaines
│   └── emploi-du-temps.blade.php  # Édition grille + modal créneau
├── teacher/
│   └── dashboard.blade.php (+ autres)
└── student/
    ├── dashboard.blade.php
    ├── emploi-du-temps.blade.php  # Lecture seule + navigation semaines
    └── (autres vues évaluation)

routes/
└── web.php                        # Route::controller() par contrôleur
```

---

## Sécurité

- Authentification via **Laravel Fortify** (register, login, rate limiting)
- Chaque contrôleur est scopé à l'entité de l'utilisateur connecté (pas de fuite inter-université / inter-annexe)
- `EnsureIsGestionnaire` bloque l'accès aux routes gestionnaire si le rôle ou `annexe_id` est absent
- `annexe_id` soumis à l'inscription est validé (`exists:annexes,id`) côté serveur — la valeur client ne peut pas être falsifiée
- L'acronyme de l'université est re-fetché depuis la base après validation (non modifiable côté client)

---

## Licence

Projet académique — usage interne.
