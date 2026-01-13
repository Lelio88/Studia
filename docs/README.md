# Assistant Pédagogique IA — Projet MMI3

Ce projet a pour objectif de concevoir une API complète permettant à un formateur de :

- importer un syllabus (PDF ou texte)
- générer automatiquement un plan de cours via IA
- générer les séances avec objectifs, contenus, activités
- suivre la progression réelle
- adapter les séances suivantes grâce à l’IA
- générer des exercices via IA
- chatter avec une IA spécialisée sur le cours

Le projet est réalisé avec :

- Symfony 7  
- API Platform  
- JWT Authentication  
- Un LLM externe (OpenAI, Mistral, LM Studio…)

---

## 📂 Structure recommandée du projet

```

/backend
|- src
|- config
|- migrations
|- public
|- ...
/docs
|- architecture.md
|- uml.md
|- prompts.md
|- tests.md
|- guide_etudiants.md

````

---

## 🚀 Objectifs pédagogiques

- Comprendre les enjeux d’un vrai projet API structuré  
- Manipuler Symfony + API Platform  
- Concevoir un modèle de données cohérent  
- Intégrer un service IA externe  
- Structurer des prompts  
- Comprendre une architecture moderne orientée API  

---

## 🔧 Installation

### 1. Installer les dépendances
```bash
composer install
````

### 2. Configurer l’environnement

Créer un fichier `.env.local` :

```
DATABASE_URL="mysql://root:root@127.0.0.1:3306/cours?serverVersion=8"
LLM_API_KEY="votre_clef"
LLM_API_URL="https://api.votre-llm.fr"
```

### 3. Créer la base

```bash
php bin/console doctrine:database:create
php bin/console doctrine:migrations:migrate
```

### 4. Lancer le serveur

```bash
symfony serve -d
```

---

## 📘 Documentation associée

La documentation à placer dans `/docs` :

* **architecture.md** → Architecture globale + diagrammes
* **uml.md** → Modèle de données UML
* **prompts.md** → Prompts IA prêts à l’emploi
* **tests.md** → Cahier de tests complet
* **guide_etudiants.md** → Guide étape par étape pour les étudiants

---

## 🧱 Endpoints majeurs

### Syllabus

* `POST /syllabuses`
* `GET /syllabuses/{id}`

### Course Plans

* `POST /ai/generate-course-plan`
* `GET /course_plans/{id}`

### Sessions

* `PATCH /sessions/{id}/complete`

### Exercices

* `POST /ai/generate-exercises`

### Chat

* `POST /ai/chat`

---

## 🔐 Sécurité

* Authentification via JWT
* Rôle recommandé : **ROLE_TEACHER**

---

## 🧭 Roadmap recommandée

### **Semaine 1 : Fondation**

* Setup Symfony + API Platform
* Auth JWT
* Entités principales : Syllabus, CoursePlan, Session

### **Semaine 2 : Fonctionnel**

* Import syllabus
* Analyse IA
* Génération plan de cours
* CRUD Sessions
* Marquer une séance comme "faite"

### **Semaine 3 : IA & Bonus**

* Génération d’exercices
* Chat IA
* Dashboard progression
* Amélioration des prompts

---

## ✔️ Livrables attendus

### Obligatoires

* API REST fonctionnelle
* Documentation Swagger complète
* Endpoints IA opérationnels
* Modèle de données cohérent
* README clair
* Tests minimum sur les parcours critiques

### Optionnels

* Petit front (React, Vue, Symfony UX)
* Chat IA complet
* Export PDF
* Correction automatique d’exercices
* Suivi individuel des étudiants

---

## ☕ Une question, un blocage, un choix technique ?

Tu peux demander de l’aide, mais vous êtes encouragés
à prendre des décisions d’équipe, comme dans un vrai projet professionnel.

Bon courage et amusez-vous — c’est votre premier “vrai” gros projet d’ingénierie 🎓🚀

```
