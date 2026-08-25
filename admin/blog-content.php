<?php
session_start();

if (!isset($_SESSION['admin_id'])) {
    header("Location: login.php");
    exit();
}
require_once 'includes/db.php';
/**@var mysqli $conn */

$msg = "";
if (isset($_SESSION['msg'])) {
    $msg = $_SESSION['msg'];
    unset($_SESSION['msg']);
}

// Convert a title into a URL-friendly slug
function slugify($text) {
    $text = strtolower(trim($text));
    $text = preg_replace('/[^a-z0-9]+/', '-', $text);
    return trim($text, '-');
}

// ==================================================================
// POST handling (save + delete)
// ==================================================================
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['action'])) {

    $action = $_POST['action'];

    if ($action == 'save_blog') {

        $id                = isset($_POST['id']) && $_POST['id'] !== '' ? (int)$_POST['id'] : null;
        $title             = mysqli_real_escape_string($conn, trim($_POST['title'] ?? ''));
        $excerpt           = mysqli_real_escape_string($conn, trim($_POST['excerpt'] ?? ''));
        $content           = mysqli_real_escape_string($conn, $_POST['content'] ?? '');
        $author_name       = mysqli_real_escape_string($conn, trim($_POST['author_name'] ?? 'Sell My Phone'));
        $published_date    = mysqli_real_escape_string($conn, $_POST['published_date'] ?? date('Y-m-d'));
        $status            = $_POST['status'] === 'draft' ? 'draft' : 'published';
        $meta_title        = mysqli_real_escape_string($conn, trim($_POST['meta_title'] ?? ''));
        $meta_description  = mysqli_real_escape_string($conn, trim($_POST['meta_description'] ?? ''));
        $meta_keywords     = mysqli_real_escape_string($conn, trim($_POST['meta_keywords'] ?? ''));
        $meta_robots       = mysqli_real_escape_string($conn, trim($_POST['meta_robots'] ?? 'index, follow'));

        // Build a unique slug from the title
        $base_slug = slugify($title);
        $slug = $base_slug;
        $i = 1;
        while (true) {
            $check_sql = "SELECT id FROM blog_posts WHERE slug='" . mysqli_real_escape_string($conn, $slug) . "'" . ($id ? " AND id != $id" : "");
            $check = mysqli_query($conn, $check_sql);
            if (mysqli_num_rows($check) == 0) break;
            $i++;
            $slug = $base_slug . '-' . $i;
        }

        // Thumbnail upload
        $thumb_sql_part = "";
        if (!empty($_FILES['thumbnail']['name'])) {
            $ext = strtolower(pathinfo($_FILES['thumbnail']['name'], PATHINFO_EXTENSION));
            $new_name = 'blog_' . time() . '_' . rand(1000,9999) . '.' . $ext;
            $dest = '../assets/images/' . $new_name;
            if (move_uploaded_file($_FILES['thumbnail']['tmp_name'], $dest)) {
                $thumb_sql_part = ", thumbnail='" . mysqli_real_escape_string($conn, $new_name) . "'";
            }
        }

        if ($id) {
            $sql = "UPDATE blog_posts SET
                        title='$title', slug='$slug', excerpt='$excerpt', content='$content',
                        author_name='$author_name', published_date='$published_date', status='$status',
                        meta_title='$meta_title', meta_description='$meta_description',
                        meta_keywords='$meta_keywords', meta_robots='$meta_robots'
                        $thumb_sql_part
                    WHERE id=$id";
        } else {
            $thumb_val = '';
            if ($thumb_sql_part) {
                preg_match("/thumbnail='([^']*)'/", $thumb_sql_part, $m);
                $thumb_val = $m[1] ?? '';
            }
            $sql = "INSERT INTO blog_posts
                        (title, slug, excerpt, content, thumbnail, author_name, published_date, status, meta_title, meta_description, meta_keywords, meta_robots)
                    VALUES
                        ('$title', '$slug', '$excerpt', '$content', '$thumb_val', '$author_name', '$published_date', '$status', '$meta_title', '$meta_description', '$meta_keywords', '$meta_robots')";
        }

        mysqli_query($conn, $sql);

        $_SESSION['msg'] = "<div class='alert alert-success w-50 mb-0'>Blog post saved successfully.</div>";
        header("Location: blog-content.php" . ($id ? "?edit=$id" : ""));
        exit();
    }

    if ($action == 'delete_blog') {
        $id = (int)$_POST['id'];
        mysqli_query($conn, "DELETE FROM blog_posts WHERE id=$id");
        $_SESSION['msg'] = "<div class='alert alert-success w-50 mb-0'>Blog post deleted successfully.</div>";
        header("Location: blog-posts.php");
        exit();
    }
}

// ==================================================================
// Load for edit (if ?edit=ID in URL)
// ==================================================================
$editing = null;
if (isset($_GET['edit'])) {
    $eid = (int)$_GET['edit'];
    $editing = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM blog_posts WHERE id=$eid"));
}

require 'includes/ad-header.php';
require_once 'includes/sidebar.php';
?>
<link rel="stylesheet" href="home-content.css">

<!-- CKEditor 5 (Classic build) -->
<script src="https://cdn.ckeditor.com/ckeditor5/41.3.1/classic/ckeditor.js"></script>

<div class="container-fluid main-content">

