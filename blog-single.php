<?php
require_once 'admin/includes/db.php';
/**@var mysqli $conn */

$slug = $_GET['slug'] ?? '';
$post = null;

if ($slug !== '') {
    $safe_slug = mysqli_real_escape_string($conn, $slug);
    $post = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM blog_posts WHERE slug='$safe_slug' AND status='published' LIMIT 1"));
}

if (!$post) {
    header("Location: blog.php");
    exit();
}

$page_title = htmlspecialchars($post['meta_title'] ?: $post['title']) . " | SellMyPhone UAE";
$meta_description = htmlspecialchars($post['meta_description'] ?: $post['excerpt']);

// ---------------- Read time ----------------
$word_count = str_word_count(strip_tags($post['content']));
$read_time  = max(1, (int)ceil($word_count / 200));

// ---------------- Related Articles (title word match) ----------------
$stopwords = ['the','and','for','with','from','your','you','how','best','are','can','sell','phone','phones','dubai','uae','a','an','in','on','to','of','is','it'];
$words = preg_split('/\s+/', strtolower(preg_replace('/[^a-z0-9\s]/i', '', $post['title'])));
$keywords = array_filter($words, function($w) use ($stopwords) {
    return strlen($w) > 3 && !in_array($w, $stopwords);
});

$related = [];
if (!empty($keywords)) {
    $like_parts = [];
    foreach ($keywords as $kw) {
        $kw_safe = mysqli_real_escape_string($conn, $kw);
        $like_parts[] = "title LIKE '%$kw_safe%'";
    }
    $where_likes = implode(' OR ', $like_parts);
    $rel_sql = "SELECT id, title, slug, thumbnail, published_date FROM blog_posts
                WHERE status='published' AND id != {$post['id']} AND ($where_likes)
                ORDER BY published_date DESC LIMIT 3";
    $rel_result = mysqli_query($conn, $rel_sql);
    while ($r = mysqli_fetch_assoc($rel_result)) {
        $related[] = $r;
    }
}


if (empty($related)) {
    $rel_sql = "SELECT id, title, slug, thumbnail, published_date FROM blog_posts
                WHERE status='published' AND id != {$post['id']}
                ORDER BY published_date DESC LIMIT 3";
    $rel_result = mysqli_query($conn, $rel_sql);
    while ($r = mysqli_fetch_assoc($rel_result)) {
        $related[] = $r;
    }
}
?>

<?php include 'includes/header.php'; ?>

<div class="container blog-page-wrap">

    <a href="<?php echo BASE_URL; ?>blog" class="blog-back-link"><i class="fa-solid fa-arrow-left"></i> Back to Blogs</a>

    <div class="blog-title-card">
        <h1><?php echo htmlspecialchars($post['title']); ?></h1>
        <div class="blog-single-meta">
            <span><i class="fa-regular fa-calendar"></i> <?php echo date('F j, Y', strtotime($post['published_date'])); ?></span>
            <span><i class="fa-regular fa-user"></i> <?php echo htmlspecialchars($post['author_name']); ?></span>
            <span><i class="fa-regular fa-clock"></i> <?php echo $read_time; ?> min read</span>
        </div>
    </div>

    <div class="blog-single-layout">

        <div class="blog-single-main">
            <?php if (!empty($post['thumbnail'])): ?>
            <img src="<?php echo BASE_URL; ?>assets/images/<?php echo htmlspecialchars($post['thumbnail']); ?>" class="blog-single-thumb" alt="<?php echo htmlspecialchars($post['title']); ?>">
            <?php endif; ?>

            <div class="blog-single-content">
                <?php echo $post['content']; // stored HTML from admin (h2, h3, p tags), intentionally not escaped ?>
            </div>
        </div>

        <?php if (!empty($related)): ?>
        <aside class="related-sidebar">
            <h3>Related Articles</h3>
            <?php foreach ($related as $rel): ?>
           <a href="<?php echo BASE_URL; ?>blog/<?php echo urlencode($rel['slug']); ?>" class="related-card">
                <img src="<?php echo BASE_URL; ?>assets/images/<?php echo htmlspecialchars($rel['thumbnail'] ?: 'blog-placeholder.jpg'); ?>" alt="<?php echo htmlspecialchars($rel['title']); ?>">
                <div class="related-card-body">
                    <span class="related-card-title"><?php echo htmlspecialchars($rel['title']); ?></span>
                    <span class="related-card-date"><?php echo date('M j, Y', strtotime($rel['published_date'])); ?></span>
                </div>
            </a>
            <?php endforeach; ?>
        </aside>
        <?php endif; ?>

    </div>
</div>

<?php require 'includes/footer.php'; ?>