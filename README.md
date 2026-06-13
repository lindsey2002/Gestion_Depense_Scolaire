#  ISI-Scolarité : Plateforme de Gestion Académique & Comptable

Une application web moderne, fluide et sécurisée de gestion administrative et financière pour les établissements scolaires. Conçue avec une architecture découplée (**API REST Laravel** & **Front-end Vue.js**), cette plateforme optimise le suivi de la scolarité à travers une synchronisation en temps réel entre l'administration, la comptabilité et les parents d'élèves.

---

##  Objectifs du Projet

L'objectif de cette application est de digitaliser et de centraliser les flux métiers d'une école en remplaçant les processus manuels ou les fichiers Excel isolés par un système interconnecté et automatisé. 

L'application répond aux besoins de 3 acteurs clés :
* **L'Administration / Directeur :** Supervision des effectifs, gestion des dossiers élèves et vision globale de la santé financière.
* **Le Comptable :** Encaissement sécurisé des frais, gestion des dépenses et génération automatique des reçus uniques.
* **Les Parents :** Un espace transparent pour suivre en temps réel l'état des mensualités de leurs enfants, le reste à payer et les notes/annonces de l'établissement.

---

##  Les Points Forts de l'Application

* **Gestion Flexible par "Vagues" :** Contrairement aux systèmes rigides, la grille de suivi des mensualités s'adapte automatiquement à la vague d'intégration de l'étudiant (ex: Vague Octobre, Vague Janvier) et génère dynamiquement le nombre de mois dus.
* **Sécurité Métier Stricte (Rôles Hermétiques) :** L'administration supervise, mais **seul le comptable** possède les droits d'écriture sur la caisse (`POST /paiements`). Les doublons de paiement pour un même mois sont bloqués mathématiquement en base de données.
* **Synchronisation Vue <-> API en Temps Réel :** Dès qu'un paiement est validé à la caisse, le tableau de bord du parent recalcule instantanément le reste à payer global et met à jour les badges de suivi (passage du rouge au vert).
* **Architecture Évolutive :** Une base de données optimisée et centralisée, idéale pour de futures extensions en **Data Science** (ex: analyse prédictive des risques d'impayés).

---

##  Stack Technique

* **Backend :** Laravel (PHP) - API RESTful sécurisée par jetons (Sanctum/Bearer Tokens).
* **Frontend :** Vue.js (JavaScript) - Single Page Application (SPA) rapide et interface responsive (Tailwind CSS).
* **Base de Données :** MySQL.
* **Export :** Intégration de Maatwebsite Excel pour l'extraction des journaux comptables.

---

##  Installation et Configuration

### 1. Prérequis
* PHP (version >= 8.1)
* Composer
* Node.js & NPM
* MySQL

### 2. Configuration du Backend (Laravel)
1. Naviguez dans le dossier backend et installez les dépendances :
   ```bash
   composer install

   cp .env.example .env
   php artisan serve
   npm install
   npm run dev

   // Zone exclusive au Comptable (Sécurité financière)
Route::middleware('role:comptable')->group(function () {
    Route::post('/paiements', [PaiementController::class, 'store']);
    Route::apiResource('depenses', DepenseController::class);
});