<?php
session_start();


// Block direct access - must be logged in to view this page
if (!isset($_SESSION['admin_id'])) {
    header("Location: login.php");
    exit();
}

require_once 'includes/db.php';
require_once 'includes/home-helpers.php';
/**@var mysqli $conn */

$msg = "";
if (isset($_SESSION['msg'])) {
    $msg = $_SESSION['msg'];
    unset($_SESSION['msg']);
}

// Handle form submit
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['update_seo'])) {
    $id = intval($_POST['id']);
    home_row_update($conn, 'page_seo', $id, ['page_title', 'meta_description', 'meta_keywords', 'meta_robots']);
    $_SESSION['msg'] = "SEO details updated successfully.";
    header("Location: seo-settings.php?tab=" . urlencode($_POST['tab'] ?? ''));
    exit();
}

// Fetch all pages (keyed by slug so we can look up the active tab)
$pages = [];
$result = mysqli_query($conn, "SELECT * FROM page_seo ORDER BY id ASC");
if ($result) {
    while ($row = mysqli_fetch_assoc($result)) {
        $pages[$row['page_slug']] = $row;
    }
}

// which tab to show (falls back to the first page in the list)
$firstSlug = array_key_first($pages) ?? '';
$tab = $_GET['tab'] ?? $firstSlug;
if (!isset($pages[$tab])) {
    $tab = $firstSlug;
}
$activeSeo = $pages[$tab] ?? null;

// icon per page slug (falls back to a generic file icon)
$icons = [
    'home'         => 'fa-house',
    'about'        => 'fa-circle-info',
    'apple'        => 'fa-mobile-screen',
    'samsung'      => 'fa-mobile-screen-button',
    'contact'      => 'fa-envelope',
    'blog'         => 'fa-newspaper',
    'sell-iphone'  => 'fa-mobile-screen',
    'sell-samsung' => 'fa-mobile-screen-button',
];

// robots dropdown options
$robots_options = [
    'index, follow'     => 'Index & Follow (default - show in search, follow links)',
    'noindex, follow'   => 'Noindex & Follow (hide from search, still follow links)',
    'index, nofollow'   => 'Index & Nofollow (show in search, do not follow links)',
    'noindex, nofollow' => 'Noindex & Nofollow (fully hide from search)',
];

require 'includes/ad-header.php';
require_once 'includes/sidebar.php';
?>

<div class="container-fluid main-content">

<nav class="navbar navbar-expand-lg">
   <div class="container-fluid">
        <a class="navbar-brand fw-bold text-dark fs-3" href="#">SEO Settings</a>
    </div>
</nav><hr class="m-0">

<?php if ($msg): ?>
    <div class="alert alert-success mt-3"><?php echo htmlspecialchars($msg); ?></div>
<?php endif; ?>

<div class="ct-shell mt-3">

    <!-- Section nav -->
    <div class="ct-nav nav flex-column" role="tablist">
        <?php foreach ($pages as $slug => $p): ?>
        <a class="nav-link <?php echo $tab === $slug ? 'active' : ''; ?>" href="?tab=<?php echo urlencode($slug); ?>">
            <i class="fa-solid <?php echo $icons[$slug] ?? 'fa-file'; ?>"></i>
            <?php echo htmlspecialchars(ucwords(str_replace('-', ' ', $slug))); ?> Page
        </a>
        <?php endforeach; ?>
    </div>

    <!-- Panel -->
    <div class="ct-panel">

    <?php if ($activeSeo): ?>

        <div class="ct-panel-header">
            <div>
                <h3 class="ct-panel-title"><?php echo htmlspecialchars(ucwords(str_replace('-', ' ', $tab))); ?> Page SEO</h3>
                <p class="ct-panel-sub">Title, description, keywords and search-engine indexing for this page.</p>
            </div>
        </div>

        <form method="POST" class="hc-card">
            <input type="hidden" name="tab" value="<?php echo htmlspecialchars($tab); ?>">
            <input type="hidden" name="id" value="<?php echo $activeSeo['id']; ?>">

            <div class="row g-3">
                <div class="col-12">
                    <label class="form-label fw-bold">Page Title</label>
                    <input type="text" class="form-control" name="page_title" maxlength="255"
                           value="<?php echo htmlspecialchars($activeSeo['page_title']); ?>" required>
                    <small class="text-muted">Recommended: 50-60 characters</small>
                </div>

                <div class="col-12">
                    <label class="form-label fw-bold">Meta Description</label>
                    <textarea class="form-control" name="meta_description" rows="3" maxlength="500" required><?php echo htmlspecialchars($activeSeo['meta_description']); ?></textarea>
                    <small class="text-muted">Recommended: 150-160 characters</small>
                </div>

                <div class="col-12">
                    <label class="form-label fw-bold">Meta Keywords</label>
                    <input type="text" class="form-control" name="meta_keywords" maxlength="500"
                           value="<?php echo htmlspecialchars($activeSeo['meta_keywords']); ?>">
                    <small class="text-muted">Comma separated</small>
                </div>

                <div class="col-12">
                    <label class="form-label fw-bold">Robots (Search Engine Indexing)</label>
                    <select class="form-select" name="meta_robots">
                        <?php $currentRobots = $activeSeo['meta_robots'] ?? 'index, follow'; ?>
                        <?php foreach ($robots_options as $value => $label): ?>
                        <option value="<?php echo htmlspecialchars($value); ?>" <?php echo $currentRobots === $value ? 'selected' : ''; ?>>
                            <?php echo htmlspecialchars($label); ?>
                        </option>
                        <?php endforeach; ?>
                    </select>
                    <small class="text-muted">Controls whether search engines index this page and follow its links.</small>
                </div>
            </div>

            <button type="submit" name="update_seo" class="btn btn-warning mt-3">Save Changes</button>
        </form>

    <?php else: ?>
        <div class="ct-soon">
            <i class="fa-solid fa-triangle-exclamation"></i>
            <p>No SEO rows found in the database yet.</p>
        </div>
    <?php endif; ?>

    </div>

</div>

</div>