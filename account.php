<?php
require_once __DIR__ . '/includes/config.php';

if (!isLoggedIn()) redirect('/listeners_lounge/auth.php');

$conn = getConnection();
$uid = $_SESSION['user_id'];
$pageTitle = 'My Account';

// Get user info
$stmt = $conn->prepare("SELECT * FROM users WHERE id = ?");
$stmt->bind_param("i", $uid);
$stmt->execute();
$user = $stmt->get_result()->fetch_assoc();

// Handle profile update
$updateErrors = [];
$updateSuccess = false;
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';

    if ($action === 'update_profile') {
        $newUsername = trim($_POST['username'] ?? '');
        $newEmail = trim($_POST['email'] ?? '');
        
        if (strlen($newUsername) < 3) {
            $updateErrors[] = 'Username must be at least 3 characters.';
        } elseif (!filter_var($newEmail, FILTER_VALIDATE_EMAIL)) {
            $updateErrors[] = 'Invalid email address.';
        } else {
            // Check unique
            $s = $conn->prepare("SELECT id FROM users WHERE (username = ? OR email = ?) AND id != ?");
            $s->bind_param("ssi", $newUsername, $newEmail, $uid);
            $s->execute();
            if ($s->get_result()->fetch_assoc()) {
                $updateErrors[] = 'Username or email already taken.';
            } else {
                $s2 = $conn->prepare("UPDATE users SET username = ?, email = ? WHERE id = ?");
                $s2->bind_param("ssi", $newUsername, $newEmail, $uid);
                $s2->execute();
                $_SESSION['username'] = $newUsername;
                $_SESSION['flash'] = ['type' => 'success', 'message' => 'Profile updated successfully!'];
                redirect('/listeners_lounge/account.php#settings');
            }
        }
    }

    if ($action === 'update_password') {
        $current = $_POST['current_password'] ?? '';
        $new = $_POST['new_password'] ?? '';
        $confirm = $_POST['confirm_password'] ?? '';

        if (!password_verify($current, $user['password'])) {
            $updateErrors[] = 'Current password is incorrect.';
        } elseif (strlen($new) < 8) {
            $updateErrors[] = 'New password must be at least 8 characters.';
        } elseif ($new !== $confirm) {
            $updateErrors[] = 'New passwords do not match.';
        } else {
            $hash = password_hash($new, PASSWORD_DEFAULT);
            $s = $conn->prepare("UPDATE users SET password = ? WHERE id = ?");
            $s->bind_param("si", $hash, $uid);
            $s->execute();
            $_SESSION['flash'] = ['type' => 'success', 'message' => 'Password updated!'];
            redirect('/listeners_lounge/account.php#settings');
        }
    }

    if ($action === 'delete_review') {
        $rid = intval($_POST['review_id'] ?? 0);
        $s = $conn->prepare("DELETE FROM reviews WHERE id = ? AND user_id = ?");
        $s->bind_param("ii", $rid, $uid);
        $s->execute();
        $_SESSION['flash'] = ['type' => 'success', 'message' => 'Review deleted.'];
        redirect('/listeners_lounge/account.php#reviews');
    }

    if ($action === 'remove_favourite') {
        $aid = intval($_POST['album_id'] ?? 0);
        $s = $conn->prepare("DELETE FROM favourites WHERE album_id = ? AND user_id = ?");
        $s->bind_param("ii", $aid, $uid);
        $s->execute();
        $_SESSION['flash'] = ['type' => 'success', 'message' => 'Removed from favourites.'];
        redirect('/listeners_lounge/account.php#favourites');
    }
}

