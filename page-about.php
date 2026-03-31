<?php
/**
 * Template Name: صفحة من نحن (About Us)
 */
get_header(); 

// قراءة بيانات صفحة "من نحن" من الإعدادات
$hero_title    = get_option('gs_about_hero_title',    'من نحن');
$hero_subtitle = get_option('gs_about_hero_subtitle', 'نسعى دائماً لتقديم الأفضل لعملائنا في جميع أنحاء الجزائر.');
$main_content  = get_option('gs_about_main_content',  '');
$main_heading  = get_option('gs_about_main_heading',  'قصتنا مع الأحذية');
$social_title  = get_option('gs_about_social_title',  'انضم إلى مجتمعنا');
$social_desc   = get_option('gs_about_social_desc',   '');
$cta_heading   = get_option('gs_about_cta_heading',   'هل أنت مستعد للأناقة؟');
$cta_desc      = get_option('gs_about_cta_desc',      'مجموعتنا الجديدة من الأحذية الفاخرة بانتظارك الآن.');
$cta_btn       = get_option('gs_about_cta_btn',       'اكتشف كوليكشن الأحذية 👟');

// روابط التواصل الاجتماعي
$fb = get_option('gs_store_fb', '');
$ig = get_option('gs_store_ig', '');
$tk = get_option('gs_store_tk', '');
$wa = get_option('gs_store_wa', '');
?>

<!-- Container Break for Full Width -->
</div> <!-- close .ast-container -->
</div> <!-- close .site-content -->

