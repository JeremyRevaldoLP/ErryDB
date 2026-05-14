<?php
// ================================================
// SPARQL_HELPER.PHP - Query SPARQL & Logika RDF
// ================================================

require_once __DIR__ . '/../vendor/autoload.php';
require_once __DIR__ . '/config.php';

use EasyRdf\Graph;
use EasyRdf\Sparql\Client;

// ------------------------------------------------
// FUNGSI UTAMA: Load Graph RDF
// ------------------------------------------------
function loadGraph() {
    // Register namespace agar EasyRdf mengenali prefix film:
    \EasyRdf\RdfNamespace::set('film', 'http://www.semanticweb.org/filmrekomendasi/ontology#');
    \EasyRdf\RdfNamespace::set('xsd',  'http://www.w3.org/2001/XMLSchema#');

    $graph = new \EasyRdf\Graph();
    $graph->parseFile(RDF_FILE, 'rdfxml');
    return $graph;
}

// ------------------------------------------------
// FUNGSI PEMBANTU: Jalankan SPARQL pada Graph Lokal
// Karena EasyRdf tidak punya SPARQL engine lokal,
// kita parse hasil RDF secara manual menggunakan
// metode graph traversal EasyRdf
// ------------------------------------------------

// ------------------------------------------------
// QUERY 1: Ambil Semua Film
// SPARQL Equivalent:
//   SELECT ?film ?title ?year ?rating
//   WHERE {
//     ?film rdf:type film:Film .
//     ?film film:hasTitle ?title .
//     ?film film:hasYear  ?year .
//     ?film film:hasRating ?rating .
//   }
//   ORDER BY DESC(?rating)
// ------------------------------------------------
function getAllFilms() {
    $graph = loadGraph();
    $films = [];

    $filmResources = $graph->allOfType('film:Film');

    foreach ($filmResources as $filmResource) {
        $title  = $filmResource->getLiteral('film:hasTitle');
        $year   = $filmResource->getLiteral('film:hasYear');
        $rating = $filmResource->getLiteral('film:hasRating');
        $poster = $filmResource->getLiteral('film:hasPoster');

        if (!$title) continue;

        // Ambil genre (bisa lebih dari 1)
        $genreList = [];
        $genres = $filmResource->all('film:hasGenre');
        foreach ($genres as $genre) {
            $label = $genre->label();
            if ($label) $genreList[] = (string)$label;
        }

        // Ambil sutradara
        $sutradaraName = '';
        $sutradara = $filmResource->get('film:directedBy');
        if ($sutradara) {
            $sutradaraName = (string)$sutradara->label();
        }

        // Ambil ID film dari URI
        $uri  = $filmResource->getUri();
        $id   = str_replace(NS_FILM, '', $uri);

        $films[] = [
            'id'        => $id,
            'uri'       => $uri,
            'title'     => (string)$title,
            'year'      => (string)$year,
            'rating'    => (float)(string)$rating,
            'genres'    => $genreList,
            'sutradara' => $sutradaraName,
            'poster'    => $poster ? (string)$poster : '',
        ];
    }

    // ORDER BY DESC(rating) — urutkan rating tertinggi
    usort($films, fn($a, $b) => $b['rating'] <=> $a['rating']);

    return $films;
}

