<?php
/**
 * Template Name: صفحة المنتجات (Premium Shop)
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; 
}

get_header(); 

// Safety Check
if ( ! class_exists( 'WooCommerce' ) ) {
    echo '<div style="text-align:center; padding:100px 20px;"><h3>يرجى تفعيل إضافة WooCommerce لتتمكن من رؤية المنتجات.</h3></div>';
    get_footer();
    return;
}

$f_id = get_option('page_on_front');
?>

<!-- Container Break for Full Width -->
</div> <!-- close .ast-container -->
</div> <!-- close .site-content -->

<style>
/* 1. Shop Hero Section - Premium Dark Mode */
.shop-hero { 
    background: linear-gradient(135deg, #111, #333); 
    padding: 100px 20px; 
    text-align: center; 
    color: #fff; 
    border-bottom: 5px solid var(--brand-red);
    position: relative;
    overflow: hidden;
}
.shop-hero h1 { font-size: 4.5rem; font-weight: 950; margin: 0; letter-spacing: -2px; text-transform: uppercase; }
.shop-hero p { font-size: 1.4rem; color: #ccc; margin-top: 15px; font-weight: 600; }

/* 2. Shop Layout - Edge to Edge */
.shop-main-wrapper { 
    background: #fdfdfd; 
    padding: 80px 4%; 
    direction: rtl; 
    font-family: 'Cairo', sans-serif; 
    display: flex; 
    gap: 50px; 
}
.shop-sidebar { width: 320px; flex-shrink: 0; position: sticky; top: 120px; align-self: flex-start; }
.shop-content { flex-grow: 1; }

/* 3. Filter Sidebar UI - Glassmorphism touch */
.filter-box { 
    background: #fff; 
    padding: 35px; 
    border-radius: 30px; 
    box-shadow: 0 15px 45px rgba(0,0,0,0.04); 
    margin-bottom: 35px; 
    border: 1px solid #f0f0f0; 
}
.filter-box h3 { font-size: 1.5rem; font-weight: 950; margin-bottom: 30px; color: #111; border-right: 6px solid var(--brand-red); padding-right: 20px; }

.search-field-wrap { position: relative; }
.search-field-wrap input { 
    width: 100%; padding: 18px 25px; border-radius: 40px; border: 2px solid #eee; background: #fafafa; 
    font-size: 1.15rem; outline: none; transition: 0.4s; font-family: inherit;
}
.search-field-wrap input:focus { border-color: var(--brand-red); background: #fff; box-shadow: 0 8px 25px rgba(184,0,0,0.12); }

.category-list { list-style: none; padding: 0; margin: 0; }
.category-list li { margin-bottom: 15px; }
.category-list a { 
    text-decoration: none; color: #555; font-weight: 850; font-size: 1.2rem; 
    display: flex; justify-content: space-between; align-items: center; transition: 0.4s; padding: 8px 0;
}
.category-list a:hover, .category-list a.active { color: var(--brand-red); transform: translateX(-10px); }
.cat-badge { background: #f5f5f5; padding: 4px 15px; border-radius: 15px; font-size: 0.9rem; color: #999; font-weight: 800; border: 1px solid #eee; }
.category-list a.active .cat-badge { background: var(--brand-red); color: #fff; border-color: var(--brand-red); }

/* 4. PREMIUM PRODUCT CARDS - Unified High-End Design */
.custom-products-grid { 
    display: grid; grid-template-columns: repeat(auto-fill, minmax(280px, 1fr)); gap: 35px; 
}
.product-card { 
    background: #fff; border-radius: 25px; overflow: hidden; box-shadow: 0 15px 40px rgba(0,0,0,0.05); 
    transition: 0.5s; text-decoration: none !important; display: block; text-align: center; border: 1px solid #f5f5f5; position: relative; 
}
.product-card:hover { transform: translateY(-15px); box-shadow: 0 25px 60px rgba(0,0,0,0.15); border-color: var(--brand-red); }

.card-img-wrap { position: relative; width: 100%; padding-top: 110%; background: #fcfcfc; overflow: hidden; }
.card-img-wrap img { position: absolute; top: 0; left: 0; width: 100%; height: 100%; object-fit: cover; transition: 0.8s cubic-bezier(0.2, 1, 0.3, 1); }
.product-card:hover .card-img-wrap img { transform: scale(1.08); }

.card-badge { 
    position: absolute; top: 20px; left: 20px; background: #c00000; color: #fff; 
    padding: 6px 18px; border-radius: 30px; font-size: 0.9rem; z-index: 5; font-weight: 900; 
    display: flex; align-items: center; gap: 10px; box-shadow: 0 10px 20px rgba(192,0,0,0.3);
}

.card-content { padding: 30px 20px; }
.card-title { font-size: 1.4rem; font-weight: 950; color: #111; margin-bottom: 20px; display: block; line-height: 1.3; }
.card-price { font-size: 1.5rem; font-weight: 900; color: var(--brand-red); display: flex; align-items: center; justify-content: center; gap: 12px; }
.card-price del { font-size: 1.1rem; color: #bbb; text-decoration: line-through; font-weight: 600; }

.view-btn { 
    display: inline-block; margin-top: 20px; background: #111; color: #fff; padding: 12px 30px; border-radius: 30px; 
    font-weight: 800; transition: 0.3s; width: 100%; 
}
.product-card:hover .view-btn { background: var(--brand-red); }

/* Responsive Adjustments */
@media (max-width: 992px) {
    .shop-main-wrapper { padding: 40px 15px; flex-direction: column; }
    .shop-sidebar { width: 100%; position: relative; top: 0; }
    .custom-products-grid { grid-template-columns: repeat(2, 1fr); gap: 15px; }
    .shop-hero h1 { font-size: 3rem; }
}
@media (max-width: 480px) {
    .custom-products-grid { grid-template-columns: 1fr; }
}
</style>

<div class="shop-hero">
    <h1>مجموعة منتجاتنا</h1>
    <p>اختر ما يناسب ذوقك من أرقى الماركات العالمية والمحلية</p>
</div>

<div class="shop-main-wrapper">
    <aside class="shop-sidebar">
        <div class="filter-box">
            <h3>ابحث في المتجر</h3>
            <form action="<?php echo home_url('/shop'); ?>" method="get" class="search-field-wrap">
                <input type="text" name="s_name" value="<?php echo esc_attr($_GET['s_name'] ?? ''); ?>" placeholder="اكتب اسم المنتج...">
            </form>
        </div>

        <div class="filter-box">
            <h3>الأقسام</h3>
            <ul class="category-list">
                <li>
                    <a href="<?php echo home_url('/shop'); ?>" class="<?php echo !isset($_GET['cat']) ? 'active' : ''; ?>">
                        <span>الكل</span>
                        <span class="cat-badge"><?php echo wp_count_posts('product')->publish; ?></span>
                    </a>
                </li>
                <?php
                $terms = get_terms(['taxonomy' => 'product_cat', 'hide_empty' => true]);
                if (!is_wp_error($terms)) :
                    foreach ($terms as $term) : ?>
                        <li>
                            <a href="?cat=<?php echo $term->slug; ?>" class="<?php echo ($_GET['cat'] ?? '') == $term->slug ? 'active' : ''; ?>">
                                <span><?php echo $term->name; ?></span>
                                <span class="cat-badge"><?php echo $term->count; ?></span>
                            </a>
                        </li>
                    <?php endforeach;
                endif; ?>
            </ul>
        </div>
    </aside>

    <div class="shop-content">
        <div class="custom-products-grid">
            <?php
            $paged = (get_query_var('paged')) ? get_query_var('paged') : 1;
            $args = [
                'post_type'      => 'product',
                'posts_per_page' => 12,
                'paged'          => $paged,
                'status'         => 'publish'
            ];

            if (!empty($_GET['cat'])) {
                $args['tax_query'] = [[
                    'taxonomy' => 'product_cat',
                    'field'    => 'slug',
                    'terms'    => sanitize_text_field($_GET['cat'])
                ]];
            }
            if (!empty($_GET['s_name'])) {
                $args['s'] = sanitize_text_field($_GET['s_name']);
            }

            $loop = new WP_Query($args);
            if ($loop->have_posts()) :
                while ($loop->have_posts()) : $loop->the_post();
                    $product = wc_get_product(get_the_ID());
                    $badge = get_post_meta(get_the_ID(), '_custom_badge_text', true);
            ?>
                <a href="<?php the_permalink(); ?>" class="product-card">
                    <div class="card-img-wrap">
                        <?php if ($badge) : ?>
                            <div class="card-badge"><?php echo esc_html($badge); ?> <i class="fa fa-bolt"></i></div>
                        <?php elseif ($product->is_on_sale()) : ?>
                             <div class="card-badge">تخفيض <i class="fa fa-percent"></i></div>
                        <?php endif; ?>
                        <?php 
                        if (has_post_thumbnail()) {
                            the_post_thumbnail('large'); 
                        } else {
                            echo wc_placeholder_img('large');
                        }
                        ?>
                    </div>
                    <div class="card-content">
                        <span class="card-title"><?php the_title(); ?></span>
                        <div class="card-price">
                            <?php if ($product->is_on_sale()) : ?>
                                <del><?php echo wc_price($product->get_regular_price()); ?></del>
                                <span><?php echo wc_price($product->get_sale_price()); ?></span>
                            <?php else : ?>
                                <span><?php echo wc_price($product->get_price()); ?></span>
                            <?php endif; ?>
                        </div>
                        <span class="view-btn">تفاصيل العرض</span>
                    </div>
                </a>
            <?php endwhile; wp_reset_postdata(); else : ?>
                <div style="text-align:center; grid-column: 1/-1; padding: 100px 20px;">
                    <i class="fa fa-search" style="font-size: 5rem; color: #ddd; margin-bottom: 25px;"></i>
                    <h2 style="color: #111; font-weight: 900;">لم يتم العثور على أي منتجات</h2>
                    <p style="color: #666; font-size: 1.2rem;">جرب البحث بكلمات أخرى أو اختر قسماً آخر.</p>
                </div>
            <?php endif; ?>
        </div>
        
        <!-- Pagination -->
        <div class="shop-pagination" style="margin-top: 50px; text-align: center;">
            <?php
            echo paginate_links([
                'total'   => $loop->max_num_pages,
                'current' => $paged,
                'format'  => '?paged=%#%',
                'type'    => 'plain',
                'prev_text' => '<i class="fa fa-chevron-right"></i>',
                'next_text' => '<i class="fa fa-chevron-left"></i>',
            ]);
            ?>
        </div>
    </div>
</div>

<!-- Reopen containers for footer -->
<div id="content" class="site-content">
<div class="ast-container">

<?php get_footer(); ?>
