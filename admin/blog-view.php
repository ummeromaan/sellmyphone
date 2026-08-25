<?php
session_start();

if (!isset($_SESSION['admin_id'])) {
    header("Location: login.php");
    exit();
}
require_once 'includes/db.php';
/**@var mysqli $conn */

$id = (int)($_GET['id'] ?? 0);
$post = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM blog_posts WHERE id=$id"));

if (!$post) {
    header("Location: blog-posts.php");
    exit();
}

$word_count = str_word_count(strip_tags($post['content']));
$read_time  = max(1, (int)ceil($word_count / 200));

// Fix relative image paths inside content so they display correctly from the admin folder
$preview_content = str_replace('src="assets/images/', 'src="../assets/images/', $post['content']);

require 'includes/ad-header.php';
require_once 'includes/sidebar.php';
?>
<link rel="stylesheet" href="home-content.css">
<link rel="stylesheet" href="../assets/css/blog.css">

<div class="container-fluid main-content">

<nav class="navbar navbar-expand-lg">
   <div class="container-fluid d-flex justify-content-between">
        <a class="navbar-brand fw-bold text-dark fs-3" href="#">Blog Preview</a>
        <div>
            <a href="blog-content.php?edit=<?php echo $post['id']; ?>" class="btn up-btn">Edit This Post</a>
            <a href="blog-posts.php" class="btn btn-outline-secondary">Back to All Posts</a>
        </div>
    </div>
</nav><hr class="m-0">

<div class="blog-page-wrap mt-3">
    <div class="blog-title-card">
        <h1><?php echo htmlspecialchars($post['title']); ?></h1>
        <div class="blog-single-meta">
            <span><i class="fa-regular fa-calendar"></i> <?php echo date('F j, Y', strtotime($post['published_date'])); ?></span>
            <span><i class="fa-regular fa-user"></i> <?php echo htmlspecialchars($post['author_name']); ?></span>
            <span><i class="fa-regular fa-clock"></i> <?php echo $read_time; ?> min read</span>
            <span>
                <?php if ($post['status'] == 'published'): ?>
                <span class="fw-bold text-success">Published</span>
                <?php else: ?>
                <span class="fw-bold text-muted">Draft</span>
                <?php endif; ?>
            </span>
        </div>
    </div>

    <div class="blog-single-layout" style="grid-template-columns: 1fr;">
        <div class="blog-single-main">
            <?php if (!empty($post['thumbnail'])): ?>
            <img src="../assets/images/<?php echo htmlspecialchars($post['thumbnail']); ?>" class="blog-single-thumb" alt="<?php echo htmlspecialchars($post['title']); ?>">
            <?php endif; ?>
            <div class="blog-single-content">
                <?php echo $preview_content; ?>
            </div>
        </div>
    </div>
</div>

</div><!--main content-->
</body>
</html>