<?php
$page_title = "Sell Your iPhone in UAE | Instant Cash Offer - SellMyPhone";
$meta_description = "Sell your iPhone for the best price in UAE. Get instant valuation for iPhone 15, 14, 13 and more, with free pickup and same-day payment.";
$meta_keywords = "sell iPhone UAE, sell iPhone Dubai, iPhone buyback, iPhone cash offer";
?>
<?php require 'includes/header.php';?>



<!-- Price stepper + calculator share ONE calc widget, same as the homepage
     calculator, so top/bottom spacing matches automatically. Brand is already chosen
     (Apple) since this is the dedicated Apple page, so step 1 shows "done" and
     step 2 (Model) starts active. Synced with the tabs via the script below. -->
<section class="brand-section">
  <div class="container calc-widget calc-widget-plain" id="calc-standalone">

    <div class="price-stepper" id="price-stepper-standalone">

        <div class="step-item done" data-step="1">
            <span class="step-circle"><i class="fa-solid fa-check"></i></span>
            <span class="step-label">Brand</span>
            <span class="step-sublabel">Apple</span>
        </div>
        <span class="step-line"><i class="fa-solid fa-chevron-right"></i></span>

        <div class="step-item active" data-step="2">
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

    </div><!--price-stepper-->

    <?php $standalone_page = true; include 'apple-content.php'; ?>

  </div><!--calc-widget-->
</section>

<script>
(function () {
    var TAB_TO_STEP = {
        'pills-model-tab':       2,
        'pills-storage-tab':     3,
        'pills-condition-tab':   4,
        'pills-accessories-tab': 5
    };
    function setActiveStep(step) {
        document.querySelectorAll('#price-stepper-standalone .step-item').forEach(function (item) {
            var s = parseInt(item.getAttribute('data-step'), 10);
            item.classList.remove('active');
            item.classList.toggle('done', s < step || s === 1);
            if (s === step) item.classList.add('active');
        });
    }
    document.body.addEventListener('shown.bs.tab', function (e) {
        var id = e.target && e.target.id;
        if (id && TAB_TO_STEP[id]) setActiveStep(TAB_TO_STEP[id]);
    });
})();
</script>

<?php include 'includes/footer.php';?>