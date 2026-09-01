<?php
session_start();

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

// which tab to reopen after a save (falls back to hero)
$tab = $_GET['tab'] ?? ($_POST['section'] ?? 'hero');

// ==================================================================
//  POST handling
// ==================================================================
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['action'])) {

    $action = $_POST['action'];

    switch ($action) {

        // ---------- HERO ----------
        case 'update_hero':
            home_save_single($conn, 'iphone_hero',
                ['badge_text', 'title_pre', 'title_highlight', 'subtitle', 'button_text', 'button_link'],
               ['hero_image' => 'hero_image', 'bg_image' => 'bg_image']);
            break;

        // ---------- BRAND CARD ----------
        case 'update_brand':
            home_save_single($conn, 'iphone_brand',
                ['badge_text', 'heading_pre', 'heading_highlight', 'heading_post', 'subtext',
                 'left_title', 'left_text', 'right_badge', 'right_logo_text', 'right_text',
                 'button_text', 'button_link']);
            break;
        case 'add_brand_feature':
            home_row_add($conn, 'iphone_brand_features', ['icon', 'title', 'subtitle', 'sort_order']);
            break;
        case 'update_brand_feature':
            home_row_update($conn, 'iphone_brand_features', $_POST['id'], ['icon', 'title', 'subtitle', 'sort_order']);
            break;
        case 'delete_brand_feature':
            home_row_delete($conn, 'iphone_brand_features', $_POST['id']);
            break;

        // ---------- SERIES / MODELS ----------
        case 'update_series':
            home_save_single($conn, 'iphone_series',
                ['tag_text', 'title', 'text', 'button_text', 'button_link']);
            break;
        case 'add_series_card':
            home_row_add($conn, 'iphone_series_cards', ['icon', 'card_title', 'card_title_paren', 'desc', 'models', 'sort_order']);
            break;
        case 'update_series_card':
            home_row_update($conn, 'iphone_series_cards', $_POST['id'], ['icon', 'card_title', 'card_title_paren', 'desc', 'models', 'sort_order']);
            break;
        case 'delete_series_card':
            home_row_delete($conn, 'iphone_series_cards', $_POST['id']);
            break;

        // ---------- PROCESS STEPS ----------
        case 'update_process':
            home_save_single($conn, 'iphone_process', ['tag_text', 'title', 'subtitle']);
            break;
        case 'add_process_step':
            home_row_add($conn, 'iphone_process_steps', ['step_label', 'card_title', 'icon', 'detail_title', 'detail_desc', 'sort_order']);
            break;
        case 'update_process_step':
            home_row_update($conn, 'iphone_process_steps', $_POST['id'], ['step_label', 'card_title', 'icon', 'detail_title', 'detail_desc', 'sort_order']);
            break;
        case 'delete_process_step':
            home_row_delete($conn, 'iphone_process_steps', $_POST['id']);
            break;

        // ---------- LOCATION / COVERAGE ----------
        case 'update_location':
            home_save_single($conn, 'iphone_location', ['badge_text', 'heading', 'text']);
            break;
        case 'add_location_area':
            home_row_add($conn, 'iphone_location_areas', ['area_name', 'sort_order']);
            break;
        case 'update_location_area':
            home_row_update($conn, 'iphone_location_areas', $_POST['id'], ['area_name', 'sort_order']);
            break;
        case 'delete_location_area':
            home_row_delete($conn, 'iphone_location_areas', $_POST['id']);
            break;

        // ---------- COMPARISON TABLE ----------
        case 'update_comparison':
            home_save_single($conn, 'iphone_comparison',
                ['brand_name', 'badge_text', 'heading', 'text', 'col2_header', 'col3_header', 'col4_header']);
            break;
        case 'add_comparison_row':
            home_row_add($conn, 'iphone_comparison_rows', ['feature', 'col1_value', 'col2_value', 'col3_value', 'col4_value', 'sort_order']);
            break;
        case 'update_comparison_row':
            home_row_update($conn, 'iphone_comparison_rows', $_POST['id'], ['feature', 'col1_value', 'col2_value', 'col3_value', 'col4_value', 'sort_order']);
            break;
        case 'delete_comparison_row':
            home_row_delete($conn, 'iphone_comparison_rows', $_POST['id']);
            break;

        // ---------- ACCEPTANCE POLICY ----------
        case 'update_policy':
            home_save_single($conn, 'iphone_policy',
                ['tag_text', 'title', 'subtitle', 'banner_text', 'banner_highlight', 'banner_title', 'banner_sub']);
            break;
        case 'add_policy_card':
            home_row_add($conn, 'iphone_policy_cards', ['icon', 'title', 'list_type', 'items', 'sort_order']);
            break;
        case 'update_policy_card':
            home_row_update($conn, 'iphone_policy_cards', $_POST['id'], ['icon', 'title', 'list_type', 'items', 'sort_order']);
            break;
        case 'delete_policy_card':
            home_row_delete($conn, 'iphone_policy_cards', $_POST['id']);
            break;

        // ---------- FAQ ----------
        case 'update_faq_header':
            home_save_single($conn, 'iphone_faq_header', ['tag_text', 'title']);
            break;
        case 'add_faq':
            home_row_add($conn, 'iphone_faq', ['question', 'answer', 'column_no', 'sort_order']);
            break;
        case 'update_faq':
            home_row_update($conn, 'iphone_faq', $_POST['id'], ['question', 'answer', 'column_no', 'sort_order']);
            break;
        case 'delete_faq':
            home_row_delete($conn, 'iphone_faq', $_POST['id']);
            break;

        // ---------- CTA ----------
        case 'update_cta':
            home_save_single($conn, 'iphone_cta',
                ['tag_text', 'heading', 'text', 'card_heading', 'primary_btn_text', 'primary_btn_link', 'whatsapp_link']);
            break;
    }

    $_SESSION['msg'] = "<div class='alert alert-success mb-0'>Saved successfully.</div>";
    header("Location: sell-iphone-content.php?tab=" . urlencode($tab));
    exit();
}

