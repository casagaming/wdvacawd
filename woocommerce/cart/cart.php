<?php
/**
 * Custom High-Conversion Cart Template - Premium Edition with AJAX Quantity Controls
 */

defined( 'ABSPATH' ) || exit;

get_header();
?>
<link href="https://fonts.googleapis.com/css2?family=Cairo:wght@400;700;900&family=Outfit:wght@400;700;900&display=swap" rel="stylesheet">

<div class="premium-cart-overlay">
    
    <style>
    :root {
        --gs-primary: #111111;
        --gs-primary-grad: linear-gradient(135deg, #111111 0%, #333333 100%);
        --gs-cta: #8b0000;
        --gs-cta-grad: linear-gradient(135deg, #8b0000 0%, #d91c1c 100%);
        --gs-bg: #f9f9f9;
        --gs-card: #ffffff;
        --gs-radius: 24px;
        --gs-shadow: 0 15px 45px rgba(0,0,0,0.05);
    }

    body.woocommerce-cart {
        background-color: var(--gs-bg) !important;
        font-family: 'Cairo', sans-serif !important;
    }

    /* Force Full Width Layout Override */
    .ast-container, #content .ast-container { max-width: 100% !important; padding: 0 !important; margin: 0 !important; }
    .site-content { padding-top: 0 !important; padding-bottom: 0 !important; }
    #primary { margin: 0 !important; padding: 0 !important; width: 100% !important; }

    .premium-cart-container {
        max-width: 1200px;
        margin: 40px auto;
        padding: 0 20px;
        direction: rtl;
        text-align: right;
        animation: fadeIn 0.6s ease-out;
    }

    @keyframes fadeIn {
        from { opacity: 0; transform: translateY(15px); }
        to { opacity: 1; transform: translateY(0); }
    }

    .cart-header { text-align: center; margin-bottom: 40px; }
    .cart-header h1 { font-family: 'Outfit', sans-serif; font-size: 2.5rem; font-weight: 950; color: #111; letter-spacing: -1px; }

    .cart-grid {
        display: grid;
        grid-template-columns: 2fr 1fr;
        gap: 30px;
        align-items: start;
    }

    @media (max-width: 1024px) {
        .cart-grid { grid-template-columns: 1fr; }
        .cart-header h1 { font-size: 2rem; }
    }

    .p-card {
        background: var(--gs-card);
        border-radius: var(--gs-radius);
        padding: 40px;
        box-shadow: var(--gs-shadow);
        border: 1px solid #f2f2f2;
    }

    .cart-row {
        display: flex;
        align-items: center;
        gap: 20px;
        padding: 25px 0;
        border-bottom: 1px solid #f5f5f5;
        position: relative;
    }
    .cart-row:last-child { border-bottom: none; }
    
    .cart-img-box {
        width: 110px;
        height: 110px;
        border-radius: 20px;
        overflow: hidden;
        border: 1px solid #eee;
        flex-shrink: 0;
    }
    .cart-img-box img { width: 100%; height: 100%; object-fit: cover; }
    
    .cart-info { flex: 1; }
    .cart-name { font-weight: 950; font-size: 1.2rem; color: #000; margin-bottom: 4px; }
    .cart-variation { font-size: 0.85rem; color: #888; margin-bottom: 15px; }
    
    /* Quantity Control */
    .qty-box {
        display: flex;
        align-items: center;
        gap: 0;
        background: #f7f7f7;
        padding: 4px;
        border-radius: 12px;
        width: fit-content;
    }
    .qty-btn {
        width: 34px;
        height: 34px;
        border: none;
        background: #fff;
        border-radius: 8px;
        cursor: pointer;
        font-weight: 950;
        font-size: 1.2rem;
        display: flex;
        align-items: center;
        justify-content: center;
        box-shadow: 0 2px 5px rgba(0,0,0,0.05);
        color: #333;
    }
    .qty-box input {
        width: 45px;
        text-align: center;
        border: none;
        background: transparent;
        font-weight: 950;
        font-size: 1.1rem;
        color: #000;
        -moz-appearance: textfield;
    }
    .qty-box input::-webkit-outer-spin-button,
    .qty-box input::-webkit-inner-spin-button { -webkit-appearance: none; margin: 0; }

    .cart-price-side { text-align: left; }
    .item-price { font-weight: 950; font-size: 1.3rem; color: var(--gs-cta); }
    .item-remove { 
        position: absolute; top: 25px; left: 0;
        color: #ff4d4d; font-size: 1.1rem; cursor: pointer; opacity: 0.6; transition: 0.3s;
    }
    .item-remove:hover { opacity: 1; }

    /* Summary Bar */
    .summary-title { font-size: 1.5rem; font-weight: 950; margin-bottom: 30px; border-right: 4px solid var(--gs-cta); padding-right: 15px; }
    
    .total-line { display: flex; justify-content: space-between; margin-bottom: 12px; font-weight: 700; color: #666; }
    .total-grand { border-top: 2px solid #f0f0f0; padding-top: 20px; margin-top: 15px; font-size: 1.8rem; color: #000; font-weight: 950; }

    .premium-checkout-btn {
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 12px;
        background: var(--gs-cta-grad);
        color: #fff !important;
        text-decoration: none !important;
        padding: 22px;
        border-radius: var(--gs-radius);
        font-weight: 950;
        font-size: 1.25rem;
        box-shadow: 0 12px 30px rgba(139,0,0,0.2);
        transition: 0.4s;
        margin-top: 25px;
        width: 100%;
        border: none;
        cursor: pointer;
    }
    .premium-checkout-btn:hover { transform: translateY(-4px); box-shadow: 0 15px 40px rgba(139,0,0,0.3); }

    .loading-overlay {
        position: fixed; inset: 0; background: rgba(255,255,255,0.7); 
        display: none; align-items: center; justify-content: center; z-index: 9999;
    }
    </style>

    <div class="loading-overlay"><i class="fa fa-spinner fa-spin fa-3x" style="color:var(--gs-cta);"></i></div>

    <div class="premium-cart-container">
        <header class="cart-header">
            <h1>عربة تسوق</h1>
            <p style="color:#888; font-weight:700;">Slatak (Shopping Cart)</p>
        </header>

        <?php if ( WC()->cart->is_empty() ) : ?>
            <div class="p-card" style="text-align:center; padding:80px 20px;">
                <i class="fa fa-shopping-basket" style="font-size:5rem; color:#eee; margin-bottom:30px; display:block;"></i>
                <h2 style="font-weight:950; margin-bottom:30px;">سلة المشتريات فارغة حالياً</h2>
                <a href="<?php echo home_url( '/' ); ?>" class="premium-checkout-btn" style="display:inline-flex; width:auto; padding:15px 50px;">العودة للرئيسية</a>
            </div>
        <?php else : ?>

        <div class="cart-grid">
            <div class="cart-main p-card">
                <div class="summary-title">محتويات السلة</div>
                
                <form class="woocommerce-cart-form" action="<?php echo esc_url( wc_get_cart_url() ); ?>" method="post">
                <div class="cart-list">
                    <?php
                    foreach ( WC()->cart->get_cart() as $cart_item_key => $cart_item ) {
                        $_product = apply_filters( 'woocommerce_cart_item_product', $cart_item['data'], $cart_item, $cart_item_key );
                        if ( $_product && $_product->exists() && $cart_item['quantity'] > 0 ) {
                            $product_permalink = apply_filters( 'woocommerce_cart_item_permalink', $_product->is_visible() ? $_product->get_permalink( $cart_item ) : '', $cart_item, $cart_item_key );
                            $img_id = $_product->get_image_id();
                            $img_url = $img_id ? wp_get_attachment_image_url($img_id, 'thumbnail') : wc_placeholder_img_src();
                            ?>
                            <div class="cart-row" data-cart-key="<?php echo $cart_item_key; ?>">
                                <a href="<?php echo esc_url( wc_get_cart_remove_url( $cart_item_key ) ); ?>" class="item-remove"><i class="fa fa-times"></i></a>
                                
                                <div class="cart-img-box"><img src="<?php echo $img_url; ?>" alt=""></div>
                                
                                <div class="cart-info">
                                    <h3 class="cart-name"><?php echo $_product->get_name(); ?></h3>
                                    <div class="cart-variation"><?php echo wc_get_formatted_cart_item_data( $cart_item ); ?></div>
                                    
                                    <div class="qty-box">
                                        <button type="button" class="qty-btn minus">-</button>
                                        <input type="number" name="cart[<?php echo $cart_item_key; ?>][qty]" value="<?php echo $cart_item['quantity']; ?>" min="1" readonly>
                                        <button type="button" class="qty-btn plus">+</button>
                                    </div>
                                </div>

                                <div class="cart-price-side">
                                    <div class="item-price"><?php echo WC()->cart->get_product_price( $_product ); ?></div>
                                </div>
                            </div>
                            <?php
                        }
                    }
                    ?>
                </div>
                <button type="submit" name="update_cart" style="display:none;" id="trigger-update">Update</button>
                <?php wp_nonce_field( 'woocommerce-cart', 'woocommerce-cart-nonce' ); ?>
                </form>

                <div style="margin-top:25px;">
                    <a href="<?php echo home_url( '/' ); ?>" style="color:#888; font-weight:800; text-decoration:none;"><i class="fa fa-reply"></i> مواصلة التسوق من الرئيسية</a>
                </div>
            </div>

            <div class="cart-sidebar" style="position:sticky; top:30px;">
                <div class="p-card">
                    <div class="summary-title">ملخص الحساب</div>
                    
                    <div class="total-line">
                        <span>المجموع الفرعي:</span>
                        <span><?php echo WC()->cart->get_cart_subtotal(); ?></span>
                    </div>
                    <div class="total-line">
                        <span>قيمة التوصيل:</span>
                        <span style="color:#28a745; font-weight:900;">يُحسب عند الدفع</span>
                    </div>
                    
                    <div class="total-line total-grand">
                        <span>الإجمالي:</span>
                        <span><?php wc_cart_totals_order_total_html(); ?></span>
                    </div>

                    <a href="<?php echo esc_url( wc_get_checkout_url() ); ?>" class="premium-checkout-btn">
                        <span>إتمام الطلب الآن</span>
                        <i class="fa fa-chevron-left"></i>
                    </a>

                    <div style="margin-top:20px; text-align:center; color:#999; font-size:0.8rem; font-weight:700;">
                         دفع عند الاستلام - توصيل لـ 58 ولاية <i class="fa fa-shield"></i>
                    </div>
                </div>
            </div>
        </div>

        <?php endif; ?>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const loader = document.querySelector('.loading-overlay');
    
    document.querySelectorAll('.qty-btn').forEach(btn => {
        btn.addEventListener('click', function() {
            const input = this.parentElement.querySelector('input');
            let val = parseInt(input.value);
            
            if (this.classList.contains('plus')) {
                val++;
            } else if (val > 1) {
                val--;
            }
            
            input.value = val;
            updateCart();
        });
    });

    function updateCart() {
        loader.style.display = 'flex';
        // Simplified approach: Trigger the hidden WooCommerce update button
        document.getElementById('trigger-update').click();
    }
});
</script>

<?php get_footer(); ?>
