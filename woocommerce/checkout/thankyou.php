<?php
/**
 * Simple Integrated Thank You Page
 *
 * @package WooCommerce\Templates
 * @version 8.1.0
 */

defined( 'ABSPATH' ) || exit;
?>

<style>
.simple-thank-you {
    margin: 40px auto;
    padding: 40px 30px;
    background: #fff;
    border-radius: 16px;
    box-shadow: 0 5px 20px rgba(0,0,0,0.05);
    text-align: center;
    font-family: 'Cairo', sans-serif;
    border: 1px solid #f0f0f0;
}

.sty-icon {
    width: 80px;
    height: 80px;
    background: #28a745;
    color: #fff;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 2.5rem;
    margin: 0 auto 20px;
}

.sty-title {
    font-size: 2rem;
    font-weight: 800;
    color: #333;
    margin-bottom: 10px;
}

.sty-desc {
    font-size: 1.1rem;
    color: #666;
    margin-bottom: 30px;
}

.sty-details {
    background: #fdfdfd;
    border: 1px solid #eee;
    border-radius: 12px;
    padding: 20px;
    margin-bottom: 30px;
}

.sty-row {
    display: flex;
    justify-content: space-between;
    padding: 10px 0;
    border-bottom: 1px dashed #ddd;
}
.sty-row:last-child {
    border-bottom: none;
}
.sty-label {
    color: #777;
    font-weight: 600;
}
.sty-val {
    color: #111;
    font-weight: 800;
}

.sty-notice {
    background: #fff3cd;
    color: #856404;
    padding: 15px;
    border-radius: 8px;
    border: 1px solid #ffeeba;
    font-weight: 700;
    margin-bottom: 30px;
}

.sty-btn {
    display: inline-block;
    background: #111;
    color: #fff;
    padding: 14px 40px;
    border-radius: 50px;
    text-decoration: none;
    font-weight: 800;
    transition: 0.3s;
}
.sty-btn:hover {
    background: #c0392b;
    color: #fff;
}
</style>

<div class="woocommerce-order simple-thank-you">

	<?php
	if ( $order ) :
		$do_not_active = $order->has_status( 'failed' );
		?>

		<?php if ( $do_not_active ) : ?>

			<div class="sty-icon" style="background:#dc3545;"><i class="fa fa-times"></i></div>
			<h1 class="sty-title">عذراً، فشل الطلب</h1>
            <p class="sty-desc">واجهتنا مشكلة أثناء معالجة طلبك.</p>
			
            <a href="<?php echo esc_url( $order->get_checkout_payment_url() ); ?>" class="sty-btn">حاول مرة أخرى</a>

		<?php else : ?>

            <div class="sty-icon"><i class="fa fa-check"></i></div>
            <h1 class="sty-title">شكراً لك! تم استلام طلبك</h1>
            <p class="sty-desc">سيتواصل معك فريقنا قريباً لتأكيد التوصيل.</p>

            <div class="sty-details">
                <div class="sty-row">
                    <span class="sty-label">رقم الطلب:</span>
                    <span class="sty-val">#<?php echo $order->get_order_number(); ?></span>
                </div>
                <div class="sty-row">
                    <span class="sty-label">التاريخ:</span>
                    <span class="sty-val"><?php echo wc_format_datetime( $order->get_date_created() ); ?></span>
                </div>
                <div class="sty-row">
                    <span class="sty-label">الإجمالي:</span>
                    <span class="sty-val"><?php echo $order->get_formatted_order_total(); ?></span>
                </div>
                <div class="sty-row">
                    <span class="sty-label">الدفع:</span>
                    <span class="sty-val"><?php echo wp_kses_post( $order->get_payment_method_title() ); ?></span>
                </div>
            </div>

            <div class="sty-notice">
                <i class="fa fa-phone"></i> يرجى إبقاء الهاتف مفتوحاً للرد على مكالمة التأكيد قبل الشحن.
            </div>

            <a href="<?php echo home_url('/'); ?>" class="sty-btn">العودة للتسوق</a>

		<?php endif; ?>

	<?php else : ?>

        <div class="sty-icon"><i class="fa fa-check"></i></div>
        <h1 class="sty-title">تم استلام طلبك!</h1>
        <p class="sty-desc">شكراً لثقتك بنا.</p>

        <a href="<?php echo home_url('/'); ?>" class="sty-btn">العودة للتسوق</a>

	<?php endif; ?>

</div>
