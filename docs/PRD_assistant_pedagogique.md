# **📘 PRD — Product Requirements Document**

## *Assistant Pédagogique IA — Projet MMI3*

Ce document présente **tout ce qu’il faut savoir** pour comprendre, concevoir et développer le projet.
Il suit les **bonnes pratiques réelles** du métier (structure claire, objectifs, contexte, fonctionnalités, contraintes, KPIs, règles métier…).

---

# 1. 🎯 **Résumé exécutif (Executive Summary)**

Le projet consiste à créer un **assistant pédagogique intelligent** permettant à un formateur de :

1. **Importer un syllabus** (PDF ou texte brut)
2. **Générer automatiquement un plan de cours**
3. **Obtenir des séances détaillées**
4. **Suivre la progression réelle** des cours avec les étudiants
5. **Adapter la suite du programme** grâce à l’IA
6. **Générer automatiquement des exercices**
7. **Discuter avec une IA pédagogique contextuelle**

Ce produit vise à **aider les formateurs** dans la préparation, l’organisation et l’ajustement des cours tout en montrant aux étudiants comment construire une application moderne mêlant API, gestion de données et IA.

---

# 2. 🎯 **Objectifs du produit**

## 2.1. Objectif principal

Faciliter la **préparation**, la **gestion** et l’**adaptation** des cours de manière intelligente grâce à l’IA.

## 2.2. Objectifs secondaires

* Automatise la création de contenus pédagogiques.
* Réduit le temps de préparation des cours.
* Harmonise la structure des séances.
* Propose un suivi clair de la progression.
* Rend les séances plus adaptées au niveau du groupe.

---

# 3. 👥 **Personas & utilisateurs**

### 🎓 **Persona 1 : Le Formateur**

* Prépare les cours
* Gère un planning
* Souhaite améliorer la cohérence pédagogique
* Manque de temps pour structurer toutes les séances
* Doit s’adapter au niveau réel des étudiants

### 🧠 **Persona 2 : L’Étudiant (facultatif pour le MVP)**

* Participe aux séances
* Bénéficie d’exercices adaptés
* Peut avoir un espace de progression personnelle (optionnel)

### 🤖 **Persona 3 : Le Système IA**

* Analyse un syllabus
* Structure un parcours
* Génère exercices et suggestions
* S’adapte au contexte

---

# 4. 🧩 **Portée du projet (Scope)**

## 4.1. In Scope (inclus dans ce projet)

* API Symfony + API Platform
* Upload d’un fichier ou saisie de texte
* Analyse automatique du contenu via IA
* Génération du plan de cours
* Création des séances et objectifs
* Marquage d’une séance comme “faite”
* Ajustement du plan via IA
* Génération d’exercices
* Chat IA contextuel
* Authentification (JWT)

## 4.2. Out of Scope (non inclus dans ce projet)

* Interface front-end complète (React/Vue)
* Application mobile
* Export PDF final du plan de cours
* Système de gestion d’élèves très avancé
* Correction automatique d’exercices complexes (bonus seulement)

---

# 5. 🧱 **Description fonctionnelle**

## 5.1. Import du syllabus

Le formateur peut :

* **ajouter un syllabus** sous forme de

  * PDF
  * ou texte brut

Une fois importé, il est stocké dans la base de données.

## 5.2. Analyse IA du syllabus

L’IA doit extraire automatiquement :

* les **compétences**
* les **thématiques**
* les **prérequis**
* une **estimation de durée**
* le **niveau** ciblé (débutant / intermédiaire / avancé)

## 5.3. Génération du plan de cours

L’IA doit produire :

* un **plan général**
* une liste de **séances** (nombre défini par le formateur)
* pour chaque séance :

  * objectifs
  * contenus
  * activités
  * ressources
  * durée prévue

## 5.4. Suivi de progression

Après chaque séance, le formateur peut marquer :

* séance faite → oui/non
* notes réelles (ce qui a été vu)

## 5.5. Ajustement IA

À partir de la progression réelle, l’IA doit pouvoir proposer :

* un rééquilibrage des séances restantes
* une mise à jour du contenu des prochaines séances

## 5.6. Génération d’exercices

En fonction du contenu d’une séance, l’IA peut :

* créer des exercices
* proposer une solution exemples

## 5.7. Chat IA contextuel

Le formateur pose des questions du type :

