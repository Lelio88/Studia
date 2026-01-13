
## 5. Guide étape par étape pour les étudiants

### Étape 0 – Organisation

1. Former des équipes (3–4 étudiants).
2. Désigner :

   * 1 référent technique
   * 1 référent “produit / pédagogie”
   * 1 référent “qualité / tests”

---

### Étape 1 – Mise en place du projet

1. Créer un projet Symfony (`symfony new` ou `composer create-project`).
2. Installer API Platform.
3. Installer un système d’authentification (JWT recommandé).
4. Configurer la base de données + migrations.

Livrable : projet qui tourne, `/api` accessible.

---

### Étape 2 – Modèle de données

1. Créer les entités :

   * `User`
   * `Syllabus`
   * `CoursePlan`
   * `Session`
   * `Exercise` (facultatif dans un premier temps)
2. Générer les migrations.
3. Vérifier les relations (OneToMany, ManyToOne) dans API Platform (normalization).

Livrable : les ressources CRUD de base exposées dans /api.

---

### Étape 3 – Import du syllabus

1. Implémenter un endpoint POST `/syllabuses` qui accepte :

   * soit un texte brut
   * soit, en bonus, un fichier PDF
2. Sauvegarder `rawText` en base.
3. (Option) brancher une lib d’extraction PDF.

Livrable : on peut créer un syllabus via Swagger ou Postman.

---

### Étape 4 – Intégration de l’IA (analyse + plan)

1. Créer un service Symfony `LlmClient` qui :

   * prend un prompt
   * appelle l’API LLM
   * renvoie le JSON
2. Implémenter `/ai/generate-course-plan` :

   * récupère le syllabus
   * appelle l’IA avec le prompt adéquat
   * parse le JSON
   * crée un `CoursePlan` + les `Session` en base

Livrable : en partant d’un syllabus, tu obtiens un plan de cours complet en DB.

---

### Étape 5 – Suivi de séances

1. Ajouter un endpoint pour marquer une session comme “done” + notes réelles.
2. (Bonus) Ajouter `/ai/update-course-plan` pour proposer des ajustements.

Livrable : tu peux suivre le déroulé des séances et voir lesquelles ont été faites.

---

### Étape 6 – Génération d’exercices (optionnel mais fun)

1. Endpoint `/ai/generate-exercises` lié à une session.
2. Stocker les exercices générés en `Exercise`.

Livrable : une séance peut avoir ses exercices auto-générés.

---

### Étape 7 – Chat IA (bonus)

1. Endpoint `/ai/chat` qui prend :

   * `coursePlanId`
   * `message`
2. Construit un prompt avec le contexte (coursePlan, sessions faites).
3. Renvoie la réponse de l’IA telle quelle.

---

### Étape 8 – Qualité & Docs

1. Rédiger un README clair :

   * objectifs du projet
   * comment lancer le projet
   * comment configurer la clé API LLM
   * endpoints principaux
2. Ajouter les cas de tests majeurs (manuels ou automatisés).

---
