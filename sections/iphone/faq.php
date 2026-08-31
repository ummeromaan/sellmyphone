<?php
require_once 'admin/includes/db.php';
require_once 'includes/home-data.php';
$faqh = home_single($conn, 'iphone_faq_header');
$faqAll = home_rows($conn, 'iphone_faq', 'column_no ASC, sort_order ASC, id ASC');
$col1 = array_filter($faqAll, function($f){ return $f['column_no']==1; });
$col2 = array_filter($faqAll, function($f){ return $f['column_no']!=1; });
?>
<section class="samsung-faq-section" id="samsung-faq">
  <div class="container-fluid samsung-faq-wrap">

    <div class="samsung-faq-header">
      <span class="samsung-faq-tag"><?php echo htmlspecialchars($faqh['tag_text'] ?? 'Got Questions?'); ?></span>
      <h2 class="samsung-faq-title"><?php echo htmlspecialchars($faqh['title'] ?? 'Everything You Need to Know'); ?></h2>
    </div>

    <div class="samsung-faq-columns" id="samsungFaqList">
      <div class="samsung-faq-list">
        <?php foreach ($col1 as $f): ?>
        <div class="samsung-faq-item">
          <button type="button" class="samsung-faq-question">
            <h3 class="samsung-faq-q"><?php echo htmlspecialchars($f['question']); ?></h3>
            <i class="fa-solid fa-chevron-down"></i>
          </button>
          <div class="samsung-faq-answer"><p><?php echo htmlspecialchars($f['answer']); ?></p></div>
        </div>
        <?php endforeach; ?>
      </div>
      <div class="samsung-faq-list">
        <?php foreach ($col2 as $f): ?>
        <div class="samsung-faq-item">
          <button type="button" class="samsung-faq-question">
            <h3 class="samsung-faq-q"><?php echo htmlspecialchars($f['question']); ?></h3>
            <i class="fa-solid fa-chevron-down"></i>
          </button>
          <div class="samsung-faq-answer"><p><?php echo htmlspecialchars($f['answer']); ?></p></div>
        </div>
        <?php endforeach; ?>
      </div>
    </div>

  </div>
</section>

<script>
(function () {
    var items = document.querySelectorAll('#samsungFaqList .samsung-faq-item');
    items.forEach(function (item) {
        var question = item.querySelector('.samsung-faq-question');
        question.addEventListener('click', function () {
            var isOpen = item.classList.contains('active');
            items.forEach(function (i) { i.classList.remove('active'); });
            if (!isOpen) { item.classList.add('active'); }
        });
    });
})();
</script>