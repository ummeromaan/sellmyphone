<?php
$page_title = "Blog | SellMyPhone UAE";
$meta_description = "Stay updated with the latest mobile phone trends, selling tips, and technology insights from UAE's premier phone selling platform.";
$meta_keywords = "phone selling tips, mobile trends UAE, sellmyphone blog";

require_once 'admin/includes/db.php';
/**@var mysqli $conn */

$blogs = mysqli_query($conn, "SELECT * FROM blog_posts WHERE status='published' ORDER BY published_date DESC, id DESC");
$blog_count = mysqli_num_rows($blogs);
?>

<?php include 'includes/header.php';?>

<section class="blog-section">
    <div class="container">
        <div class="blog-hero-card">
            <h1 class="titleblog fw-bold">Our Blog Collection</h1>
            <p class="subtitle fs-5 mt-3">Stay updated with the latest mobile phone trends, selling tips, and<br> technology insights from Dubai's premier phone selling platform.</p>
        </div>
    </div>
</section>
<?php if ($blog_count === 0): ?>

<section class="no-blog">
    <div class="container text-center p-5">
        <div class="icon-div mb-3">
            <i class="fa-solid fa-newspaper text-muted fs-1"></i>
        </div>
        <div>
            <h4 class="blog-title fw-bold">No Blog Posts Found</h4>
            <p class="blog-subtitle text-muted">Check back soon for new articles!</p>
        </div>
    </div>
</section>

<?php else: ?>


<section class="blog-grid-section">
    <div class="container">
        <div class="blog-grid">
            <?php while ($post = mysqli_fetch_assoc($blogs)): ?>
           <a href="<?php echo BASE_URL; ?>blog/<?php echo urlencode($post['slug']); ?>" class="blog-card">
                <div class="blog-card-img-wrap">
                    <?php if (!empty($post['thumbnail'])): ?>
                    <img src="<?php echo BASE_URL; ?>assets/images/<?php echo htmlspecialchars($post['thumbnail']); ?>" alt="<?php echo htmlspecialchars($post['title']); ?>">
                    <?php else: ?>
                    <img src="<?php echo BASE_URL; ?>assets/images/blog-placeholder.jpg" alt="<?php echo htmlspecialchars($post['title']); ?>">
                    <?php endif; ?>
                    <div class="blog-card-overlay">
                        <span><?php echo htmlspecialchars($post['title']); ?></span>
                    </div>
                </div>
                <div class="blog-card-body">
                    <h3 class="blog-card-title"><?php echo htmlspecialchars($post['title']); ?></h3>
                    <p class="blog-card-author"><?php echo htmlspecialchars($post['author_name']); ?></p>
                    <div class="blog-card-footer">
                        <span class="blog-card-date">
                            <i class="fa-regular fa-calendar"></i>
                            <?php echo date('F j, Y', strtotime($post['published_date'])); ?>
                        </span>
                        <span class="blog-card-readmore">Read more <i class="fa-solid fa-arrow-right"></i></span>
                    </div>
                </div>
            </a>
            <?php endwhile; ?>
        </div>
    </div>
</section>

<?php endif; ?>

<?php require 'includes/footer.php';?>