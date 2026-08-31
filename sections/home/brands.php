<?php
require_once 'admin/includes/db.php';
require_once 'includes/home-data.php';
/**@var mysqli $conn */

$brands_header   = home_single($conn, 'home_brands_header');
$brand_cards_raw = home_rows($conn, 'home_brand_cards', 'id ASC');
$brand_cards = [];
foreach ($brand_cards_raw as $bc) { $brand_cards[$bc['brand_key']] = $bc; }
$apple          = $brand_cards['apple'] ?? [];
$samsung        = $brand_cards['samsung'] ?? [];
$brand_features = home_rows($conn, 'home_brand_features');
?>
<!--bootstrap brand sec-->


<section class="brand-section pt-5" id="brands">
    <div class="container-fluid">

        <!-- Header (tag + heading + subtitle) sits directly on the section background,
             outside the white card below. Two side phone images sit behind it, their
             bottom part gets covered by the white card underneath. -->
        <div class="brand-hero-wrap">

            <!-- Dynamic left/right phone images (editable from Admin > Home Page Content) -->
            <img src="<?php echo htmlspecialchars(home_img($brands_header['left_image'] ?? '', 'gold.png')); ?>" alt="iPhone" class="brand-side-phone brand-side-phone-left">
            <img src="<?php echo htmlspecialchars(home_img($brands_header['right_image'] ?? '', 's.png')); ?>" alt="Samsung" class="brand-side-phone brand-side-phone-right">

            <div class="brand-header text-center mb-5" id="brand-header">

               <div class="mini-tag-wrap">
                    <span class="mini-tag-text">
                        <i class="<?php echo htmlspecialchars($brands_header['tag_icon'] ?? 'fa-solid fa-bolt'); ?>"></i>
                        <?php echo $brands_header['tag_text'] ?? ''; ?>
                    </span>
                </div>

              <h2 class="calc-title fw-bold mt-3">
    <?php echo $brands_header['title_pre'] ?? 'Find Out How Much'; ?>
    <span class="highlight">
        <?php echo $brands_header['title_highlight'] ?? 'Your Phone'; ?>
    </span>
        &nbsp;<?php echo $brands_header['title_post'] ?? 'Is Worth.'; ?>
</h2>

                <p class="calc-subtitle">
                    <?php echo $brands_header['subtitle'] ?? ''; ?>
                </p>
            </div>

        </div>

        <!-- Card: holds the step progress + brand selection + the model/storage/condition/accessories flow -->
        <div class="container calc-widget" id="calc">

            <!-- Step Progress (1 Brand, 2 Model, 3 Storage, 4 Condition, 5 Accessories) - kept static, this is functional UI for the calculator, not content -->
            <div class="price-stepper" id="price-stepper">

                <div class="step-item active" data-step="1">
                    <span class="step-circle">1</span>
                    <span class="step-label">Brand</span>
                    <span class="step-sublabel">Choose brand</span>
                </div>
                <span class="step-line"><i class="fa-solid fa-chevron-right"></i></span>

                <div class="step-item" data-step="2">
                    <span class="step-circle">2</span>
                    <span class="step-label">Model</span>
                    <span class="step-sublabel">Select model</span>
                </div>
                <span class="step-line"><i class="fa-solid fa-chevron-right"></i></span>

                <div class="step-item" data-step="3">
                    <span class="step-circle">3</span>
                    <span class="step-label">Storage</span>
                    <span class="step-sublabel">Choose storage</span>
                </div>
                <span class="step-line"><i class="fa-solid fa-chevron-right"></i></span>

                <div class="step-item" data-step="4">
                    <span class="step-circle">4</span>
                    <span class="step-label">Condition</span>
                    <span class="step-sublabel">Device condition</span>
                </div>
                <span class="step-line"><i class="fa-solid fa-chevron-right"></i></span>

                <div class="step-item" data-step="5">
                    <span class="step-circle">5</span>
                    <span class="step-label">Accessories</span>
                    <span class="step-sublabel">Add accessories</span>
                </div>

            </div>

            <!-- Brands -->
            <div id="brand-buttons">

                <div class="mini-tag-wrap">
                    <span class="mini-tag-line"></span>
                    <span class="mini-tag-text"><?php echo htmlspecialchars($brands_header['choose_label'] ?? 'CHOOSE YOUR BRAND'); ?></span>
                    <span class="mini-tag-line"></span>
                </div>

                <div class="row justify-content-center g-4 mt-2">

                    <!-- Apple card -->
                    <div class="col-6 col-lg-4">

                        <button type="button" class="brand-card brand-card-apple"
                                onclick="showbrand('apple')">

                            <div class="brand-card-visual">
                                <img src="<?php echo htmlspecialchars(home_img($apple['phone_image'] ?? '', 'ifon.png')); ?>" alt="iPhone" class="brand-phone-img">
                            </div>

                            <span class="brand-card-divider"></span>

                            <div class="brand-card-info">
                                <div class="brand-title-row">
                                    <div class="brand-badge">
                                        <img src="<?php echo htmlspecialchars(home_img($apple['badge_image'] ?? '', 'app.png')); ?>" alt="Apple">
                                    </div>
                                    <div class="brand-title-col">
                                        <h3 class="brand-title-name"><?php echo $apple['title'] ?? 'iPhone'; ?></h3>
                                        <span class="brand-sub-name"><?php echo $apple['subtitle'] ?? 'Apple'; ?></span>
                                    </div>
                                </div>
                                <p class="brand-desc"><?php echo $apple['description'] ?? ''; ?></p>

                                <span class="brand-arrow-btn">
                                    <i class="fa-solid fa-arrow-right"></i>
                                </span>
                            </div>

                        </button>

                    </div>

                    <!-- Samsung card -->
                    <div class="col-6 col-lg-4">

                        <button type="button" class="brand-card brand-card-samsung"
                                onclick="showbrand('samsung')">

                            <div class="brand-card-visual">
                                <img src="<?php echo htmlspecialchars(home_img($samsung['phone_image'] ?? '', 'sam.png')); ?>" alt="Samsung" class="brand-phone-img">
                            </div>

                            <span class="brand-card-divider"></span>

                            <div class="brand-card-info">
                                <div class="brand-title-row">
                                    <div class="brand-badge">
                                        <img src="<?php echo htmlspecialchars(home_img($samsung['badge_image'] ?? '', 's-logo.png')); ?>" class="img1">
                                    </div>
                                    <div class="brand-title-col">
                                        <h3 class="brand-title-name"><?php echo $samsung['title'] ?? 'Samsung'; ?></h3>
                                        <span class="brand-sub-name"><?php echo $samsung['subtitle'] ?? 'Galaxy'; ?></span>
                                    </div>
                                </div>
                                <p class="brand-desc"><?php echo $samsung['description'] ?? ''; ?></p>

                                <span class="brand-arrow-btn">
                                    <i class="fa-solid fa-arrow-right"></i>
                                </span>
                            </div>

                        </button>

                    </div>

                </div>
            </div>

            <!-- Model/Storage/Condition/Accessories load here, inside this same widget -->
            <div id="brand-content"></div>

        </div><!--calc-widget-->

        <!-- Trust features strip - sits below the calc-widget card, inside the brand section -->
        <div class="brand-features-bar">
            <?php foreach ($brand_features as $bf): ?>
            <div class="brand-feature-item">
                <i class="<?php echo htmlspecialchars($bf['icon_class']); ?> brand-feature-icon"></i>
                <h4 class="brand-feature-title"><?php echo $bf['title']; ?></h4>
                <span class="brand-feature-divider"></span>
                <p class="brand-feature-desc"><?php echo $bf['description']; ?></p>
            </div>
            <?php endforeach; ?>
        </div><!--brand-features-bar-->

    </div>
