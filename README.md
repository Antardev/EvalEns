# ÉvalENS — Plateforme d'évaluation des enseignements

Plateforme web multi-universités de gestion et d'évaluation des enseignements. Développée avec **Laravel 13**, **Laravel Fortify** et **Bootstrap 5**.

---

## Sommaire

- [Présentation](#présentation)
- [Stack technique](#stack-technique)
- [Installation](#installation)
- [Architecture des rôles](#architecture-des-rôles)
- [Système de questionnaires](#système-de-questionnaires)
- [Structure de la base de données](#structure-de-la-base-de-données)
- [Fonctionnalités par rôle](#fonctionnalités-par-rôle)
- [Routes](#routes)
- [Structure des fichiers](#structure-des-fichiers)
- [Sécurité](#sécurité)

---

## Présentation

ÉvalENS permet à des universités multi-sites de gérer les évaluations anonymes des enseignants par leurs étudiants.

Fonctionnalités principales :
- **Inscription des universités** via un directeur — validée par le SuperAdmin
- **Gestion des annexes** (campus physiques) avec gestionnaire dédié par site
- **Inscription des enseignants** sur plusieurs annexes (many-to-many)
- **Questionnaires d'évaluation** générés par lien tokenisé, accessibles publiquement
- **14 critères pondérés** configurables par université ou hérités des critères globaux
- **Résultats en temps réel** pour les enseignants — scores, moyennes, commentaires
- **Tableaux de bord** avec données réelles pour chaque niveau hiérarchique

---

## Stack technique

| Composant | Version / Détail |
|---|---|
| PHP | 8.2+ |
| Laravel | 13.x |
| Laravel Fortify | Authentification (register, login) |
| Base de données | MariaDB 10.4 / MySQL 8 |
| Frontend | Bootstrap 5, LineIcons, Chart.js |
| JavaScript | Vanilla JS |

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

# 6. Insérer les critères par défaut
php artisan db:seed --class=CritereSeeder

# 7. (Optionnel) Vider les caches
php artisan optimize:clear

# 8. Lancer le serveur
php artisan serve
```

---

## Architecture des rôles

```
SuperAdmin
 └── Gère le référentiel des universités (university_references)
 └── Valide ou rejette les inscriptions des directeurs

Directeur (Admin Université)
 └── Gère les annexes de son université
 └── Crée les gestionnaires d'annexe
 └── Configure les critères d'évaluation de son université
 └── Consulte tous les enseignants et les statistiques globales

Gestionnaire d'annexe
 └── Gère les enseignants de son annexe
 └── Crée et partage des liens questionnaires (tokenisés)
 └── Consulte les réponses et résultats par questionnaire
 └── Configure les critères d'évaluation (héritables)

Enseignant
 └── Peut appartenir à plusieurs annexes (many-to-many)
 └── Consulte ses scores, moyennes par critère et commentaires
 └── Suit son évolution par questionnaire

Étudiant / Répondant
 └── Accède au questionnaire via un lien public (sans compte)
 └── Soumet ses évaluations de façon anonyme
```

---

## Système de questionnaires

### Flux complet

```
Gestionnaire
  └── Crée un lien questionnaire (classe, matière, enseignant, date d'expiration)
  └── Partage l'URL publique avec les étudiants

Étudiant
  └── Ouvre l'URL (pas de connexion requise)
  └── Note chaque critère de 20% à 100% (1 à 5 en base)
  └── Laisse un commentaire optionnel
  └── Soumet → réponse enregistrée anonymement

Enseignant
  └── Voit ses résultats agrégés en temps réel

Gestionnaire / Directeur
  └── Consulte les réponses, statistiques, commentaires
```

### Critères d'évaluation

14 critères pondérés (total 100%) définis par défaut :

| # | Critère | Poids |
|---|---|---|
| 1 | Degré de satisfaction | 8% |
| 2 | Organisation du cours | 7% |
| 3 | Gestion du temps | 7% |
| 4 | Traçabilité | 7% |
| 5 | Qualité de l'animation | 7% |
| 6 | Interaction avec les étudiants | 7% |
| 7 | Indication sur le déroulement de l'évaluation | 7% |
| 8 | Cohérence / Clarté du cours | 7% |
| 9 | Pragmatisme | 7% |
| 10 | Présentation de l'enseignant | 7% |
| 11 | Qualité des outils et des supports | 7% |
| 12 | Qualité pédagogique | 8% |
| 13 | Adéquation cours et les sujets de contrôle | 7% |
| 14 | Relation avec l'étudiant(e) | 7% |

Chaque université peut surcharger ces critères (ses propres critères remplacent les globaux).

### Échelle de notation

| Valeur BD | Affichage |
|---|---|
| 1 | 20% — Très insuffisant |
| 2 | 40% — Insuffisant |
| 3 | 60% — Passable |
| 4 | 80% — Bien |
| 5 | 100% — Excellent |

### Expiration des liens

Un lien peut avoir une `expire_at`. Passée cette date/heure :
- Le questionnaire devient inaccessible (page "Expiré" affichée)
- Le bouton "Fermer/Rouvrir" est remplacé par "Expiré le JJ/MM/AAAA à HH:MM"

---

## Structure de la base de données

### Tables principales

#### `university_references`
Référentiel des universités géré par le SuperAdmin.

| Colonne | Type | Description |
|---|---|---|
| nom | string (unique) | Nom officiel |
| acronyme | string(20) nullable | Ex : UGB, UCAD |

#### `universities`
Instances d'université créées par les directeurs.

| Colonne | Type | Description |
|---|---|---|
| nom | string | Nom |
| statut | enum | `en_attente`, `active`, `rejetee` |
| directeur_id | FK users | Directeur propriétaire |
| motif_rejet | text nullable | Motif si rejeté |
| validee_at | timestamp nullable | Date de validation |
| validee_par | FK users nullable | SuperAdmin validateur |

#### `annexes`
Sites physiques d'une université.

| Colonne | Type | Description |
|---|---|---|
| university_id | FK universities | Université parente |
| nom | string | Nom du site |
| adresse / ville / pays | string nullable | Localisation |
| email / telephone | string nullable | Contact |

#### `users`
Tous les utilisateurs de la plateforme.

| Colonne | Type | Description |
|---|---|---|
| prenom / nom | string | Identité |
| email | string unique | Connexion |
| role | enum | `superadmin`, `directeur`, `gestionnaire`, `enseignant` |
| university_id | FK nullable | Université |
| annexe_id | FK nullable | Annexe (gestionnaire / directeur) |

#### `enseignant_annexes` *(pivot many-to-many)*
Rattachement des enseignants à plusieurs annexes.

| Colonne | Type |
|---|---|
| user_id | FK users |
| annexe_id | FK annexes |

#### `criteres`
Critères d'évaluation globaux ou par université.

| Colonne | Type | Description |
|---|---|---|
| university_id | FK nullable | null = global (partagé) |
| nom | string | Intitulé du critère |
| description | string nullable | Détail |
| poids | integer | Pondération (0–100) |
| ordre | integer | Ordre d'affichage |
| actif | boolean | Critère activé |

#### `liens_questionnaires`
Liens tokenisés créés par les gestionnaires.

| Colonne | Type | Description |
|---|---|---|
| annexe_id | FK annexes | Annexe concernée |
| enseignant_id | FK users nullable | Enseignant évalué |
| token | string unique | Token URL public |
| titre | string | Titre du questionnaire |
| classe | string | Classe ciblée |
| matiere | string nullable | Matière |
| questions | JSON | Snapshot des critères à la création |
| statut | enum | `actif`, `ferme` |
| expire_at | datetime nullable | Date d'expiration |

#### `reponses_questionnaires`
Réponses anonymes des étudiants.

| Colonne | Type | Description |
|---|---|---|
| lien_id | FK liens_questionnaires | Lien source |
| scores | JSON | `[{label, score}]` — score 1–5 |
| commentaire | text nullable | Commentaire libre |
| soumis_at | timestamp | Date de soumission |

---

## Fonctionnalités par rôle

### SuperAdmin (`/superadmin`)

- **Tableau de bord** — universités actives, utilisateurs, évaluations, demandes en attente
- **Référentiel universités** — CRUD (nom + acronyme)
- **Inscriptions** — valider / rejeter les demandes des directeurs
- **Historique** — inscriptions traitées
- **Utilisateurs** — liste globale avec filtres

### Directeur / Admin Université (`/adminuniversity`)

- **Tableau de bord** — KPIs réels, satisfaction par annexe (graphe), enseignants récents
- **Enseignants** — liste paginée avec toutes leurs annexes (badges)
- **Annexes** — CRUD, création/suppression de gestionnaire par annexe
- **Critères** — configuration des critères d'évaluation (surcharge des globaux)
- **Rapports** — exports PDF

### Gestionnaire d'annexe (`/gestionnaire`)

- **Tableau de bord** — compteurs enseignants / liens / réponses
- **Enseignants** — liste avec recherche, résultats et commentaires par enseignant
- **Liens questionnaires** — créer, fermer, supprimer ; copier l'URL ; voir les réponses
  - Badges statut : Actif / Fermé / **Expiré** (avec date)
  - Alerte si critères désynchronisés (bouton Rafraîchir)
- **Configuration critères** — table éditable, toggle actif, poids, graphe doughnut

### Enseignant (`/teacher`)

- **Tableau de bord** — satisfaction globale (%), total réponses, graphe radar par critère
- **Mes résultats** — par questionnaire, scores per-critère avec barres de progression
- **Commentaires** — filtrables par questionnaire, avec scores associés en accordéon

### Questionnaire public (`/q/{token}`)

- Accessible sans authentification
- Page d'information anonymat + enseignant/matière
- 14 critères avec boutons 20%→100%
- Commentaire libre (max 1000 caractères)
- Page **Expiré** si `expire_at` dépassé
- Page **Fermé** si fermé manuellement
- Page **Merci** après soumission

---

## Routes

### SuperAdminController — `/superadmin`

| Méthode | URI | Action |
|---|---|---|
| GET | `/superadmin` | Dashboard |
| GET | `/superadmin/inscriptions` | Demandes en attente |
| GET | `/superadmin/inscriptions/historique` | Historique |
| POST | `/superadmin/inscriptions/{id}/approuver` | Approuver |
| POST | `/superadmin/inscriptions/{id}/rejeter` | Rejeter |
| GET/POST | `/superadmin/universites` | CRUD référentiel |
| GET | `/superadmin/utilisateurs` | Liste utilisateurs |

### AdminUniversityController — `/adminuniversity`

| Méthode | URI | Action |
|---|---|---|
| GET | `/adminuniversity` | Dashboard |
| GET | `/adminuniversity/enseignants` | Liste enseignants |
| GET/POST/PUT/DELETE | `/adminuniversity/annexes` | CRUD annexes |
| POST | `/adminuniversity/annexes/{id}/gestionnaire` | Créer gestionnaire |
| DELETE | `/adminuniversity/annexes/{id}/gestionnaire` | Retirer gestionnaire |
| GET/POST | `/adminuniversity/questionnaires` | Configurer critères |
| GET/POST | `/adminuniversity/rapports` | Rapports |

### GestionnaireController — `/gestionnaire`

| Méthode | URI | Action |
|---|---|---|
| GET | `/gestionnaire` | Dashboard |
| GET | `/gestionnaire/enseignants` | Liste enseignants |
| GET | `/gestionnaire/liens` | Liste des liens |
| POST | `/gestionnaire/liens` | Créer un lien |
| POST | `/gestionnaire/liens/{id}/fermer` | Fermer / rouvrir |
| POST | `/gestionnaire/liens/{id}/rafraichir` | Rafraîchir snapshot critères |
| DELETE | `/gestionnaire/liens/{id}` | Supprimer |
| GET | `/gestionnaire/liens/{id}/reponses` | Voir les réponses |
| GET/POST | `/gestionnaire/questionnaires` | Configurer critères |

### TeacherController — `/teacher`

| Méthode | URI | Action |
|---|---|---|
| GET | `/teacher` | Dashboard |
| GET | `/teacher/resultats` | Résultats par questionnaire |
| GET | `/teacher/commentaires` | Commentaires filtrables |

### QuestionnairePublicController — `/q`

| Méthode | URI | Action |
|---|---|---|
| GET | `/q/{token}` | Afficher le questionnaire |
| POST | `/q/{token}` | Soumettre les réponses |

---

## Structure des fichiers

```
app/
├── Actions/Fortify/
│   └── CreateNewUser.php           # Validation + attach annexes (many-to-many)
├── Http/Controllers/
│   ├── SuperAdminController.php
│   ├── DirectorOnboardingController.php
│   ├── AdminUniversityController.php
│   ├── GestionnaireController.php
│   ├── TeacherController.php
│   └── QuestionnairePublicController.php
├── Http/Middleware/
│   ├── EnsureIsGestionnaire.php
│   └── CheckDirectorOnboarding.php
└── Models/
    ├── User.php                    # annexes() BelongsToMany, annexe() BelongsTo
    ├── University.php
    ├── UniversityReference.php
    ├── Annexe.php                  # enseignants() BelongsToMany
    ├── Critere.php                 # pourUniversite(?int $universityId)
    ├── LienQuestionnaire.php       # isActif(), urlPublique()
    └── ReponseQuestionnaire.php

database/
├── migrations/
│   ├── ..._create_users_table.php
│   ├── ..._create_universities_table.php
│   ├── ..._create_university_references_table.php
│   ├── ..._create_annexes_table.php
│   ├── ..._create_criteres_table.php
│   ├── ..._create_liens_questionnaires_table.php
│   ├── ..._create_reponses_questionnaires_table.php
│   └── ..._create_enseignant_annexes_table.php
└── seeders/
    └── CritereSeeder.php           # 14 critères par défaut (100%)

resources/views/
├── layouts/
│   ├── app.blade.php               # Sidebar dynamique par rôle
│   ├── questionnaire.blade.php     # Layout public (sans sidebar)
│   └── superadmin.blade.php
├── auth/
│   ├── login.blade.php
│   └── register.blade.php          # Sélection université (cards) + annexes (chips)
├── SuperAdmin/
│   ├── dashboard.blade.php
│   ├── inscriptions.blade.php
│   └── universites.blade.php
├── adminuniversity/
│   ├── dashboard.blade.php         # KPIs réels + graphe satisfaction
│   ├── enseignants.blade.php       # Table paginée + badges annexes
│   ├── annexes.blade.php
│   └── questionnaires.blade.php    # Critères éditables + doughnut
├── gestionnaire/
│   ├── dashboard.blade.php
│   ├── enseignants.blade.php
│   ├── liens.blade.php             # Statuts Actif/Fermé/Expiré
│   ├── reponses.blade.php
│   └── questionnaires.blade.php
├── teacher/
│   ├── dashboard.blade.php         # Radar chart + progress bars
│   ├── resultats.blade.php
│   └── commentaires.blade.php
└── questionnaire/
    ├── show.blade.php              # Formulaire public (14 critères)
    ├── ferme.blade.php             # Fermé ou Expiré
    └── merci.blade.php             # Confirmation soumission
```

---

## Sécurité

- Authentification via **Laravel Fortify** (register, login, rate limiting)
- Chaque contrôleur est scopé à l'entité de l'utilisateur connecté (pas de fuite inter-université / inter-annexe)
- `EnsureIsGestionnaire` bloque l'accès aux routes gestionnaire si le rôle ou `annexe_id` est absent
- Les annexes soumises à l'inscription sont validées (`exists:annexes,id`) côté serveur
- Les questionnaires publics sont accessibles via token opaque (UUID) uniquement
- Les réponses sont strictement anonymes — aucun identifiant étudiant n'est stocké

---

## Licence

Projet académique — usage interne.
