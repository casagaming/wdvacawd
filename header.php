<?php
/**
 * The header for Astra Child Theme.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

?><!DOCTYPE html>
<html <?php language_attributes(); ?> dir="rtl">
<head>
<meta charset="<?php bloginfo( 'charset' ); ?>">
<meta name="viewport" content="width=device-width, initial-scale=1">
<?php
// Retrieve Global Branding & SEO Settings from Front Page
$f_id      = get_option('page_on_front');
$seo_title = get_post_meta($f_id, '_store_seo_title', true) ?: get_bloginfo('name');
$seo_desc  = get_post_meta($f_id, '_store_seo_desc', true) ?: get_bloginfo('description');
$og_img    = get_post_meta($f_id, '_store_og_img', true);
$favicon   = get_post_meta($f_id, '_store_favicon', true);
?>
<title><?php echo esc_html($seo_title); ?></title>
<meta name="description" content="<?php echo esc_attr($seo_desc); ?>">

<!-- Favicon -->
<?php if ($favicon) : ?>
    <link rel="icon" href="<?php echo esc_url($favicon); ?>" type="image/x-icon">
    <link rel="apple-touch-icon" href="<?php echo esc_url($favicon); ?>">
<?php endif; ?>

<!-- Social Media & WhatsApp Sharing (Open Graph) -->
<meta property="og:type" content="website">
<meta property="og:url" content="<?php echo esc_url(home_url('/')); ?>">
<meta property="og:title" content="<?php echo esc_attr($seo_title); ?>">
<meta property="og:description" content="<?php echo esc_attr($seo_desc); ?>">
<?php if ($og_img) : ?>
    <meta property="og:image" content="<?php echo esc_url($og_img); ?>">
<?php endif; ?>

<!-- Twitter -->
<meta property="twitter:card" content="summary_large_image">
<meta property="twitter:title" content="<?php echo esc_attr($seo_title); ?>">
<meta property="twitter:description" content="<?php echo esc_attr($seo_desc); ?>">
<?php if ($og_img) : ?>
    <meta property="twitter:image" content="<?php echo esc_url($og_img); ?>">
<?php endif; ?>

<?php wp_head(); ?>
<style>
/* --- Premium Header & Loader Styles --- */
:root {
    --brand-red: #c00000;
    --brand-dark: #111;
    --transition-speed: 0.3s;
}

body { margin: 0; padding: 0; }

/* Loading Screen */
#site-loader {
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: #fff;
    display: flex;
    flex-direction: column;
    justify-content: center;
    align-items: center;
    z-index: 100000;
    transition: opacity 0.6s ease, visibility 0.6s;
}
#site-loader.hidden { opacity: 0; visibility: hidden; pointer-events: none; }
#site-loader img { width: 220px; height: auto; margin-bottom: 25px; }
#site-loader .spinner {
    width: 50px;
    height: 50px;
    border: 4px solid #f3f3f3;
    border-top: 4px solid var(--brand-red);
    border-radius: 50%;
    animation: spin 1s linear infinite;
}
@keyframes spin {
    0% { transform: rotate(0deg); }
    100% { transform: rotate(360deg); }
}

/* Red Top Bar */
.premium-top-bar {
    background: var(--brand-red);
    color: #fff;
    padding: 10px 0;
    text-align: center;
    font-weight: 700;
    font-size: 0.95rem;
    font-family: 'Cairo', sans-serif;
    z-index: 9999;
}

/* Main Header */
.premium-header {
    background: #fff;
    padding: 15px 0;
    box-shadow: 0 4px 15px rgba(0,0,0,0.04);
    position: sticky;
    top: 0;
    z-index: 9998;
    display: flex;
    align-items: center;
    justify-content: center;
    direction: rtl;
}

.header-container {
    width: 92%;
    max-width: 1400px;
    display: grid;
    grid-template-columns: 1fr auto 1fr;
    align-items: center;
}

/* Left Section: Icons & Tools */
.header-left {
    display: flex;
    align-items: center;
    gap: 15px;
    justify-content: flex-start;
}
.header-tool-link {
    color: #333;
    text-decoration: none;
    font-weight: 700;
    font-size: 0.95rem;
    display: flex;
    align-items: center;
    gap: 8px;
    transition: var(--transition-speed);
}
.header-tool-link:hover { color: var(--brand-red); }
.header-tool-icon { font-size: 1.2rem; cursor: pointer; }

/* Centered Logo */
.header-center {
    display: flex;
    justify-content: center;
}
.header-logo img {
    height: 70px;
    width: auto;
    display: block;
}