// Get reviews
$reviewsStmt = $conn->prepare("
    SELECT r.*, a.title as album_title, a.artist, a.cover_color, a.cover_emoji, a.id as album_id
    FROM reviews r
    JOIN albums a ON r.album_id = a.id
    WHERE r.user_id = ?
    ORDER BY r.created_at DESC
");
$reviewsStmt->bind_param("i", $uid);
$reviewsStmt->execute();
$reviews = $reviewsStmt->get_result()->fetch_all(MYSQLI_ASSOC);

// Get favourites
$favsStmt = $conn->prepare("
    SELECT f.*, a.title, a.artist, a.cover_color, a.cover_emoji, a.id as album_id
    FROM favourites f
    JOIN albums a ON f.album_id = a.id
    WHERE f.user_id = ?
    ORDER BY f.created_at DESC
");
$favsStmt->bind_param("i", $uid);
$favsStmt->execute();
$favourites = $favsStmt->get_result()->fetch_all(MYSQLI_ASSOC);

$avgRating = count($reviews) > 0 ? round(array_sum(array_column($reviews, 'rating')) / count($reviews), 1) : null;
$memberSince = date('M Y', strtotime($user['created_at']));

$conn->close();
?>
<?php require_once __DIR__ . '/includes/header.php'; ?>

<div class="account-page">
    <!-- ACCOUNT HEADER -->
    <div class="account-header">
        <div class="account-avatar">
            <?= strtoupper(substr($user['username'], 0, 1)) ?>
        </div>
        <div>
            <div class="account-name"><?= h($user['username']) ?></div>
            <div class="account-meta"><?= h($user['email']) ?> · Member since <?= $memberSince ?></div>
            <div class="account-stats">
                <div class="stat-chip">
                    <div class="stat-num"><?= count($reviews) ?></div>
                    <div class="stat-label">Reviews</div>
                </div>
                <div class="stat-chip">
                    <div class="stat-num"><?= count($favourites) ?></div>
                    <div class="stat-label">Favourites</div>
                </div>
                <?php if ($avgRating): ?>
                <div class="stat-chip">
                    <div class="stat-num"><?= $avgRating ?> ★</div>
                    <div class="stat-label">Avg Rating</div>
                </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- TABS -->
    <div class="account-tabs">
        <div class="account-tab active" data-panel="reviews">My Reviews (<?= count($reviews) ?>)</div>
        <div class="account-tab" data-panel="favourites">Favourites (<?= count($favourites) ?>)</div>
        <div class="account-tab" data-panel="settings">Settings</div>
    </div>

    <!-- REVIEWS PANEL -->
    <div class="account-panel active" id="reviews">
        <?php if (empty($reviews)): ?>
        <div class="empty-state">
            <div class="empty-state-icon">📝</div>
            <h3>No reviews yet</h3>
            <p>Browse albums and share your thoughts!</p>
            <a href="/listeners_lounge/index.php" class="btn btn--accent" style="width: auto; display: inline-flex;">Browse Albums</a>
        </div>
        <?php else: ?>
            <?php foreach ($reviews as $review): ?>
            <div class="my-review-card">
                <a href="/listeners_lounge/album.php?id=<?= $review['album_id'] ?>">
                    <div class="my-review-cover" style="background: <?= h($review['cover_color']) ?>;">
                        <?= h($review['cover_emoji']) ?>
                    </div>
                </a>
                <div class="my-review-body">
                    <a href="/listeners_lounge/album.php?id=<?= $review['album_id'] ?>">
                        <div class="my-review-title"><?= h($review['album_title']) ?> — <?= h($review['artist']) ?></div>
                    </a>
                    <?= renderStars($review['rating']) ?>
                    <div class="my-review-text"><?= h($review['review_text']) ?></div>
                    <div class="my-review-actions">
                        <a href="/listeners_lounge/album.php?id=<?= $review['album_id'] ?>#review-form" class="btn btn--outline btn--sm">Edit</a>
                        <form method="POST" style="display: inline;">
                            <input type="hidden" name="action" value="delete_review">
                            <input type="hidden" name="review_id" value="<?= $review['id'] ?>">
                            <button type="submit" class="btn btn--danger btn--sm" 
                                    data-confirm="Delete this review? This cannot be undone.">Delete</button>
                        </form>
                    </div>
                </div>
                <div style="color: var(--muted); font-size: 0.75rem; flex-shrink: 0;">
                    <?= date('M j, Y', strtotime($review['created_at'])) ?>
                </div>
            </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>

    <!-- FAVOURITES PANEL -->
    <div class="account-panel" id="favourites">
        <?php if (empty($favourites)): ?>
        <div class="empty-state">
            <div class="empty-state-icon">♡</div>
            <h3>No favourites yet</h3>
            <p>Add albums to your favourites from their detail pages.</p>
            <a href="/listeners_lounge/index.php" class="btn btn--accent" style="width: auto; display: inline-flex;">Browse Albums</a>
        </div>
        <?php else: ?>
        <div class="favourites-grid">
            <?php foreach ($favourites as $fav): ?>
            <div class="fav-card">
                <a href="/listeners_lounge/album.php?id=<?= $fav['album_id'] ?>">
                    <div class="album-cover" style="background: <?= h($fav['cover_color']) ?>; border-radius: 0; font-size: 2.5rem;">
                        <?= h($fav['cover_emoji']) ?>
                    </div>
                </a>
                <div class="fav-card-info">
                    <div class="fav-card-title"><?= h($fav['title']) ?></div>
                    <div class="fav-card-artist"><?= h($fav['artist']) ?></div>
                    <form method="POST" style="margin-top: 8px;">
                        <input type="hidden" name="action" value="remove_favourite">
                        <input type="hidden" name="album_id" value="<?= $fav['album_id'] ?>">
                        <button type="submit" class="btn btn--danger btn--sm" style="width: 100%;"
                                data-confirm="Remove from favourites?">Remove</button>
                    </form>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
        <?php endif; ?>
    </div>

    <!-- SETTINGS PANEL -->
    <div class="account-panel" id="settings">
        <?php if (!empty($updateErrors)): ?>
        <div class="flash flash--error" style="border-radius: 8px; margin-bottom: 20px; border: none;">
            <?= implode('<br>', array_map('htmlspecialchars', $updateErrors)) ?>
        </div>
        <?php endif; ?>

        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 24px; flex-wrap: wrap;">
            <!-- Update Profile -->
            <div class="review-form-card" style="background: var(--bg2);">
                <h3>Profile Information</h3>
                <form method="POST" class="edit-profile-form">
                    <input type="hidden" name="action" value="update_profile">
                    <div class="form-group">
                        <label class="form-label">Username</label>
                        <input type="text" name="username" class="form-input" value="<?= h($user['username']) ?>">
                    </div>
                    <div class="form-group">
                        <label class="form-label">Email</label>
                        <input type="email" name="email" class="form-input" value="<?= h($user['email']) ?>">
                    </div>
                    <button type="submit" class="btn btn--accent" style="width: auto;">Save Changes</button>
                </form>
            </div>

            <!-- Change Password -->
            <div class="review-form-card" style="background: var(--bg2);">
                <h3>Change Password</h3>
                <form method="POST">
                    <input type="hidden" name="action" value="update_password">
                    <div class="form-group">
                        <label class="form-label">Current Password</label>
                        <input type="password" name="current_password" class="form-input" placeholder="Enter current password">
                    </div>
                    <div class="form-group">
                        <label class="form-label">New Password</label>
                        <input type="password" name="new_password" class="form-input" placeholder="Min. 8 characters">
                    </div>
                    <div class="form-group">
                        <label class="form-label">Confirm New Password</label>
                        <input type="password" name="confirm_password" class="form-input" placeholder="Repeat new password">
                    </div>
                    <button type="submit" class="btn btn--outline" style="width: auto;">Update Password</button>
                </form>
            </div>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