// ------------------------------------------------
// QUERY 2: Filter Film Berdasarkan Genre
// SPARQL Equivalent:
//   SELECT ?film ?title ?year ?rating
//   WHERE {
//     ?film rdf:type    film:Film .
//     ?film film:hasTitle  ?title .
//     ?film film:hasYear   ?year .
//     ?film film:hasRating ?rating .
//     ?film film:hasGenre  ?genre .
//     ?genre rdfs:label    ?genreLabel .
//     FILTER(CONTAINS(LCASE(?genreLabel), LCASE("$keyword")))
//   }
//   ORDER BY DESC(?rating)
// ------------------------------------------------
function getFilmsByGenre($genreKeyword) {
    $graph = loadGraph();
    $films = [];

    $filmResources = $graph->allOfType('film:Film');

    foreach ($filmResources as $filmResource) {
        $title  = $filmResource->getLiteral('film:hasTitle');
        $year   = $filmResource->getLiteral('film:hasYear');
        $rating = $filmResource->getLiteral('film:hasRating');

        if (!$title) continue;

        // Cek apakah film punya genre yang cocok (CONTAINS + LCASE)
        $genreList   = [];
        $genreMatch  = false;
        $genres = $filmResource->all('film:hasGenre');

        foreach ($genres as $genre) {
            $label = (string)$genre->label();
            if ($label) {
                $genreList[] = $label;
                // FILTER(CONTAINS(LCASE(?genreLabel), LCASE(?keyword)))
                if (stripos($label, $genreKeyword) !== false) {
                    $genreMatch = true;
                }
            }
        }

        if (!$genreMatch) continue;

        $sutradaraName = '';
        $sutradara = $filmResource->get('film:directedBy');
        if ($sutradara) $sutradaraName = (string)$sutradara->label();

        $uri = $filmResource->getUri();
        $id  = str_replace(NS_FILM, '', $uri);

        $films[] = [
            'id'        => $id,
            'uri'       => $uri,
            'title'     => (string)$title,
            'year'      => (string)$year,
            'rating'    => (float)(string)$rating,
            'genres'    => $genreList,
            'sutradara' => $sutradaraName,
        ];
    }

    usort($films, fn($a, $b) => $b['rating'] <=> $a['rating']);
    return $films;
}

// ------------------------------------------------
// QUERY 3: Cari Film Berdasarkan Sutradara
// SPARQL Equivalent:
//   SELECT ?film ?title ?year ?rating
//   WHERE {
//     ?film rdf:type      film:Film .
//     ?film film:hasTitle  ?title .
//     ?film film:hasYear   ?year .
//     ?film film:hasRating ?rating .
//     ?film film:directedBy ?sutradara .
//     ?sutradara rdfs:label ?sutradaraLabel .
//     FILTER(CONTAINS(LCASE(?sutradaraLabel), LCASE("$keyword")))
//   }
// ------------------------------------------------
function getFilmsBySutradara($keyword) {
    $graph = loadGraph();
    $films = [];

    $filmResources = $graph->allOfType('film:Film');

    foreach ($filmResources as $filmResource) {
        $title  = $filmResource->getLiteral('film:hasTitle');
        $year   = $filmResource->getLiteral('film:hasYear');
        $rating = $filmResource->getLiteral('film:hasRating');

        if (!$title) continue;

        $sutradaraName = '';
        $sutradara = $filmResource->get('film:directedBy');
        if ($sutradara) $sutradaraName = (string)$sutradara->label();

        // FILTER(CONTAINS(LCASE(?sutradaraLabel), LCASE(?keyword)))
        if (stripos($sutradaraName, $keyword) === false) continue;

        $genreList = [];
        foreach ($filmResource->all('film:hasGenre') as $genre) {
            $label = (string)$genre->label();
            if ($label) $genreList[] = $label;
        }

        $uri = $filmResource->getUri();
        $id  = str_replace(NS_FILM, '', $uri);

        $films[] = [
            'id'        => $id,
            'uri'       => $uri,
            'title'     => (string)$title,
            'year'      => (string)$year,
            'rating'    => (float)(string)$rating,
            'genres'    => $genreList,
            'sutradara' => $sutradaraName,
        ];
    }

    usort($films, fn($a, $b) => $b['rating'] <=> $a['rating']);
    return $films;
}