<nav class="navbar navbar-expand-lg">
   <div class="container-fluid d-flex justify-content-between">
        <a class="navbar-brand fw-bold text-dark fs-3" href="#">Blog Posts</a>
        <a href="blog-posts.php" class="btn up-btn">View All Blog Posts</a>
    </div>
</nav><hr class="m-0">

<div class="mt-3"><?php echo $msg; ?></div>

<!-- ==================== ADD / EDIT FORM ==================== -->
<div class="hc-card mb-4">
    <h5 class="hc-subhead mt-0"><?php echo $editing ? 'Edit Blog Post' : 'Add New Blog Post'; ?></h5>

    <form method="POST" enctype="multipart/form-data" id="blogForm">
        <input type="hidden" name="action" value="save_blog">
        <?php if ($editing): ?>
        <input type="hidden" name="id" value="<?php echo $editing['id']; ?>">
        <?php endif; ?>

        <div class="row g-3">
            <div class="col-md-8">
                <label class="form-label fw-bold">Title</label>
                <input type="text" class="form-control" name="title" required
                       value="<?php echo htmlspecialchars($editing['title'] ?? ''); ?>">
            </div>
            <div class="col-md-2">
                <label class="form-label fw-bold">Author</label>
                <input type="text" class="form-control" name="author_name"
                       value="<?php echo htmlspecialchars($editing['author_name'] ?? 'Sell My Phone'); ?>">
            </div>
            <div class="col-md-2">
                <label class="form-label fw-bold">Published Date</label>
                <input type="date" class="form-control" name="published_date"
                       value="<?php echo htmlspecialchars($editing['published_date'] ?? date('Y-m-d')); ?>">
            </div>

            <div class="col-md-8">
                <label class="form-label fw-bold">Excerpt <small class="text-muted">(short summary shown on the blog grid)</small></label>
                <textarea class="form-control" name="excerpt" rows="2"><?php echo htmlspecialchars($editing['excerpt'] ?? ''); ?></textarea>
            </div>
            <div class="col-md-2">
                <label class="form-label fw-bold">Status</label>
                <select class="form-select" name="status">
                    <option value="published" <?php echo (($editing['status'] ?? 'published') == 'published') ? 'selected' : ''; ?>>Published</option>
                    <option value="draft" <?php echo (($editing['status'] ?? '') == 'draft') ? 'selected' : ''; ?>>Draft</option>
                </select>
            </div>
            <div class="col-md-2">
                <label class="form-label fw-bold">Thumbnail Image</label>
                <input type="file" class="form-control" name="thumbnail" accept="image/*">
                <?php if (!empty($editing['thumbnail'])): ?>
                <img src="../assets/images/<?php echo htmlspecialchars($editing['thumbnail']); ?>" class="hc-preview mt-2">
                <?php endif; ?>
            </div>

            <div class="col-12">
                <label class="form-label fw-bold">Content</label>
                <textarea class="form-control" name="content" id="content" rows="10"><?php echo htmlspecialchars($editing['content'] ?? ''); ?></textarea>
            </div>

            <div class="col-md-6">
                <label class="form-label fw-bold">Meta Title <small class="text-muted">(SEO, optional)</small></label>
                <input type="text" class="form-control" name="meta_title"
                       value="<?php echo htmlspecialchars($editing['meta_title'] ?? ''); ?>">
            </div>
            <div class="col-md-6">
                <label class="form-label fw-bold">Meta Description <small class="text-muted">(SEO, optional)</small></label>
                <input type="text" class="form-control" name="meta_description"
                       value="<?php echo htmlspecialchars($editing['meta_description'] ?? ''); ?>">
            </div>
            <div class="col-md-6">
                <label class="form-label fw-bold">Meta Keywords <small class="text-muted">(comma separated, optional)</small></label>
                <input type="text" class="form-control" name="meta_keywords" placeholder="sell phone dubai, iphone resale, ..."
                       value="<?php echo htmlspecialchars($editing['meta_keywords'] ?? ''); ?>">
            </div>
            <div class="col-md-6">
    <label class="form-label fw-bold">Robots Tag <small class="text-muted">(e.g. index, follow, max-image-preview:large, max-snippet:-1)</small></label>
    <input type="text" class="form-control" name="meta_robots"
           value="<?php echo htmlspecialchars($editing['meta_robots'] ?? 'index, follow, max-image-preview:large, max-snippet:-1'); ?>">
</div>
        </div>

        <button type="submit" class="btn up-btn mt-3"><?php echo $editing ? 'Update Blog Post' : 'Publish Blog Post'; ?></button>
        <?php if ($editing): ?>
        <a href="blog-content.php" class="btn btn-outline-secondary mt-3">Cancel Edit</a>
        <?php endif; ?>
    </form>
</div>

</div><!--main content-->

<script>
    // Initialize CKEditor on the content textarea
    let blogEditor;
    ClassicEditor
        .create(document.querySelector('#content'), {
            simpleUpload: {
                uploadUrl: 'ckeditor-upload.php'
            }
        })
        .then(editor => {
            blogEditor = editor;
        })
        .catch(error => {
            console.error(error);
        });

    // Sync CKEditor content back into the textarea before submitting the form
    document.getElementById('blogForm').addEventListener('submit', function () {
        if (blogEditor) {
            document.getElementById('content').value = blogEditor.getData();
        }
    });
</script>

</body>
</html>