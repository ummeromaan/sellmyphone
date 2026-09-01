<?php
require_once 'admin/includes/db.php';
require_once 'includes/home-data.php';
$cmp = home_single($conn, 'samsung_comparison');
$rows = home_rows($conn, 'samsung_comparison_rows');
$brand_name = $cmp['brand_name'] ?? 'SellMyPhone';
?>
<section id="samsung-comparison" class="samsung-comparison-section">
  <div class="container-fluid samsung-comparison-wrap">

    <span class="samsung-comparison-badge"><i class="fa-solid fa-star"></i> <?php echo htmlspecialchars($cmp['badge_text'] ?? 'The Smarter Alternative'); ?></span>

    <h2 class="samsung-comparison-heading">
      <span class="samsung-comparison-brand"><?php echo htmlspecialchars($brand_name); ?></span> <?php echo htmlspecialchars($cmp['heading'] ?? 'vs Traditional Selling Options'); ?>
    </h2>

    <p class="samsung-comparison-text"><?php echo htmlspecialchars($cmp['text'] ?? 'See why thousands of Dubai smartphone owners choose our doorstep service over retail shops, classified boards, carrier trade-ins, and store trade-ins.'); ?></p>

    <div class="samsung-comparison-table-wrap">
      <table class="samsung-comparison-table">
        <thead>
          <tr>
            <th class="samsung-col-feature">Feature</th>
            <th class="samsung-col-highlight"><?php echo htmlspecialchars($brand_name); ?><br><span>(Direct Buyer)</span></th>
            <th><?php echo nl2br(htmlspecialchars($cmp['col2_header'] ?? "Deira / Mall\nPhone Shops")); ?></th>
            <th><?php echo nl2br(htmlspecialchars($cmp['col3_header'] ?? "Online Classified\nAds (Dubizzle)")); ?></th>
            <th><?php echo nl2br(htmlspecialchars($cmp['col4_header'] ?? "Carrier & Store\nTrade-Ins")); ?></th>
          </tr>
        </thead>
        <tbody>
          <?php foreach ($rows as $r): ?>
          <tr>
            <td class="samsung-row-label" data-label="Feature"><?php echo htmlspecialchars($r['feature']); ?></td>
            <td class="samsung-col-highlight" data-label="<?php echo htmlspecialchars($brand_name); ?>"><?php echo nl2br(htmlspecialchars($r['col1_value'])); ?></td>
           <td data-label="<?php echo htmlspecialchars(str_replace("\n", ' ', $cmp['col2_header'] ?? 'Deira / Mall Phone Shops')); ?>"><?php echo nl2br(htmlspecialchars($r['col2_value'])); ?></td>
          <td data-label="<?php echo htmlspecialchars(str_replace("\n", ' ', $cmp['col3_header'] ?? 'Online Classified Ads (Dubizzle)')); ?>"><?php echo nl2br(htmlspecialchars($r['col3_value'])); ?></td>
          <td data-label="<?php echo htmlspecialchars(str_replace("\n", ' ', $cmp['col4_header'] ?? 'Carrier & Store Trade-Ins')); ?>"><?php echo nl2br(htmlspecialchars($r['col4_value'])); ?></td>
          </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    </div>

  </div>
</section>