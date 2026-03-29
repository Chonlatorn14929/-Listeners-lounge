<?php
require_once __DIR__ . '/includes/config.php';

$conn = getConnection();
$genre = trim($_GET['genre'] ?? '');

$validGenres = ['Electronic', 'R&B', 'Pop', 'Rock', 'Hip-Hop', 'Jazz', 'Country', 'Indie', 'Classical', 'Metal'];
if (!in_array($genre, $validGenres)) redirect('/listeners_lounge/index.php');

$pageTitle = $genre . ' Albums';

$stmt = $conn->prepare("
    SELECT a.*, AVG(r.rating) as avg_rating, COUNT(r.id) as review_count
    FROM albums a
    LEFT JOIN reviews r ON a.id = r.album_id
    WHERE a.genre = ?
    GROUP BY a.id
    ORDER BY avg_rating DESC, a.title
");
$stmt->bind_param("s", $genre);
$stmt->execute();
$albums = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);

$conn->close();
?>
<?php require_once __DIR__ . '/includes/header.php'; ?>

<div class="genre-hero">
    <div class="breadcrumb"><a href="/listeners_lounge/index.php">Home</a> / <?= h($genre) ?></div>
    <div class="genre-badge">Genre · <span><?= h($genre) ?></span></div>
    <h1 style="font-family: var(--font-display); font-size: clamp(2rem, 5vw, 3.5rem); font-weight: 800; letter-spacing: -0.02em; margin-bottom: 8px;">
        <?= h($genre) ?> Albums
    </h1>
    <p style="color: var(--muted);"><?= count($albums) ?> album<?= count($albums) !== 1 ? 's' : '' ?> in our collection</p>
</div>

<div class="section" style="padding-top: 8px;">
    <?php if (empty($albums)): ?>
    <div class="empty-state">
        <div class="empty-state-icon">🎵</div>
        <h3>No albums yet</h3>
        <p>No <?= h($genre) ?> albums in the collection yet.</p>
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
                    <span class="album-card-genre"><?= h($album['release_year']) ?></span>
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
