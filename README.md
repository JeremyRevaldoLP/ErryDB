# 🎬 ErryDB - Semantic Web Movie Recommendation System

ErryDB is a simple Semantic Web-based web application used to display, search, and recommend movies using RDF/XML, OWL, and SPARQL technologies.

This project was developed as an implementation of Semantic Web concepts in movie data management using ontologies and semantic relationships between entities.

---

## 📌 Main Features

- Display movie lists including:
  - Posters
  - Ratings
  - Genres
  - Directors
- Complete movie detail pages
- Semantic-based movie search
- Filtering by:
  - Genre
  - Director
  - Release year range
- Movie recommendation system
- Display SPARQL queries on the detail page
- Data representation using RDF/XML and OWL

---

## 🛠️ Technologies Used

| Technology | Function |
|---|---|
| HTML | Web page structure |
| CSS | User interface styling |
| PHP | Backend application |
| RDF/XML | Semantic data storage |
| OWL | Ontology and data relationships |
| SPARQL | Semantic data querying |
| EasyRdf | RDF parsing and traversal |
| Composer | PHP dependency manager |

---

## 📂 Project Structure

```bash
film-rekomendasi/
├── data/
│   ├── ontology.owl
│   └── data.rdf
├── includes/
│   ├── config.php
│   └── sparql_helper.php
├── assets/
│   ├── css/style.css
│   └── img/films/
├── vendor/
├── index.php
├── search.php
└── detail.php
```

---

## 🧠 System Ontology

The system uses an OWL ontology with the following structure:

### Classes
- Film
- Genre
- Director

### Object Properties
- `hasGenre`
- `directedBy`
- `hasRecommendation`

### Data Properties
- `hasTitle`
- `hasYear`
- `hasRating`
- `hasPoster`

---

## 🔍 Example SPARQL Query

### Display All Movies

```sparql
PREFIX film: <http://www.semanticweb.org/filmrekomendasi/ontology#>

SELECT ?film ?title ?year ?rating
WHERE {
   ?film rdf:type film:Film .
   ?film film:hasTitle ?title .
   ?film film:hasYear ?year .
   ?film film:hasRating ?rating .
}
ORDER BY DESC(?rating)
```

---

## 🚀 How to Run the Project

### 1. Clone the Repository

```bash
git clone https://github.com/username/errydb.git
```

### 2. Navigate to the Project Folder

```bash
cd errydb
```

### 3. Install Dependencies

```bash
composer install
```

### 4. Run on Localhost

Move the project folder into:

- `htdocs` (XAMPP)
- `www` (Laragon)

Then start Apache and open:

```bash
http://localhost/errydb
```

---

## 📸 System Pages

### Home Page
Displays movie lists, dataset statistics, and filtering options.

### Detail Page
Displays complete movie information along with RDF/OWL relationships and recommendations.

### Search Page
Allows users to search movies by title, genre, or director using semantic queries.

---

## 📚 Dataset

The dataset consists of:
- 20 movies
- 7 genres
- 10 directors

All data is represented using RDF/XML according to W3C Semantic Web standards.

---

## 🎯 Project Goals

- Implement Semantic Web concepts
- Apply RDF/XML, OWL, and SPARQL in a web application
- Represent relationships between data semantically
- Build a simple movie recommendation system without a relational database

---

## 👨‍💻 Author

Jeremy Revaldo Latuperisa  
F1G123046  
Computer Science Study Program  
Halu Oleo University

---

## 📄 License

This project was created for academic and educational purposes.