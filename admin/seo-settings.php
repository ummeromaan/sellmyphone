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
    home_row_update($conn, 'page_seo', $id, ['page_title', 'meta_description', 'meta_keywords']);
    $_SESSION['msg'] = "SEO details updated successfully.";
    header("Location: seo-settings.php");
    exit();
}

// Fetch all pages
$pages = [];
$result = mysqli_query($conn, "SELECT * FROM page_seo ORDER BY id ASC");
if ($result) {
    while ($row = mysqli_fetch_assoc($result)) {
        $pages[] = $row;
    }
}

require 'includes/ad-header.php';
require_once 'includes/sidebar.php';
?>

<div class="container-fluid main-content">

    <h2 class="fw-bold mb-4">SEO Settings</h2>

    <?php if ($msg): ?>
        <div class="alert alert-success"><?php echo htmlspecialchars($msg); ?></div>
    <?php endif; ?>

    <div class="accordion" id="seoAccordion">
        <?php foreach ($pages as $i => $p): ?>
            <div class="accordion-item mb-3">
                <h2 class="accordion-header">
                    <button class="accordion-button <?php echo $i > 0 ? 'collapsed' : ''; ?>" type="button"
                            data-bs-toggle="collapse" data-bs-target="#seo<?php echo $p['id']; ?>">
                        <?php echo htmlspecialchars(ucfirst($p['page_slug'])); ?> Page
                    </button>
                </h2>
                <div id="seo<?php echo $p['id']; ?>"
                     class="accordion-collapse collapse <?php echo $i == 0 ? 'show' : ''; ?>"
                     data-bs-parent="#seoAccordion">
                    <div class="accordion-body">
                        <form method="POST">
                            <input type="hidden" name="id" value="<?php echo $p['id']; ?>">

                            <div class="mb-3">
                                <label class="form-label">Page Title</label>
                                <input type="text" name="page_title" class="form-control" maxlength="255"
                                       value="<?php echo htmlspecialchars($p['page_title']); ?>" required>
                                <small class="text-muted">Recommended: 50-60 characters</small>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Meta Description</label>
                                <textarea name="meta_description" class="form-control" rows="3" maxlength="500" required><?php echo htmlspecialchars($p['meta_description']); ?></textarea>
                                <small class="text-muted">Recommended: 150-160 characters</small>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Meta Keywords</label>
                                <input type="text" name="meta_keywords" class="form-control" maxlength="500"
                                       value="<?php echo htmlspecialchars($p['meta_keywords']); ?>">
                                <small class="text-muted">Comma separated</small>
                            </div>

                            <button type="submit" name="update_seo" class="btn btn-warning">Save Changes</button>
                        </form>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    </div>

</div>
</body>
</html>