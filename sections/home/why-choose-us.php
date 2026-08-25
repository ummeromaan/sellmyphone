<?php
require_once 'admin/includes/db.php';
require_once 'includes/home-data.php';
/**@var mysqli $conn */

$wcu            = home_single($conn, 'home_wcu');
$wcu_cards      = home_rows($conn, 'home_wcu_cards');
$wcu_highlight  = home_single($conn, 'home_wcu_highlight');
$wcu_highlight_items = home_rows($conn, 'home_wcu_highlight_items');
?>
<!--why choose us - centered heading, 6 feature cards in a grid + one highlight card of equal height-->

<section class="wcu-section" id="why-choose">
    <div class="container">

        <!-- Centered heading -->
        <div class="wcu-header">
             <div class="wcu-tag-wrap">
                <span class="wcu-tag-text">
                   <?php echo $wcu['tag_text'] ?? 'WHY CHOOSE US'; ?>
                </span>
            </div>
        
            <h2 class="wcu-title"><?php echo $wcu['title_pre'] ?? 'Why Choose SellMyPhone'; ?> <span class="highlight"><?php echo $wcu['title_highlight'] ?? 'Dubai?'; ?></span></h2>
            <span class="wcu-underline"></span>
        </div>

        <!-- Everything (6 cards + highlight card) sits inside one wrapper -->
        <div class="wcu-content">

            <div class="wcu-grid ">

                <?php foreach ($wcu_cards as $card): ?>
                <div class="wcu-card">
                    <div class="wcu-icon">
                        <i class="<?php echo htmlspecialchars($card['icon_class']); ?> fs-4" style="color:<?php echo htmlspecialchars($card['icon_color'] ?: '#ebb917'); ?>;"></i>
                    </div>
                    <h4 class="wcu-card-title reveal-item"><?php echo $card['title']; ?></h4>
                    <p class="wcu-card-desc reveal-item"><?php echo $card['description']; ?></p>
                </div>
                <?php endforeach; ?>

            </div>

            <!-- Highlight card: same total height as the 6-card grid, text only (no image) -->
            <div class="wcu-highlight-card">
                <h3 class="wcu-highlight-title"><span class="gold"><?php echo $wcu_highlight['highlight_text'] ?? 'The Smarter Way'; ?></span><?php echo $wcu_highlight['rest_text'] ?? 'to Sell Your Phone'; ?></h3>
                <ul class="wcu-highlight-list">
                    <?php foreach ($wcu_highlight_items as $item): ?>
                    <li><i class="<?php echo htmlspecialchars($item['icon_class']); ?>"></i> <?php echo $item['text']; ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>

        </div>

    </div>
</section>
