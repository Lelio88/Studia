
## 2. Modèle de données UML (proposition)

### 2.1. Classes principales

#### `User`

* `id: int`
* `email: string`
* `password: string`
* `roles: json` (ROLE_TEACHER, ROLE_ADMIN…)

Relations :

* 1 `User` → * `Syllabus`
* 1 `User` → * `CoursePlan` (optionnel)

---

#### `Syllabus`

* `id: int`
* `title: string`
* `rawText: text` (texte brut du syllabus, import PDF ou copié/collé)
* `extractedCompetences: json nullable`
* `createdAt: datetime_immutable`
* `owner: User`

Relations :

* 1 `Syllabus` → 0..1 `CoursePlan` (un syllabus peut avoir un plan de cours)

---

#### `CoursePlan`

* `id: int`
* `title: string` (nom du module / cours)
* `generalPlan: text` (vue globale)
* `evaluationCriteria: json` (modalités, critères)
* `nbSessionsPlanned: int`
* `expectedTotalHours: int`
* `createdAt: datetime_immutable`
* `syllabus: Syllabus`
* `owner: User` (ou récupéré via syllabus.owner)

Relations :

* 1 `CoursePlan` → * `Session`

---

#### `Session`

* `id: int`
* `indexNumber: int` (1, 2, 3…)
* `title: string`
* `objectives: json` (liste d’objectifs)
* `contents: json` (points de cours)
* `activities: json` (TP, exercices, discussions)
* `resources: json` (liens, docs…)
* `done: bool` (par défaut false)
* `actualNotes: text nullable` (ce qui a réellement été fait)
* `plannedDurationMinutes: int`
* `coursePlan: CoursePlan`

Relations :

* 1 `Session` → * `Exercise` (optionnel)

---

#### `Exercise`

* `id: int`
* `title: string`
* `instruction: text`
* `difficulty: string` (EASY / MEDIUM / HARD)
* `expectedDurationMinutes: int`
* `correction: json nullable` (proposition de correction par l’IA)
* `session: Session`

---

#### (Optionnel) `Student`

* `id: int`
* `firstName: string`
* `lastName: string`
* `email: string`

#### (Optionnel) `StudentProgress`

* `id: int`
* `student: Student`
* `coursePlan: CoursePlan`
* `globalLevel: string` (BEGINNER / INTERMEDIATE / ADVANCED)
* `notes: text nullable`

---

### 2.2. Relations résumé

* `User` 1—* `Syllabus`
* `Syllabus` 1—0..1 `CoursePlan`
* `CoursePlan` 1—* `Session`
* `Session` 1—* `Exercise`
* (Optionnel) `CoursePlan` 1—* `StudentProgress`
* (Optionnel) `Student` 1—* `StudentProgress`

---

