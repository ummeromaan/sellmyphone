<?php
session_start();

// Block direct access - must be logged in to view this page
if (!isset($_SESSION['admin_id'])) {
    header("Location: login.php");
    exit();
}
require_once 'includes/db.php';
require_once 'includes/home-helpers.php';
require_once '../includes/home-data.php';
/**@var mysqli $conn */

$msg = "";
if (isset($_SESSION['msg'])) {
    $msg = $_SESSION['msg'];
    unset($_SESSION['msg']);
}

// ==================================================================
//  POST handling - every form on this page posts back here with a
//  hidden "action" field so we know what to save.
// ==================================================================
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['action'])) {

    $action  = $_POST['action'];
    $section = $_POST['section'] ?? 'hero'; // which accordion tab to reopen after redirect

    switch ($action) {

        // ---------- SITE BRANDING (favicon + nav logo) ----------
        case 'update_branding':
    home_save_single($conn, 'home_branding',
        ['nav_home', 'nav_about', 'nav_sell_samsung', 'nav_sell_iphone', 'nav_brands', 'nav_blog', 'nav_testimonials', 'nav_contact'],
        ['favicon' => 'favicon', 'logo' => 'logo']);
    break;
       
        // ---------- FOOTER & CONTACT INFO (also powers floating WhatsApp button) ----------
        case 'update_footer':
            home_save_single($conn, 'home_footer',
                ['whatsapp_number', 'phone_number', 'email', 'address', 'brand_name', 'brand_highlight', 'tagline', 'facebook_url', 'instagram_url', 'copyright_text']);
            break;

        // ---------- HERO ----------
        case 'update_hero':
            home_save_single($conn, 'home_hero',
                ['badge_icon', 'badge_text', 'title_pre', 'title_highlight', 'subtitle'],
                ['side_image' => 'side_image', 'bg_image' => 'bg_image']);
            break;

        case 'add_hero_feature':
            home_row_add($conn, 'home_hero_features', ['icon_class', 'label', 'sort_order']);
            break;
        case 'update_hero_feature':
            home_row_update($conn, 'home_hero_features', $_POST['id'], ['icon_class', 'label', 'sort_order']);
            break;
        case 'delete_hero_feature':
            home_row_delete($conn, 'home_hero_features', $_POST['id']);
            break;

        case 'add_hero_stat':
            home_row_add($conn, 'home_hero_stats', ['icon_class', 'icon_color', 'value', 'label', 'sort_order']);
            break;
        case 'update_hero_stat':
            home_row_update($conn, 'home_hero_stats', $_POST['id'], ['icon_class', 'icon_color', 'value', 'label', 'sort_order']);
            break;
        case 'delete_hero_stat':
            home_row_delete($conn, 'home_hero_stats', $_POST['id']);
            break;

        // ---------- BRANDS ----------
        case 'update_brands_header':
            home_save_single($conn, 'home_brands_header',
                ['tag_icon', 'tag_text', 'title_pre', 'title_highlight', 'title_post', 'subtitle', 'choose_label'],
                ['left_image' => 'left_image', 'right_image' => 'right_image']);
            break;

        case 'update_brand_card':
            $brand_key   = mysqli_real_escape_string($conn, $_POST['brand_key']);
            $title       = mysqli_real_escape_string($conn, trim($_POST['title'] ?? ''));
            $subtitle    = mysqli_real_escape_string($conn, trim($_POST['subtitle'] ?? ''));
            $description = mysqli_real_escape_string($conn, trim($_POST['description'] ?? ''));
            $sets = "title='$title', subtitle='$subtitle', description='$description'";
            $phone_image = home_admin_upload('phone_image');
            if ($phone_image) $sets .= ", phone_image='" . mysqli_real_escape_string($conn, $phone_image) . "'";
            $badge_image = home_admin_upload('badge_image');
            if ($badge_image) $sets .= ", badge_image='" . mysqli_real_escape_string($conn, $badge_image) . "'";
            mysqli_query($conn, "UPDATE home_brand_cards SET $sets WHERE brand_key='$brand_key'");
            break;

        case 'add_brand_feature':
            home_row_add($conn, 'home_brand_features', ['icon_class', 'title', 'description', 'sort_order']);
            break;
        case 'update_brand_feature':
            home_row_update($conn, 'home_brand_features', $_POST['id'], ['icon_class', 'title', 'description', 'sort_order']);
            break;
        case 'delete_brand_feature':
            home_row_delete($conn, 'home_brand_features', $_POST['id']);
            break;

        // ---------- HOW IT WORKS ----------
        case 'update_hiw':
            home_save_single($conn, 'home_hiw', ['tag_text', 'title']);
            break;
        case 'add_hiw_step':
            home_row_add($conn, 'home_hiw_steps', ['step_number', 'icon_class', 'title', 'description', 'sort_order']);
            break;
        case 'update_hiw_step':
            home_row_update($conn, 'home_hiw_steps', $_POST['id'], ['step_number', 'icon_class', 'title', 'description', 'sort_order']);
            break;
        case 'delete_hiw_step':
            home_row_delete($conn, 'home_hiw_steps', $_POST['id']);
            break;

        // ---------- INSTANT QUOTE CTA ----------
        case 'update_iqc':
            home_save_single($conn, 'home_iqc',
                ['title_pre', 'title_highlight', 'description', 'estimate_label', 'estimate_upto', 'estimate_value', 'estimate_badge_text'],
                ['bg_image' => 'bg_image']);
            break;
        case 'add_iqc_feature':
            home_row_add($conn, 'home_iqc_features', ['icon_class', 'icon_color', 'text', 'sort_order']);
            break;
        case 'update_iqc_feature':
            home_row_update($conn, 'home_iqc_features', $_POST['id'], ['icon_class', 'icon_color', 'text', 'sort_order']);
            break;
        case 'delete_iqc_feature':
            home_row_delete($conn, 'home_iqc_features', $_POST['id']);
            break;

        // ---------- WHY CHOOSE US ----------
        case 'update_wcu':
            home_save_single($conn, 'home_wcu', ['tag_text', 'title_pre', 'title_highlight']);
            break;
        case 'add_wcu_card':
            home_row_add($conn, 'home_wcu_cards', ['icon_class', 'icon_color', 'title', 'description', 'sort_order']);
            break;
        case 'update_wcu_card':
            home_row_update($conn, 'home_wcu_cards', $_POST['id'], ['icon_class', 'icon_color', 'title', 'description', 'sort_order']);
            break;
        case 'delete_wcu_card':
            home_row_delete($conn, 'home_wcu_cards', $_POST['id']);
            break;

        case 'update_wcu_highlight':
            home_save_single($conn, 'home_wcu_highlight', ['highlight_text', 'rest_text']);
            break;
        case 'add_wcu_highlight_item':
            home_row_add($conn, 'home_wcu_highlight_items', ['icon_class', 'text', 'sort_order']);
            break;
        case 'update_wcu_highlight_item':
            home_row_update($conn, 'home_wcu_highlight_items', $_POST['id'], ['icon_class', 'text', 'sort_order']);
            break;
        case 'delete_wcu_highlight_item':
            home_row_delete($conn, 'home_wcu_highlight_items', $_POST['id']);
            break;

        // ---------- TESTIMONIALS ----------
        case 'add_testimonial':
            home_row_add($conn, 'home_testimonials', ['rating', 'quote', 'avatar_letter', 'author_name', 'author_location', 'sort_order']);
            break;
        case 'update_testimonial':
            home_row_update($conn, 'home_testimonials', $_POST['id'], ['rating', 'quote', 'avatar_letter', 'author_name', 'author_location', 'sort_order']);
            break;
        case 'delete_testimonial':
            home_row_delete($conn, 'home_testimonials', $_POST['id']);
            break;

        // ---------- FAQ ----------
        case 'update_faq_header':
            home_save_single($conn, 'home_faq_header', ['tag_text', 'title']);
            break;
        case 'add_faq':
            home_row_add($conn, 'home_faq', ['question', 'answer', 'column_no', 'sort_order']);
            break;
        case 'update_faq':
            home_row_update($conn, 'home_faq', $_POST['id'], ['question', 'answer', 'column_no', 'sort_order']);
            break;
        case 'delete_faq':
            home_row_delete($conn, 'home_faq', $_POST['id']);
            break;

        case 'update_faq_cta':
            home_save_single($conn, 'home_faq_cta', ['title', 'description', 'badges'], ['phone_image' => 'phone_image', 'bg_image' => 'bg_image']);
            break;
    }

    $_SESSION['msg'] = "<div class='alert alert-success w-50 mb-0'>Home Page Updated.</div>";
    header("Location: home-content.php?tab=" . urlencode($section));
    exit();
}

