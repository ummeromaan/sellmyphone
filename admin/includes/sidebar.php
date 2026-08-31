<?php $current = basename($_SERVER['PHP_SELF']);?>
<div class="sidebar">

<div class="logo d-flex">
     <i class="fa-solid fa-mobile-screen"></i>
<div class="logo-header align-content-center"><span>PhoneDubai<span>
<p class="p">Admin Panel</p></div>
</div>

<ul>

<li><a href="dashboard.php" class="<?php echo $current == 'dashboard.php' ? 'active' : ''; ?>"><i class="fa-solid fa-house  mx-2"></i> Dashboard</a></li>

<li>
<a href="phones.php" class="<?php echo $current == 'phones.php' ? 'active' : ''; ?>"><i class="fa-solid fa-mobile-screen mx-2"></i>
Models & Pricing
</a>
</li>

<li>
<a href="orders.php" class="<?php echo $current == 'orders.php' ? 'active' : ''; ?>">
<i class="fa-solid fa-cart-shopping mx-2"></i>
Orders
</a>
</li>

<li>
<a href="messages.php" class="<?php echo $current == 'messages.php' ? 'active' : ''; ?>">
<i class="fa-solid fa-envelope mx-2"></i>
Messages
</a>
</li>

<li>
<a href="settings.php" class="<?php echo $current == 'settings.php' ? 'active' : ''; ?>">
<i class="fa-solid fa-gear mx-2"></i>
Settings
</a>
</li>

<li>
<a href="add-admin.php" class="<?php echo $current == 'add-admin.php' ? 'active' : ''; ?>">
<i class="fa-solid fa-users mx-2"></i>
Add/Manage Admins
</a>
</li>

<?php
// "Content" group covers every editable page's content in one place.
// Stays expanded automatically if the current page is one of its children.
$content_pages = ['home-content.php', 'sell-iphone-content.php', 'sell-samsung-content.php', 'contact-info.php'];
$content_open  = in_array($current, $content_pages);
?>
<li>
<a href="#contentMenu" class="d-flex justify-content-between align-items-center <?php echo $content_open ? '' : 'collapsed'; ?>"
   data-bs-toggle="collapse" role="button" aria-expanded="<?php echo $content_open ? 'true' : 'false'; ?>">
<span><i class="fa-solid fa-file-pen mx-2"></i>Content</span>
<i class="fa-solid fa-chevron-down submenu-caret"></i>
</a>
<div class="collapse <?php echo $content_open ? 'show' : ''; ?>" id="contentMenu">
  <ul class="submenu">
    <li><a href="home-content.php" class="<?php echo $current == 'home-content.php' ? 'active' : ''; ?>">Home Content</a></li>
    <li><a href="sell-iphone-content.php" class="<?php echo $current == 'sell-iphone-content.php' ? 'active' : ''; ?>">Sell iPhone Content</a></li>
    <li><a href="sell-samsung-content.php" class="<?php echo $current == 'sell-samsung-content.php' ? 'active' : ''; ?>">Sell Samsung Content</a></li>
    <li><a href="contact-info.php" class="<?php echo $current == 'contact-info.php' ? 'active' : ''; ?>">Contact</a></li>
  </ul>
</div>
</li>

<li>
<a href="seo-settings.php" class="<?php echo $current == 'seo-settings.php' ? 'active' : ''; ?>"><i class="fa-solid fa-magnifying-glass mx-2"></i>
SEO Settings
</a>
</li>
<li>
<a href="blog-posts.php" class="<?php echo $current == 'blog-posts.php' ? 'active' : ''; ?>"><i class="fa-solid fa-file-pen mx-2"></i>
Blogs
</a>
</li>
<hr class="w-100">
<li>
<a href="logout.php" class="<?php echo $current == 'logout.php' ? 'active' : ''; ?>">
<i class="fa-solid fa-right-from-bracket mx-2"></i>
Logout
</a>
</li>

</ul>

</div>