// ==================================================================
//  Load data for every tab (cheap single-page-load reads, same
//  pattern as home-content.php)
// ==================================================================
$hero        = home_single($conn, 'iphone_hero');

$brand       = home_single($conn, 'iphone_brand');
$brand_feats = home_rows($conn, 'iphone_brand_features');

$series      = home_single($conn, 'iphone_series');
$series_cards = home_rows($conn, 'iphone_series_cards');

$process     = home_single($conn, 'iphone_process');
$proc_steps  = home_rows($conn, 'iphone_process_steps');

$loc         = home_single($conn, 'iphone_location');
$loc_areas   = home_rows($conn, 'iphone_location_areas');

$cmp         = home_single($conn, 'iphone_comparison');
$cmp_rows    = home_rows($conn, 'iphone_comparison_rows');

$policy      = home_single($conn, 'iphone_policy');
$policy_cards = home_rows($conn, 'iphone_policy_cards');

$faqh        = home_single($conn, 'iphone_faq_header');
$faq_all     = home_rows($conn, 'iphone_faq', 'column_no ASC, sort_order ASC, id ASC');

$cta         = home_single($conn, 'iphone_cta');

require 'includes/ad-header.php';
require_once 'includes/sidebar.php';
?>
<link rel="stylesheet" href="home-content.css">
<link rel="stylesheet" href="content-tabs.css">

<div class="container-fluid main-content">

<nav class="navbar navbar-expand-lg">
   <div class="container-fluid">
        <a class="navbar-brand fw-bold text-dark fs-3" href="#">Sell iPhone Page Content</a>
    </div>
</nav><hr class="m-0">

<div class="mt-3"><?php echo $msg; ?></div>

