<?php
require_once __DIR__ . '/includes/config.php';

$conn = getConnection();
$q = trim($_GET['q'] ?? '');
$pageTitle = $q ? "Search: $q" : 'Browse All Albums';

$albums = [];
if ($q) {
    $search = "%$q%";
    $stmt = $conn->prepare("
        SELECT a.*, AVG(r.rating) as avg_rating, COUNT(r.id) as review_count
        FROM albums a
        LEFT JOIN reviews r ON a.id = r.album_id
        WHERE a.title LIKE ? OR a.artist LIKE ?
        GROUP BY a.id
        ORDER BY a.title
    ");
    $stmt->bind_param("ss", $search, $search);
    $stmt->execute();
    $albums = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
} else {
    $result = $conn->query("
        SELECT a.*, AVG(r.rating) as avg_rating, COUNT(r.id) as review_count
        FROM albums a
        LEFT JOIN reviews r ON a.id = r.album_id
        GROUP BY a.id
        ORDER BY a.title
    ");
    $albums = $result->fetch_all(MYSQLI_ASSOC);
}
$conn->close();
?>
<?php require_once __DIR__ . '/includes/header.php'; ?>

<div class="search-page">
    <div class="page-header" style="padding: 0 0 32px;">
        <h1><?= $q ? 'Search Results' : 'Browse All Albums' ?></h1>
        <?php if ($q): ?>
        <p class="search-info">
            Found <strong><?= count($albums) ?></strong> result<?= count($albums) !== 1 ? 's' : '' ?> for "<strong><?= h($q) ?></strong>"
            · <a href="/listeners_lounge/search.php" style="color: var(--accent)">Browse all</a>
        </p>
        <?php else: ?>
        <p class="text-muted"><?= count($albums) ?> albums in the collection</p>
        <?php endif; ?>
    </div>

    <?php if (empty($albums)): ?>
    <div class="empty-state">
        <div class="empty-state-icon">🔍</div>
        <h3>No albums found</h3>
        <p>Try a different search term or browse all albums.</p>
        <a href="/listeners_lounge/search.php" class="btn btn--accent" style="width: auto; display: inline-flex;">Browse All Albums</a>
    </div>
    <?php else: ?>
    <div class="album-grid album-grid--large">
        <?php foreach ($albums as $album): ?>
        <a href="/listeners_lounge/album.php?id=<?= $album['id'] ?>" class="album-card">
            <div class="album-cover" style="background: <?= h($album['cover_color']) ?>;">
                <?= h($album['cover_emoji']) ?>
            </div>
            <div class="album-card-info">
                <div class="album-card-title"><?= h($album['title']) ?></div>
                <div class="album-card-artist"><?= h($album['artist']) ?></div>
                <div class="album-card-meta">
                    <span class="album-card-genre"><?= h($album['genre']) ?></span>
                    <?php if ($album['avg_rating']): ?>
                    <div class="rating-display">
                        <span class="rating-score">★ <?= round($album['avg_rating'], 1) ?></span>
                        <span>(<?= $album['review_count'] ?>)</span>
                    </div>
                    <?php endif; ?>
                </div>
            </div>
        </a>
        <?php endforeach; ?>
    </div>
    <?php endif; ?>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