/* Right Section: Navigation */
.header-right {
    display: flex;
    align-items: center;
    gap: 20px;
    justify-content: flex-end;
}
.nav-menu-list {
    display: flex;
    gap: 20px;
    list-style: none;
    margin: 0;
    padding: 0;
}
.nav-menu-list a {
    text-decoration: none;
    color: #333;
    font-weight: 700;
    font-size: 1rem;
    transition: var(--brand-red);
}
.nav-menu-list a:hover { color: var(--brand-red); }

/* Category Dropdown Styles */
.nav-item-dropdown { position: relative; }
.nav-dropdown-menu { 
    position: absolute; top: 100%; right: 0; background: #fff; min-width: 220px; 
    box-shadow: 0 10px 30px rgba(0,0,0,0.1); border-radius: 12px; list-style: none; 
    padding: 10px 0; margin: 0; opacity: 0; visibility: hidden; 
    transform: translateY(10px); transition: 0.3s; z-index: 1000; border-top: 3px solid var(--brand-red);
}
.nav-item-dropdown:hover .nav-dropdown-menu { opacity: 1; visibility: visible; transform: translateY(0); }
.nav-dropdown-menu li { padding: 0; }
.nav-dropdown-menu li a { 
    padding: 10px 20px; display: block; font-size: 0.95rem; color: #333; 
    transition: 0.2s; border-bottom: 1px solid #f9f9f9; 
}
.nav-dropdown-menu li a:hover { background: #fff9f9; color: var(--brand-red); padding-right: 25px; }
.nav-dropdown-menu li:last-child a { border-bottom: none; }

.mobile-menu-toggle {
    display: none;
    font-size: 1.5rem;
    cursor: pointer;
    color: #333;
}

/* Mobile Responsive */
@media (max-width: 992px) {
    .header-container { grid-template-columns: 1fr auto; }
    .header-right { order: 2; padding-left: 15px; }
    .header-center { order: 1; flex: 1; justify-content: flex-start; }
    .header-left { display: none; } /* Hide tools on mobile or move to menu */
    .nav-menu-list { display: none; }
    .mobile-menu-toggle { display: block; }
    .header-logo img { height: 50px; }
}

/* Cart Count Badge */
.cart-badge-wrap { position: relative; }
.cart-badge {
    position: absolute;
    top: -8px;
    right: -10px;
    background: var(--brand-red);
    color: #fff;
    font-size: 0.7rem;
    width: 18px;
    height: 18px;
    border-radius: 50%;
    display: flex;
    justify-content: center;
    align-items: center;
    font-weight: bold;
}
/* Mobile Menu Drawer */
.mobile-menu-drawer {
    position: fixed;
    top: 0;
    right: -300px;
    width: 280px;
    height: 100%;
    background: #fff;
    z-index: 100001;
    transition: right 0.3s ease;
    box-shadow: -5px 0 15px rgba(0,0,0,0.1);
    padding: 30px;
}
.mobile-menu-drawer.open { right: 0; }
.mobile-menu-overlay {
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: rgba(0,0,0,0.5);
    z-index: 100000;
    display: none;
}
.mobile-menu-drawer ul { list-style: none; padding: 0; margin: 30px 0; }
.mobile-menu-drawer ul li { border-bottom: 1px solid #eee; padding: 15px 0; }
.mobile-menu-drawer ul li a { text-decoration: none; color: #333; font-weight: 800; font-size: 1.2rem; display: block; }
.close-drawer { font-size: 1.5rem; cursor: pointer; color: #999; text-align: left; display: block; }
</style>
</head>

<body <?php body_class(); ?>>
<?php wp_body_open(); ?>

<!-- Loading Transition Overlay -->
<div id="site-loader">
    <img src="<?php echo get_stylesheet_directory_uri(); ?>/assets/eagle-logo.png" alt="Loading...">
    <div class="spinner"></div>
</div>

<!-- Mobile Menu Components -->
<div class="mobile-menu-overlay" id="drawer_overlay"></div>
<div class="mobile-menu-drawer" id="mobile_drawer">
    <span class="close-drawer" id="close_drawer"><i class="fa fa-times"></i></span>
    <ul class="nav-menu-list-mobile">
        <li><a href="<?php echo esc_url( home_url('/') ); ?>">الرئيسية</a></li>
        <li><a href="<?php echo esc_url( home_url('/shop') ); ?>">كل المنتجات</a></li>
        <?php 
        $cats = get_header_categories_dropdown();
        if (!is_wp_error($cats) && !empty($cats)) :
            foreach ($cats as $cat) : ?>
                <li style="margin-right: 15px; border-bottom: 0;"><a href="<?php echo esc_url(get_term_link($cat)); ?>" style="font-size: 1rem; font-weight: 600;">- <?php echo esc_html($cat->name); ?></a></li>
            <?php endforeach;
        endif; ?>
        <li><a href="<?php echo esc_url( home_url('/faq') ); ?>">الأسئلة المتكررة</a></li>
        <li><a href="<?php echo esc_url( home_url('/about-us') ); ?>">من نحن</a></li>
    </ul>
</div>

<header id="masthead" class="site-header" role="banner">
    <!-- Red Top Bar -->
    <div class="premium-top-bar">
        اختر حذائك، ونحن نوصله لك مجاناً 🚚 ⚡
    </div>

    <!-- Main Header -->
    <div class="premium-header">
        <div class="header-container">
            <!-- Left: Icons & Buttons -->
            <div class="header-left">
                <a href="<?php echo esc_url( wc_get_cart_url() ); ?>" class="header-tool-link cart-badge-wrap">
                    <span class="cart-badge"><?php echo (WC()->cart) ? WC()->cart->get_cart_contents_count() : '0'; ?></span>
                    <i class="fa fa-shopping-cart header-tool-icon"></i>
                    السلة
                </a>
                <div class="header-tool-icon search-trigger" style="cursor:pointer;"><i class="fa fa-search"></i></div>
            </div>

            <!-- Center: Logo -->
            <div class="header-center">
                <a href="<?php echo esc_url( home_url('/') ); ?>" class="header-logo">
                    <img src="<?php echo get_stylesheet_directory_uri(); ?>/assets/main-logo.png" alt="Gentle Shoes">
                </a>
            </div>

            <!-- Right: Menu & Hamburger -->
            <div class="header-right">
                <nav class="desktop-nav">
                    <ul class="nav-menu-list">
                        <li><a href="<?php echo esc_url( home_url('/') ); ?>">الرئيسية</a></li>
                        <li class="nav-item-dropdown">
                            <a href="<?php echo esc_url( home_url('/shop') ); ?>">الأصناف <i class="fa fa-chevron-down" style="font-size: 0.7em; margin-right: 3px;"></i></a>
                            <ul class="nav-dropdown-menu">
                                <li><a href="<?php echo esc_url( home_url('/shop') ); ?>">كل المنتجات</a></li>
                                <?php 
                                if (!is_wp_error($cats) && !empty($cats)) :
                                    foreach ($cats as $cat) : ?>
                                        <li><a href="<?php echo esc_url(get_term_link($cat)); ?>"><?php echo esc_html($cat->name); ?></a></li>
                                    <?php endforeach;
                                endif; ?>
                            </ul>
                        </li>
                        <li><a href="<?php echo esc_url( home_url('/faq') ); ?>">الأسئلة المتكررة</a></li>
                        <li><a href="<?php echo esc_url( home_url('/about-us') ); ?>">من نحن</a></li>
                    </ul>
                </nav>
                <div class="mobile-menu-toggle" id="open_drawer"><i class="fa fa-bars"></i></div>
            </div>
        </div>
    </div>
</header>

<script>
// Loader Logic
document.addEventListener('DOMContentLoaded', function() {
    const loader = document.getElementById('site-loader');
    
    // Smooth fade out after 0.8 seconds (faster)
    setTimeout(function() {
        loader.classList.add('hidden');
    }, 800);

    // Fade in on page leave
    window.addEventListener('beforeunload', function() {
        loader.classList.remove('hidden');
    });

    // Mobile Drawer Logic
    const openBtn = document.getElementById('open_drawer');
    const closeBtn = document.getElementById('close_drawer');
    const drawer = document.getElementById('mobile_drawer');
    const overlay = document.getElementById('drawer_overlay');

    openBtn.addEventListener('click', () => {
        overlay.style.display = 'block';
        setTimeout(() => drawer.classList.add('open'), 10);
    });

    const closeDrawer = () => {
        drawer.classList.remove('open');
        setTimeout(() => overlay.style.display = 'none', 300);
    };

    closeBtn.addEventListener('click', closeDrawer);
    overlay.addEventListener('click', closeDrawer);

    // Search Trigger (Simple redirect to search or show bar)
    document.querySelector('.search-trigger').addEventListener('click', function() {
        let q = prompt('عن ماذا تبحث؟');
        if (q) window.location.href = '<?php echo home_url("/"); ?>?s=' + q;
    });
});
</script>

<div id="page" class="hfeed site">
    <div id="content" class="site-content">
        <div class="ast-container">
