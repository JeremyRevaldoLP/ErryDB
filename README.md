# 🎬 ErryDB - Semantic Web Movie Recommendation System

ErryDB adalah aplikasi web sederhana berbasis Web Semantic yang digunakan untuk menampilkan, mencari, dan merekomendasikan film menggunakan teknologi RDF/XML, OWL, dan SPARQL.

Proyek ini dibuat sebagai implementasi konsep Semantic Web dalam pengelolaan data film menggunakan ontologi dan relasi antar entitas.

---

## 📌 Fitur Utama

- Menampilkan daftar film beserta:
  - Poster
  - Rating
  - Genre
  - Sutradara
- Detail lengkap setiap film
- Pencarian film berbasis semantik
- Filter berdasarkan:
  - Genre
  - Sutradara
  - Rentang tahun rilis
- Sistem rekomendasi film
- Menampilkan query SPARQL pada halaman detail
- Representasi data menggunakan RDF/XML dan OWL

---

## 🛠️ Teknologi yang Digunakan

| Teknologi | Fungsi |
|---|---|
| HTML | Struktur halaman web |
| CSS | Styling antarmuka |
| PHP | Backend aplikasi |
| RDF/XML | Penyimpanan data semantik |
| OWL | Ontologi dan relasi data |
| SPARQL | Query data semantik |
| EasyRdf | Parsing dan traversal RDF |
| Composer | Dependency manager PHP |

---

## 📂 Struktur Proyek

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

## 🧠 Ontologi Sistem

Sistem menggunakan ontologi OWL dengan struktur berikut:

### Class
- Film
- Genre
- Sutradara

### Object Property
- `hasGenre`
- `directedBy`
- `hasRecommendation`

### Data Property
- `hasTitle`
- `hasYear`
- `hasRating`
- `hasPoster`

---

## 🔍 Contoh Query SPARQL

### Menampilkan Semua Film

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

## 🚀 Cara Menjalankan Proyek

### 1. Clone Repository

```bash
git clone https://github.com/username/errydb.git
```

### 2. Masuk ke Folder Proyek

```bash
cd errydb
```

### 3. Install Dependency

```bash
composer install
```

### 4. Jalankan di Localhost

Pindahkan proyek ke folder:

- `htdocs` (XAMPP)
- `www` (Laragon)

Lalu jalankan Apache dan buka:

```bash
http://localhost/errydb
```

---

## 📸 Halaman Sistem

### Halaman Utama
Menampilkan daftar film, statistik dataset, dan filter pencarian.

### Halaman Detail
Menampilkan informasi lengkap film beserta relasi RDF/OWL dan rekomendasi film.

### Halaman Pencarian
Mencari film berdasarkan judul, genre, atau sutradara menggunakan query semantik.

---

## 📚 Dataset

Dataset terdiri dari:
- 20 film
- 7 genre
- 10 sutradara

Seluruh data direpresentasikan menggunakan RDF/XML sesuai standar Semantic Web W3C.

---

## 🎯 Tujuan Proyek

- Mengimplementasikan konsep Semantic Web
- Menggunakan RDF/XML, OWL, dan SPARQL dalam aplikasi web
- Merepresentasikan hubungan antar data secara semantik
- Membuat sistem rekomendasi film sederhana tanpa database relasional

---

## 👨‍💻 Author

Jeremy Revaldo Latuperisa  
F1G123046  
Program Studi Ilmu Komputer  
Universitas Halu Oleo

---

## 📄 License

Project ini dibuat untuk keperluan akademik dan pembelajaran.