> *“Peux-tu me proposer une activité d’introduction à Symfony ?”*

L’IA répond en tenant compte :

* du syllabus
* du plan de cours
* des séances déjà faites

---

# 6. 🗄️ **Modèle de données (résumé)**

### Principales entités :

* **User**
* **Syllabus**
* **CoursePlan**
* **Session**
* **Exercise**

(Détail complet dans `uml.md`)

---

# 7. 🎛️ **Exigences techniques**

## 7.1. Backend

* Symfony 7
* API Platform
* Doctrine ORM
* Security : JWT
* Appel IA via HTTP client Symfony

## 7.2. Base de données

* MySQL ou PostgreSQL
* Migrations Doctrine obligatoires

## 7.3. IA

* OpenAI / Mistral / LM Studio
* Réponses en **JSON strict**

## 7.4. Upload

* PDF traité via VichUploader (optionnel)

---

# 8. 📡 **API — Endpoints prioritaires**

| Endpoint                        | Action      | Description           |
| ------------------------------- | ----------- | --------------------- |
| `POST /syllabuses`              | Création    | Import texte/PDF      |
| `POST /ai/generate-course-plan` | IA          | Génère plan + séances |
| `GET /course_plans/{id}`        | Lecture     | Récupère plan complet |
| `PATCH /sessions/{id}/complete` | Mise à jour | Marquer une séance    |
| `POST /ai/generate-exercises`   | IA          | Génère des exercices  |
| `POST /ai/chat`                 | IA          | Chat contextuel       |

---

# 9. 🔐 **Sécurité**

* Authentification obligatoire
* Rôle recommandé : **ROLE_TEACHER**
* Accès restreint aux ressources du formateur

---

# 10. 🎨 **Exigences UX (minimalistes)**

Même sans front :

* Swagger doit présenter toutes les opérations clairement
* Des messages d’erreurs explicites
* Des exemples JSON dans chaque endpoint

---

# 11. ⚙️ **Règles métier**

* Impossible de générer un plan sans syllabus.
* Le nombre de séances doit être ≥ 1.
* Une séance marquée “faite” doit inclure `actualNotes`.
* L’IA doit renvoyer du JSON valide, sinon l’API rejette la réponse.
* Le formateur ne peut accéder qu’à ses propres syllabus/plans.

---

# 12. 📊 **KPIs (Comment mesurer la réussite du projet)**

Même s’il s’agit d’un projet d’étude, un vrai PRD inclut des KPIs.

* Temps moyen de génération d’un plan : < 5 secondes
* 0 erreur JSON IA sur 10 générations
* 100% des endpoints décrits fonctionnels
* Structure du plan comprise par un humain (évaluation qualitative)

---

# 13. 🧭 **Roadmap (3 semaines)**

### **Semaine 1**

* Setup Symfony
* Auth
* Entités
* Import syllabus
* Analyse IA

### **Semaine 2**

* Génération plan de cours
* CRUD Sessions
* Suivi progression

### **Semaine 3**

* Génération d’exercices
* Chat IA
* Nettoyage & tests
* Documentation finale

---

# 14. 📥 **Livrables**

### Requis :

* API opérationnelle
* Documentation Swagger
* README complet
* PRD présent et suivi
* Tests minimaux

### Bonus :

* Mini-front (Vue/React)
* Corrections IA
* Export PDF du plan

---

# 15. 🧠 **Risques identifiés**

* Réponses IA non valides en JSON
* Temps limité → besoin de prioriser
* Tentation d’ajouter trop de features

Solution : **se concentrer sur le MVP**.

---

# 16. 🧰 **Définitions**

* **Syllabus** : document décrivant le contenu d’un cours
* **IA générative** : IA capable de produire du texte
* **Endpoint** : URL qui permet d’interagir avec une API
* **JWT** : système d’authentification sécurisé
* **MVP** : première version fonctionnelle minimale

---

# 17. ✔️ **Conclusion**

Ce PRD présente toutes les exigences nécessaires au développement du projet.
Votre mission est de créer **une API moderne, robuste et intelligente**, en priorité :

* Import du syllabus
* Analyse IA
* Génération du plan de cours
* Création des séances
* Suivi de progression

Le reste est bonus.

Bonne chance : c’est votre **premier vrai projet d’ingénierie**, soyez fiers de vous. 🚀
