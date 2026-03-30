<?php
/**
 * Template Name: صفحة الأسئلة الشائعة (Premium FAQ)
 */
get_header(); 

$f_id = get_option('page_on_front');
$wa   = get_post_meta($f_id, '_store_wa', true);
$phone = get_post_meta($f_id, '_store_phone', true);
$f_logo = get_post_meta($f_id, '_store_footer_logo', true) ?: get_stylesheet_directory_uri() . '/assets/main-logo.png';
?>

<!-- Container Break for Full Width -->
</div> <!-- close .ast-container -->
</div> <!-- close .site-content -->

<style>
.faq-page-wrapper { background: #fdfdfd; padding: 100px 20px; font-family: 'Cairo', sans-serif; direction: rtl; }
.faq-header { text-align: center; margin-bottom: 80px; }
.faq-header img.brand-logo { width: 180px; height: auto; margin-bottom: 35px; filter: drop-shadow(0 10px 25px rgba(0,0,0,0.1)); }
.faq-header h1 { font-size: 4rem; font-weight: 950; color: #111; margin: 0 0 20px; letter-spacing: -1.5px; }
.faq-header p { color: #555; font-size: 1.3rem; font-weight: 600; max-width: 700px; margin: 0 auto 40px; }

.contact-box { background: #fff; border: 2px solid #f0f0f0; padding: 30px 50px; border-radius: 60px; display: inline-flex; align-items: center; gap: 25px; box-shadow: 0 15px 45px rgba(0,0,0,0.04); }
.contact-box span { font-weight: 900; color: #222; font-size: 1.2rem; }
.contact-btn { background: #25d366; color: #fff !important; padding: 15px 40px; border-radius: 40px; font-weight: 950; text-decoration: none; transition: 0.4s; box-shadow: 0 10px 25px rgba(37,211,102,0.3); font-size: 1.15rem; }
.contact-btn:hover { background: #20b355; transform: translateY(-3px); box-shadow: 0 15px 35px rgba(37,211,102,0.4); }

.faq-container { max-width: 950px; margin: 0 auto; }
.faq-item { background: #fff; border: 1px solid #eee; border-radius: 25px; margin-bottom: 25px; overflow: hidden; transition: 0.4s; }
.faq-item:hover { border-color: var(--brand-red); box-shadow: 0 20px 50px rgba(0,0,0,0.06); }
.faq-question { padding: 30px 35px; display: flex; justify-content: space-between; align-items: center; cursor: pointer; user-select: none; }
.q-title { display: flex; align-items: center; gap: 20px; font-size: 1.4rem; font-weight: 900; color: #111; }
.q-title i { color: var(--brand-red); font-size: 1.7rem; width: 35px; text-align: center; }
.q-arrow { width: 40px; height: 40px; background: #f8f8f8; border-radius: 50%; display: flex; align-items: center; justify-content: center; transition: 0.4s; color: #aaa; }
.faq-item.active .q-arrow { background: var(--brand-red); color: #fff; transform: rotate(180deg); }

.faq-answer { display: none; padding: 0 35px 35px 90px; color: #555; font-size: 1.25rem; line-height: 1.9; border-top: 1px solid #fcfcfc; padding-top: 25px; text-align: justify; }

@media (max-width: 768px) {
    .faq-header h1 { font-size: 2.8rem; }
    .q-title { font-size: 1.2rem; gap: 12px; }
    .contact-box { flex-direction: column; padding: 25px; border-radius: 30px; width: 100%; gap: 15px; }
    .contact-btn { width: 100%; text-align: center; }
}
</style>

<div class="faq-page-wrapper">
    <div class="faq-header">
        <img src="<?php echo esc_url($f_logo); ?>" alt="Premium Support" class="brand-logo">
        <h1>الأسئلة الشائعة</h1>
        
        <div style="margin: 30px auto; max-width: 800px; text-align: center; color: #555; font-size: 1.2rem; line-height: 1.8;">
            <?php if (have_posts()) : while (have_posts()) : the_post(); the_content(); endwhile; endif; ?>
        </div>
        

    </div>

    <div class="faq-container">
        <!-- Everything is now dynamic from the "FAQ" CPT -->
        <?php
        $faq_query = new WP_Query([
            'post_type'      => 'faq',
            'posts_per_page' => -1,
            'orderby'        => 'menu_order',
            'order'          => 'ASC'
        ]);
        if ($faq_query->have_posts()) :
            while ($faq_query->have_posts()) : $faq_query->the_post(); ?>
                <div class="faq-item">
                    <div class="faq-question">
                        <div class="q-title"><i class="fa fa-question-circle"></i><span><?php the_title(); ?></span></div>
                        <div class="q-arrow"><i class="fa fa-chevron-down"></i></div>
                    </div>
                    <div class="faq-answer"><?php the_content(); ?></div>
                </div>
            <?php endwhile; wp_reset_postdata();
        else : ?>
            <div style="text-align:center; padding: 100px 20px; color: #bbb; border: 2px dashed #eee; border-radius: 30px;">
                <i class="fa fa-info-circle" style="font-size: 3rem; margin-bottom: 20px;"></i>
                <p style="font-size: 1.2rem;">لا توجد أسئلة مضافة حالياً. يرجى إضافة أسئلة من لوحة التحكم.</p>
            </div>
        <?php endif; ?>
    </div>
</div>

<script>
jQuery(document).ready(function($) {
    $('.faq-question').on('click', function() {
        var item = $(this).closest('.faq-item');
        item.toggleClass('active').find('.faq-answer').slideToggle(400);
        $('.faq-item').not(item).removeClass('active').find('.faq-answer').slideUp(300);
    });
});
</script>

<?php get_footer(); ?>

<!-- Reopen containers for footer -->
<div id="content" class="site-content">
<div class="ast-container">
