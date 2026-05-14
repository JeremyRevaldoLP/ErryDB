<?php
require_once 'includes/sparql_helper.php';

$keyword = isset($_GET['q']) ? trim($_GET['q']) : '';
$films   = [];
$searched = false;

if ($keyword !== '') {
    $films   = searchFilms($keyword);
    $searched = true;
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
    <title>Pencarian — <?= APP_NAME ?></title>
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
            <a href="search.php" class="nav-link active">🔍 Pencarian</a>
        </div>
    </div>
</nav>

<!-- SEARCH HERO -->
<section class="search-hero">
    <h1>🔍 Pencarian Film</h1>
    <p>Cari berdasarkan judul, genre, atau nama sutradara menggunakan SPARQL</p>

    <form class="search-box" action="search.php" method="GET">
        <input type="text" name="q"
               placeholder="Contoh: Nolan, Sci-Fi, Inception..."
               value="<?= htmlspecialchars($keyword) ?>"
               autocomplete="off"
               autofocus>
        <button type="submit">Cari</button>
    </form>
</section>

<!-- RESULTS -->
<div class="container">

    <?php if (!$searched): ?>
        <!-- Belum ada pencarian -->
        <div style="margin-top:2rem">
            <div class="section-title" style="margin-bottom:1.5rem">
                💡 Tips Pencarian SPARQL
            </div>
            <div style="display:grid;grid-template-columns:repeat(auto-fill,minmax(240px,1fr));gap:1rem">
                <?php
                $tips = [
                    ['🎬','Cari judul film','Ketik nama film, contoh: "Inception" atau "Matrix"'],
                    ['🎭','Cari berdasarkan genre','Ketik nama genre, contoh: "Sci-Fi" atau "Drama"'],
                    ['👤','Cari berdasarkan sutradara','Ketik nama sutradara, contoh: "Nolan" atau "Tarantino"'],
                    ['⭐','Temukan rekomendasi','Buka detail film untuk melihat film yang direkomendasikan'],
                ];
                foreach ($tips as $tip): ?>
                    <div style="background:var(--bg-card);border:1px solid var(--border);
                                border-radius:var(--radius);padding:1.25rem">
                        <div style="font-size:2rem;margin-bottom:0.5rem"><?= $tip[0] ?></div>
                        <div style="font-weight:600;margin-bottom:0.25rem"><?= $tip[1] ?></div>
                        <div style="font-size:0.85rem;color:var(--text-muted)"><?= $tip[2] ?></div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>

    <?php elseif (empty($films)): ?>
        <!-- Tidak ada hasil -->
        <div class="empty-state" style="margin-top:2rem">
            <span class="empty-icon">🔍</span>
            <h3>Tidak ada hasil untuk "<strong><?= htmlspecialchars($keyword) ?></strong>"</h3>
            <p>Coba kata kunci lain atau periksa ejaan Anda.</p>
        </div>

    <?php else: ?>
        <!-- Ada hasil -->
        <div class="search-results-info">
            Ditemukan <strong><?= count($films) ?> film</strong>
            untuk kata kunci "<strong><?= htmlspecialchars($keyword) ?></strong>"
            — query SPARQL menggunakan <code>FILTER(CONTAINS(LCASE(...)))</code>
        </div>

        <div class="films-grid" style="margin-top:1rem">
            <?php foreach ($films as $film): ?>
                <div class="film-card"
                     onclick="window.location='detail.php?id=<?= urlencode($film['id']) ?>'">
                    <div class="film-card-poster">
                        <?php if (!empty($film['poster'])): ?>
                            <img src="assets/img/films/<?= htmlspecialchars($film['poster']) ?>"
                                alt="<?= htmlspecialchars($film['title']) ?>"
                                style="width:100%;height:100%;object-fit:cover;">
                        <?php else: ?>
                            <?= getPosterEmoji($film['genres']) ?>
                        <?php endif; ?>
                    </div>
                    <div class="film-card-body">
                        <div class="film-card-title">
                            <?= htmlspecialchars($film['title']) ?>
                        </div>
                        <div class="film-card-meta">
                            <span class="film-year">📅 <?= $film['year'] ?></span>
                            <span class="film-rating">
                                ★ <?= number_format($film['rating'], 1) ?>
                            </span>
                        </div>
                        <div class="film-genres">
                            <?php foreach (array_slice($film['genres'], 0, 2) as $genre): ?>
                                <span class="genre-tag"><?= htmlspecialchars($genre) ?></span>
                            <?php endforeach; ?>
                        </div>
                    </div>
                    <div class="film-card-footer">
                        <span class="film-director">
                            🎬 <?= htmlspecialchars($film['sutradara']) ?>
                        </span>
                        <a href="detail.php?id=<?= urlencode($film['id']) ?>"
                           class="btn-detail"
                           onclick="event.stopPropagation()">Detail</a>
                    </div>
                </div>
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