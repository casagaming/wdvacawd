<?php
/**
 * The template for displaying the footer.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

?>
		</div> <!-- ast-container -->
	</div><!-- #content -->

<?php 
$f_id = get_option('page_on_front');
$fb = get_post_meta($f_id, '_store_fb', true);
$ig = get_post_meta($f_id, '_store_ig', true);
$tk = get_post_meta($f_id, '_store_tk', true);
$wa = get_post_meta($f_id, '_store_wa', true);
$phone = get_post_meta($f_id, '_store_phone', true);
$addr = get_post_meta($f_id, '_store_addr', true);
$f_logo = get_post_meta($f_id, '_store_footer_logo', true) ?: get_stylesheet_directory_uri() . '/assets/main-logo.png';
?>

<footer class="site-footer" style="background-color: #111; color: #eee; padding: 80px 20px 40px; font-family: 'Cairo', sans-serif; border-top: 5px solid var(--brand-red);" dir="rtl">
    <div class="ast-container" style="max-width: 1200px; margin: 0 auto;">
        <div style="display: flex; flex-wrap: wrap; justify-content: space-between; gap: 50px; margin-bottom: 60px;">
            
            <!-- Column 1: Brand -->
            <div style="flex: 1.5; min-width: 300px;">
                <img src="<?php echo esc_url($f_logo); ?>" alt="Gentle Shoes" style="height: 90px; width: auto; filter: brightness(0) invert(1); margin-bottom: 25px; object-fit: contain;">
                <p style="color: #aaa; line-height: 1.8; font-size: 1rem; margin-bottom: 30px;">
                    Gentle Shoes هي وجهتك الأولى للأحذية العصرية التي تجمع بين الجودة العالية والراحة المثالية. نسعى دائماً لتقديم الأفضل لعملائنا في جميع أنحاء الجزائر.
                </p>
                <div style="display: flex; gap: 15px;">
                    <?php if ($fb): ?><a href="<?php echo esc_url($fb); ?>" target="_blank" style="width: 45px; height: 45px; background: #3b5998; color: #fff; border-radius: 12px; display: flex; align-items: center; justify-content: center; transition: 0.3s; font-size: 1.2rem;"><i class="fa fa-facebook"></i></a><?php endif; ?>
                    <?php if ($ig): ?><a href="<?php echo esc_url($ig); ?>" target="_blank" style="width: 45px; height: 45px; background: #e1306c; color: #fff; border-radius: 12px; display: flex; align-items: center; justify-content: center; transition: 0.3s; font-size: 1.2rem;"><i class="fa fa-instagram"></i></a><?php endif; ?>
                    <?php if ($tk): ?><a href="<?php echo esc_url($tk); ?>" target="_blank" style="width: 45px; height: 45px; background: #000; color: #fff; border-radius: 12px; display: flex; align-items: center; justify-content: center; transition: 0.3s; font-size: 1.2rem; border: 1px solid #333;"><i class="fa fa-tiktok"></i></a><?php endif; ?>

                </div>
            </div>

            <!-- Column 2: Quick Navigation -->
            <div style="flex: 1; min-width: 180px;">
                <h3 style="color: #fff; margin-bottom: 30px; font-weight: 900; font-size: 1.2rem; border-right: 4px solid var(--brand-red); padding-right: 15px;">روابط سريعة</h3>
                <ul style="list-style: none; padding: 0; line-height: 2.5; font-size: 1.05rem;">
                    <li><a href="<?php echo home_url('/'); ?>" style="color: #bbb; text-decoration: none; transition: 0.3s;">الرئيسية</a></li>
                    <li><a href="<?php echo home_url('/shop'); ?>" style="color: #bbb; text-decoration: none; transition: 0.3s;">كل المنتجات</a></li>
                    <li><a href="<?php echo home_url('/about-us'); ?>" style="color: #bbb; text-decoration: none; transition: 0.3s;">من نحن</a></li>
                    <li><a href="<?php echo home_url('/faq'); ?>" style="color: #bbb; text-decoration: none; transition: 0.3s;">الأسئلة المتكررة</a></li>
                </ul>
            </div>

            <!-- Column 3: Contact Info -->
            <div style="flex: 1.2; min-width: 250px;">
                <h3 style="color: #fff; margin-bottom: 30px; font-weight: 900; font-size: 1.2rem; border-right: 4px solid var(--brand-red); padding-right: 15px;">تواصل معنا</h3>
                <div style="margin-bottom: 25px;">
                    <p style="color: #888; font-size: 0.9rem; margin-bottom: 5px;">رقم الهاتف:</p>
                    <div style="display: flex; align-items: center; gap: 12px; color: #fff; font-weight: 900; font-size: 1.3rem;">
                        <i class="fa fa-phone" style="color: var(--brand-red);"></i>
                        <span style="direction: ltr;"><?php echo esc_html($phone ?: $wa); ?></span>
                    </div>
                </div>
                <div>
                    <p style="color: #888; font-size: 0.9rem; margin-bottom: 5px;">الموقع:</p>
                    <div style="display: flex; align-items: center; gap: 12px; color: #bbb; font-size: 1.1rem; line-height: 1.4;">
                        <i class="fa fa-map-marker" style="color: var(--brand-red); font-size: 1.4rem;"></i>
                        <span><?php echo esc_html($addr ?: 'الجزائر العاصمة'); ?></span>
                    </div>
                </div>
            </div>

        </div>

        <!-- Bottom Copyright -->
        <div style="border-top: 1px solid #222; padding-top: 35px; display: flex; flex-wrap: wrap; justify-content: space-between; align-items: center; font-size: 0.95rem; color: #666;">
            <p>جميع الحقوق محفوظة © <?php echo date('Y'); ?> Gentle Shoes. صمم باحترافية.</p>
            <div style="display: flex; gap: 25px;">
                <a href="<?php echo home_url('/privacy-policy'); ?>" style="color: #666; text-decoration: none;">سياسة الخصوصية</a>
                <a href="<?php echo home_url('/terms'); ?>" style="color: #666; text-decoration: none;">الشروط والأحكام</a>
            </div>
        </div>
    </div>
</footer>

</div><!-- #page -->

<?php wp_footer(); ?>
</body>
</html>
