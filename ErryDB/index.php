<?php
require_once 'includes/sparql_helper.php';

// Ambil parameter filter dari URL
$filterGenre    = isset($_GET['genre'])      ? trim($_GET['genre'])      : '';
$filterYearFrom = isset($_GET['year_from'])  ? (int)$_GET['year_from']  : '';
$filterYearTo   = isset($_GET['year_to'])    ? (int)$_GET['year_to']    : '';
$filterSutradara = isset($_GET['sutradara']) ? trim($_GET['sutradara'])  : '';

// Jalankan query sesuai filter aktif
if ($filterGenre) {
    $films = getFilmsByGenre($filterGenre);
    $pageTitle = "Genre: $filterGenre";
} elseif ($filterSutradara) {
    $films = getFilmsBySutradara($filterSutradara);
    $pageTitle = "Sutradara: $filterSutradara";
} elseif ($filterYearFrom || $filterYearTo) {
    $from  = $filterYearFrom ?: 1900;
    $to    = $filterYearTo   ?: date('Y');
    $films = getFilmsByYear($from, $to);
    $pageTitle = "Tahun: $from – $to";
} else {
    $films = getAllFilms();
    $pageTitle = "Semua Film";
}

$allGenres   = getAllGenres();
$totalFilms  = count($films);
$isFiltered  = $filterGenre || $filterSutradara || $filterYearFrom || $filterYearTo;

// Emoji poster berdasarkan genre
function getPosterEmoji($genres) {
    $map = [
        'Sci-Fi'   => '🚀',
        'Aksi'     => '⚔️',
        'Drama'    => '🎭',
        'Thriller' => '🔪',
        'Animasi'  => '✨',
        'Komedi'   => '😂',
        'Horor'    => '👻',
    ];
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
    <title><?= APP_NAME ?></title>
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
            <a href="index.php" class="nav-link active">🏠 Beranda</a>
            <a href="index.php" class="nav-link">🎞️ Daftar Film</a>
            <a href="search.php" class="nav-link">🔍 Pencarian</a>
        </div>
    </div>
</nav>

<!-- HERO (hanya tampil jika tidak ada filter) -->
<?php if (!$isFiltered): ?>
<section class="hero">
    <div class="hero-content">
        <div class="hero-badge">🌐 Berbasis Web Semantik & RDF</div>
        <h1>Temukan Film<br><span>Favoritmu</span></h1>
        <p>Sistem rekomendasi film cerdas menggunakan teknologi ontologi OWL dan query SPARQL.</p>

        <form class="hero-search" action="search.php" method="GET">
            <input type="text" name="q"
                   placeholder="Cari judul, genre, atau sutradara..."
                   autocomplete="off">
            <button type="submit">🔍 Cari</button>
        </form>

        <div class="hero-stats">
            <div class="stat-item">
                <span class="stat-number">20</span>
                <span class="stat-label">Film</span>
            </div>
            <div class="stat-item">
                <span class="stat-number">7</span>
                <span class="stat-label">Genre</span>
            </div>
            <div class="stat-item">
                <span class="stat-number">10</span>
                <span class="stat-label">Sutradara</span>
            </div>
        </div>
    </div>
</section>
<?php endif; ?>

<!-- MAIN CONTENT -->
<div class="container">

    <!-- FILTER BAR -->
    <form method="GET" action="index.php">
        <div class="filter-bar">
            <div class="filter-group">
                <label>🎭 Genre</label>
                <select name="genre">
                    <option value="">Semua Genre</option>
                    <?php foreach ($allGenres as $genre): ?>
                        <option value="<?= htmlspecialchars($genre) ?>"
                            <?= $filterGenre === $genre ? 'selected' : '' ?>>
                            <?= htmlspecialchars($genre) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="filter-group">
                <label>🎬 Sutradara</label>
                <input type="text" name="sutradara"
                       placeholder="Nama sutradara..."
                       value="<?= htmlspecialchars($filterSutradara) ?>">
            </div>

            <div class="filter-group" style="max-width:110px">
                <label>📅 Tahun Dari</label>
                <input type="number" name="year_from"
                       placeholder="1990"
                       min="1900" max="2099"
                       value="<?= htmlspecialchars($filterYearFrom) ?>">
            </div>

            <div class="filter-group" style="max-width:110px">
                <label>📅 Tahun Sampai</label>
                <input type="number" name="year_to"
                       placeholder="2024"
                       min="1900" max="2099"
                       value="<?= htmlspecialchars($filterYearTo) ?>">
            </div>

            <button type="submit" class="btn-filter">Filter</button>
            <?php if ($isFiltered): ?>
                <a href="index.php" class="btn-reset">Reset</a>
            <?php endif; ?>
        </div>
    </form>

    <!-- SECTION HEADER -->
    <div class="section-header">
        <h2 class="section-title"><?= htmlspecialchars($pageTitle) ?></h2>
        <span class="section-count"><?= $totalFilms ?> film ditemukan</span>
    </div>

    <!-- FILM GRID -->
    <?php if (empty($films)): ?>
        <div class="empty-state">
            <span class="empty-icon">🎬</span>
            <h3>Tidak ada film ditemukan</h3>
            <p>Coba ubah filter pencarian Anda.</p>
        </div>
    <?php else: ?>
        <div class="films-grid">
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