<style>
/* 1. About Hero Section */
.about-hero { background: #111; padding: 120px 20px; text-align: center; color: #fff; position: relative; overflow: hidden; border-bottom: 4px solid var(--brand-red); }
.about-hero::before { 
    content: ''; position: absolute; top:0; left:0; width:100%; height:100%; 
    background: radial-gradient(circle, rgba(184,0,0,0.15) 0%, rgba(0,0,0,0) 70%); 
    pointer-events: none; 
}
.about-hero h1 { font-size: 5rem; font-weight: 950; margin: 0 0 20px; letter-spacing: -2px; position: relative; z-index: 2; line-height: 1.1; }
.about-hero p { font-size: 1.6rem; color: #ccc; max-width: 850px; margin: 0 auto; line-height: 1.6; position: relative; z-index: 2; font-weight: 500; }

/* 2. Content Sections */
.about-content-area { padding: 100px 20px; direction: rtl; font-family: 'Cairo', sans-serif; background: #fff; }
.about-editor-content { max-width: 1000px; margin: 0 auto 100px; font-size: 1.4rem; line-height: 2.2; color: #444; }
.about-editor-content h2 { font-size: 3.2rem; font-weight: 950; color: #111; margin-bottom: 50px; border-right: 10px solid var(--brand-red); padding-right: 30px; line-height: 1.2; }
.about-main-text { font-size: 1.2rem; line-height: 2; color: #555; white-space: pre-line; }

/* 3. Professional Social Connect Section */
.social-connect-section { background: #fdfdfd; padding: 100px 5%; text-align: center; border-radius: 60px; margin: 0 5% 100px; border: 1px solid #f0f0f0; box-shadow: 0 20px 80px rgba(0,0,0,0.02); }
.social-connect-section h2 { font-size: 3rem; font-weight: 950; color: #111; margin-bottom: 20px; }
.social-extra-desc { font-size: 1.3rem; color: #777; max-width: 700px; margin: 0 auto 60px; line-height: 1.7; }
.social-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(220px, 1fr)); gap: 35px; max-width: 1100px; margin: 0 auto; }

.social-box { 
    background: #fff; padding: 45px 25px; border-radius: 30px; border: 1px solid #f2f2f2;
    text-decoration: none; color: inherit; transition: 0.5s cubic-bezier(0.175, 0.885, 0.32, 1.275); 
    display: flex; flex-direction: column; align-items: center; gap: 20px; 
}
.social-box:hover { transform: translateY(-15px); border-color: var(--brand-red); box-shadow: 0 25px 70px rgba(184,0,0,0.1); }
.social-box i { font-size: 4rem; }
.social-box span { font-weight: 900; font-size: 1.4rem; color: #111; }
.social-box.fb i { color: #3b5998; }
.social-box.ig i { color: #e1306c; }
.social-box.tk i { color: #000; }
.social-box.wa i { color: #25d366; }

/* 4. CTA Banner */
.about-final-cta { 
    background: linear-gradient(135deg, var(--brand-red) 0%, #a00000 100%); 
    color: #fff; padding: 100px 40px; text-align: center; border-radius: 40px; 
    max-width: 1200px; margin: 0 auto 120px; box-shadow: 0 30px 60px rgba(184,0,0,0.2);
}
.about-final-cta h2 { font-size: 3.5rem; font-weight: 950; margin-bottom: 30px; letter-spacing: -1px; }
.about-btn { 
    display: inline-block; background: #fff; color: var(--brand-red); padding: 22px 65px; 
    border-radius: 50px; font-weight: 950; text-decoration: none; font-size: 1.6rem; 
    transition: 0.4s; box-shadow: 0 10px 30px rgba(255,255,255,0.2);
}
.about-btn:hover { background: #111; color: #fff; transform: translateY(-5px); box-shadow: 0 15px 40px rgba(0,0,0,0.3); }

@media (max-width: 768px) {
    .about-hero h1 { font-size: 3.5rem; }
    .social-grid { grid-template-columns: 1fr 1fr; gap: 15px; }
    .social-box { padding: 25px 15px; }
    .about-final-cta h2 { font-size: 2.5rem; }
}
</style>

<div class="about-hero">
    <div class="ast-container" style="max-width: 1200px; margin: 0 auto;">
        <h1><?php echo esc_html($hero_title); ?></h1>
        <?php if ($hero_subtitle): ?>
            <p><?php echo nl2br(esc_html($hero_subtitle)); ?></p>
        <?php endif; ?>
    </div>
</div>

<div class="about-content-area">
    <div class="ast-container" style="max-width: 1200px; margin: 0 auto;">

        <!-- قسم قصة المتجر -->
        <?php if ($main_heading || $main_content): ?>
        <div class="about-editor-content">
            <?php if ($main_heading): ?>
                <h2><?php echo esc_html($main_heading); ?></h2>
            <?php endif; ?>
            <?php if ($main_content): ?>
                <div class="about-main-text"><?php echo wp_kses_post($main_content); ?></div>
            <?php endif; ?>
        </div>
        <?php endif; ?>

        <!-- قسم التواصل الاجتماعي -->
        <section class="social-connect-section">
            <h2><?php echo esc_html($social_title); ?></h2>
            <?php if ($social_desc): ?>
                <div class="social-extra-desc"><?php echo nl2br(esc_html($social_desc)); ?></div>
            <?php endif; ?>

            <div class="social-grid">
                <?php if ($fb): ?>
                <a href="<?php echo esc_url($fb); ?>" target="_blank" class="social-box fb">
                    <i class="fa fa-facebook-square"></i>
                    <span>فيسبوك</span>
                </a>
                <?php endif; ?>

                <?php if ($ig): ?>
                <a href="<?php echo esc_url($ig); ?>" target="_blank" class="social-box ig">
                    <i class="fa fa-instagram"></i>
                    <span>إنستغرام</span>
                </a>
                <?php endif; ?>

                <?php if ($tk): ?>
                <a href="<?php echo esc_url($tk); ?>" target="_blank" class="social-box tk">
                    <i class="fa fa-tiktok"></i>
                    <span>تيك توك</span>
                </a>
                <?php endif; ?>

                <?php if ($wa): ?>
                <a href="https://wa.me/<?php echo esc_attr(preg_replace('/[^0-9]/', '', $wa)); ?>" target="_blank" class="social-box wa">
                    <i class="fa fa-whatsapp"></i>
                    <span>واتساب</span>
                </a>
                <?php endif; ?>
            </div>
        </section>

        <!-- قسم الدعوة للشراء -->
        <div class="about-final-cta">
            <h2><?php echo esc_html($cta_heading); ?></h2>
            <p style="font-size: 1.5rem; margin-bottom: 40px; opacity: 0.9;"><?php echo esc_html($cta_desc); ?></p>
            <a href="<?php echo home_url('/shop'); ?>" class="about-btn"><?php echo esc_html($cta_btn); ?></a>
        </div>

    </div>
</div>

<?php get_footer(); ?>

<!-- Reopen containers for footer -->
<div id="content" class="site-content">
<div class="ast-container">
