<?php
/**
 * Standalone Thank You Page Template
 * This completely isolates the thank you page from the Astra header/footer
 * and shows only a white screen with the success card.
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

// Ensure WooCommerce is active
if ( ! class_exists( 'WooCommerce' ) ) {
    return;
}

$order_id = isset($_GET['order-received']) ? absint($_GET['order-received']) : 0;
// Note: When permalinks are default, it might be in $_GET, but with pretty permalinks it is passed via query_vars
global $wp;
if(empty($order_id) && isset($wp->query_vars['order-received'])) {
    $order_id = absint($wp->query_vars['order-received']);
}

$order = $order_id ? wc_get_order( $order_id ) : false;
?>
<!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
    <meta charset="<?php bloginfo( 'charset' ); ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>شكراً لطلبك - <?php bloginfo('name'); ?></title>
    
    <!-- Load critical WP styles (fonts, emojis, etc) but NO theme output -->
    <?php wp_head(); ?>

    <style>
        body {
            margin: 0;
            padding: 0;
            background-color: #ffffff;
            font-family: 'Cairo', sans-serif;
            display: flex;
            align-items: center;
            justify-content: center;
            min-height: 100vh;
            direction: rtl;
        }

        .simple-thank-you {
            width: 100%;
            max-width: 500px;
            padding: 40px 30px;
            background: #fff;
            text-align: center;
        }

        .sty-icon {
            width: 90px;
            height: 90px;
            background: #28a745;
            color: #fff;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 3rem;
            margin: 0 auto 25px;
            box-shadow: 0 10px 20px rgba(40, 167, 69, 0.2);
            animation: popIn 0.5s cubic-bezier(0.175, 0.885, 0.32, 1.275) forwards;
        }

        @keyframes popIn {
            0% { transform: scale(0); opacity: 0; }
            100% { transform: scale(1); opacity: 1; }
        }

        .sty-title {
            font-size: 2.2rem;
            font-weight: 900;
            color: #111;
            margin: 0 0 10px;
            line-height: 1.3;
        }

        .sty-desc {
            font-size: 1.15rem;
            color: #555;
            margin: 0 0 35px;
            line-height: 1.6;
        }

        .sty-details {
            background: #fdfdfd;
            border: 2px solid #f0f0f0;
            border-radius: 16px;
            padding: 25px;
            margin-bottom: 35px;
            text-align: right;
        }

        .sty-row {
            display: flex;
            justify-content: space-between;
            padding: 12px 0;
            border-bottom: 1px dashed #e0e0e0;
            font-size: 1.05rem;
        }
        .sty-row:last-child {
            border-bottom: none;
            padding-bottom: 0;
        }
        .sty-row:first-child {
            padding-top: 0;
        }
        
        .sty-label {
            color: #666;
            font-weight: 600;
        }
        .sty-val {
            color: #111;
            font-weight: 800;
        }

        .sty-notice {
            background: #fff9e6;
            color: #856404;
            padding: 18px;
            border-radius: 12px;
            border: 1px solid #ffeeba;
            font-weight: 700;
            margin-bottom: 35px;
            display: flex;
            align-items: center;
            gap: 15px;
            text-align: right;
            line-height: 1.5;
        }
        
        .sty-notice i {
            font-size: 1.5rem;
            color: #d39e00;
        }

        .sty-btn {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
            background: #111;
            color: #fff;
            padding: 18px 40px;
            border-radius: 50px;
            text-decoration: none;
            font-weight: 800;
            font-size: 1.1rem;
            transition: 0.3s cubic-bezier(0.25, 0.8, 0.25, 1);
            width: 100%;
            box-sizing: border-box;
            border: none;
        }
        
        .sty-btn:hover {
            background: #c0392b;
            color: #fff;
            transform: translateY(-2px);
            box-shadow: 0 6px 15px rgba(192, 57, 43, 0.3);
        }

        .sty-btn-outline {
            background: transparent;
            color: #111;
            border: 2px solid #111;
        }
        .sty-btn-outline:hover {
            background: #111;
            color: #fff;
        }
        
        /* Hide unnecessary Astra injected elements if any */
        #wpadminbar { display: none !important; }
        html { margin-top: 0 !important; }
    </style>
</head>
<body>

<div class="simple-thank-you">

    <?php
    if ( $order ) :
        $do_not_active = $order->has_status( 'failed' );
        ?>

        <?php if ( $do_not_active ) : ?>

            <div class="sty-icon" style="background:#dc3545; box-shadow: 0 10px 20px rgba(220, 53, 69, 0.2);">
                <i class="fa fa-times"></i>
            </div>
            <h1 class="sty-title">عذراً، فشل الطلب</h1>
            <p class="sty-desc">واجهتنا مشكلة أثناء معالجة طلبك.</p>
            
            <a href="<?php echo esc_url( $order->get_checkout_payment_url() ); ?>" class="sty-btn">حاول مرة أخرى <i class="fa fa-refresh"></i></a>

        <?php else : ?>

            <div class="sty-icon"><i class="fa fa-check"></i></div>
            <h1 class="sty-title">شكراً لطلبك!</h1>
            <p class="sty-desc">تم استلام طلبك بنجاح. سنتواصل معك لتأكيد التوصيل.</p>

            <div class="sty-details">
                <div class="sty-row">
                    <span class="sty-label">رقم الطلب</span>
                    <span class="sty-val">#<?php echo $order->get_order_number(); ?></span>
                </div>
                <div class="sty-row">
                    <span class="sty-label">الإجمالي</span>
                    <span class="sty-val" style="color:#c0392b; font-size:1.15rem;"><?php echo wp_strip_all_tags($order->get_formatted_order_total()); ?></span>
                </div>
                <div class="sty-row">
                    <span class="sty-label">طريقة الدفع</span>
                    <span class="sty-val"><?php echo wp_kses_post( $order->get_payment_method_title() ); ?></span>
                </div>
            </div>

            <div class="sty-notice">
                <i class="fa fa-phone-square"></i> 
                <div>يرجى إبقاء هاتفك متاحاً للرد على موظف التأكيد لتسريع عملية الشحن.</div>
            </div>

            <a href="<?php echo home_url('/'); ?>" class="sty-btn">العودة للرئيسية <i class="fa fa-home"></i></a>

        <?php endif; ?>

    <?php else : ?>

        <div class="sty-icon"><i class="fa fa-check"></i></div>
        <h1 class="sty-title">تم استلام الطلب</h1>
        <p class="sty-desc">شكراً لتسوقك معنا.</p>
        <a href="<?php echo home_url('/'); ?>" class="sty-btn">العودة للتسوق <i class="fa fa-shopping-cart"></i></a>

    <?php endif; ?>

</div>

<?php 
// Load WP footer scripts but no theme footer
wp_footer(); 
?>
</body>
</html>