$tab = isset($_GET['tab']) ? $_GET['tab'] : 'branding';

// ==================================================================
//  Load everything fresh for display
// ==================================================================
$branding = home_single($conn, 'home_branding');
$footer   = home_single($conn, 'home_footer');

$hero          = home_single($conn, 'home_hero');
$hero_features = home_rows($conn, 'home_hero_features');
$hero_stats    = home_rows($conn, 'home_hero_stats');

$brands_header = home_single($conn, 'home_brands_header');
$brand_cards_raw = home_rows($conn, 'home_brand_cards', 'id ASC');
$brand_cards = [];
foreach ($brand_cards_raw as $bc) { $brand_cards[$bc['brand_key']] = $bc; }
$apple   = $brand_cards['apple'] ?? [];
$samsung = $brand_cards['samsung'] ?? [];
$brand_features = home_rows($conn, 'home_brand_features');

$hiw       = home_single($conn, 'home_hiw');
$hiw_steps = home_rows($conn, 'home_hiw_steps');

$iqc          = home_single($conn, 'home_iqc');
$iqc_features = home_rows($conn, 'home_iqc_features');

$wcu           = home_single($conn, 'home_wcu');
$wcu_cards     = home_rows($conn, 'home_wcu_cards');
$wcu_highlight = home_single($conn, 'home_wcu_highlight');
$wcu_highlight_items = home_rows($conn, 'home_wcu_highlight_items');

$testimonials = home_rows($conn, 'home_testimonials');

$faq_header = home_single($conn, 'home_faq_header');
$faq_all    = home_rows($conn, 'home_faq', 'column_no ASC, sort_order ASC, id ASC');
$faq_cta    = home_single($conn, 'home_faq_cta');

require 'includes/ad-header.php';
require_once 'includes/sidebar.php';
?>
<link rel="stylesheet" href="home-content.css">

<div class="container-fluid main-content">

<nav class="navbar navbar-expand-lg">
   <div class="container-fluid">
        <a class="navbar-brand fw-bold text-dark fs-3" href="#">Home Page Content</a>
        <ul class="navbar-nav ms-auto">
            <li class="nav-item dropdown">
                <a class="nav-link dropdown-toggle d-flex align-items-center fs-5 text-dark" href="#" id="navbarDropdownMenuLink" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                    <div class="user mx-2"><i class="fa-solid fa-user"></i></div> Admin
                </a>
                <ul class="dropdown-menu dropdown-menu-end">
                    <li><a class="dropdown-item" href="add-admin.php">Profile</a></li>
                    <li><a class="dropdown-item" href="settings.php">Settings</a></li>
                    <li><a class="dropdown-item" href="logout.php">Logout</a></li>
                </ul>
            </li>
        </ul>
    </div>
</nav><hr class="m-0">

<div class="mt-3"><?php echo $msg; ?></div>

<p class="text-muted mb-4">
    Edit everything shown on the public Home Page here
    (Book Free Pickup, brand calculator steps, etc.) are not editable from here since they're functional, not content.
</p>

<div class="ct-shell mt-3">

    <!-- Section nav -->
    <div class="ct-nav nav flex-column" role="tablist">
        <a class="nav-link <?php echo $tab=='branding' ? 'active' : ''; ?>" href="?tab=branding"><i class="fa-solid fa-image"></i> Site Branding</a>
        <a class="nav-link <?php echo $tab=='hero' ? 'active' : ''; ?>" href="?tab=hero"><i class="fa-solid fa-house"></i> Hero Section</a>
        <a class="nav-link <?php echo $tab=='brands' ? 'active' : ''; ?>" href="?tab=brands"><i class="fa-solid fa-mobile"></i> Brand Calculator</a>
        <a class="nav-link <?php echo $tab=='hiw' ? 'active' : ''; ?>" href="?tab=hiw"><i class="fa-solid fa-list-check"></i> How It Works & Quote</a>
        <a class="nav-link <?php echo $tab=='wcu' ? 'active' : ''; ?>" href="?tab=wcu"><i class="fa-solid fa-star"></i> Why Choose Us</a>
        <a class="nav-link <?php echo $tab=='testimonials' ? 'active' : ''; ?>" href="?tab=testimonials"><i class="fa-solid fa-comment-dots"></i> Testimonials</a>
        <a class="nav-link <?php echo $tab=='faq' ? 'active' : ''; ?>" href="?tab=faq"><i class="fa-solid fa-circle-question"></i> FAQ</a>
        <a class="nav-link <?php echo $tab=='footer' ? 'active' : ''; ?>" href="?tab=footer"><i class="fa-solid fa-shoe-prints"></i> Footer & Contact Info</a>
    </div>

    <!-- Panel -->
    <div class="ct-panel">

    <!-- ============ SITE BRANDING (Favicon + Nav Logo) ============ -->
    <?php if ($tab == 'branding'): ?>

    <div class="ct-panel-header">
        <div>
            <h3 class="ct-panel-title"><i class="fa-solid fa-image"></i> Site Branding (Favicon &amp; Nav Logo)</h3>
        </div>
    </div>


                <form method="POST" enctype="multipart/form-data" class="hc-card">
                    <input type="hidden" name="action" value="update_branding">
                    <input type="hidden" name="section" value="branding">

                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label fw-bold">Browser Icon (Favicon)</label>
                            <input type="file" class="form-control" name="favicon" accept=".png,.ico">
                            <?php if (!empty($branding['favicon'])): ?>
                            <div><img src="<?php echo home_admin_img($branding['favicon']); ?>" class="hc-preview mt-2" style="width:40px;height:40px;"></div>
                            <?php endif; ?>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-bold">Nav Bar Logo</label>
                            <input type="file" class="form-control" name="logo" accept=".png,.jpg,.jpeg,.webp">
                            <?php if (!empty($branding['logo'])): ?>
                            <div><img src="<?php echo home_admin_img($branding['logo']); ?>" class="hc-preview mt-2" style="width:140px;"></div>
                            <?php endif; ?>
                        </div>
                    </div>
                  <hr class="my-4">