// ------------------------------------------------
// QUERY 4: Filter Film Berdasarkan Rentang Tahun
// SPARQL Equivalent:
//   SELECT ?film ?title ?year ?rating
//   WHERE {
//     ?film rdf:type      film:Film .
//     ?film film:hasTitle  ?title .
//     ?film film:hasYear   ?year .
//     ?film film:hasRating ?rating .
//     FILTER(?year >= $tahunDari && ?year <= $tahunSampai)
//   }
//   ORDER BY ASC(?year)
// ------------------------------------------------
function getFilmsByYear($yearFrom, $yearTo) {
    $graph = loadGraph();
    $films = [];

    $filmResources = $graph->allOfType('film:Film');

    foreach ($filmResources as $filmResource) {
        $title  = $filmResource->getLiteral('film:hasTitle');
        $year   = $filmResource->getLiteral('film:hasYear');
        $rating = $filmResource->getLiteral('film:hasRating');

        if (!$title || !$year) continue;

        $yearInt = (int)(string)$year;

        // FILTER(?year >= $yearFrom && ?year <= $yearTo)
        if ($yearInt < $yearFrom || $yearInt > $yearTo) continue;

        $genreList = [];
        foreach ($filmResource->all('film:hasGenre') as $genre) {
            $label = (string)$genre->label();
            if ($label) $genreList[] = $label;
        }

        $sutradaraName = '';
        $sutradara = $filmResource->get('film:directedBy');
        if ($sutradara) $sutradaraName = (string)$sutradara->label();

        $uri = $filmResource->getUri();
        $id  = str_replace(NS_FILM, '', $uri);

        $films[] = [
            'id'        => $id,
            'uri'       => $uri,
            'title'     => (string)$title,
            'year'      => (string)$year,
            'rating'    => (float)(string)$rating,
            'genres'    => $genreList,
            'sutradara' => $sutradaraName,
        ];
    }

    // ORDER BY ASC(?year)
    usort($films, fn($a, $b) => $a['year'] <=> $b['year']);
    return $films;
}

// ------------------------------------------------
// QUERY 5: Detail 1 Film + Rekomendasi
// SPARQL Equivalent:
//   SELECT ?title ?year ?rating ?genreLabel ?sutradaraLabel
//          ?rekTitle ?rekYear ?rekRating
//   WHERE {
//     film:$filmId film:hasTitle    ?title .
//     film:$filmId film:hasYear     ?year .
//     film:$filmId film:hasRating   ?rating .
//     film:$filmId film:hasGenre    ?genre .
//     film:$filmId film:directedBy  ?sutradara .
//     ?genre      rdfs:label        ?genreLabel .
//     ?sutradara  rdfs:label        ?sutradaraLabel .
//     OPTIONAL {
//       film:$filmId film:hasRecommendation ?rek .
//       ?rek film:hasTitle  ?rekTitle .
//       ?rek film:hasYear   ?rekYear .
//       ?rek film:hasRating ?rekRating .
//     }
//   }
// ------------------------------------------------
function getFilmDetail($filmId) {
    $graph = loadGraph();

    $filmUri      = NS_FILM . $filmId;
    $filmResource = $graph->resource($filmUri);

    if (!$filmResource) return null;

    $title  = $filmResource->getLiteral('film:hasTitle');
    $year   = $filmResource->getLiteral('film:hasYear');
    $rating = $filmResource->getLiteral('film:hasRating');
    $poster = $filmResource->getLiteral('film:hasPoster');

    if (!$title) return null;

    // Genre (bisa lebih dari 1)
    $genreList = [];
    foreach ($filmResource->all('film:hasGenre') as $genre) {
        $label = (string)$genre->label();
        if ($label) $genreList[] = $label;
    }

    // Sutradara
    $sutradaraName = '';
    $sutradara = $filmResource->get('film:directedBy');
    if ($sutradara) $sutradaraName = (string)$sutradara->label();

    // OPTIONAL: Rekomendasi film
    $recommendations = [];
    $reks = $filmResource->all('film:hasRecommendation');
    foreach ($reks as $rek) {
        $rekTitle  = $rek->getLiteral('film:hasTitle');
        $rekYear   = $rek->getLiteral('film:hasYear');
        $rekRating = $rek->getLiteral('film:hasRating');
        $rekPoster = $rek->getLiteral('film:hasPoster');
        $rekUri    = $rek->getUri();
        $rekId     = str_replace(NS_FILM, '', $rekUri);

        $rekGenres = [];
        foreach ($rek->all('film:hasGenre') as $rg) {
            $rl = (string)$rg->label();
            if ($rl) $rekGenres[] = $rl;
        }

        if ($rekTitle) {
            $recommendations[] = [
                'id'     => $rekId,
                'title'  => (string)$rekTitle,
                'year'   => (string)$rekYear,
                'rating' => (float)(string)$rekRating,
                'genres' => $rekGenres,
                'poster' => $rekPoster ? (string)$rekPoster : '',
            ];
        }
    }

    return [
        'id'              => $filmId,
        'uri'             => $filmUri,
        'title'           => (string)$title,
        'year'            => (string)$year,
        'rating'          => (float)(string)$rating,
        'genres'          => $genreList,
        'sutradara'       => $sutradaraName,
        'poster'          => $poster ? (string)$poster : '',
        'recommendations' => $recommendations,
    ];
}

