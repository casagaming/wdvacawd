<?php
/**
 * Template Name: Homepage Redesign (الرئيسية)
 */
get_header(); ?>

        </div> <!-- Close ast-container from header to allow full width -->
    </div> <!-- Close site-content from header to allow full width -->

<style>
/* Reset and ensure full width */
.custom-homepage-wrapper {
    width: 100%;
    overflow-x: hidden;
    margin: 0;
    padding: 0;
    font-family: 'Cairo', sans-serif;
    color: #333;
}

/* 1. Hero Slider */
.homepage-hero { position: relative; width: 100%; height: 80vh; overflow: hidden; background: #000; }
.swiper { width: 100%; height: 100%; }
.swiper-slide { 
    background-size: cover; background-position: center; 
    display: flex; align-items: center; justify-content: flex-end; 
    padding: 50px 10%; box-sizing: border-box; 
    position: relative;
}
/* Enhanced Overlay from Screenshot */
.swiper-slide::before {
    content: ''; position: absolute; top:0; left:0; width:100%; height:100%;
    background: linear-gradient(to right, rgba(0,0,0,0.6) 0%, rgba(0,0,0,0.1) 50%, rgba(0,0,0,0.4) 100%); 
    z-index: 1;
}
.hero-content { max-width: 600px; text-align: right; direction: rtl; position: relative; z-index: 2; }
.hero-content h3 { 
    color: #fff; font-size: 1.4rem; font-weight: 800; margin-bottom: 12px; 
    background: var(--brand-red); display: inline-block; padding: 6px 18px; 
    border-radius: 4px; box-shadow: 0 4px 10px rgba(0,0,0,0.2);
}
.hero-content h1 { 
    color: #fff; font-size: 5.5rem; font-weight: 900; margin: 0 0 15px 0; 
    text-shadow: 3px 3px 6px rgba(0,0,0,0.6); line-height: 1; 
}
.hero-content p { 
    color: #fff; font-size: 1.5rem; margin-bottom: 35px; 
    text-shadow: 1px 1px 3px rgba(0,0,0,0.4); max-width: 500px; margin-right: auto;
}
.hero-btn { 
    display: inline-flex; align-items: center; gap: 12px; background-color: var(--brand-red); 
    color: #fff !important; padding: 18px 45px; border-radius: 35px; font-weight: 800; 
    text-decoration: none; font-size: 1.4rem; transition: 0.3s; 
    box-shadow: 0 10px 25px rgba(184, 0, 0, 0.4);
}
.hero-btn:hover { background-color: #e00000; transform: translateY(-3px); }
.hero-btn i { font-size: 1.1rem; }

@media (max-width: 768px) {
    .homepage-hero { height: 65vh; }
    .hero-content h1 { font-size: 3.2rem; }
    .hero-content p { font-size: 1.1rem; }
    .hero-btn { padding: 12px 30px; font-size: 1.1rem; }
}

.swiper-button-next, .swiper-button-prev { color: #fff !important; background: rgba(0,0,0,0.2); width: 45px; height: 45px; border-radius: 50%; }
.swiper-button-next::after, .swiper-button-prev::after { font-size: 20px !important; font-weight: 900; }
.swiper-pagination-bullet { background: #fff !important; opacity: 0.5; }
.swiper-pagination-bullet-active { background: var(--brand-red) !important; opacity: 1; transform: scale(1.2); }

/* 2. Features Bar - New Design */
.features-bar {
    background: #fff;
    padding: 30px 5%;
    border-bottom: 2px solid #f0f0f0;
}
.features-grid {
    display: flex;
    justify-content: space-between;
    gap: 15px;
    flex-wrap: wrap;
    text-align: right;
    direction: rtl;
}
.feature-item {
    flex: 1;
    min-width: 200px;
    display: flex;
    align-items: center;
    gap: 12px;
    padding: 10px;
    border-right: 1px solid #eee;
}
.feature-item:last-child { border-right: none; }
.feature-item i {
    font-size: 1.5rem;
    color: #8b0000;
    background: #fffafa;
    width: 45px;
    height: 45px;
    display: flex;
    align-items: center;
    justify-content: center;
    border-radius: 50%;
    border: 1px solid #8b0000;
}
.feature-item h4 {
    margin: 0;
    font-size: 1rem;
    font-weight: 800;
    color: #111;
}
.feature-item p {
    margin: 0;
    font-size: 0.85rem;
    color: #666;
}

/* 3. Trending Section & New Product Cards */
.trending-section {
    padding: 60px 5%;
    background: #fdfdfd;
    text-align: center;
    direction: rtl;
}
.trending-header h4 {
    color: #222;
    font-size: 1.2rem;
    font-weight: 700;
    margin: 0 0 5px 0;
}
.trending-header h2 {
    font-size: 2.8rem;
    font-weight: 900;
    color: #111;
    margin: 0 0 10px 0;
}
.trending-header p {
    color: #888;
    font-size: 1.1rem;
    margin-bottom: 40px;
}

.custom-products-grid {
    display: grid;
    grid-template-columns: repeat(5, 1fr);
    gap: 15px;
    padding: 20px 0;
}
@media(max-width: 1200px) {
    .custom-products-grid { grid-template-columns: repeat(4, 1fr); }
}
@media(max-width: 992px) {
    .custom-products-grid { grid-template-columns: repeat(3, 1fr); }
}
@media(max-width: 600px) {
    .custom-products-grid { grid-template-columns: repeat(2, 1fr); gap: 10px; }
}

.product-card {
    background: #fff;
    border-radius: 12px;
    overflow: hidden;
    box-shadow: 0 10px 30px rgba(0,0,0,0.05);
    transition: 0.4s;
    text-decoration: none !important;
    display: block;
    text-align: center;
    border: 1px solid #eee;
}
.product-card:hover { transform: translateY(-10px); box-shadow: 0 15px 40px rgba(0,0,0,0.1); }

.card-img-wrap { position: relative; width: 100%; padding-top: 100%; border-bottom: 1px solid #f5f5f5; }
.card-img-wrap img { position: absolute; top: 0; left: 0; width: 100%; height: 100%; object-fit: cover; }

.card-content { padding: 12px; }
.card-title { font-size: 1.1rem; font-weight: 800; color: #111; margin-bottom: 5px; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
.card-price { font-size: 1rem; font-weight: 700; color: #8b0000; }
.card-price del { font-size: 0.85rem; color: #999; margin-left: 5px; }

.card-badge { position: absolute; top: 15px; left: 15px; background: rgba(0,0,0,0.85); color: #fff; padding: 5px 15px; border-radius: 25px; font-size: 0.85rem; z-index: 5; font-weight: 700; display: flex; align-items: center; gap: 8px; box-shadow: 0 4px 10px rgba(0,0,0,0.2); }
.card-badge i { color: #fff; text-shadow: 0 0 10px rgba(255,255,255,0.5); }
</style>

<div class="custom-homepage-wrapper">

    <!-- 1. Hero Slider Section -->
    <div class="homepage-hero">
        <div class="swiper mySwiper">
            <div class="swiper-wrapper">
                <?php 
                $slides = new WP_Query([
                    'post_type' => 'hero_slide',
                    'posts_per_page' => -1,
                    'orderby' => 'menu_order',
                    'order' => 'ASC'
                ]);
                
                if ($slides->have_posts()) : 
                    while ($slides->have_posts()) : $slides->the_post();
                        $cid        = get_the_ID();
                        $bg_img     = get_post_meta($cid, '_slide_img', true);
                        $subtitle   = get_post_meta($cid, '_slide_subtitle', true);
                        $title      = get_the_title();
                        $desc       = get_post_meta($cid, '_slide_desc', true);
                        $btn_text   = get_post_meta($cid, '_slide_btn_text', true);
                        $btn_url    = get_post_meta($cid, '_slide_btn_url', true);
                        
                        // Fallback image
                        if (empty($bg_img)) $bg_img = 'https://images.unsplash.com/photo-1542291026-7eec264c27ff?q=80&w=2070&auto=format&fit=crop';
                ?>
                <div class="swiper-slide" style="background-image: url('<?php echo esc_url($bg_img); ?>');">
                    <div class="hero-content">
                        <?php if ($subtitle) : ?>
                        <h3><?php echo esc_html($subtitle); ?></h3>
                        <?php endif; ?>
                        
                        <h1><?php echo esc_html($title); ?></h1>
                        <p><?php echo esc_html($desc); ?></p>
                        
                        <?php if ($btn_text && $btn_url) : ?>
                            <a href="<?php echo esc_url($btn_url); ?>" class="hero-btn">
                                <?php echo esc_html($btn_text); ?> 
                                <i class="fa fa-chevron-left" style="font-size:0.8em; margin-right:8px;"></i>
                            </a>
                        <?php endif; ?>
                    </div>
                </div>
                <?php endwhile; wp_reset_postdata(); else : ?>
                <!-- Default Slide if none created -->
                <div class="swiper-slide" style="background-image: url('https://images.unsplash.com/photo-1542291026-7eec264c27ff?q=80&w=2070&auto=format&fit=crop');">
                    <div class="hero-content">
                        <h3>الراحة في كل خطوة</h3>
                        <h1>Lacoste Nitro</h1>
                        <p>الراحة في كل خطوة... لدينا كل ما تحتاجه لتمشي في المقدمة.</p>
                        <a href="#offers" class="hero-btn">احصل على العروض <i class="fa fa-chevron-left"></i></a>
                    </div>
                </div>
                <?php endif; ?>
            </div>
            <!-- Slider Controls -->
            <div class="swiper-button-next"></div>
            <div class="swiper-button-prev"></div>
            <div class="swiper-pagination"></div>
        </div>
    </div>

    <!-- 2. Features Bar -->
    <div class="features-bar">
        <div class="features-grid">
            <div class="feature-item">
                <i class="fa fa-truck"></i>
                <div>
                    <h4>التوصيل مجاني وسريع</h4>
                    <p>يصلك الحذاء لباب بيتك مجاناً</p>
                </div>
            </div>
            <div class="feature-item">
                <i class="fa fa-check-circle-o"></i>
                <div>
                    <h4>تحقق من الحذاء</h4>
                    <p>لك الحق في فتح الطرد قبل الدفع</p>
                </div>
            </div>
            <div class="feature-item">
                <i class="fa fa-phone"></i>
                <div>
                    <h4>خدمة العملاء 24 ساعة</h4>
                    <p>سنبقى على تواصل دائم</p>
                </div>
            </div>
            <div class="feature-item">
                <i class="fa fa-money"></i>
                <div>
                    <h4>الدفع عند الاستلام</h4>
                    <p>ادفع فقط عند استلام طلبك</p>
                </div>
            </div>
        </div>
    </div>

    <!-- 3. Trending Products Section -->
    <div class="trending-section" id="offers">
        <div class="trending-header">
            <h4>اكتشف الآن!</h4>
            <h2>أحذيتنا الأكثر طلباً <i class="fa fa-fire" style="color:#ff4500;"></i></h2>
            <p>موديلات مختارة بعناية... الأكثر طلباً من زبائننا اليوم</p>
        </div>
        
        <div class="custom-products-grid">
            <?php 
            $args = array(
                'post_type' => 'product',
                'posts_per_page' => 10,
            );
            $loop = new WP_Query( $args );
            while ( $loop->have_posts() ) : $loop->the_post();
                global $product;
                ?>
                <a href="<?php the_permalink(); ?>" class="product-card">
                    <div class="card-img-wrap">
                        <?php 
                        $custom_badge = get_post_meta(get_the_ID(), '_custom_badge_text', true);
                        if ($custom_badge) : ?>
                            <div class="card-badge"><?php echo esc_html($custom_badge); ?> <i class="fa fa-bolt"></i></div>
                        <?php endif; ?>
                        <?php the_post_thumbnail('medium'); ?>
                    </div>
                    <div class="card-content">
                        <h3 class="card-title"><?php the_title(); ?></h3>
                        <div class="card-price">
                            <?php if ($product->get_sale_price()) : ?>
                                <del><?php echo wc_price($product->get_regular_price()); ?></del>
                                <span><?php echo wc_price($product->get_sale_price()); ?></span>
                            <?php else : ?>
                                <span><?php echo wc_price($product->get_price()); ?></span>
                            <?php endif; ?>
                        </div>
                    </div>
                </a>
            <?php endwhile; wp_reset_query(); ?>
        </div>
    </div>

</div> <!-- End custom-homepage-wrapper -->

<script>
// Initialize Swiper
document.addEventListener('DOMContentLoaded', function() {
    if (typeof Swiper !== 'undefined') {
        var swiper = new Swiper(".mySwiper", {
            loop: true,
            autoplay: {
                delay: 5000,
                disableOnInteraction: false,
            },
            navigation: {
                nextEl: ".swiper-button-next",
                prevEl: ".swiper-button-prev",
            },
            pagination: {
                el: ".swiper-pagination",
                clickable: true,
            },
        });
    }
});
</script>

    <!-- Reopen containers for footer -->
    <div id="content" class="site-content">
        <div class="ast-container">

<?php get_footer(); ?>
