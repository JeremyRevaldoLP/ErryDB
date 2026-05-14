<?php
require_once 'includes/sparql_helper.php';

$filmId = isset($_GET['id']) ? trim($_GET['id']) : '';

if (!$filmId) {
    header('Location: index.php');
    exit;
}

$film = getFilmDetail($filmId);

if (!$film) {
    header('Location: index.php');
    exit;
}

function getPosterEmoji($genres) {
    $map = ['Sci-Fi'=>'🚀','Aksi'=>'⚔️','Drama'=>'🎭',
            'Thriller'=>'🔪','Animasi'=>'✨','Komedi'=>'😂','Horor'=>'👻'];
    foreach ($genres as $g) {
        if (isset($map[$g])) return $map[$g];
    }
    return '🎬';
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($film['title']) ?> — <?= APP_NAME ?></title>
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>

<!-- NAVBAR -->
<nav class="navbar">
    <div class="navbar-inner">
        <a href="index.php" class="navbar-brand">
            <div class="navbar-icon">🎬</div>
            Erry<span>DB</span>
        </a>
        <div class="navbar-links">
            <a href="index.php" class="nav-link">🏠 Beranda</a>
            <a href="index.php" class="nav-link">🎞️ Daftar Film</a>
            <a href="search.php" class="nav-link">🔍 Pencarian</a>
        </div>
    </div>
</nav>

<!-- DETAIL HERO -->
<section class="detail-hero">
    <div class="detail-hero-inner">
        <!-- Poster -->
        <div class="detail-poster" style="<?= !empty($film['poster']) ? 'padding:0;overflow:hidden' : '' ?>">
            <?php if (!empty($film['poster'])): ?>
                <img src="assets/img/films/<?= htmlspecialchars($film['poster']) ?>"
                    alt="<?= htmlspecialchars($film['title']) ?>"
                    style="width:100%;height:100%;object-fit:cover;border-radius:var(--radius)">
            <?php else: ?>
                <?= getPosterEmoji($film['genres']) ?>
            <?php endif; ?>
        </div>

        <!-- Info Utama -->
        <div class="detail-info">
            <div class="detail-breadcrumb">
                <a href="index.php">🏠 Beranda</a>
                <span>›</span>
                <a href="index.php">Daftar Film</a>
                <span>›</span>
                <span><?= htmlspecialchars($film['title']) ?></span>
            </div>

            <h1 class="detail-title">
                <?= htmlspecialchars($film['title']) ?>
            </h1>

            <div class="detail-meta-row">
                <span class="detail-year">📅 <?= $film['year'] ?></span>
                <span class="detail-rating-badge">
                    ★ <?= number_format($film['rating'], 1) ?> / 10
                </span>
            </div>

            <div class="detail-genres">
                <?php foreach ($film['genres'] as $genre): ?>
                    <a href="index.php?genre=<?= urlencode($genre) ?>"
                       class="genre-tag" style="font-size:0.85rem;padding:0.3rem 0.75rem">
                        <?= htmlspecialchars($genre) ?>
                    </a>
                <?php endforeach; ?>
            </div>

            <div class="detail-director">
                <span>🎬 Sutradara:</span>
                <strong>
                    <a href="index.php?sutradara=<?= urlencode($film['sutradara']) ?>"
                       style="color:var(--primary)">
                        <?= htmlspecialchars($film['sutradara']) ?>
                    </a>
                </strong>
            </div>
        </div>
    </div>
</section>

<!-- DETAIL CONTENT -->
<div class="container">

    <a href="javascript:history.back()" class="btn-back">← Kembali</a>

    <!-- INFO GRID -->
    <div class="detail-grid">

        <!-- Informasi Film -->
        <div class="detail-section">
            <div class="detail-section-title">📋 Informasi Film</div>
            <div class="info-row">
                <span class="info-label">Judul</span>
                <span class="info-value"><?= htmlspecialchars($film['title']) ?></span>
            </div>
            <div class="info-row">
                <span class="info-label">Tahun Rilis</span>
                <span class="info-value"><?= $film['year'] ?></span>
            </div>
            <div class="info-row">
                <span class="info-label">Sutradara</span>
                <span class="info-value"><?= htmlspecialchars($film['sutradara']) ?></span>
            </div>
            <div class="info-row">
                <span class="info-label">Genre</span>
                <span class="info-value"><?= implode(', ', $film['genres']) ?></span>
            </div>
            <div class="info-row">
                <span class="info-label">Rating</span>
                <span class="info-value" style="color:var(--gold)">
                    ★ <?= number_format($film['rating'], 1) ?> / 10
                </span>
            </div>
        </div>

        <!-- Data Semantik -->
        <div class="detail-section">
            <div class="detail-section-title">🌐 Data Semantik (RDF/OWL)</div>
            <div class="info-row">
                <span class="info-label">Tipe</span>
                <span class="info-value" style="font-size:0.8rem;color:var(--primary)">
                    owl:Class → film:Film
                </span>
            </div>
            <div class="info-row">
                <span class="info-label">URI</span>
                <span class="info-value"
                      style="font-size:0.7rem;color:var(--text-muted);word-break:break-all">
                    <?= htmlspecialchars($film['uri']) ?>
                </span>
            </div>
            <div class="info-row">
                <span class="info-label">hasTitle</span>
                <span class="info-value" style="font-size:0.8rem">
                    "<?= htmlspecialchars($film['title']) ?>"^^xsd:string
                </span>
            </div>
            <div class="info-row">
                <span class="info-label">hasYear</span>
                <span class="info-value" style="font-size:0.8rem">
                    "<?= $film['year'] ?>"^^xsd:integer
                </span>
            </div>
            <div class="info-row">
                <span class="info-label">hasRating</span>
                <span class="info-value" style="font-size:0.8rem">
                    "<?= $film['rating'] ?>"^^xsd:decimal
                </span>
            </div>
            <div class="info-row">
                <span class="info-label">Rekomendasi</span>
                <span class="info-value">
                    <?= count($film['recommendations']) ?> film terkait
                </span>
            </div>
        </div>
    </div>

    <!-- SPARQL QUERY DISPLAY -->
    <div class="detail-section" style="margin-bottom:2rem">
        <div class="detail-section-title">⚡ Query SPARQL yang Digunakan</div>
        <pre style="background:var(--bg-dark);border:1px solid var(--border);
                    border-radius:var(--radius-sm);padding:1.25rem;
                    font-size:0.78rem;color:#a8d8a8;overflow-x:auto;
                    font-family:'Courier New',monospace;line-height:1.6">
PREFIX film: &lt;http://www.semanticweb.org/filmrekomendasi/ontology#&gt;
PREFIX rdf:  &lt;http://www.w3.org/1999/02/22-rdf-syntax-ns#&gt;
PREFIX rdfs: &lt;http://www.w3.org/2000/01/rdf-schema#&gt;

SELECT ?title ?year ?rating ?genreLabel ?sutradaraLabel ?rekTitle
WHERE {
  film:<?= htmlspecialchars($filmId) ?> film:hasTitle    ?title .
  film:<?= htmlspecialchars($filmId) ?> film:hasYear     ?year .
  film:<?= htmlspecialchars($filmId) ?> film:hasRating   ?rating .
  film:<?= htmlspecialchars($filmId) ?> film:hasGenre    ?genre .
  film:<?= htmlspecialchars($filmId) ?> film:directedBy  ?sutradara .
  ?genre     rdfs:label ?genreLabel .
  ?sutradara rdfs:label ?sutradaraLabel .
  OPTIONAL {
    film:<?= htmlspecialchars($filmId) ?> film:hasRecommendation ?rek .
    ?rek film:hasTitle ?rekTitle .
  }
}</pre>
    </div>

    <!-- REKOMENDASI -->
    <?php if (!empty($film['recommendations'])): ?>
        <div class="section-header">
            <h2 class="section-title">🎯 Film yang Direkomendasikan</h2>
            <span class="section-count"><?= count($film['recommendations']) ?> film</span>
        </div>

        <div class="rec-grid" style="margin-bottom:2rem">
            <?php foreach ($film['recommendations'] as $rec): ?>
                <a href="detail.php?id=<?= urlencode($rec['id']) ?>"
                   class="rec-card">
                    <div class="rec-card-emoji" style="<?= !empty($rec['poster']) ? 'font-size:0;padding:0;height:80px;overflow:hidden;border-radius:6px;margin-bottom:0.5rem' : '' ?>">
                        <?php if (!empty($rec['poster'])): ?>
                            <img src="assets/img/films/<?= htmlspecialchars($rec['poster']) ?>"
                                alt="<?= htmlspecialchars($rec['title']) ?>"
                                style="width:100%;height:80px;object-fit:cover;border-radius:6px;">
                        <?php else: ?>
                            <?= getPosterEmoji($rec['genres']) ?>
                        <?php endif; ?>
                    </div>
                    <div class="rec-card-title">
                        <?= htmlspecialchars($rec['title']) ?>
                    </div>
                    <div class="rec-card-meta">
                        <span><?= $rec['year'] ?></span>
                        <span class="rec-card-rating">★ <?= number_format($rec['rating'],1) ?></span>
                    </div>
                </a>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>

</div>

<!-- FOOTER -->
<footer>
    <p>🎬 <span>ErryDB</span> — Sistem Rekomendasi Film Berbasis Web Semantik</p>
    <p style="margin-top:0.5rem">Dibangun dengan RDF/XML · OWL Ontology · SPARQL · PHP · EasyRdf</p>
</footer>

</body>
</html>