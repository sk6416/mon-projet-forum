# Planification du projet Form'Campus

## 1. Architecture et Technologies

- **Frontend:** HTML5, CSS3, JavaScript
- **Backend:** PHP
- **Base de données:** MySQL
- **Serveur:** Apache (ou équivalent)

## 2. Structure des fichiers

```
/form_campus
├── index.php
├── formations.php
├── inscription.php
├── login.php
├── admin/
│   ├── admin_formations.php
│   └── admin_inscriptions.php
├── assets/
│   ├── css/
│   │   └── style.css
│   └── js/
│       └── script.js
├── includes/
│   ├── connexion.php
│   ├── header.php
│   └── footer.php
└── README.md
```

## 3. Schéma de la base de données

**Nom de la base de données:** `formcampus`

**Tables:**

1.  **`formations`**
    -   `id` (INT, PRIMARY KEY, AUTO_INCREMENT)
    -   `titre` (VARCHAR(255))
    -   `categorie` (VARCHAR(255))
    -   `description` (TEXT)
    -   `duree` (VARCHAR(100))
    -   `prix` (DECIMAL(10, 2))

2.  **`inscriptions`**
    -   `id` (INT, PRIMARY KEY, AUTO_INCREMENT)
    -   `nom` (VARCHAR(255))
    -   `prenom` (VARCHAR(255))
    -   `email` (VARCHAR(255))
    -   `tel` (VARCHAR(20))
    -   `id_formation` (INT, FOREIGN KEY REFERENCES formations(id))
    -   `commentaire` (TEXT)
    -   `date_inscription` (TIMESTAMP, DEFAULT CURRENT_TIMESTAMP)

3.  **`users`**
    -   `id` (INT, PRIMARY KEY, AUTO_INCREMENT)
    -   `login` (VARCHAR(255), UNIQUE)
    -   `password` (VARCHAR(255)) -- Le mot de passe sera haché

## 4. Design et Interface Utilisateur

- **Palette de couleurs:** Bleu et blanc pour un look professionnel et épuré.
- **Typographie:** Police sans-serif moderne pour une bonne lisibilité.
- **Mise en page:** Responsive pour une bonne expérience sur mobile et ordinateur.

## 5. Plan de développement

1.  **Phase 1: Initialisation**
    -   Créer la structure des fichiers.
    -   Mettre en place la base de données et les tables.

2.  **Phase 2: Partie Publique**
    -   Développer la page d'accueil (`index.php`).
    -   Développer la page listant les formations (`formations.php`).
    -   Développer la page d'inscription (`inscription.php`) avec validation JavaScript.

3.  **Phase 3: Partie Administration**
    -   Développer la page de connexion (`login.php`).
    -   Développer la page de gestion des formations (`admin_formations.php`).
    -   Développer la page de gestion des inscriptions (`admin_inscriptions.php`).

4.  **Phase 4: Finalisation**
    -   Tester toutes les fonctionnalités.
    -   Améliorer le design et l'expérience utilisateur.
    -   Rédiger la documentation (`README.md`).
