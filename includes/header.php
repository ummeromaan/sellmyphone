<?php
// Detect the current page so we can highlight the matching nav link
$current_page = basename($_SERVER['PHP_SELF']);

switch ($current_page) {
    case 'index.php':
        $active_page = 'home';
        break;
    case 'about.php':
        $active_page = 'about';
        break;
    case 'blog.php':
        $active_page = 'blog';
        break;
    case 'contact.php':
        $active_page = 'contact';
        break;
    default:
        $active_page = '';
        break;
}

// ==================================================================
//  Dynamic SEO - pulls title/description/keywords from the page_seo
//  table based on the current page. Falls back to hardcoded defaults
//  if the DB has no row yet.
// ==================================================================
$page_slug = str_replace('.php', '', $current_page);
if ($page_slug === 'index' || $page_slug === '') {
    $page_slug = 'home';
}

require_once 'admin/includes/db.php';

$seo_row = [];
$slug_escaped = mysqli_real_escape_string($conn, $page_slug);
$seo_result = mysqli_query($conn, "SELECT * FROM page_seo WHERE page_slug = '$slug_escaped' LIMIT 1");
if ($seo_result && mysqli_num_rows($seo_result) > 0) {
    $seo_row = mysqli_fetch_assoc($seo_result);
}

$page_title       = $seo_row['page_title'] ?? 'SellMyPhone | Sell Your Phone for Instant Cash in UAE';
$meta_description = $seo_row['meta_description'] ?? 'Sell your old phone for the best price in UAE. Instant valuation, free pickup, and quick secure payment for iPhones, Samsung, and more.';
$meta_keywords    = $seo_row['meta_keywords'] ?? 'sell phone UAE, sell iPhone Dubai, sell Samsung Dubai, phone buyback UAE, cash for old phone';
$meta_robots = $meta_robots ?? 'index, follow, max-image-preview:large, max-snippet:-1';

// Canonical URL - built automatically from the current page
$protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https://' : 'http://';
$canonical_url = $protocol . $_SERVER['HTTP_HOST'] . strtok($_SERVER['REQUEST_URI'], '?');
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <base href="/sellmyphone/">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($page_title); ?></title>
    <meta name="description" content="<?php echo htmlspecialchars($meta_description); ?>">
    <meta name="keywords" content="<?php echo htmlspecialchars($meta_keywords); ?>">
    <link rel="canonical" href="<?php echo htmlspecialchars($canonical_url); ?>">
    <meta name="robots" content="<?php echo htmlspecialchars($meta_robots); ?>">
    <meta name="author" content="SellMyPhone">
    <meta name="publisher" content="SellMyPhone">

    <script src="https://kit.fontawesome.com/d23f23e6ea.js" crossorigin="anonymous"></script>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <!-- Latest compiled JavaScript -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"></script>
    <script src="assets/js/brands.js"></script>
   <link rel="icon" type="image/png" href="assets/images/icon.png">
    <link rel="stylesheet" href="assets/css/header.css">
    <link rel="stylesheet" href="assets/css/index.css">
    <link rel="stylesheet" href="assets/css/brands.css">
    <link rel="stylesheet" href="assets/css/cta.css">
    <link rel="stylesheet" href="assets/css/about.css">
    <link rel="stylesheet" href="assets/css/blog.css">
    <link rel="stylesheet" href="assets/css/apple.css">
    <link rel="stylesheet" href="assets/css/contact.css">
    <link rel="stylesheet" href="assets/css/footer.css">

</head>
<body>

<nav class="navbar navbar-expand-lg navbar-dark custom-navbar">
  <div class="container-fluid">

    <!-- Logo -->
    <a class="navbar-brand d-flex align-items-center" href="index.php">
      <img src="assets/images/newlogo.png" alt="SellPhone Dubai" width="140" height="60">
    </a>

    <!-- 3 lines button (show on small screen) -->
    <button class="navbar-toggler" type="button"
            data-bs-toggle="collapse"
            data-bs-target="#navbarNav"
            aria-controls="navbarNav"
            aria-expanded="false"
            aria-label="Toggle navigation">
      <span class="navbar-toggler-icon"></span>
    </button>

    <!-- Navigation links -->
    <div class="collapse navbar-collapse" id="navbarNav">

      <ul class="navbar-nav mx-auto gap-3">

        <li class="nav-item mx-2">
          <a class="nav-link <?php echo $active_page == 'home' ? 'active' : ''; ?>" href="index">Home</a>
        </li>

        <li class="nav-item mx-2">
          <a class="nav-link <?php echo $active_page == 'about' ? 'active' : ''; ?>" href="about">About</a>
        </li>

        <li class="nav-item mx-2">
          <!-- Goes to the Brands section on the homepage, works from any page -->
          <a class="nav-link" href="index#calc">Get Instant Qoute</a>
        </li>

        <li class="nav-item mx-2">
          <a class="nav-link <?php echo $active_page == 'blog' ? 'active' : ''; ?>" href="blog">Blogs</a>
        </li>

        <li class="nav-item mx-2">
          <!-- Goes to the Testimonials section on the homepage, works from any page -->
          <a class="nav-link" href="index#testimonials">Testimonials</a>
        </li>

        <li class="nav-item mx-2">
          <a class="nav-link <?php echo $active_page == 'contact' ? 'active' : ''; ?>" href="contact">Contact</a>
        </li>
      </ul>

      <!-- WhatsApp CTA -->
      <a href="https://wa.me/971502166562" target="_blank" class="whatsapp-cta">
        <i class="fa-brands fa-whatsapp fs-4" style="color:#0a0820;"></i>
        <span class="whatsapp-text">
          <strong>+971502166562</strong>
          <small>Chat on WhatsApp</small>
        </span>
      </a>

    </div>
  </div>
</nav>
<script>
  // Adds a class to the fixed navbar once the page is scrolled a bit,
  // so it gets a small shadow and looks "locked" in place.
  $(window).on('scroll', function () {
    if ($(window).scrollTop() > 20) {
      $('.custom-navbar').addClass('nav-scrolled');
    } else {
      $('.custom-navbar').removeClass('nav-scrolled');
    }
  });
</script>