<h6 class="fw-bold mb-3">Nav Bar Link Labels</h6>
<div class="row g-3">
    <div class="col-md-3">
        <label class="form-label">Home</label>
        <input type="text" class="form-control" name="nav_home" value="<?php echo htmlspecialchars($branding['nav_home'] ?? 'Home'); ?>">
    </div>
    <div class="col-md-3">
        <label class="form-label">About</label>
        <input type="text" class="form-control" name="nav_about" value="<?php echo htmlspecialchars($branding['nav_about'] ?? 'About'); ?>">
    </div>
    <div class="col-md-3">
        <label class="form-label">Sell Samsung</label>
        <input type="text" class="form-control" name="nav_sell_samsung" value="<?php echo htmlspecialchars($branding['nav_sell_samsung'] ?? 'Sell Samsung'); ?>">
    </div>
    <div class="col-md-3">
        <label class="form-label">Sell iPhone</label>
        <input type="text" class="form-control" name="nav_sell_iphone" value="<?php echo htmlspecialchars($branding['nav_sell_iphone'] ?? 'Sell iPhone'); ?>">
    </div>
    <div class="col-md-3">
        <label class="form-label">Brands</label>
        <input type="text" class="form-control" name="nav_brands" value="<?php echo htmlspecialchars($branding['nav_brands'] ?? 'Brands'); ?>">
    </div>
    <div class="col-md-3">
        <label class="form-label">Blogs</label>
        <input type="text" class="form-control" name="nav_blog" value="<?php echo htmlspecialchars($branding['nav_blog'] ?? 'Blogs'); ?>">
    </div>
    <div class="col-md-3">
        <label class="form-label">Testimonials</label>
        <input type="text" class="form-control" name="nav_testimonials" value="<?php echo htmlspecialchars($branding['nav_testimonials'] ?? 'Testimonials'); ?>">
    </div>
    <div class="col-md-3">
        <label class="form-label">Contact</label>
        <input type="text" class="form-control" name="nav_contact" value="<?php echo htmlspecialchars($branding['nav_contact'] ?? 'Contact'); ?>">
    </div>
</div>
<p class="text-muted mt-2 mb-0" style="font-size:13px;">
    WhatsApp number (nav button + floating button) is edited on the
    <a href="?tab=footer">Footer &amp; Contact Info</a> tab — it's shared across the whole site.
