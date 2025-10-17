# B3-IN-backend-AntoineROCQ
# cd B3-IN-backend-AntoineROCQ
# symfony server:start
# Tests POSTMAN :
# Instructions Postman - API Bibliothèque

Base URL: `http://127.0.0.1:8000`

---

## 1. CRUD Livres

### 1.1. Créer un livre
**POST** `/api/livres`

**Body (JSON):**
```json
{
  "titre": "Le Petit Prince",
  "datePublication": "1943-04-06",
  "disponible": true,
  "auteur_id": 1,
  "categorie_id": 1
}
```

---

### 1.2. Liste tous les livres
**GET** `/api/livres`

---

### 1.3. Afficher un livre spécifique
**GET** `/api/livres/1`

---

### 1.4. Modifier un livre
**PUT** `/api/livres/1`

**Body (JSON):**
```json
{
  "titre": "Le Petit Prince - Edition 2024",
  "datePublication": "2024-01-01",
  "disponible": false,
  "auteur_id": 1,
  "categorie_id": 2
}
```

---

### 1.5. Supprimer un livre
**DELETE** `/api/livres/1`

---

## 2. Gestion des Emprunts

### 2.1. Emprunter un livre
**POST** `/api/emprunts/emprunter`

**Body (JSON):**
```json
{
  "utilisateur_id": 1,
  "livre_id": 1
}
```

**Règles:**
- Le livre doit être disponible
- L'utilisateur ne peut pas avoir plus de 4 emprunts actifs

---

### 2.2. Retourner un livre
**POST** `/api/emprunts/retourner/1`

---

## 3. Statistiques Utilisateur

### 3.1. Voir les emprunts en cours d'un utilisateur
**GET** `/api/utilisateurs/1/emprunts`

**Réponse:**
- Nombre d'emprunts en cours
- Liste triée par date (du plus ancien au plus récent)

---

## 4. Recherche par Auteur et Dates

### 4.1. Livres d'un auteur empruntés entre deux dates
**GET** `/api/utilisateurs/auteur/1/livres?dateDebut=2024-01-01&dateFin=2024-12-31`

**Paramètres:**
- `dateDebut`: Date de début (format: YYYY-MM-DD)
- `dateFin`: Date de fin (format: YYYY-MM-DD)
