<?php $current = basename($_SERVER['PHP_SELF']);?>
<div class="sidebar">

<div class="logo d-flex">
     <i class="fa-solid fa-mobile"></i>
<div class="logo-header align-content-center"><span>SellMyPhone<span>
<p class="p">Admin Panel</p></div>
</div>

<ul>

<li><a href="dashboard.php" class="<?php echo $current == 'dashboard.php' ? 'active' : ''; ?>"><i class="fa-solid fa-house  mx-2 fs-4"></i> Dashboard</a></li>

<li>
<a href="phones.php" class="<?php echo $current == 'phones.php' ? 'active' : ''; ?>"><i class="fa-solid fa-mobile-screen mx-2 fs-4"></i>
Models & Pricing
</a>
</li>

<li>
<a href="orders.php" class="<?php echo $current == 'orders.php' ? 'active' : ''; ?>">
<i class="fa-solid fa-cart-shopping mx-2 fs-4"></i>
Orders
</a>
</li>

<li>
<a href="messages.php" class="<?php echo $current == 'messages.php' ? 'active' : ''; ?>">
<i class="fa-solid fa-envelope mx-2 fs-4"></i>
Messages
</a>
</li>

<li>
<a href="settings.php" class="<?php echo $current == 'settings.php' ? 'active' : ''; ?>">
<i class="fa-solid fa-gear mx-2 fs-4"></i>
Settings
</a>
</li>

<li>
<a href="add-admin.php" class="<?php echo $current == 'add-admin.php' ? 'active' : ''; ?>">
<i class="fa-solid fa-users mx-2 fs-4"></i>
Add/Manage Admins
</a>
</li>
<li>
<a href="home-content.php" class="<?php echo $current == 'home-content.php' ? 'active' : ''; ?>"><i class="fa-solid fa-file-pen mx-2 fs-4"></i>
Home Page Content
</a>
</li>
<li>
<a href="seo-settings.php" class="<?php echo $current == 'seo-settings.php' ? 'active' : ''; ?>"><i class="fa-solid fa-magnifying-glass mx-2 fs-4"></i>
SEO Settings
</a>
</li>
<li>
<a href="blog-posts.php" class="<?php echo $current == 'blog-posts.php' ? 'active' : ''; ?>"><i class="fa-solid fa-file-pen mx-2 fs-4"></i>
Blogs
</a>
</li>
<li class="nav-item">
    <a class="nav-link <?php echo basename($_SERVER['PHP_SELF']) == 'contact-info.php' ? 'active' : ''; ?>" href="contact-info.php">
        <i class="fa-solid fa-address-book"></i>
        <span>Contact Info</span>
    </a>
</li>
<hr class="w-100">
<li>
<a href="logout.php" class="<?php echo $current == 'logout.php' ? 'active' : ''; ?>">
<i class="fa-solid fa-right-from-bracket mx-2 fs-4"></i>
Logout
</a>
</li>

</ul>

</div>