</p>
            


                    <button type="submit" class="btn up-btn mt-3">Save Branding</button>
                </form>


    <?php endif; ?>

    <!-- ============ HERO ============ -->
    <?php if ($tab == 'hero'): ?>

    <div class="ct-panel-header">
        <div>
            <h3 class="ct-panel-title"><i class="fa-solid fa-house"></i> Hero Section</h3>
        </div>
    </div>


                <form method="POST" enctype="multipart/form-data" class="hc-card">
                    <input type="hidden" name="action" value="update_hero">
                    <input type="hidden" name="section" value="hero">

                    <div class="row g-3">
                        <div class="col-md-3">
                            <label class="form-label fw-bold">Badge Icon (Font Awesome class)</label>
                            <input type="text" class="form-control" name="badge_icon" value="<?php echo htmlspecialchars($hero['badge_icon'] ?? ''); ?>">
                        </div>
                        <div class="col-md-9">
                            <label class="form-label fw-bold">Badge Text</label>
                            <input type="text" class="form-control" name="badge_text" value="<?php echo htmlspecialchars($hero['badge_text'] ?? ''); ?>">
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-bold">Title (before highlight)</label>
                            <textarea class="form-control" name="title_pre" rows="2"><?php echo htmlspecialchars($hero['title_pre'] ?? ''); ?></textarea>
                            <small class="text-muted">You can use &lt;br&gt; for a line break.</small>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold">Title Highlight (gold text)</label>
                            <input type="text" class="form-control" name="title_highlight" value="<?php echo htmlspecialchars($hero['title_highlight'] ?? ''); ?>">
                        </div>

                        <div class="col-12">
                            <label class="form-label fw-bold">Subtitle</label>
                            <textarea class="form-control" name="subtitle" rows="2"><?php echo htmlspecialchars($hero['subtitle'] ?? ''); ?></textarea>
                        </div>

                        <div class="col-md-4">
                            <label class="form-label fw-bold">Side Image</label>
                            <input type="file" class="form-control" name="side_image">
                            <?php if (!empty($hero['side_image'])): ?>
                            <img src="<?php echo home_admin_img($hero['side_image']); ?>" class="hc-preview mt-2">
                            <?php endif; ?>
                        </div>

                        <div class="col-md-4">
                            <label class="form-label fw-bold">Background Image</label>
                            <input type="file" class="form-control" name="bg_image">
                            <?php if (!empty($hero['bg_image'])): ?>
                            <img src="<?php echo home_admin_img($hero['bg_image']); ?>" class="hc-preview mt-2">
                            <?php endif; ?>
                        </div>
                    </div>

                    <button type="submit" class="btn up-btn mt-3">Save Hero Section</button>
                </form>

                <h5 class="hc-subhead">Trust Feature Chips <small class="text-muted">(icon + label row under the subtitle)</small></h5>
                <div class="hc-rows">
                    <?php foreach ($hero_features as $f): ?>
                    <form method="POST" class="hc-row">
                        <input type="hidden" name="action" value="update_hero_feature">
                        <input type="hidden" name="section" value="hero">
                        <input type="hidden" name="id" value="<?php echo $f['id']; ?>">
                        <input type="text" class="form-control" name="icon_class" value="<?php echo htmlspecialchars($f['icon_class']); ?>" placeholder="fa-solid fa-tag">
                        <input type="text" class="form-control" name="label" value="<?php echo htmlspecialchars($f['label']); ?>" placeholder="Label">
                        <input type="number" class="form-control hc-order" name="sort_order" value="<?php echo htmlspecialchars($f['sort_order']); ?>">
                        <button type="submit" class="btn btn-sm up-btn">Save</button>
                        <button type="submit" formaction="home-content.php" name="action" value="delete_hero_feature" class="btn btn-sm btn-outline-danger" onclick="return confirm('Delete this item?');">Delete</button>
                    </form>
                    <?php endforeach; ?>

                    <form method="POST" class="hc-row hc-row-new">
                        <input type="hidden" name="action" value="add_hero_feature">
                        <input type="hidden" name="section" value="hero">
                        <input type="text" class="form-control" name="icon_class" placeholder="fa-solid fa-tag">
                        <input type="text" class="form-control" name="label" placeholder="Label">
                        <input type="number" class="form-control hc-order" name="sort_order" placeholder="Order" value="0">
                        <button type="submit" class="btn btn-sm btn-outline-success">+ Add</button>
                        <span></span>
                    </form>
                </div>

                <h5 class="hc-subhead">Trust Stats Bar <small class="text-muted">(50,000+ Happy Customers, etc.)</small></h5>
                <div class="hc-rows">
                    <?php foreach ($hero_stats as $s): ?>
                    <form method="POST" class="hc-row hc-row-5">
                        <input type="hidden" name="action" value="update_hero_stat">
                        <input type="hidden" name="section" value="hero">
                        <input type="hidden" name="id" value="<?php echo $s['id']; ?>">
                        <input type="text" class="form-control" name="icon_class" value="<?php echo htmlspecialchars($s['icon_class']); ?>" placeholder="Icon class">
                        <input type="text" class="form-control" name="icon_color" value="<?php echo htmlspecialchars($s['icon_color']); ?>" placeholder="#f7c82f">
                        <input type="text" class="form-control" name="value" value="<?php echo htmlspecialchars($s['value']); ?>" placeholder="50,000+">
                        <input type="text" class="form-control" name="label" value="<?php echo htmlspecialchars($s['label']); ?>" placeholder="Label">
                        <input type="number" class="form-control hc-order" name="sort_order" value="<?php echo htmlspecialchars($s['sort_order']); ?>">
                        <button type="submit" class="btn btn-sm up-btn">Save</button>
                        <button type="submit" name="action" value="delete_hero_stat" class="btn btn-sm btn-outline-danger" onclick="return confirm('Delete this item?');">Delete</button>
                    </form>
                    <?php endforeach; ?>

                    <form method="POST" class="hc-row hc-row-5 hc-row-new">
                        <input type="hidden" name="action" value="add_hero_stat">
                        <input type="hidden" name="section" value="hero">
                        <input type="text" class="form-control" name="icon_class" placeholder="Icon class">
                        <input type="text" class="form-control" name="icon_color" placeholder="#f7c82f">
                        <input type="text" class="form-control" name="value" placeholder="50,000+">
                        <input type="text" class="form-control" name="label" placeholder="Label">
                        <input type="number" class="form-control hc-order" name="sort_order" placeholder="Order" value="0">
                        <button type="submit" class="btn btn-sm btn-outline-success">+ Add</button>
                        <span></span>
                    </form>
                </div>


    <?php endif; ?>

    <!-- ============ BRANDS ============ -->
    <?php if ($tab == 'brands'): ?>

    <div class="ct-panel-header">
        <div>
            <h3 class="ct-panel-title"><i class="fa-solid fa-mobile"></i> Brand Calculator Section</h3>
        </div>
    </div>


                <form method="POST" enctype="multipart/form-data" class="hc-card">
                    <input type="hidden" name="action" value="update_brands_header">
                    <input type="hidden" name="section" value="brands">

                    <div class="row g-3">
                        <div class="col-md-4">
                            <label class="form-label fw-bold">Tag Icon</label>
                            <input type="text" class="form-control" name="tag_icon" value="<?php echo htmlspecialchars($brands_header['tag_icon'] ?? ''); ?>">
                        </div>
                        <div class="col-md-8">
                            <label class="form-label fw-bold">Tag Text</label>
                            <input type="text" class="form-control" name="tag_text" value="<?php echo htmlspecialchars($brands_header['tag_text'] ?? ''); ?>">
                        </div>

                        <div class="col-md-4">
                            <label class="form-label fw-bold">Title (before highlight)</label>
                            <input type="text" class="form-control" name="title_pre" value="<?php echo htmlspecialchars($brands_header['title_pre'] ?? ''); ?>">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-bold">Title Highlight</label>
                            <input type="text" class="form-control" name="title_highlight" value="<?php echo htmlspecialchars($brands_header['title_highlight'] ?? ''); ?>">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-bold">Title (after highlight)</label>
                            <input type="text" class="form-control" name="title_post" value="<?php echo htmlspecialchars($brands_header['title_post'] ?? ''); ?>">
                        </div>

                        <div class="col-12">
                            <label class="form-label fw-bold">Subtitle</label>
                            <textarea class="form-control" name="subtitle" rows="2"><?php echo htmlspecialchars($brands_header['subtitle'] ?? ''); ?></textarea>
                        </div>

                        <div class="col-md-4">
                            <label class="form-label fw-bold">"Choose Your Brand" Label</label>
                            <input type="text" class="form-control" name="choose_label" value="<?php echo htmlspecialchars($brands_header['choose_label'] ?? 'CHOOSE YOUR BRAND'); ?>">
                        </div>

                        <div class="col-md-4">
                            <label class="form-label fw-bold">Left Phone Image</label>
                            <input type="file" class="form-control" name="left_image">
                            <?php if (!empty($brands_header['left_image'])): ?><img src="<?php echo home_admin_img($brands_header['left_image']); ?>" class="hc-preview mt-2"><?php endif; ?>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-bold">Right Phone Image</label>
                            <input type="file" class="form-control" name="right_image">
                            <?php if (!empty($brands_header['right_image'])): ?><img src="<?php echo home_admin_img($brands_header['right_image']); ?>" class="hc-preview mt-2"><?php endif; ?>
                        </div>
                    </div>

                    <button type="submit" class="btn up-btn mt-3">Save Brands Header</button>
                </form>

                <h5 class="hc-subhead">Brand Cards <small class="text-muted">(Apple &amp; Samsung cards - image, badge, title, description)</small></h5>

                <?php foreach (['apple' => $apple, 'samsung' => $samsung] as $key => $card): ?>
                <form method="POST" enctype="multipart/form-data" class="hc-card mb-3">
                    <input type="hidden" name="action" value="update_brand_card">
                    <input type="hidden" name="section" value="brands">
                    <input type="hidden" name="brand_key" value="<?php echo $key; ?>">
                    <h6 class="fw-bold text-capitalize mb-3"><?php echo $key; ?> Card</h6>
                    <div class="row g-3">
                        <div class="col-md-3">
                            <label class="form-label">Phone Image</label>
                            <input type="file" class="form-control" name="phone_image">
                            <?php if (!empty($card['phone_image'])): ?><img src="<?php echo home_admin_img($card['phone_image']); ?>" class="hc-preview mt-2"><?php endif; ?>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Badge Image</label>
                            <input type="file" class="form-control" name="badge_image">
                            <?php if (!empty($card['badge_image'])): ?><img src="<?php echo home_admin_img($card['badge_image']); ?>" class="hc-preview mt-2"><?php endif; ?>
                        </div>
                        <div class="col-md-2">
                            <label class="form-label">Title</label>
                            <input type="text" class="form-control" name="title" value="<?php echo htmlspecialchars($card['title'] ?? ''); ?>">
                        </div>
                        <div class="col-md-2">
                            <label class="form-label">Subtitle</label>
                            <input type="text" class="form-control" name="subtitle" value="<?php echo htmlspecialchars($card['subtitle'] ?? ''); ?>">
                        </div>
                        <div class="col-md-2">
                            <label class="form-label">Description</label>
                            <input type="text" class="form-control" name="description" value="<?php echo htmlspecialchars($card['description'] ?? ''); ?>">
                        </div>
                    </div>
                    <button type="submit" class="btn btn-sm up-btn mt-3">Save <?php echo ucfirst($key); ?> Card</button>
                </form>
                <?php endforeach; ?>

                <h5 class="hc-subhead">Trust Features Strip <small class="text-muted">(below the calculator card)</small></h5>
                <div class="hc-rows">
                    <?php foreach ($brand_features as $bf): ?>
                    <form method="POST" class="hc-row hc-row-4">
                        <input type="hidden" name="action" value="update_brand_feature">
                        <input type="hidden" name="section" value="brands">
                        <input type="hidden" name="id" value="<?php echo $bf['id']; ?>">
                        <input type="text" class="form-control" name="icon_class" value="<?php echo htmlspecialchars($bf['icon_class']); ?>" placeholder="Icon class">
                        <input type="text" class="form-control" name="title" value="<?php echo htmlspecialchars($bf['title']); ?>" placeholder="Title">
                        <input type="text" class="form-control" name="description" value="<?php echo htmlspecialchars($bf['description']); ?>" placeholder="Description">
                        <input type="number" class="form-control hc-order" name="sort_order" value="<?php echo htmlspecialchars($bf['sort_order']); ?>">
                        <button type="submit" class="btn btn-sm up-btn">Save</button>
                        <button type="submit" name="action" value="delete_brand_feature" class="btn btn-sm btn-outline-danger" onclick="return confirm('Delete this item?');">Delete</button>
                    </form>
                    <?php endforeach; ?>

                    <form method="POST" class="hc-row hc-row-4 hc-row-new">
                        <input type="hidden" name="action" value="add_brand_feature">
                        <input type="hidden" name="section" value="brands">
                        <input type="text" class="form-control" name="icon_class" placeholder="Icon class">
                        <input type="text" class="form-control" name="title" placeholder="Title">
                        <input type="text" class="form-control" name="description" placeholder="Description">
                        <input type="number" class="form-control hc-order" name="sort_order" placeholder="Order" value="0">
                        <button type="submit" class="btn btn-sm btn-outline-success">+ Add</button>
                        <span></span>
                    </form>
                </div>


    <?php endif; ?>

    <!-- ============ HOW IT WORKS + INSTANT QUOTE ============ -->
    <?php if ($tab == 'hiw'): ?>

    <div class="ct-panel-header">
        <div>
            <h3 class="ct-panel-title"><i class="fa-solid fa-list-check"></i> How It Works &amp; Instant Quote</h3>
        </div>
    </div>


                <form method="POST" class="hc-card">
                    <input type="hidden" name="action" value="update_hiw">
                    <input type="hidden" name="section" value="hiw">
                    <div class="row g-3">
                        <div class="col-md-4">
                            <label class="form-label fw-bold">Tag Text</label>
                            <input type="text" class="form-control" name="tag_text" value="<?php echo htmlspecialchars($hiw['tag_text'] ?? ''); ?>">
                        </div>
                        <div class="col-md-8">
                            <label class="form-label fw-bold">Title</label>
                            <input type="text" class="form-control" name="title" value="<?php echo htmlspecialchars($hiw['title'] ?? ''); ?>">
                        </div>
                    </div>
                    <button type="submit" class="btn up-btn mt-3">Save Heading</button>
                </form>

                <h5 class="hc-subhead">Steps (1, 2, 3, 4...)</h5>
                <div class="hc-rows">
                    <?php foreach ($hiw_steps as $st): ?>
                    <form method="POST" class="hc-row hc-row-5">
                        <input type="hidden" name="action" value="update_hiw_step">
                        <input type="hidden" name="section" value="hiw">
                        <input type="hidden" name="id" value="<?php echo $st['id']; ?>">
                        <input type="text" class="form-control" name="step_number" value="<?php echo htmlspecialchars($st['step_number']); ?>" placeholder="1">
                        <input type="text" class="form-control" name="icon_class" value="<?php echo htmlspecialchars($st['icon_class']); ?>" placeholder="Icon class">
                        <input type="text" class="form-control" name="title" value="<?php echo htmlspecialchars($st['title']); ?>" placeholder="Title">
                        <input type="text" class="form-control" name="description" value="<?php echo htmlspecialchars($st['description']); ?>" placeholder="Description">
                        <input type="number" class="form-control hc-order" name="sort_order" value="<?php echo htmlspecialchars($st['sort_order']); ?>">
                        <button type="submit" class="btn btn-sm up-btn">Save</button>
                        <button type="submit" name="action" value="delete_hiw_step" class="btn btn-sm btn-outline-danger" onclick="return confirm('Delete this item?');">Delete</button>
                    </form>
                    <?php endforeach; ?>

                    <form method="POST" class="hc-row hc-row-5 hc-row-new">
                        <input type="hidden" name="action" value="add_hiw_step">
                        <input type="hidden" name="section" value="hiw">
                        <input type="text" class="form-control" name="step_number" placeholder="5">
                        <input type="text" class="form-control" name="icon_class" placeholder="Icon class">
                        <input type="text" class="form-control" name="title" placeholder="Title">
                        <input type="text" class="form-control" name="description" placeholder="Description">
                        <input type="number" class="form-control hc-order" name="sort_order" placeholder="Order" value="0">
                        <button type="submit" class="btn btn-sm btn-outline-success">+ Add</button>
                        <span></span>
                    </form>
                </div>

                <h5 class="hc-subhead">Instant Quote CTA Block</h5>
                <form method="POST" enctype="multipart/form-data" class="hc-card">
                    <input type="hidden" name="action" value="update_iqc">
                    <input type="hidden" name="section" value="hiw">
                    <div class="row g-3">
                        <div class="col-md-4">
                            <label class="form-label">Title (before highlight)</label>
                            <input type="text" class="form-control" name="title_pre" value="<?php echo htmlspecialchars($iqc['title_pre'] ?? ''); ?>">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Title Highlight</label>
                            <input type="text" class="form-control" name="title_highlight" value="<?php echo htmlspecialchars($iqc['title_highlight'] ?? ''); ?>">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Estimate Label</label>
                            <input type="text" class="form-control" name="estimate_label" value="<?php echo htmlspecialchars($iqc['estimate_label'] ?? ''); ?>">
                        </div>
                        <div class="col-12">
                            <label class="form-label">Description</label>
                            <textarea class="form-control" name="description" rows="2"><?php echo htmlspecialchars($iqc['description'] ?? ''); ?></textarea>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Estimate "Up to" text</label>
                            <input type="text" class="form-control" name="estimate_upto" value="<?php echo htmlspecialchars($iqc['estimate_upto'] ?? ''); ?>">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Estimate Value</label>
                            <input type="text" class="form-control" name="estimate_value" value="<?php echo htmlspecialchars($iqc['estimate_value'] ?? ''); ?>">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Estimate Badge Text</label>
                            <input type="text" class="form-control" name="estimate_badge_text" value="<?php echo htmlspecialchars($iqc['estimate_badge_text'] ?? ''); ?>">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Background Image</label>
                            <input type="file" class="form-control" name="bg_image">
                            <?php if (!empty($iqc['bg_image'])): ?>
                            <img src="<?php echo home_admin_img($iqc['bg_image']); ?>" class="hc-preview mt-2">
                            <?php endif; ?>
                        </div>
                    </div>
                    <button type="submit" class="btn up-btn mt-3">Save Instant Quote Block</button>
                </form>

                <h5 class="hc-subhead">Instant Quote Feature List <small class="text-muted">(Free Valuation, No Obligation...)</small></h5>
                <div class="hc-rows">
                    <?php foreach ($iqc_features as $f): ?>
                    <form method="POST" class="hc-row hc-row-4">
                        <input type="hidden" name="action" value="update_iqc_feature">
                        <input type="hidden" name="section" value="hiw">
                        <input type="hidden" name="id" value="<?php echo $f['id']; ?>">
                        <input type="text" class="form-control" name="icon_class" value="<?php echo htmlspecialchars($f['icon_class']); ?>" placeholder="Icon class">
                        <input type="text" class="form-control" name="icon_color" value="<?php echo htmlspecialchars($f['icon_color']); ?>" placeholder="#D4AF37">
                        <input type="text" class="form-control" name="text" value="<?php echo htmlspecialchars($f['text']); ?>" placeholder="Text">
                        <input type="number" class="form-control hc-order" name="sort_order" value="<?php echo htmlspecialchars($f['sort_order']); ?>">
                        <button type="submit" class="btn btn-sm up-btn">Save</button>
                        <button type="submit" name="action" value="delete_iqc_feature" class="btn btn-sm btn-outline-danger" onclick="return confirm('Delete this item?');">Delete</button>
                    </form>
                    <?php endforeach; ?>

                    <form method="POST" class="hc-row hc-row-4 hc-row-new">
                        <input type="hidden" name="action" value="add_iqc_feature">
                        <input type="hidden" name="section" value="hiw">
                        <input type="text" class="form-control" name="icon_class" placeholder="Icon class">
                        <input type="text" class="form-control" name="icon_color" placeholder="#D4AF37">
                        <input type="text" class="form-control" name="text" placeholder="Text">
                        <input type="number" class="form-control hc-order" name="sort_order" placeholder="Order" value="0">
                        <button type="submit" class="btn btn-sm btn-outline-success">+ Add</button>
                        <span></span>
                    </form>
                </div>


    <?php endif; ?>

    <!-- ============ WHY CHOOSE US ============ -->
    <?php if ($tab == 'wcu'): ?>

    <div class="ct-panel-header">
        <div>
            <h3 class="ct-panel-title"><i class="fa-solid fa-star"></i> Why Choose Us</h3>
        </div>
    </div>


                <form method="POST" class="hc-card">
                    <input type="hidden" name="action" value="update_wcu">
                    <input type="hidden" name="section" value="wcu">
                    <div class="row g-3">
                        <div class="col-md-4">
                            <label class="form-label">Tag Text</label>
                            <input type="text" class="form-control" name="tag_text" value="<?php echo htmlspecialchars($wcu['tag_text'] ?? ''); ?>">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Title (before highlight)</label>
                            <input type="text" class="form-control" name="title_pre" value="<?php echo htmlspecialchars($wcu['title_pre'] ?? ''); ?>">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Title Highlight</label>
                            <input type="text" class="form-control" name="title_highlight" value="<?php echo htmlspecialchars($wcu['title_highlight'] ?? ''); ?>">
                        </div>
                    </div>
                    <button type="submit" class="btn up-btn mt-3">Save Heading</button>
                </form>

                <h5 class="hc-subhead">Feature Cards (6-grid)</h5>
                <div class="hc-rows">
                    <?php foreach ($wcu_cards as $c): ?>
                    <form method="POST" class="hc-row hc-row-4">
                        <input type="hidden" name="action" value="update_wcu_card">
                        <input type="hidden" name="section" value="wcu">
                        <input type="hidden" name="id" value="<?php echo $c['id']; ?>">
                        <input type="text" class="form-control" name="icon_class" value="<?php echo htmlspecialchars($c['icon_class']); ?>" placeholder="Icon class">
                        <input type="text" class="form-control" name="icon_color" value="<?php echo htmlspecialchars($c['icon_color']); ?>" placeholder="#ebb917">
                        <input type="text" class="form-control" name="title" value="<?php echo htmlspecialchars($c['title']); ?>" placeholder="Title">
                        <input type="text" class="form-control" name="description" value="<?php echo htmlspecialchars($c['description']); ?>" placeholder="Description">
                        <input type="number" class="form-control hc-order" name="sort_order" value="<?php echo htmlspecialchars($c['sort_order']); ?>">
                        <button type="submit" class="btn btn-sm up-btn">Save</button>
                        <button type="submit" name="action" value="delete_wcu_card" class="btn btn-sm btn-outline-danger" onclick="return confirm('Delete this item?');">Delete</button>
                    </form>
                    <?php endforeach; ?>

                    <form method="POST" class="hc-row hc-row-4 hc-row-new">
                        <input type="hidden" name="action" value="add_wcu_card">
                        <input type="hidden" name="section" value="wcu">
                        <input type="text" class="form-control" name="icon_class" placeholder="Icon class">
                        <input type="text" class="form-control" name="icon_color" placeholder="#ebb917">
                        <input type="text" class="form-control" name="title" placeholder="Title">
                        <input type="text" class="form-control" name="description" placeholder="Description">
                        <input type="number" class="form-control hc-order" name="sort_order" placeholder="Order" value="0">
                        <button type="submit" class="btn btn-sm btn-outline-success">+ Add</button>
                        <span></span>
                    </form>
                </div>

                <h5 class="hc-subhead">Highlight Card</h5>
                <form method="POST" class="hc-card">
                    <input type="hidden" name="action" value="update_wcu_highlight">
                    <input type="hidden" name="section" value="wcu">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label">Highlight Text (gold)</label>
                            <input type="text" class="form-control" name="highlight_text" value="<?php echo htmlspecialchars($wcu_highlight['highlight_text'] ?? ''); ?>">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Rest of Title</label>
                            <input type="text" class="form-control" name="rest_text" value="<?php echo htmlspecialchars($wcu_highlight['rest_text'] ?? ''); ?>">
                        </div>
                    </div>
                    <button type="submit" class="btn up-btn mt-3">Save Highlight Title</button>
                </form>

                <h5 class="hc-subhead">Highlight Card - Checklist Items</h5>
                <div class="hc-rows">
                    <?php foreach ($wcu_highlight_items as $it): ?>
                    <form method="POST" class="hc-row">
                        <input type="hidden" name="action" value="update_wcu_highlight_item">
                        <input type="hidden" name="section" value="wcu">
                        <input type="hidden" name="id" value="<?php echo $it['id']; ?>">
                        <input type="text" class="form-control" name="icon_class" value="<?php echo htmlspecialchars($it['icon_class']); ?>" placeholder="Icon class">
                        <input type="text" class="form-control" name="text" value="<?php echo htmlspecialchars($it['text']); ?>" placeholder="Text">
                        <input type="number" class="form-control hc-order" name="sort_order" value="<?php echo htmlspecialchars($it['sort_order']); ?>">
                        <button type="submit" class="btn btn-sm up-btn">Save</button>
                        <button type="submit" name="action" value="delete_wcu_highlight_item" class="btn btn-sm btn-outline-danger" onclick="return confirm('Delete this item?');">Delete</button>
                    </form>
                    <?php endforeach; ?>

                    <form method="POST" class="hc-row hc-row-new">
                        <input type="hidden" name="action" value="add_wcu_highlight_item">
                        <input type="hidden" name="section" value="wcu">
                        <input type="text" class="form-control" name="icon_class" placeholder="Icon class">
                        <input type="text" class="form-control" name="text" placeholder="Text">
                        <input type="number" class="form-control hc-order" name="sort_order" placeholder="Order" value="0">
                        <button type="submit" class="btn btn-sm btn-outline-success">+ Add</button>
                        <span></span>
                    </form>
                </div>


    <?php endif; ?>

    <!-- ============ TESTIMONIALS ============ -->
    <?php if ($tab == 'testimonials'): ?>

    <div class="ct-panel-header">
        <div>
            <h3 class="ct-panel-title"><i class="fa-solid fa-comment-dots"></i> Testimonials</h3>
        </div>
    </div>


                <div class="hc-rows">
                    <?php foreach ($testimonials as $t): ?>
                    <form method="POST" class="hc-row hc-row-tsn">
                        <input type="hidden" name="action" value="update_testimonial">
                        <input type="hidden" name="section" value="testimonials">
                        <input type="hidden" name="id" value="<?php echo $t['id']; ?>">
                        <input type="number" min="1" max="5" class="form-control" name="rating" value="<?php echo htmlspecialchars($t['rating']); ?>" placeholder="Rating 1-5">
                        <input type="text" class="form-control" name="quote" value="<?php echo htmlspecialchars($t['quote']); ?>" placeholder="Quote">
                        <input type="text" class="form-control" name="avatar_letter" value="<?php echo htmlspecialchars($t['avatar_letter']); ?>" placeholder="A">
                        <input type="text" class="form-control" name="author_name" value="<?php echo htmlspecialchars($t['author_name']); ?>" placeholder="Name">
                        <input type="text" class="form-control" name="author_location" value="<?php echo htmlspecialchars($t['author_location']); ?>" placeholder="Location">
                        <input type="number" class="form-control hc-order" name="sort_order" value="<?php echo htmlspecialchars($t['sort_order']); ?>">
                        <button type="submit" class="btn btn-sm up-btn">Save</button>
                        <button type="submit" name="action" value="delete_testimonial" class="btn btn-sm btn-outline-danger" onclick="return confirm('Delete this item?');">Delete</button>
                    </form>
                    <?php endforeach; ?>

                    <form method="POST" class="hc-row hc-row-tsn hc-row-new">
                        <input type="hidden" name="action" value="add_testimonial">
                        <input type="hidden" name="section" value="testimonials">
                        <input type="number" min="1" max="5" class="form-control" name="rating" placeholder="Rating 1-5" value="5">
                        <input type="text" class="form-control" name="quote" placeholder="Quote">
                        <input type="text" class="form-control" name="avatar_letter" placeholder="A">
                        <input type="text" class="form-control" name="author_name" placeholder="Name">
                        <input type="text" class="form-control" name="author_location" placeholder="Location">
                        <input type="number" class="form-control hc-order" name="sort_order" placeholder="Order" value="0">
                        <button type="submit" class="btn btn-sm btn-outline-success">+ Add</button>
                        <span></span>
                    </form>
                </div>


    <?php endif; ?>

    <!-- ============ FAQ ============ -->
    <?php if ($tab == 'faq'): ?>

    <div class="ct-panel-header">
        <div>
            <h3 class="ct-panel-title"><i class="fa-solid fa-circle-question"></i> FAQ</h3>
        </div>
    </div>


                <form method="POST" class="hc-card">
                    <input type="hidden" name="action" value="update_faq_header">
                    <input type="hidden" name="section" value="faq">
                    <div class="row g-3">
                        <div class="col-md-4">
                            <label class="form-label">Tag Text</label>
                            <input type="text" class="form-control" name="tag_text" value="<?php echo htmlspecialchars($faq_header['tag_text'] ?? ''); ?>">
                        </div>
                        <div class="col-md-8">
                            <label class="form-label">Title</label>
                            <input type="text" class="form-control" name="title" value="<?php echo htmlspecialchars($faq_header['title'] ?? ''); ?>">
                        </div>
                    </div>
                    <button type="submit" class="btn up-btn mt-3">Save Heading</button>
                </form>

                <h5 class="hc-subhead">Questions <small class="text-muted">(column 1 = left, column 2 = right)</small></h5>
                <div class="hc-rows">
                    <?php foreach ($faq_all as $f): ?>
                    <form method="POST" class="hc-row hc-row-faq">
                        <input type="hidden" name="action" value="update_faq">
                        <input type="hidden" name="section" value="faq">
                        <input type="hidden" name="id" value="<?php echo $f['id']; ?>">
                        <input type="text" class="form-control" name="question" value="<?php echo htmlspecialchars($f['question']); ?>" placeholder="Question">
                        <input type="text" class="form-control" name="answer" value="<?php echo htmlspecialchars($f['answer']); ?>" placeholder="Answer">
                        <select class="form-select" name="column_no">
                            <option value="1" <?php echo $f['column_no']==1?'selected':''; ?>>Column 1</option>
                            <option value="2" <?php echo $f['column_no']!=1?'selected':''; ?>>Column 2</option>
                        </select>
                        <input type="number" class="form-control hc-order" name="sort_order" value="<?php echo htmlspecialchars($f['sort_order']); ?>">
                        <button type="submit" class="btn btn-sm up-btn">Save</button>
                        <button type="submit" name="action" value="delete_faq" class="btn btn-sm btn-outline-danger" onclick="return confirm('Delete this item?');">Delete</button>
                    </form>
                    <?php endforeach; ?>

                    <form method="POST" class="hc-row hc-row-faq hc-row-new">
                        <input type="hidden" name="action" value="add_faq">
                        <input type="hidden" name="section" value="faq">
                        <input type="text" class="form-control" name="question" placeholder="Question">
                        <input type="text" class="form-control" name="answer" placeholder="Answer">
                        <select class="form-select" name="column_no">
                            <option value="1">Column 1</option>
                            <option value="2">Column 2</option>
                        </select>
                        <input type="number" class="form-control hc-order" name="sort_order" placeholder="Order" value="0">
                        <button type="submit" class="btn btn-sm btn-outline-success">+ Add</button>
                        <span></span>
                    </form>
                </div>

                <h5 class="hc-subhead">Bottom CTA Banner ("Ready to Sell Your Phone?")</h5>
                <form method="POST" enctype="multipart/form-data" class="hc-card">
                    <input type="hidden" name="action" value="update_faq_cta">
                    <input type="hidden" name="section" value="faq">
                    <div class="row g-3">
                        <div class="col-md-4">
                            <label class="form-label">Title</label>
                            <input type="text" class="form-control" name="title" value="<?php echo htmlspecialchars($faq_cta['title'] ?? ''); ?>">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Description</label>
                            <input type="text" class="form-control" name="description" value="<?php echo htmlspecialchars($faq_cta['description'] ?? ''); ?>">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Badges <small class="text-muted">(comma separated)</small></label>
                            <input type="text" class="form-control" name="badges" value="<?php echo htmlspecialchars($faq_cta['badges'] ?? ''); ?>" placeholder="Fast,Secure,Best Price">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Phone Image</label>
                            <input type="file" class="form-control" name="phone_image">
                            <?php if (!empty($faq_cta['phone_image'])): ?><img src="<?php echo home_admin_img($faq_cta['phone_image']); ?>" class="hc-preview mt-2"><?php endif; ?>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Background Image</label>
                            <input type="file" class="form-control" name="bg_image">
                            <?php if (!empty($faq_cta['bg_image'])): ?><img src="<?php echo home_admin_img($faq_cta['bg_image']); ?>" class="hc-preview mt-2"><?php endif; ?>
                        </div>
                    </div>
                    <button type="submit" class="btn up-btn mt-3">Save CTA Banner</button>
                </form>


    <?php endif; ?>

    <!-- ============ FOOTER & CONTACT INFO ============ -->
    <?php if ($tab == 'footer'): ?>

    <div class="ct-panel-header">
        <div>
            <h3 class="ct-panel-title"><i class="fa-solid fa-shoe-prints"></i> Footer &amp; Contact Info</h3>
        </div>
    </div>


                <form method="POST" class="hc-card">
                    <input type="hidden" name="action" value="update_footer">
                    <input type="hidden" name="section" value="footer">

                    <div class="row g-3">
                        <div class="col-md-3">
                            <label class="form-label fw-bold">Brand Name</label>
                            <input type="text" class="form-control" name="brand_name" value="<?php echo htmlspecialchars($footer['brand_name'] ?? ''); ?>" placeholder="SellMyPhone">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label fw-bold">Brand Highlight <small class="text-muted">(gold word)</small></label>
                            <input type="text" class="form-control" name="brand_highlight" value="<?php echo htmlspecialchars($footer['brand_highlight'] ?? ''); ?>" placeholder="Dubai">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold">Tagline</label>
                            <input type="text" class="form-control" name="tagline" value="<?php echo htmlspecialchars($footer['tagline'] ?? ''); ?>">
                        </div>

                        <div class="col-md-3">
                            <label class="form-label fw-bold">WhatsApp Number</label>
                            <input type="text" class="form-control" name="whatsapp_number" value="<?php echo htmlspecialchars($footer['whatsapp_number'] ?? ''); ?>" placeholder="971502166562">
                            <small class="text-muted">No spaces/+. Used in nav CTA, footer, AND the floating WhatsApp button site-wide.</small>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label fw-bold">Phone Number</label>
                            <input type="text" class="form-control" name="phone_number" value="<?php echo htmlspecialchars($footer['phone_number'] ?? ''); ?>" placeholder="971502166562">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label fw-bold">Email</label>
                            <input type="email" class="form-control" name="email" value="<?php echo htmlspecialchars($footer['email'] ?? ''); ?>">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label fw-bold">Address</label>
                            <input type="text" class="form-control" name="address" value="<?php echo htmlspecialchars($footer['address'] ?? ''); ?>">
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-bold">Facebook URL</label>
                            <input type="text" class="form-control" name="facebook_url" value="<?php echo htmlspecialchars($footer['facebook_url'] ?? ''); ?>" placeholder="https://facebook.com/...">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold">Instagram URL</label>
                            <input type="text" class="form-control" name="instagram_url" value="<?php echo htmlspecialchars($footer['instagram_url'] ?? ''); ?>" placeholder="https://instagram.com/...">
                        </div>

                        <div class="col-12">
                            <label class="form-label fw-bold">Copyright Text</label>
                            <input type="text" class="form-control" name="copyright_text" value="<?php echo htmlspecialchars($footer['copyright_text'] ?? ''); ?>">
                        </div>
                    </div>

                    <button type="submit" class="btn up-btn mt-3">Save Footer &amp; Contact Info</button>
                </form>


    <?php endif; ?>

    </div><!--ct-panel-->

</div><!--ct-shell-->

</div><!--main content-->

</body>
</html>