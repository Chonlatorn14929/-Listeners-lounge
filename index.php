<?php
$pageTitle = 'Discover Music';
require_once __DIR__ . '/includes/config.php';

$conn = getConnection();

// Get 8 random albums for featured section
$featuredResult = $conn->query("SELECT * FROM albums ORDER BY RAND() LIMIT 8");
$featured = $featuredResult->fetch_all(MYSQLI_ASSOC);

// Get recently reviewed albums (with ratings)
$recentResult = $conn->query("
    SELECT a.*, AVG(r.rating) as avg_rating, COUNT(r.id) as review_count
    FROM albums a
    JOIN reviews r ON a.id = r.album_id
    GROUP BY a.id
    ORDER BY MAX(r.created_at) DESC
    LIMIT 4
");
$recentReviewed = $recentResult->fetch_all(MYSQLI_ASSOC);

// Genre counts
$genreResult = $conn->query("SELECT genre, COUNT(*) as count FROM albums GROUP BY genre ORDER BY count DESC");
$genres = $genreResult->fetch_all(MYSQLI_ASSOC);

$conn->close();
?>
<?php require_once __DIR__ . '/includes/header.php'; ?>

<!-- HERO -->
<section class="hero">
    <div class="hero-eyebrow">🎵 Music Collection · <?= date('Y') ?></div>
    <h1>Discover &amp; Review<br>Your <em>Favourite</em> Albums</h1>
    <p>Join thousands of music lovers sharing opinions on the best albums across every genre.</p>
    <?php if (!isLoggedIn()): ?>
        <a href="/listeners_lounge/auth.php?mode=register" class="hero-cta">Get Started →</a>
    <?php else: ?>
        <a href="/listeners_lounge/search.php" class="hero-cta">Browse All Albums →</a>
    <?php endif; ?>
</section>

<!-- FEATURED ALBUMS -->
<section class="section">
    <div class="section-header">
        <h2 class="section-title">Featured Albums</h2>
        <a href="/listeners_lounge/search.php" class="section-link">View all →</a>
    </div>
    <div class="album-grid album-grid--large">
        <?php foreach ($featured as $album):
            $ratingData = getAverageRating($conn ?? getConnection(), $album['id']);
            $avg = $ratingData['avg_rating'] ? round($ratingData['avg_rating'], 1) : null;
            $count = $ratingData['count'];
        ?>
        <a href="/listeners_lounge/album.php?id=<?= $album['id'] ?>" class="album-card">
            <div class="album-cover" style="background: <?= h($album['cover_color']) ?>;">
                <?= h($album['cover_emoji']) ?>
            </div>
            <div class="album-card-info">
                <div class="album-card-title"><?= h($album['title']) ?></div>
                <div class="album-card-artist"><?= h($album['artist']) ?></div>
                <div class="album-card-meta">
                    <span class="album-card-genre"><?= h($album['genre']) ?></span>
                    <?php if ($avg): ?>
                    <div class="rating-display">
                        <span class="rating-score">★ <?= $avg ?></span>
                        <span>(<?= $count ?>)</span>
                    </div>
                    <?php endif; ?>
                </div>
            </div>
        </a>
        <?php endforeach; ?>
    </div>
</section>

<!-- BROWSE BY GENRE -->
<section class="section" style="padding-top: 0;">
    <div class="section-header">
        <h2 class="section-title">Browse by Genre</h2>
    </div>
    <div style="display: flex; flex-wrap: wrap; gap: 10px;">
        <?php foreach ($genres as $g): ?>
        <a href="/listeners_lounge/genre.php?genre=<?= urlencode($g['genre']) ?>" 
           style="background: var(--bg2); border: 1px solid var(--border); padding: 8px 20px; border-radius: 99px; font-size: 0.88rem; transition: all 0.2s; display: flex; align-items: center; gap: 8px;"
           onmouseover="this.style.borderColor='var(--accent)'; this.style.color='var(--accent)'"
           onmouseout="this.style.borderColor=''; this.style.color=''">
            <?= h($g['genre']) ?>
            <span style="background: var(--bg3); padding: 1px 7px; border-radius: 99px; font-size: 0.75rem; color: var(--muted);"><?= $g['count'] ?></span>
        </a>
        <?php endforeach; ?>
    </div>
</section>

<!-- RECENTLY REVIEWED -->
<?php if (!empty($recentReviewed)): ?>
<section class="section" style="padding-top: 0;">
    <div class="section-header">
        <h2 class="section-title">Recently Reviewed</h2>
    </div>
    <div class="album-grid">
        <?php foreach ($recentReviewed as $album): ?>
        <a href="/listeners_lounge/album.php?id=<?= $album['id'] ?>" class="album-card">
            <div class="album-cover" style="background: <?= h($album['cover_color']) ?>;">
                <?= h($album['cover_emoji']) ?>
            </div>
            <div class="album-card-info">
                <div class="album-card-title"><?= h($album['title']) ?></div>
                <div class="album-card-artist"><?= h($album['artist']) ?></div>
                <div class="album-card-meta">
                    <span class="album-card-genre"><?= h($album['genre']) ?></span>
                    <div class="rating-display">
                        <span class="rating-score">★ <?= round($album['avg_rating'], 1) ?></span>
                    </div>
                </div>
            </div>
        </a>
        <?php endforeach; ?>
    </div>
</section>
<?php endif; ?>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