<div class="ct-shell mt-3">

    <!-- Section nav -->
    <div class="ct-nav nav flex-column" role="tablist">
        <a class="nav-link <?php echo $tab=='hero' ? 'active' : ''; ?>" href="?tab=hero"><i class="fa-solid fa-image"></i> Hero</a>
        <a class="nav-link <?php echo $tab=='brand' ? 'active' : ''; ?>" href="?tab=brand"><i class="fa-solid fa-mobile-screen"></i> Brand Card</a>
        <a class="nav-link <?php echo $tab=='series' ? 'active' : ''; ?>" href="?tab=series"><i class="fa-solid fa-layer-group"></i> Series / Models</a>
        <a class="nav-link <?php echo $tab=='process' ? 'active' : ''; ?>" href="?tab=process"><i class="fa-solid fa-list-ol"></i> Process Steps</a>
        <a class="nav-link <?php echo $tab=='location' ? 'active' : ''; ?>" href="?tab=location"><i class="fa-solid fa-location-dot"></i> Location</a>
        <a class="nav-link <?php echo $tab=='comparison' ? 'active' : ''; ?>" href="?tab=comparison"><i class="fa-solid fa-table"></i> Comparison</a>
        <a class="nav-link <?php echo $tab=='policy' ? 'active' : ''; ?>" href="?tab=policy"><i class="fa-solid fa-shield-halved"></i> Policy</a>
        <a class="nav-link <?php echo $tab=='faq' ? 'active' : ''; ?>" href="?tab=faq"><i class="fa-solid fa-circle-question"></i> FAQ</a>
        <a class="nav-link <?php echo $tab=='cta' ? 'active' : ''; ?>" href="?tab=cta"><i class="fa-solid fa-bolt"></i> CTA</a>
    </div>

    <!-- Panel -->
    <div class="ct-panel">

    <?php if ($tab == 'hero'): ?>

        <div class="ct-panel-header">
            <div>
                <h3 class="ct-panel-title">Hero Section</h3>
                <p class="ct-panel-sub">The top banner visitors see first on the Sell iPhone page.</p>
            </div>
        </div>

        <form method="POST" enctype="multipart/form-data">
            <input type="hidden" name="action" value="update_hero">
            <input type="hidden" name="section" value="hero">

            <div class="hc-card">
                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label fw-bold">Badge Text</label>
                        <input type="text" class="form-control" name="badge_text" value="<?php echo htmlspecialchars($hero['badge_text'] ?? 'IPHONE BUYBACK IN DUBAI'); ?>">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label fw-bold">Heading (before highlight)</label>
                        <input type="text" class="form-control" name="title_pre" value="<?php echo htmlspecialchars($hero['title_pre'] ?? 'Sell Your iPhone'); ?>">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label fw-bold">Heading (highlighted part)</label>
                        <input type="text" class="form-control" name="title_highlight" value="<?php echo htmlspecialchars($hero['title_highlight'] ?? 'in Dubai'); ?>">
                    </div>
                    <div class="col-12">
                        <label class="form-label fw-bold">Subtitle</label>
                        <textarea class="form-control" name="subtitle" rows="4"><?php echo htmlspecialchars($hero['subtitle'] ?? ''); ?></textarea>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-bold">Button Text</label>
                        <input type="text" class="form-control" name="button_text" value="<?php echo htmlspecialchars($hero['button_text'] ?? 'Get Instant Price Now'); ?>">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-bold">Button Link</label>
                        <input type="text" class="form-control" name="button_link" value="<?php echo htmlspecialchars($hero['button_link'] ?? '#apple'); ?>">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label fw-bold">Hero Image</label>
                        <?php if (!empty($hero['hero_image'])): ?>
                            <img src="<?php echo home_admin_img($hero['hero_image']); ?>" class="hc-preview mb-2">
                        <?php endif; ?>
                        <input type="file" class="form-control" name="hero_image">
                    </div>
                     <div class="col-md-4">
                        <label class="form-label fw-bold">Background Image (full hero banner background)</label>
                        <?php if (!empty($hero['bg_image'])): ?>
                            <img src="<?php echo home_admin_img($hero['bg_image']); ?>" class="hc-preview mb-2">
                        <?php endif; ?>
                        <input type="file" class="form-control" name="bg_image">
                    </div>
                </div>
            </div>

            <button type="submit" class="btn btn-dark mt-3">Save Hero Section</button>
        </form>

    <?php elseif ($tab == 'brand'): ?>

        <div class="ct-panel-header">
            <div>
                <h3 class="ct-panel-title">Brand Card Section</h3>
                <p class="ct-panel-sub">The "We Buy All Latest iPhones" quote card, right after the hero.</p>
            </div>
        </div>

        <form method="POST" class="hc-card">
            <input type="hidden" name="action" value="update_brand">
            <input type="hidden" name="section" value="brand">
            <div class="row g-3">
                <div class="col-md-4">
                    <label class="form-label fw-bold">Badge Text</label>
                    <input type="text" class="form-control" name="badge_text" value="<?php echo htmlspecialchars($brand['badge_text'] ?? 'Get Instant Quote'); ?>">
                </div>
                <div class="col-md-8">
                    <label class="form-label fw-bold">Right Card - Text</label>
                    <input type="text" class="form-control" name="right_text" value="<?php echo htmlspecialchars($brand['right_text'] ?? 'Select iPhone to get an instant price for your phone.'); ?>">
                </div>

                <div class="col-md-4">
                    <label class="form-label fw-bold">Heading (before highlight)</label>
                    <input type="text" class="form-control" name="heading_pre" value="<?php echo htmlspecialchars($brand['heading_pre'] ?? 'Sell Your'); ?>">
                </div>
                <div class="col-md-4">
                    <label class="form-label fw-bold">Heading (highlighted)</label>
                    <input type="text" class="form-control" name="heading_highlight" value="<?php echo htmlspecialchars($brand['heading_highlight'] ?? 'iPhone'); ?>">
                </div>
                <div class="col-md-4">
                    <label class="form-label fw-bold">Heading (after highlight)</label>
                    <input type="text" class="form-control" name="heading_post" value="<?php echo htmlspecialchars($brand['heading_post'] ?? 'the Smart Way'); ?>">
                </div>

                <div class="col-12">
                    <label class="form-label fw-bold">Subtext</label>
                    <input type="text" class="form-control" name="subtext" value="<?php echo htmlspecialchars($brand['subtext'] ?? 'Get an instant, no-obligation price for your iPhone and turn it into cash today.'); ?>">
                </div>

                <div class="col-md-6">
                    <label class="form-label fw-bold">Left Card - Title</label>
                    <input type="text" class="form-control" name="left_title" value="<?php echo htmlspecialchars($brand['left_title'] ?? 'We Buy All Latest iPhones'); ?>">
                </div>
                <div class="col-md-6">
                    <label class="form-label fw-bold">Left Card - Text</label>
                    <input type="text" class="form-control" name="left_text" value="<?php echo htmlspecialchars($brand['left_text'] ?? 'Sell your iPhone in Dubai and get the best value instantly. Quick, easy and 100% secure process.'); ?>">
                </div>

                <div class="col-md-4">
                    <label class="form-label fw-bold">Right Card - Badge</label>
                    <input type="text" class="form-control" name="right_badge" value="<?php echo htmlspecialchars($brand['right_badge'] ?? 'SELL IPHONE'); ?>">
                </div>
                <div class="col-md-4">
                    <label class="form-label fw-bold">Right Card - Logo Text</label>
                    <input type="text" class="form-control" name="right_logo_text" value="<?php echo htmlspecialchars($brand['right_logo_text'] ?? 'iPhone'); ?>">
                </div>
                <div class="col-md-4">
                    <label class="form-label fw-bold">Right Card - Button Link</label>
                    <input type="text" class="form-control" name="button_link" value="<?php echo htmlspecialchars($brand['button_link'] ?? 'apple.php#apple'); ?>">
                </div>
                <div class="col-md-6">
                    <label class="form-label fw-bold">Right Card - Button Text</label>
                    <input type="text" class="form-control" name="button_text" value="<?php echo htmlspecialchars($brand['button_text'] ?? 'Select iPhone'); ?>">
                </div>
            </div>
            <button type="submit" class="btn up-btn mt-3">Save Brand Card</button>
        </form>

        <h5 class="hc-subhead">Feature Bullets <small class="text-muted">(the checklist under the left card title)</small></h5>
        <div class="hc-rows">
            <?php foreach ($brand_feats as $f): ?>
            <form method="POST" class="hc-row">
                <input type="hidden" name="action" value="update_brand_feature">
                <input type="hidden" name="section" value="brand">
                <input type="hidden" name="id" value="<?php echo $f['id']; ?>">
                <input type="text" class="form-control" name="icon" value="<?php echo htmlspecialchars($f['icon']); ?>" placeholder="fa-solid fa-check (icon class)">
                <input type="text" class="form-control" name="title" value="<?php echo htmlspecialchars($f['title']); ?>" placeholder="Title">
                <input type="text" class="form-control" name="subtitle" value="<?php echo htmlspecialchars($f['subtitle']); ?>" placeholder="Subtitle">
                <input type="number" class="form-control hc-order" name="sort_order" value="<?php echo htmlspecialchars($f['sort_order']); ?>">
                <button type="submit" class="btn btn-sm up-btn">Save</button>
                <button type="submit" name="action" value="delete_brand_feature" class="btn btn-sm btn-outline-danger" onclick="return confirm('Delete this item?');">Delete</button>
            </form>
            <?php endforeach; ?>

            <form method="POST" class="hc-row hc-row-new">
                <input type="hidden" name="action" value="add_brand_feature">
                <input type="hidden" name="section" value="brand">
                <input type="text" class="form-control" name="icon" placeholder="fa-solid fa-check (icon class)">
                <input type="text" class="form-control" name="title" placeholder="Title">
                <input type="text" class="form-control" name="subtitle" placeholder="Subtitle">
                <input type="number" class="form-control hc-order" name="sort_order" placeholder="Order" value="0">
                <button type="submit" class="btn btn-sm btn-outline-success">+ Add</button>
                <span></span>
            </form>
        </div>

    <?php elseif ($tab == 'series'): ?>

        <div class="ct-panel-header">
            <div>
                <h3 class="ct-panel-title">Series / Models Section</h3>
                <p class="ct-panel-sub">The "We Buy All Latest iPhones" grid with model cards.</p>
            </div>
        </div>

        <form method="POST" class="hc-card">
            <input type="hidden" name="action" value="update_series">
            <input type="hidden" name="section" value="series">
            <div class="row g-3">
                <div class="col-md-4">
                    <label class="form-label fw-bold">Tag Text</label>
                    <input type="text" class="form-control" name="tag_text" value="<?php echo htmlspecialchars($series['tag_text'] ?? 'IPHONE BUYBACK'); ?>">
                </div>
                <div class="col-md-8">
                    <label class="form-label fw-bold">Title</label>
                    <input type="text" class="form-control" name="title" value="<?php echo htmlspecialchars($series['title'] ?? 'We Buy All Latest iPhones'); ?>">
                </div>
                <div class="col-12">
                    <label class="form-label fw-bold">Text</label>
                    <textarea class="form-control" name="text" rows="2"><?php echo htmlspecialchars($series['text'] ?? ''); ?></textarea>
                </div>
                <div class="col-md-8">
                    <label class="form-label fw-bold">Button Text</label>
                    <input type="text" class="form-control" name="button_text" value="<?php echo htmlspecialchars($series['button_text'] ?? 'View All iPhone Models'); ?>">
                </div>
                <div class="col-md-4">
                    <label class="form-label fw-bold">Button Link</label>
                    <input type="text" class="form-control" name="button_link" value="<?php echo htmlspecialchars($series['button_link'] ?? 'apple.php#apple'); ?>">
                </div>
            </div>
            <button type="submit" class="btn up-btn mt-3">Save Series Heading</button>
        </form>

        <h5 class="hc-subhead">Model Cards</h5>
        <div class="hc-rows">
            <?php foreach ($series_cards as $c): ?>
            <form method="POST" class="hc-row hc-row-wide">
                <input type="hidden" name="action" value="update_series_card">
                <input type="hidden" name="section" value="series">
                <input type="hidden" name="id" value="<?php echo $c['id']; ?>">
                <input type="text" class="form-control" name="icon" value="<?php echo htmlspecialchars($c['icon']); ?>" placeholder="Icon class">
                <input type="text" class="form-control" name="card_title" value="<?php echo htmlspecialchars($c['card_title']); ?>" placeholder="Card title e.g. iPhone 17">
                <input type="text" class="form-control" name="card_title_paren" value="<?php echo htmlspecialchars($c['card_title_paren']); ?>" placeholder="(Series)">
                <input type="text" class="form-control" name="desc" value="<?php echo htmlspecialchars($c['desc']); ?>" placeholder="Short description">
                <input type="text" class="form-control" name="models" value="<?php echo htmlspecialchars($c['models']); ?>" placeholder="Model1, Model2, Model3 (comma separated)">
                <input type="number" class="form-control hc-order" name="sort_order" value="<?php echo htmlspecialchars($c['sort_order']); ?>">
                <button type="submit" class="btn btn-sm up-btn">Save</button>
                <button type="submit" name="action" value="delete_series_card" class="btn btn-sm btn-outline-danger" onclick="return confirm('Delete this item?');">Delete</button>
            </form>
            <?php endforeach; ?>

            <form method="POST" class="hc-row hc-row-wide hc-row-new">
                <input type="hidden" name="action" value="add_series_card">
                <input type="hidden" name="section" value="series">
                <input type="text" class="form-control" name="icon" placeholder="Icon class">
                <input type="text" class="form-control" name="card_title" placeholder="Card title">
                <input type="text" class="form-control" name="card_title_paren" placeholder="(Series)">
                <input type="text" class="form-control" name="desc" placeholder="Short description">
                <input type="text" class="form-control" name="models" placeholder="Model1, Model2, Model3">
                <input type="number" class="form-control hc-order" name="sort_order" placeholder="Order" value="0">
                <button type="submit" class="btn btn-sm btn-outline-success">+ Add</button>
                <span></span>
            </form>
        </div>

    <?php elseif ($tab == 'process'): ?>

        <div class="ct-panel-header">
            <div>
                <h3 class="ct-panel-title">Process Steps Section</h3>
                <p class="ct-panel-sub">The "Sell Your iPhone in 3 Easy Steps" interactive stepper.</p>
            </div>
        </div>

        <form method="POST" class="hc-card">
            <input type="hidden" name="action" value="update_process">
            <input type="hidden" name="section" value="process">
            <div class="row g-3">
                <div class="col-md-4">
                    <label class="form-label fw-bold">Tag Text</label>
                    <input type="text" class="form-control" name="tag_text" value="<?php echo htmlspecialchars($process['tag_text'] ?? 'Simple 3-Step Flow'); ?>">
                </div>
                <div class="col-md-8">
                    <label class="form-label fw-bold">Title</label>
                    <input type="text" class="form-control" name="title" value="<?php echo htmlspecialchars($process['title'] ?? 'Sell Your iPhone in 3 Easy Steps'); ?>">
                </div>
                <div class="col-12">
                    <label class="form-label fw-bold">Subtitle</label>
                    <input type="text" class="form-control" name="subtitle" value="<?php echo htmlspecialchars($process['subtitle'] ?? ''); ?>">
                </div>
            </div>
            <button type="submit" class="btn up-btn mt-3">Save Process Heading</button>
        </form>

        <h5 class="hc-subhead">Steps</h5>
        <div class="hc-rows">
            <?php foreach ($proc_steps as $s): ?>
            <form method="POST" class="hc-row hc-row-wide">
                <input type="hidden" name="action" value="update_process_step">
                <input type="hidden" name="section" value="process">
                <input type="hidden" name="id" value="<?php echo $s['id']; ?>">
                <input type="text" class="form-control" name="step_label" value="<?php echo htmlspecialchars($s['step_label']); ?>" placeholder="Step 1">
                <input type="text" class="form-control" name="card_title" value="<?php echo htmlspecialchars($s['card_title']); ?>" placeholder="Short card title">
                <input type="text" class="form-control" name="icon" value="<?php echo htmlspecialchars($s['icon']); ?>" placeholder="Icon class">
                <input type="text" class="form-control" name="detail_title" value="<?php echo htmlspecialchars($s['detail_title']); ?>" placeholder="Detail title">
                <input type="text" class="form-control" name="detail_desc" value="<?php echo htmlspecialchars($s['detail_desc']); ?>" placeholder="Detail description">
                <input type="number" class="form-control hc-order" name="sort_order" value="<?php echo htmlspecialchars($s['sort_order']); ?>">
                <button type="submit" class="btn btn-sm up-btn">Save</button>
                <button type="submit" name="action" value="delete_process_step" class="btn btn-sm btn-outline-danger" onclick="return confirm('Delete this item?');">Delete</button>
            </form>
            <?php endforeach; ?>

            <form method="POST" class="hc-row hc-row-wide hc-row-new">
                <input type="hidden" name="action" value="add_process_step">
                <input type="hidden" name="section" value="process">
                <input type="text" class="form-control" name="step_label" placeholder="Step 1">
                <input type="text" class="form-control" name="card_title" placeholder="Short card title">
                <input type="text" class="form-control" name="icon" placeholder="Icon class">
                <input type="text" class="form-control" name="detail_title" placeholder="Detail title">
                <input type="text" class="form-control" name="detail_desc" placeholder="Detail description">
                <input type="number" class="form-control hc-order" name="sort_order" placeholder="Order" value="0">
                <button type="submit" class="btn btn-sm btn-outline-success">+ Add</button>
                <span></span>
            </form>
        </div>

    <?php elseif ($tab == 'location'): ?>

        <div class="ct-panel-header">
            <div>
                <h3 class="ct-panel-title">Location / Coverage Section</h3>
                <p class="ct-panel-sub">The doorstep pickup coverage area pills.</p>
            </div>
        </div>

        <form method="POST" class="hc-card">
            <input type="hidden" name="action" value="update_location">
            <input type="hidden" name="section" value="location">
            <div class="row g-3">
                <div class="col-md-4">
                    <label class="form-label fw-bold">Badge Text</label>
                    <input type="text" class="form-control" name="badge_text" value="<?php echo htmlspecialchars($loc['badge_text'] ?? 'Full Dubai Reach'); ?>">
                </div>
                <div class="col-md-8">
                    <label class="form-label fw-bold">Heading</label>
                    <input type="text" class="form-control" name="heading" value="<?php echo htmlspecialchars($loc['heading'] ?? 'Same-Day Doorstep Pickup Across Dubai'); ?>">
                </div>
                <div class="col-12">
                    <label class="form-label fw-bold">Text</label>
                    <textarea class="form-control" name="text" rows="2"><?php echo htmlspecialchars($loc['text'] ?? ''); ?></textarea>
                </div>
            </div>
            <button type="submit" class="btn up-btn mt-3">Save Location Heading</button>
        </form>

        <h5 class="hc-subhead">Coverage Area Pills</h5>
        <div class="hc-rows">
            <?php foreach ($loc_areas as $a): ?>
            <form method="POST" class="hc-row">
                <input type="hidden" name="action" value="update_location_area">
                <input type="hidden" name="section" value="location">
                <input type="hidden" name="id" value="<?php echo $a['id']; ?>">
                <input type="text" class="form-control" name="area_name" value="<?php echo htmlspecialchars($a['area_name']); ?>" placeholder="Area name">
                <input type="number" class="form-control hc-order" name="sort_order" value="<?php echo htmlspecialchars($a['sort_order']); ?>">
                <button type="submit" class="btn btn-sm up-btn">Save</button>
                <button type="submit" name="action" value="delete_location_area" class="btn btn-sm btn-outline-danger" onclick="return confirm('Delete this item?');">Delete</button>
            </form>
            <?php endforeach; ?>

            <form method="POST" class="hc-row hc-row-new">
                <input type="hidden" name="action" value="add_location_area">
                <input type="hidden" name="section" value="location">
                <input type="text" class="form-control" name="area_name" placeholder="Area name">
                <input type="number" class="form-control hc-order" name="sort_order" placeholder="Order" value="0">
                <button type="submit" class="btn btn-sm btn-outline-success">+ Add</button>
                <span></span>
            </form>
        </div>

    <?php elseif ($tab == 'comparison'): ?>

        <div class="ct-panel-header">
            <div>
                <h3 class="ct-panel-title">Comparison Table Section</h3>
                <p class="ct-panel-sub">"SellMyPhone vs Traditional Selling Options" table.</p>
            </div>
        </div>

        <form method="POST" class="hc-card">
            <input type="hidden" name="action" value="update_comparison">
            <input type="hidden" name="section" value="comparison">
            <div class="row g-3">
                <div class="col-md-4">
                    <label class="form-label fw-bold">Your Brand Name (column 1 header)</label>
                    <input type="text" class="form-control" name="brand_name" value="<?php echo htmlspecialchars($cmp['brand_name'] ?? 'SellMyPhone'); ?>">
                </div>
                <div class="col-md-8">
                    <label class="form-label fw-bold">Badge Text</label>
                    <input type="text" class="form-control" name="badge_text" value="<?php echo htmlspecialchars($cmp['badge_text'] ?? 'The Smarter Alternative'); ?>">
                </div>
                <div class="col-12">
                    <label class="form-label fw-bold">Heading</label>
                    <input type="text" class="form-control" name="heading" value="<?php echo htmlspecialchars($cmp['heading'] ?? 'vs Traditional Selling Options'); ?>">
                </div>
                <div class="col-12">
                    <label class="form-label fw-bold">Text</label>
                    <textarea class="form-control" name="text" rows="2"><?php echo htmlspecialchars($cmp['text'] ?? ''); ?></textarea>
                </div>
                <div class="col-md-4">
                    <label class="form-label fw-bold">Column 2 Header</label>
                    <textarea class="form-control" name="col2_header" rows="2" placeholder="Deira / Mall&#10;Phone Shops"><?php echo htmlspecialchars($cmp['col2_header'] ?? ''); ?></textarea>
                </div>
                <div class="col-md-4">
                    <label class="form-label fw-bold">Column 3 Header</label>
                    <textarea class="form-control" name="col3_header" rows="2" placeholder="Online Classified&#10;Ads (Dubizzle)"><?php echo htmlspecialchars($cmp['col3_header'] ?? ''); ?></textarea>
                </div>
                <div class="col-md-4">
                    <label class="form-label fw-bold">Column 4 Header</label>
                    <textarea class="form-control" name="col4_header" rows="2" placeholder="Carrier & Store&#10;Trade-Ins"><?php echo htmlspecialchars($cmp['col4_header'] ?? ''); ?></textarea>
                </div>
            </div>
            <button type="submit" class="btn up-btn mt-3">Save Comparison Heading</button>
        </form>

        <h5 class="hc-subhead">Comparison Rows <small class="text-muted">(use a new line inside a cell for line breaks)</small></h5>
        <div class="hc-rows">
            <?php foreach ($cmp_rows as $r): ?>
            <form method="POST" class="hc-row hc-row-wide">
                <input type="hidden" name="action" value="update_comparison_row">
                <input type="hidden" name="section" value="comparison">
                <input type="hidden" name="id" value="<?php echo $r['id']; ?>">
                <input type="text" class="form-control" name="feature" value="<?php echo htmlspecialchars($r['feature']); ?>" placeholder="Feature">
                <input type="text" class="form-control" name="col1_value" value="<?php echo htmlspecialchars($r['col1_value']); ?>" placeholder="Your value">
                <input type="text" class="form-control" name="col2_value" value="<?php echo htmlspecialchars($r['col2_value']); ?>" placeholder="Column 2 value">
                <input type="text" class="form-control" name="col3_value" value="<?php echo htmlspecialchars($r['col3_value']); ?>" placeholder="Column 3 value">
                <input type="text" class="form-control" name="col4_value" value="<?php echo htmlspecialchars($r['col4_value']); ?>" placeholder="Column 4 value">
                <input type="number" class="form-control hc-order" name="sort_order" value="<?php echo htmlspecialchars($r['sort_order']); ?>">
                <button type="submit" class="btn btn-sm up-btn">Save</button>
                <button type="submit" name="action" value="delete_comparison_row" class="btn btn-sm btn-outline-danger" onclick="return confirm('Delete this item?');">Delete</button>
            </form>
            <?php endforeach; ?>

            <form method="POST" class="hc-row hc-row-wide hc-row-new">
                <input type="hidden" name="action" value="add_comparison_row">
                <input type="hidden" name="section" value="comparison">
                <input type="text" class="form-control" name="feature" placeholder="Feature">
                <input type="text" class="form-control" name="col1_value" placeholder="Your value">
                <input type="text" class="form-control" name="col2_value" placeholder="Column 2 value">
                <input type="text" class="form-control" name="col3_value" placeholder="Column 3 value">
                <input type="text" class="form-control" name="col4_value" placeholder="Column 4 value">
                <input type="number" class="form-control hc-order" name="sort_order" placeholder="Order" value="0">
                <button type="submit" class="btn btn-sm btn-outline-success">+ Add</button>
                <span></span>
            </form>
        </div>

    <?php elseif ($tab == 'policy'): ?>

        <div class="ct-panel-header">
            <div>
                <h3 class="ct-panel-title">Acceptance Policy Section</h3>
                <p class="ct-panel-sub">"iPhone Models & Conditions We Buy" cards + trust banner.</p>
            </div>
        </div>

        <form method="POST" class="hc-card">
            <input type="hidden" name="action" value="update_policy">
            <input type="hidden" name="section" value="policy">
            <div class="row g-3">
                <div class="col-md-4">
                    <label class="form-label fw-bold">Tag Text</label>
                    <input type="text" class="form-control" name="tag_text" value="<?php echo htmlspecialchars($policy['tag_text'] ?? 'CLEAR ACCEPTANCE POLICY'); ?>">
                </div>
                <div class="col-md-8">
                    <label class="form-label fw-bold">Title <small class="text-muted">("in Dubai" is added automatically after this)</small></label>
                    <input type="text" class="form-control" name="title" value="<?php echo htmlspecialchars($policy['title'] ?? 'iPhone Models & Conditions We Buy'); ?>">
                </div>
                <div class="col-12">
                    <label class="form-label fw-bold">Subtitle</label>
                    <textarea class="form-control" name="subtitle" rows="2"><?php echo htmlspecialchars($policy['subtitle'] ?? ''); ?></textarea>
                </div>
                <div class="col-md-6">
                    <label class="form-label fw-bold">Trust Banner Text</label>
                    <input type="text" class="form-control" name="banner_text" value="<?php echo htmlspecialchars($policy['banner_text'] ?? ''); ?>">
                </div>
                <div class="col-md-6">
                    <label class="form-label fw-bold">Trust Banner Highlight</label>
                    <input type="text" class="form-control" name="banner_highlight" value="<?php echo htmlspecialchars($policy['banner_highlight'] ?? 'No hidden terms. No surprises.'); ?>">
                </div>
                <div class="col-md-6">
                    <label class="form-label fw-bold">Trust Banner Title</label>
                    <input type="text" class="form-control" name="banner_title" value="<?php echo htmlspecialchars($policy['banner_title'] ?? 'TRUSTED IN DUBAI'); ?>">
                </div>
                <div class="col-md-6">
                    <label class="form-label fw-bold">Trust Banner Sub</label>
                    <input type="text" class="form-control" name="banner_sub" value="<?php echo htmlspecialchars($policy['banner_sub'] ?? 'Fair - Fast - Transparent'); ?>">
                </div>
            </div>
            <button type="submit" class="btn up-btn mt-3">Save Policy Heading</button>
        </form>

        <h5 class="hc-subhead">Policy Cards <small class="text-muted">(one line per item in the "Items" box)</small></h5>
        <div class="hc-rows">
            <?php foreach ($policy_cards as $c): ?>
            <form method="POST" class="hc-row hc-row-wide">
                <input type="hidden" name="action" value="update_policy_card">
                <input type="hidden" name="section" value="policy">
                <input type="hidden" name="id" value="<?php echo $c['id']; ?>">
                <input type="text" class="form-control" name="icon" value="<?php echo htmlspecialchars($c['icon']); ?>" placeholder="Icon class">
                <input type="text" class="form-control" name="title" value="<?php echo htmlspecialchars($c['title']); ?>" placeholder="Card title">
                <select class="form-select" name="list_type">
                    <option value="check" <?php echo $c['list_type']!='cross'?'selected':''; ?>>Accepted (checkmarks)</option>
                    <option value="cross" <?php echo $c['list_type']=='cross'?'selected':''; ?>>Excluded (crosses)</option>
                </select>
                <textarea class="form-control" name="items" rows="3" placeholder="One item per line"><?php echo htmlspecialchars($c['items']); ?></textarea>
                <input type="number" class="form-control hc-order" name="sort_order" value="<?php echo htmlspecialchars($c['sort_order']); ?>">
                <button type="submit" class="btn btn-sm up-btn">Save</button>
                <button type="submit" name="action" value="delete_policy_card" class="btn btn-sm btn-outline-danger" onclick="return confirm('Delete this item?');">Delete</button>
            </form>
            <?php endforeach; ?>

            <form method="POST" class="hc-row hc-row-wide hc-row-new">
                <input type="hidden" name="action" value="add_policy_card">
                <input type="hidden" name="section" value="policy">
                <input type="text" class="form-control" name="icon" placeholder="Icon class">
                <input type="text" class="form-control" name="title" placeholder="Card title">
                <select class="form-select" name="list_type">
                    <option value="check">Accepted (checkmarks)</option>
                    <option value="cross">Excluded (crosses)</option>
                </select>
                <textarea class="form-control" name="items" rows="3" placeholder="One item per line"></textarea>
                <input type="number" class="form-control hc-order" name="sort_order" placeholder="Order" value="0">
                <button type="submit" class="btn btn-sm btn-outline-success">+ Add</button>
                <span></span>
            </form>
        </div>

    <?php elseif ($tab == 'faq'): ?>

        <div class="ct-panel-header">
            <div>
                <h3 class="ct-panel-title">FAQ Section</h3>
                <p class="ct-panel-sub">Frequently asked questions shown in two columns.</p>
            </div>
        </div>

        <form method="POST" class="hc-card">
            <input type="hidden" name="action" value="update_faq_header">
            <input type="hidden" name="section" value="faq">
            <div class="row g-3">
                <div class="col-md-4">
                    <label class="form-label fw-bold">Tag Text</label>
                    <input type="text" class="form-control" name="tag_text" value="<?php echo htmlspecialchars($faqh['tag_text'] ?? 'Got Questions?'); ?>">
                </div>
                <div class="col-md-8">
                    <label class="form-label fw-bold">Title</label>
                    <input type="text" class="form-control" name="title" value="<?php echo htmlspecialchars($faqh['title'] ?? 'Everything You Need to Know'); ?>">
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

    <?php elseif ($tab == 'cta'): ?>

        <div class="ct-panel-header">
            <div>
                <h3 class="ct-panel-title">Bottom CTA Section</h3>
                <p class="ct-panel-sub">The final "Sell Today" call-to-action block before the footer.</p>
            </div>
        </div>

        <form method="POST" class="hc-card">
            <input type="hidden" name="action" value="update_cta">
            <input type="hidden" name="section" value="cta">
            <div class="row g-3">
                <div class="col-md-4">
                    <label class="form-label fw-bold">Tag Text</label>
                    <input type="text" class="form-control" name="tag_text" value="<?php echo htmlspecialchars($cta['tag_text'] ?? 'SELL TODAY'); ?>">
                </div>
                <div class="col-md-8">
                    <label class="form-label fw-bold">Heading <small class="text-muted">(you can use &lt;br&gt; for a line break)</small></label>
                    <input type="text" class="form-control" name="heading" value="<?php echo htmlspecialchars($cta['heading'] ?? "Your iPhone Won't Get Any Newer.<br>Sell It Before the Price Drops."); ?>">
                </div>
                <div class="col-12">
                    <label class="form-label fw-bold">Text</label>
                    <textarea class="form-control" name="text" rows="2"><?php echo htmlspecialchars($cta['text'] ?? ''); ?></textarea>
                </div>
                <div class="col-md-6">
                    <label class="form-label fw-bold">Card Heading</label>
                    <input type="text" class="form-control" name="card_heading" value="<?php echo htmlspecialchars($cta['card_heading'] ?? 'Get an Instant Quote for Your iPhone'); ?>">
                </div>
                <div class="col-md-3">
                    <label class="form-label fw-bold">Primary Button Text</label>
                    <input type="text" class="form-control" name="primary_btn_text" value="<?php echo htmlspecialchars($cta['primary_btn_text'] ?? 'Get Instant Quote'); ?>">
                </div>
                <div class="col-md-3">
                    <label class="form-label fw-bold">Primary Button Link</label>
                    <input type="text" class="form-control" name="primary_btn_link" value="<?php echo htmlspecialchars($cta['primary_btn_link'] ?? 'apple.php#apple'); ?>">
                </div>
                <div class="col-md-6">
                    <label class="form-label fw-bold">WhatsApp Link</label>
                    <input type="text" class="form-control" name="whatsapp_link" value="<?php echo htmlspecialchars($cta['whatsapp_link'] ?? 'https://wa.me/971502166562'); ?>">
                </div>
            </div>
            <button type="submit" class="btn up-btn mt-3">Save CTA Section</button>
        </form>

    <?php endif; ?>

    </div>

</div>

</div>
