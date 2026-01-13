
## 4. Cahier de tests (MVP)

Je te propose une base, à adapter/compléter.

### 4.1. Tests fonctionnalité “Import syllabus”

**T-01 – Upload PDF simple**

* Préconditions : utilisateur connecté, syllabus de test en PDF simple.
* Étapes :

  1. POST `/syllabuses` avec fichier PDF.
  2. Vérifier code 201.
  3. GET `/syllabuses/{id}`.
* Résultat attendu :

  * `title` correct
  * `rawText` non vide

**T-02 – Syllabus via texte brut**

* Étapes : POST `/syllabuses` avec `rawText` directement.
* Résultat : objet créé, `rawText` exactement égal au texte envoyé.

---

### 4.2. Tests “Analyse syllabus + génération plan de cours”

**T-10 – Analyse simple**

* Préconditions : Syllabus avec texte simple.
* Étapes :

  1. POST `/ai/analyze-syllabus` ou déclenchement interne.
* Résultat :

  * `competences` non vide
  * `themes` non vide

**T-11 – Génération plan de cours**

* Préconditions : syllabus analysé.
* Étapes :

  1. POST `/ai/generate-course-plan`
  2. Vérifier code 201/200
  3. GET `/course_plans/{id}`
* Résultat :

  * nombre de `sessions` = `nombre_seances`
  * chaque `session` a `title`, `objectives`, `contents`.

---

### 4.3. Tests “Suivi des séances”

**T-20 – Marquer une séance comme faite**

* Étapes :

  1. PATCH `/sessions/{id}/complete` avec `done=true` et `actualNotes`.
  2. GET `/sessions/{id}`.
* Résultat :

  * `done = true`
  * `actualNotes` contient le texte envoyé.

---

### 4.4. Tests “Génération d’exercices”

**T-30 – Exercices générés**

* Étapes :

  1. POST `/ai/generate-exercises` avec session_id + difficulté.
* Résultat :

  * JSON valide
  * au moins 1 exercice
  * chaque exercice a `titre`, `instruction`, `difficulte`.

---

### 4.5. Tests “Sécurité”

**T-40 – Accès non authentifié**

* Étapes : GET `/syllabuses` sans token.
* Résultat : 401 Unauthorized.

**T-41 – Accès authentifié**

* Étapes :

  1. Login → récup token.
  2. GET `/syllabuses` avec Bearer token.
* Résultat : 200 + liste (éventuellement vide).

---
