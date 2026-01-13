# 🎓 Assistant Pédagogique IA

Ce projet est une application web développée avec **Symfony 7** et **API Platform**, conçue pour aider les formateurs à structurer leurs cours grâce à l'intelligence artificielle.

Il permet d'importer un syllabus, de générer automatiquement un plan de cours détaillé, de suivre la progression des séances et de créer des exercices sur mesure.

---

## 🚀 Fonctionnalités Clés

*   **📄 Import de Syllabus** : Support du texte brut et des fichiers **PDF** (extraction automatique du texte).
*   **🧠 Analyse IA** : Extraction automatique des compétences clés du cours.
*   **📅 Génération de Plan de Cours** : Création d'un déroulé pédagogique complet (séances, objectifs, durées) via l'IA.
*   **✅ Suivi de Progression** : Possibilité de valider les séances terminées et d'ajouter des notes réelles.
*   **🔄 Ajustement Dynamique** : L'IA peut recalculer le plan des séances restantes en fonction du retard ou de l'avance pris.
*   **📝 Générateur d'Exercices** : Création automatique d'exercices (Facile/Moyen/Difficile) pour chaque séance.
*   **💬 Assistant Chat** : Interface de discussion contextuelle pour poser des questions sur le cours à l'IA.

---

## 🛠️ Stack Technique

*   **Backend** : Symfony 7.2, API Platform, Doctrine ORM.
*   **Frontend** : Twig, Tailwind CSS (via SymfonyCasts Tailwind Bundle).
*   **IA** : Intégration de modèles LLM (Llama 3 via Groq API) via `Symfony HttpClient`.
*   **PDF** : Extraction de texte via `smalot/pdfparser`.
*   **Base de données** : SQLite (par défaut pour le dév) ou PostgreSQL/MySQL.

---

## ⚙️ Installation

### Prérequis
*   Docker
*   Docker Compose

### Étapes

1.  **Cloner le projet**
    ```bash
    git clone https://github.com/votre-repo/assistant-pedagogique.git
    cd assistant-pedagogique
    ```

2.  **Configurer l'environnement**
    Dupliquez le fichier `.env` en `.env.local` et ajoutez votre clé API Groq (gratuite) :
    ```dotenv
    # .env.local
    GROQ_API_KEY=votre_cle_api_ici
    GROQ_MODEL=llama-3.3-70b-versatile
    ```

3.  **Lancer les conteneurs**
    ```bash
    docker compose up -d --build
    ```

4.  **Installer les dépendances**
    ```bash
    docker compose exec app composer install
    ```

5.  **Préparer la base de données**
    ```bash
    docker compose exec app php bin/console doctrine:migrations:migrate
    ```

6.  **Compiler les assets (Tailwind)**
    ```bash
    docker compose exec app php bin/console tailwind:build
    # Ou pour le mode watch :
    # docker compose exec app php bin/console tailwind:build --watch
    ```

Accédez à l'application sur `http://localhost`.

---

## 📖 Utilisation

1.  Créez un compte ou connectez-vous.
2.  Sur le **Dashboard**, cliquez sur **"+ Nouveau Syllabus"**.
3.  Uploadez votre PDF de cours ou copiez le texte.
4.  Cliquez sur **"🔍 Analyser"** pour voir les compétences détectées.
5.  Cliquez sur **"✨ Générer le Plan IA"** pour créer les séances.
6.  Ouvrez le plan de cours pour valider vos séances, générer des exercices ou discuter avec l'assistant.

---

## 🧪 Tests

Le projet inclut des tests unitaires et fonctionnels (si implémentés).
```bash
docker compose exec app php bin/console test
```

---

**Projet réalisé dans le cadre du module Développement Web Avancé.**