</section>

<script>
/* ==================== Price stepper: click a step number to jump to that tab ====================
   Reuses goToTab() (already used by the Back buttons inside apple.php/samsung.php) when
   it's available; falls back to Bootstrap's own Tab API if it isn't loaded yet. */
(function () {

    var STEP_TO_TAB = {
        2: 'pills-model-tab',
        3: 'pills-storage-tab',
        4: 'pills-condition-tab',
        5: 'pills-accessories-tab'
    };

    var TAB_TO_STEP = {
        'pills-model-tab': 2,
        'pills-storage-tab': 3,
        'pills-condition-tab': 4,
        'pills-accessories-tab': 5
    };

    function setActiveStep(step) {
        document.querySelectorAll('#price-stepper .step-item').forEach(function (item) {
            var s = parseInt(item.getAttribute('data-step'), 10);
            item.classList.toggle('active', s === step);
        });
    }

    function goToStep(step) {
        // Step 1 = back to brand selection
        if (step === 1) {
            if (typeof backToBrands === 'function') {
                backToBrands();
            }
            setActiveStep(1);
            return;
        }

        var tabId = STEP_TO_TAB[step];
        var tabEl = tabId ? document.getElementById(tabId) : null;
        if (!tabEl) return; // tab not loaded yet (brand not selected) - ignore click

        if (typeof goToTab === 'function') {
            goToTab(tabId);
        } else if (window.bootstrap && bootstrap.Tab) {
            bootstrap.Tab.getOrCreateInstance(tabEl).show();
        } else {
            tabEl.click();
        }

        setActiveStep(step);
    }

    document.addEventListener('DOMContentLoaded', function () {
        document.querySelectorAll('#price-stepper .step-item').forEach(function (item) {
            item.addEventListener('click', function () {
                var step = parseInt(item.getAttribute('data-step'), 10);
                goToStep(step);
            });
        });

        // Keep the stepper synced whenever a tab is shown some other way
        // (e.g. selecting a model auto-advances to the storage tab).
        document.body.addEventListener('shown.bs.tab', function (e) {
            var id = e.target && e.target.id;
            if (id && TAB_TO_STEP[id]) {
                setActiveStep(TAB_TO_STEP[id]);
            }
        });
    });

})();
</script>