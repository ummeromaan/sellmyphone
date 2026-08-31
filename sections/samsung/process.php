<?php
require_once 'admin/includes/db.php';
require_once 'includes/home-data.php';
$process = home_single($conn, 'samsung_process');
$steps = home_rows($conn, 'samsung_process_steps');
if (empty($steps)) { $steps = [['icon'=>'fa-solid fa-file-invoice','detail_title'=>'','detail_desc'=>'']]; }
?>
<section id="samsung-process" class="samsung-process-section">
  <div class="container-fluid samsung-process-wrap">

    <span class="samsung-process-tag"><?php echo htmlspecialchars($process['tag_text'] ?? 'Simple 3-Step Flow'); ?></span>
    <h2 class="samsung-process-title"><?php echo htmlspecialchars($process['title'] ?? 'Sell Your Samsung in 3 Easy Steps'); ?></h2>
    <p class="samsung-process-subtitle"><?php echo htmlspecialchars($process['subtitle'] ?? 'Our simple and transparent process makes selling your Samsung phone quick, safe, and profitable.'); ?></p>

    <div class="samsung-process-body">
      <div class="samsung-process-steps">
        <?php foreach ($steps as $i => $s): ?>
        <div class="samsung-process-step <?php echo $i==0 ? 'active' : ''; ?>"
             data-icon="<?php echo htmlspecialchars($s['icon']); ?>"
             data-title="<?php echo htmlspecialchars($s['detail_title']); ?>"
             data-desc="<?php echo htmlspecialchars($s['detail_desc']); ?>">
          <span class="samsung-process-step-label"><?php echo htmlspecialchars($s['step_label']); ?></span>
          <h3 class="samsung-process-step-title"><?php echo htmlspecialchars($s['card_title']); ?></h3>
        </div>
        <?php endforeach; ?>
      </div>

      <div class="samsung-process-card">
        <span class="samsung-process-card-icon"><i id="samsungProcessIcon" class="<?php echo htmlspecialchars($steps[0]['icon']); ?>"></i></span>
        <h3 id="samsungProcessTitle" class="samsung-process-card-title"><?php echo htmlspecialchars($steps[0]['detail_title']); ?></h3>
        <p id="samsungProcessDesc" class="samsung-process-card-desc"><?php echo htmlspecialchars($steps[0]['detail_desc']); ?></p>
      </div>
    </div>
  </div>
</section>

<script>
document.querySelectorAll('.samsung-process-step').forEach(function (step) {
    step.addEventListener('click', function () {
        document.querySelectorAll('.samsung-process-step').forEach(function (s) { s.classList.remove('active'); });
        step.classList.add('active');
        document.getElementById('samsungProcessIcon').className = step.getAttribute('data-icon');
        document.getElementById('samsungProcessTitle').innerHTML = step.getAttribute('data-title');
        document.getElementById('samsungProcessDesc').innerHTML = step.getAttribute('data-desc');
    });
});
</script>