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

$all_blogs = mysqli_query($conn, "SELECT * FROM blog_posts ORDER BY published_date DESC, id DESC");

require 'includes/ad-header.php';
require_once 'includes/sidebar.php';
?>
<link rel="stylesheet" href="home-content.css">
<link rel="stylesheet" href="blog-posts.css">

<div class="container-fluid main-content">

<nav class="navbar navbar-expand-lg">
   <div class="container-fluid d-flex justify-content-between">
        <a class="navbar-brand fw-bold text-dark fs-3" href="#">All Blog Posts</a>
        <a href="blog-content.php" class="btn up-btn">+ Add New Blog Post</a>
    </div>
</nav><hr class="m-0">

<div class="mt-3"><?php echo $msg; ?></div>

<div class="admin-blog-grid mt-4">
    <?php if (mysqli_num_rows($all_blogs) === 0): ?>
    <p class="text-muted">No blog posts yet.</p>
    <?php endif; ?>

    <?php while ($b = mysqli_fetch_assoc($all_blogs)): ?>
    <div class="admin-blog-card">
        <a href="blog-view.php?id=<?php echo $b['id']; ?>" class="admin-blog-card-img-wrap">
            <?php if (!empty($b['thumbnail'])): ?>
            <img src="../assets/images/<?php echo htmlspecialchars($b['thumbnail']); ?>" alt="<?php echo htmlspecialchars($b['title']); ?>">
            <?php else: ?>
            <img src="../assets/images/blog-placeholder.jpg" alt="<?php echo htmlspecialchars($b['title']); ?>">
            <?php endif; ?>
            <span class="admin-blog-status-badge <?php echo $b['status'] == 'published' ? 'status-published' : 'status-draft'; ?>">
                <?php echo ucfirst($b['status']); ?>
            </span>
        </a>
        <div class="admin-blog-card-body">
            <a href="blog-view.php?id=<?php echo $b['id']; ?>" class="admin-blog-card-title">
                <?php echo htmlspecialchars($b['title']); ?>
            </a>
            <p class="admin-blog-card-author"><?php echo htmlspecialchars($b['author_name']); ?></p>
            <div class="admin-blog-card-footer">
                <span><i class="fa-regular fa-calendar"></i> <?php echo $b['published_date'] ? date('M j, Y', strtotime($b['published_date'])) : '-'; ?></span>
            </div>
            <div class="admin-blog-card-actions mt-2">
                <a href="blog-view.php?id=<?php echo $b['id']; ?>" class="btn btn-sm btn-outline-secondary">View</a>
                <a href="blog-content.php?edit=<?php echo $b['id']; ?>" class="btn btn-sm up-btn">Edit</a>
                <form method="POST" action="blog-content.php" class="d-inline" onsubmit="return confirm('Delete this blog post?');">
                    <input type="hidden" name="action" value="delete_blog">
                    <input type="hidden" name="id" value="<?php echo $b['id']; ?>">
                    <button type="submit" class="btn btn-sm btn-outline-danger">Delete</button>
                </form>
            </div>
        </div>
    </div>
    <?php endwhile; ?>
</div>

</div><!--main content-->
</body>
</html>