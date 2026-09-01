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
    case 'sell-samsung.php':
        $active_page = 'sell-samsung';
    break;
     case 'sell-iphone.php':
        $active_page = 'sell-iphone';
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
require_once 'includes/home-data.php';
$branding_row = home_single($conn, 'home_branding');
$footer_row   = home_single($conn, 'home_footer');
$nav_whatsapp = !empty($footer_row['whatsapp_number']) ? $footer_row['whatsapp_number'] : '971502166562';
$seo_row = [];
$slug_escaped = mysqli_real_escape_string($conn, $page_slug);
$seo_result = mysqli_query($conn, "SELECT * FROM page_seo WHERE page_slug = '$slug_escaped' LIMIT 1");
if ($seo_result && mysqli_num_rows($seo_result) > 0) {
    $seo_row = mysqli_fetch_assoc($seo_result);
}

$page_title       = $seo_row['page_title'] ?? 'PhoneDubai | Sell Your Phone for Instant Cash in UAE';
$meta_description = $seo_row['meta_description'] ?? 'Sell your old phone for the best price in UAE. Instant valuation, free pickup, and quick secure payment for iPhones, Samsung, and more.';
$meta_keywords    = $seo_row['meta_keywords'] ?? 'sell phone UAE, sell iPhone Dubai, sell Samsung Dubai, phone buyback UAE, cash for old phone';
$meta_robots = !empty($seo_row['meta_robots']) ? $seo_row['meta_robots'] : 'index, follow, max-image-preview:large, max-snippet:-1';

// Canonical URL - built automatically from the current page
$protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https://' : 'http://';
$canonical_url = $protocol . $_SERVER['HTTP_HOST'] . strtok($_SERVER['REQUEST_URI'], '?');

// ==================================================================
//  BASE_URL - replaces the old hardcoded <base href="/sellmyphone/">
//  tag. SCRIPT_NAME always points to the REAL physical .php file that
//  is running (index.php, blog.php, blog-single.php ...) even when a
//  clean URL like /blog/some-post is rewritten by .htaccess, so this
//  always resolves to the site's root folder - no matter how deep the
//  visited URL looks, and no matter if the site lives at the domain
//  root (live) or inside a subfolder like /sellmyphone/ (local/XAMPP).
//  All asset/page links must be written as BASE_URL . 'assets/...'
//  instead of a plain relative "assets/..." path.
// ==================================================================
if (!defined('BASE_URL')) {
    $base_path = str_replace('\\', '/', dirname($_SERVER['SCRIPT_NAME']));
    $base_path = rtrim($base_path, '/');
    define('BASE_URL', $base_path === '' ? '/' : $base_path . '/');
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($page_title); ?></title>
    <meta name="description" content="<?php echo htmlspecialchars($meta_description); ?>">
    <meta name="keywords" content="<?php echo htmlspecialchars($meta_keywords); ?>">
    <link rel="canonical" href="<?php echo htmlspecialchars($canonical_url); ?>">
    <meta name="robots" content="<?php echo htmlspecialchars($meta_robots); ?>">
    <meta name="author" content="PhoneDubai">
    <meta name="publisher" content="PhoneDubai">

    <script src="https://kit.fontawesome.com/d23f23e6ea.js" crossorigin="anonymous"></script>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <!-- Latest compiled JavaScript -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"></script>
    <script src="<?php echo BASE_URL; ?>assets/js/brands.js"></script>
   <link rel="icon" type="image/webp" href="<?php echo htmlspecialchars(home_img($branding_row['favicon'] ?? '', 'icon.webp')); ?>">
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>assets/css/header.css">
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>assets/css/index.css">
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>assets/css/brands.css">
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>assets/css/cta.css">
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>assets/css/sell.css">
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>assets/css/about.css">
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>assets/css/blog.css">
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>assets/css/apple.css">
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>assets/css/contact.css">
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>assets/css/footer.css">

</head>
<body>

<nav class="navbar navbar-expand-lg navbar-dark custom-navbar">
  <div class="container-fluid">

    <!-- Logo -->
    <a class="navbar-brand d-flex align-items-center" href="<?php echo BASE_URL; ?>index">
     <img src="<?php echo htmlspecialchars(home_img($branding_row['logo'] ?? '', 'phonedubai.png')); ?>" alt="Phone Dubai" width="180" height="70">
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

<a class="nav-link <?php echo $active_page == 'home' ? 'active' : ''; ?>" href="<?php echo BASE_URL; ?>index"><?php echo htmlspecialchars($branding_row['nav_home'] ?? 'Home'); ?></a>
        </li>
         <li class="nav-item mx-2">
<a class="nav-link <?php echo $active_page == 'about' ? 'active' : ''; ?>" href="<?php echo BASE_URL; ?>about"><?php echo htmlspecialchars($branding_row['nav_about'] ?? 'About'); ?></a>
         </li>
          <li class="nav-item mx-2">
<a class="nav-link <?php echo $active_page == 'sell-samsung' ? 'active' : ''; ?>" href="<?php echo BASE_URL; ?>sell-samsung"><?php echo htmlspecialchars($branding_row['nav_sell_samsung'] ?? 'Sell Samsung'); ?></a>
          </li>
           <li class="nav-item mx-2">
<a class="nav-link <?php echo $active_page == 'sell-iphone' ? 'active' : ''; ?>" href="<?php echo BASE_URL; ?>sell-iphone"><?php echo htmlspecialchars($branding_row['nav_sell_iphone'] ?? 'Sell iPhone'); ?></a>
           </li>
            <li class="nav-item mx-2">
<a class="nav-link" href="<?php echo BASE_URL; ?>index#calc"><?php echo htmlspecialchars($branding_row['nav_brands'] ?? 'Brands'); ?></a>
            </li>
             <li class="nav-item mx-2">
<a class="nav-link <?php echo $active_page == 'blog' ? 'active' : ''; ?>" href="<?php echo BASE_URL; ?>blog"><?php echo htmlspecialchars($branding_row['nav_blog'] ?? 'Blogs'); ?></a>
             </li>
              <li class="nav-item mx-2">
<a class="nav-link" href="<?php echo BASE_URL; ?>index#testimonials"><?php echo htmlspecialchars($branding_row['nav_testimonials'] ?? 'Testimonials'); ?></a>
              </li>
               <li class="nav-item mx-2">
<a class="nav-link <?php echo $active_page == 'contact' ? 'active' : ''; ?>" href="<?php echo BASE_URL; ?>contact"><?php echo htmlspecialchars($branding_row['nav_contact'] ?? 'Contact'); ?></a>
               </li>
</ul>
     
      <!-- WhatsApp CTA -->
     <a href="https://wa.me/<?php echo htmlspecialchars($nav_whatsapp); ?>" target="_blank" class="whatsapp-cta">
    <i class="fa-brands fa-whatsapp fs-4" style="color:#0a0820;"></i>
    <span class="whatsapp-text">
        <strong>+<?php echo htmlspecialchars($nav_whatsapp); ?></strong>
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