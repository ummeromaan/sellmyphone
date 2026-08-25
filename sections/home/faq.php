<?php
require_once 'admin/includes/db.php';
require_once 'includes/home-data.php';
/**@var mysqli $conn */

$faq_header = home_single($conn, 'home_faq_header');
$faq_all    = home_rows($conn, 'home_faq', 'column_no ASC, sort_order ASC, id ASC');
$faq_col1 = array_values(array_filter($faq_all, function ($f) { return intval($f['column_no']) == 1; }));
$faq_col2 = array_values(array_filter($faq_all, function ($f) { return intval($f['column_no']) != 1; }));

$faq_cta = home_single($conn, 'home_faq_cta');
$faq_cta_badges = !empty($faq_cta['badges']) ? array_map('trim', explode(',', $faq_cta['badges'])) : [];
?>
<!--faq section - 2 columns of questions-->

<section class="faqc-section" id="faq">
    <div class="container-fluid faqc-container-fluid">
        <div class="row g-4 align-items-stretch">

            <!-- FAQ accordion - 2 columns -->
            <div class="col-lg-12">
                <div class="faqc-faq-panel">

                    <div class="faqc-faq-header">
                        <div class="faq-tag-wrap">
                <span class="faq-tag-text">
                  <?php echo $faq_header['tag_text'] ?? "FAQ's"; ?>
                </span>
            </div>
                        <h3 class="faqc-faq-title"><?php echo $faq_header['title'] ?? 'Frequently Asked Questions'; ?></h3>
                    <span class="faqc-faq-underline"></span>
                    </div>

                    <div class="faqc-faq-columns" id="faqcList">

                        <!-- Column 1 -->
                        <div class="faqc-faq-list">
                            <?php foreach ($faq_col1 as $f): ?>
                            <div class="faqc-faq-item">
                                <button type="button" class="faqc-faq-question">
                                    <?php echo $f['question']; ?>
                                    <i class="fa-solid fa-chevron-down"></i>
                                </button>
                                <div class="faqc-faq-answer">
                                    <p><?php echo $f['answer']; ?></p>
                                </div>
                            </div>
                            <?php endforeach; ?>
                        </div>

                        <!-- Column 2 -->
                        <div class="faqc-faq-list">
                            <?php foreach ($faq_col2 as $f): ?>
                            <div class="faqc-faq-item">
                                <button type="button" class="faqc-faq-question">
                                    <?php echo $f['question']; ?>
                                    <i class="fa-solid fa-chevron-down"></i>
                                </button>
                                <div class="faqc-faq-answer">
                                    <p><?php echo $f['answer']; ?></p>
                                </div>
                            </div>
                            <?php endforeach; ?>
                        </div>

                    </div>

                </div>
            </div>

        </div>
    </div>

               <!-- Ready to Sell Your Phone - full width CTA banner -->
<div class="row mt-4">
    <div class="col-12">
        <div class="faqc-cta-banner"<?php if (!empty($faq_cta['bg_image'])): ?> style="--faqc-cta-bg:url('<?php echo htmlspecialchars(home_img($faq_cta['bg_image'])); ?>')"<?php endif; ?>>
            <div class="faqc-cta-overlay"></div>

            <div class="faqc-cta-phone">
                
                <img src="<?php echo htmlspecialchars(home_img($faq_cta['phone_image'] ?? '', 'dbl.png')); ?>" alt="Phone">
            </div>

            <div class="faqc-cta-left">
                <h3 class="faqc-cta-title"><?php echo $faq_cta['title'] ?? 'Ready to Sell Your Phone?'; ?></h3>
                <p class="faqc-cta-desc"><?php echo $faq_cta['description'] ?? ''; ?></p>
            </div>

            <div class="faqc-cta-mid">
                <button type="button" class="faqc-cta-btn" id="faqcBookPickupBtn">
                    Get My Quote <i class="fa-solid fa-arrow-right"></i>
                </button>

                <div class="faqc-cta-badges">
                    <?php foreach ($faq_cta_badges as $i => $badge): ?>
                    <span><?php echo htmlspecialchars($badge); ?></span>
                    <?php if ($i < count($faq_cta_badges) - 1): ?><span class="faqc-cta-dot">&bull;</span><?php endif; ?>
                    <?php endforeach; ?>
                </div>
            </div>

        </div>
    </div>
</div>
</section>

<script>
    document.getElementById('faqcBookPickupBtn').addEventListener('click', function () {
    var brandsSection = document.getElementById('calc');
    if (brandsSection) {
        brandsSection.scrollIntoView({ behavior: 'smooth' });
    }
});
(function () {
    var items = document.querySelectorAll('#faqcList .faqc-faq-item');

    items.forEach(function (item) {
        var question = item.querySelector('.faqc-faq-question');

        question.addEventListener('click', function () {
            var isOpen = item.classList.contains('active');

            // close all other open items
            items.forEach(function (i) {
                i.classList.remove('active');
            });

            // toggle current one
            if (!isOpen) {
                item.classList.add('active');
            }
        });
    });
})();
</script>