// ------------------------------------------------
// FUNGSI PEMBANTU: Cari Film (judul/genre/sutradara)
// Dipakai oleh search.php
// SPARQL Equivalent:
//   FILTER(
//     CONTAINS(LCASE(?title), LCASE(?keyword)) ||
//     CONTAINS(LCASE(?genreLabel), LCASE(?keyword)) ||
//     CONTAINS(LCASE(?sutradaraLabel), LCASE(?keyword))
//   )
// ------------------------------------------------
function searchFilms($keyword) {
    $graph = loadGraph();
    $films = [];

    $filmResources = $graph->allOfType('film:Film');

    foreach ($filmResources as $filmResource) {
        $title  = $filmResource->getLiteral('film:hasTitle');
        $year   = $filmResource->getLiteral('film:hasYear');
        $rating = $filmResource->getLiteral('film:hasRating');
        $poster = $filmResource->getLiteral('film:hasPoster');

        if (!$title) continue;

        $titleStr = (string)$title;

        $genreList  = [];
        $genreMatch = false;
        foreach ($filmResource->all('film:hasGenre') as $genre) {
            $label = (string)$genre->label();
            if ($label) {
                $genreList[] = $label;
                if (stripos($label, $keyword) !== false) $genreMatch = true;
            }
        }

        $sutradaraName  = '';
        $sutradaraMatch = false;
        $sutradara = $filmResource->get('film:directedBy');
        if ($sutradara) {
            $sutradaraName = (string)$sutradara->label();
            if (stripos($sutradaraName, $keyword) !== false) $sutradaraMatch = true;
        }

        $titleMatch = stripos($titleStr, $keyword) !== false;

        // FILTER: judul ATAU genre ATAU sutradara mengandung keyword
        if (!$titleMatch && !$genreMatch && !$sutradaraMatch) continue;

        $uri = $filmResource->getUri();
        $id  = str_replace(NS_FILM, '', $uri);

        $films[] = [
            'id'        => $id,
            'uri'       => $uri,
            'title'     => $titleStr,
            'year'      => (string)$year,
            'rating'    => (float)(string)$rating,
            'genres'    => $genreList,
            'sutradara' => $sutradaraName,
            'poster'    => $poster ? (string)$poster : '',
        ];
    }

    usort($films, fn($a, $b) => $b['rating'] <=> $a['rating']);
    return $films;
}

// ------------------------------------------------
// FUNGSI PEMBANTU: Daftar semua genre unik
// ------------------------------------------------
function getAllGenres() {
    $graph  = loadGraph();
    $genres = [];

    $genreResources = $graph->allOfType('film:Genre');
    foreach ($genreResources as $genre) {
        $label = (string)$genre->label();
        if ($label) $genres[] = $label;
    }

    sort($genres);
    return $genres;
}

// ------------------------------------------------
// FUNGSI PEMBANTU: Render bintang rating
// ------------------------------------------------
function renderStars($rating) {
    $stars    = round($rating / 2);
    $full     = $stars;
    $empty    = 5 - $full;
    $html     = '';
    for ($i = 0; $i < $full;  $i++) $html .= '★';
    for ($i = 0; $i < $empty; $i++) $html .= '☆';
    return $html;
}
?>