<?php
/**@var mysqli $conn */
if (!isset($conn)) {
    require_once __DIR__ . '/includes/db.php'; // adjust path to match your actual db.php location
}

$footer_row = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM home_footer ORDER BY id ASC LIMIT 1"));

$f_brand_name      = $footer_row['brand_name'] ?? 'SellMyPhone';
$f_brand_highlight = $footer_row['brand_highlight'] ?? 'Dubai';
$f_tagline         = $footer_row['tagline'] ?? '';
$f_whatsapp        = $footer_row['whatsapp_number'] ?? '971502166562';
$f_phone           = $footer_row['phone_number'] ?? $f_whatsapp;
$f_email           = $footer_row['email'] ?? '';
$f_address         = $footer_row['address'] ?? '';
$f_facebook        = $footer_row['facebook_url'] ?? '#';
$f_instagram       = $footer_row['instagram_url'] ?? '#';
$f_copyright       = $footer_row['copyright_text'] ?? '';
?>

<!-- Floating WhatsApp Button -->
<div style="
position: fixed;
bottom: 1rem;
right: 1rem;
display: flex;
flex-direction: column;
gap: 1rem;
z-index: 2147483647;">

<a href="https://wa.me/<?php echo htmlspecialchars($f_whatsapp); ?>" target="_blank" rel="noopener" class="fixed-btn">
    <i class="fa-brands fa-whatsapp"></i>
</a>

</div>

<footer class="site-footer">
    <div class="container">
        <div class="footer-top">

            <!-- Logo + tagline + social icons -->
            <div class="footer-brand">
                <div class="footer-logo"><?php echo htmlspecialchars($f_brand_name); ?> <span><?php echo htmlspecialchars($f_brand_highlight); ?></span></div>
                <p class="footer-tagline"><?php echo htmlspecialchars($f_tagline); ?></p>
                <div class="footer-social">
                    <a href="<?php echo htmlspecialchars($f_facebook); ?>" target="_blank" rel="noopener" aria-label="Facebook"><i class="fa-brands fa-facebook-f"></i></a>
                    <a href="<?php echo htmlspecialchars($f_instagram); ?>" target="_blank" rel="noopener" aria-label="Instagram"><i class="fa-brands fa-instagram"></i></a>
                    <a href="https://wa.me/<?php echo htmlspecialchars($f_whatsapp); ?>" target="_blank" rel="noopener" aria-label="WhatsApp"><i class="fa-brands fa-whatsapp"></i></a>
                </div>
            </div>

            <!-- Quick Links -->
            <div class="footer-col">
                <h4>Quick Links</h4>
                <ul>
                    <li><a href="<?php echo BASE_URL; ?>index">Home</a></li>
                    <li><a href="<?php echo BASE_URL; ?>index#calc">Sell Your Phone</a></li>
                    <li><a href="<?php echo BASE_URL; ?>index#how-it-works">How It Works</a></li>
                    <li><a href="<?php echo BASE_URL; ?>index#why-choose">Why PhoneDubai</a></li>
                    <li><a href="<?php echo BASE_URL; ?>index#faq">FAQs</a></li>
                    <li><a href="<?php echo BASE_URL; ?>contact">Contact Us</a></li>
                </ul>
            </div>

            <!-- Popular Brands -->
            <div class="footer-col">
                <h4>Popular Brands</h4>
                <ul>
                    <li><a href="<?php echo BASE_URL; ?>sell-iphone">iPhone</a></li>
                    <li><a href="<?php echo BASE_URL; ?>sell-samsung">Samsung</a></li>
                    <li><a href="<?php echo BASE_URL; ?>index#calc">All Models</a></li>
                </ul>
            </div>

            <!-- Contact Us -->
            <div class="footer-col footer-contact">
                <h4>Contact Us</h4>
                <ul>
                    <li>
                        <a href="tel:+<?php echo htmlspecialchars($f_phone); ?>">
                            <i class="fa-solid fa-phone"></i> +<?php echo htmlspecialchars($f_phone); ?>
                        </a>
                    </li>
                    <li>
                        <a href="https://wa.me/<?php echo htmlspecialchars($f_whatsapp); ?>" target="_blank" rel="noopener">
                            <i class="fa-brands fa-whatsapp"></i> +<?php echo htmlspecialchars($f_whatsapp); ?>
                        </a>
                    </li>
                    <li>
                        <a href="mailto:<?php echo htmlspecialchars($f_email); ?>">
                            <i class="fa-solid fa-envelope"></i> <?php echo htmlspecialchars($f_email); ?>
                        </a>
                    </li>
                    <li>
                        <a href="https://maps.google.com/?q=<?php echo urlencode($f_address); ?>" target="_blank" rel="noopener">
                            <i class="fa-solid fa-location-dot"></i> <?php echo htmlspecialchars($f_address); ?>
                        </a>
                    </li>
                </ul>
            </div>

        </div>

        <!-- Bottom bar: copyright only -->
        <div class="footer-bottom">
            <p><?php echo $f_copyright; // contains &copy; entity, so not escaped ?></p>
        </div>
    </div>
</footer>