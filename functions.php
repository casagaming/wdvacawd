<?php
/**
 * Astra Child Theme functions and definitions
 *
 * @link https://developer.wordpress.org/themes/basics/theme-functions/
 *
 * @package Astra Child
 * @since 1.0.0
 */

/**
 * Enqueue parent styles
 */
function astra_child_enqueue_styles() {
	wp_enqueue_style( 'astra-child-theme-css', get_stylesheet_directory_uri() . '/style.css', array('astra-theme-css'), ASTRA_THEME_VERSION, 'all' );
}
add_action( 'wp_enqueue_scripts', 'astra_child_enqueue_styles', 15 );

// --- SAFETY CHECK: Disable advanced logic if WooCommerce is missing ---
if ( ! class_exists( 'WooCommerce' ) ) {
    add_action( 'admin_notices', function() {
        echo '<div class="error"><p><strong>تنبيه من Gentle Shoes:</strong> هذا القالب يتطلب تفعيل إضافة <strong>WooCommerce</strong> للعمل بشكل صحيح. يرجى تفعيلها لتجنب أي أخطاء.</p></div>';
    });
    return; // Stop execution if WC is missing
}

/**
 * Force full-width layout for specific pages
 */
function astra_child_page_layout( $layout ) {
    if ( is_page_template( 'page-faq.php' ) ) {
        return 'no-sidebar';
    }
    return $layout;
}
add_filter( 'astra_page_layout', 'astra_child_page_layout' );

// --- Custom Modifications for Gentle Shoes Theme Redesign ---

// 1. Enqueue Google Fonts & FontAwesome
function astra_custom_enqueue_assets() {
    wp_enqueue_style( 'cairo-font', 'https://fonts.googleapis.com/css2?family=Cairo:wght@400;600;700&display=swap', array(), null );
    wp_enqueue_style( 'font-awesome', 'https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css' );
    
    // Swiper Slider Assets
    wp_enqueue_style( 'swiper-css', 'https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.css' );
    wp_enqueue_script( 'swiper-js', 'https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.js', array(), null, true );
}
add_action( 'wp_enqueue_scripts', 'astra_custom_enqueue_assets' );

// 2. Customize WooCommerce Checkout Fields
function custom_override_checkout_fields( $fields ) {
    // Unset unneeded fields
    unset($fields['billing']['billing_company']);
    unset($fields['billing']['billing_country']);
    unset($fields['billing']['billing_address_1']);
    unset($fields['billing']['billing_address_2']);
    unset($fields['billing']['billing_postcode']);
    unset($fields['billing']['billing_email']);
    
    // Modify existing fields to match design
    $fields['billing']['billing_first_name']['label'] = false;
    $fields['billing']['billing_first_name']['placeholder'] = 'الاسم الكامل';
    $fields['billing']['billing_first_name']['class'] = array('form-row-wide', 'custom-icon-user');
    $fields['billing']['billing_first_name']['priority'] = 10;
    
    // Last name is hidden or removed, using first name as full name
    unset($fields['billing']['billing_last_name']); 
    
    $fields['billing']['billing_phone']['label'] = false;
    $fields['billing']['billing_phone']['placeholder'] = 'رقم الهاتف';
    $fields['billing']['billing_phone']['class'] = array('form-row-wide', 'custom-icon-phone');
    $fields['billing']['billing_phone']['priority'] = 20;

    // Add optional second phone
    $fields['billing']['billing_phone_2'] = array(
        'type'        => 'tel',
        'label'       => false,
        'placeholder' => 'إذا عندك رقم هاتف ثاني اكتبه هنا',
        'required'    => false,
        'class'       => array('form-row-wide', 'custom-icon-phone-plus'),
        'clear'       => true,
        'priority'    => 30,
    );

    $fields['billing']['billing_state']['label'] = false;
    $fields['billing']['billing_state']['placeholder'] = 'الولاية';
    $fields['billing']['billing_state']['class'] = array('form-row-wide', 'custom-icon-location');
    $fields['billing']['billing_state']['priority'] = 40;

    $fields['billing']['billing_city']['label'] = false;
    $fields['billing']['billing_city']['placeholder'] = 'البلدية';
    $fields['billing']['billing_city']['class'] = array('form-row-wide', 'custom-icon-location');
    $fields['billing']['billing_city']['priority'] = 50;

    return $fields;
}
add_filter( 'woocommerce_checkout_fields', 'custom_override_checkout_fields' );

// Save Custom Phone 2 Field
function save_custom_checkout_fields( $order_id ) {
    if ( ! empty( $_POST['billing_phone_2'] ) ) {
        update_post_meta( $order_id, '_billing_phone_2', sanitize_text_field( $_POST['billing_phone_2'] ) );
    }
}
add_action( 'woocommerce_checkout_update_order_meta', 'save_custom_checkout_fields' );

// Add title above form
function custom_checkout_form_title() {
    echo '<div id="order-form-anchor"></div><h3 class="custom-checkout-title">لطلب، يرجى إدخال التفاصيل :</h3>';
}
add_action('woocommerce_before_checkout_billing_form', 'custom_checkout_form_title');


// 4. Old product page hooks removed — now handled by woocommerce/content-single-product.php template

// Render Buy Now + Add to Cart buttons (kept as callable function)
function render_product_action_buttons() { /* now rendered by template */ }


function render_bundle_and_save_section() {
    global $product;
    if ( ! $product->is_in_stock() ) return;
    ?>
    <style>
        /* Reverse Layout: Image on Right, Details on Left */
        @media(min-width: 769px) {
            .single-product div.product { display: flex; flex-direction: row-reverse; flex-wrap: wrap; justify-content: space-between; gap: 0; }
            .single-product div.product .woocommerce-product-gallery { width: 48% !important; margin-left: 0 !important; margin-right: 0 !important; visibility: visible !important; opacity: 1 !important; position: relative !important; }
            .single-product div.product .summary { width: 48% !important; opacity: 1 !important; visibility: visible !important; }
            .single-product div.product .woocommerce-tabs, .single-product div.product .related, .single-product div.product .up-sells { width: 100% !important; order: 10; margin-top: 50px; }
        }
        /* Hide Standard CTA Buttons */
        .summary .single_add_to_cart_button { display: none !important; }
        .summary .quantity { display: none !important; }
        
        .woocommerce-product-gallery { opacity: 1 !important; visibility: visible !important; }
        .woocommerce-product-gallery .flex-viewport, .woocommerce-product-gallery .woocommerce-product-gallery__wrapper { width: 100% !important; }
        .woocommerce-product-gallery img { opacity: 1 !important; visibility: visible !important; }
        
        /* Hide ALL prices except the one at the very top of summary */
        .summary p.price { display: none !important; } 
        .summary .product_title + p.price, .summary .product_title + .price { display: block !important; margin-bottom: 15px; }
        
        /* Layout Hierarchy Adjustment */
        .summary .woocommerce-product-details__short-description { order: 2; margin-bottom: 25px; }
        .summary .variations_form { order: 3; }
        .summary .bundle-save-section { order: 4; }

        /* Prevent Duplicates in Summary (Common in Astra) */
        .summary .variations_form:nth-of-type(n+2) { display: none !important; }
        .summary .bundle-save-section:nth-of-type(n+2) { display: none !important; }
        .summary .custom-buttons-wrapper:nth-of-type(n+2) { display: none !important; }

        /* Organize Add to Cart Elements */
        .woocommerce-variation-add-to-cart { display: flex; flex-direction: column; width: 100%; margin-top: 15px; }
        .woocommerce-variation-add-to-cart .quantity { display: none !important; } /* Hide standard quantity */
        .single_add_to_cart_button { display: none !important; } /* Hide standard button, we use cloned buttons */
        
        .custom-action-btn { width: 100%; border-radius: 30px; padding: 18px; font-size: 1.3rem; margin-bottom: 10px; font-weight: 800; cursor: pointer; transition: 0.3s; text-align: center; border:none; display: block; }
        .btn-buy-now { background: var(--ast-global-color-0); color: #fff; box-shadow: 0 5px 15px rgba(139,0,0,0.2); }
        .btn-buy-now:hover { background: var(--ast-global-color-1); transform: translateY(-2px); color: #fff; }
        .btn-add-cart { background: #555; color: #fff; font-size: 1.1rem; padding: 15px; }
        .btn-add-cart:hover { background: #333; color: #fff; }

        /* Embedded COD Form Styles (Hidden initially) */
        .embedded-cod-form { display: none; background: #fdfdfd; border: 1px solid #eaeaea; border-radius: 15px; padding: 25px; margin-bottom: 25px; margin-top: 10px; box-shadow: 0 4px 15px rgba(0,0,0,0.03); }
        .embedded-cod-title { font-size: 1.2rem; font-weight: bold; margin-bottom: 20px; color: #333; text-align: right; }
        .embedded-input-group { margin-bottom: 15px; position: relative; }
        .embedded-input-group input { width: 100%; background: #f9f9f9; border: 1px solid #ddd; border-radius: 8px; padding: 12px 45px 12px 15px !important; font-family: 'Cairo', sans-serif; font-size: 1rem; box-sizing: border-box; text-align: right; }
        .embedded-input-group i { position: absolute; right: 15px; top: 50%; transform: translateY(-50%); color: #888; font-size:1.1rem;}
        
        /* Shipping Options Styles - Professional Cards */
        .shipping-options { display: flex; gap: 12px; margin-bottom: 25px; direction: rtl; }
        .shipping-method { 
            flex: 1; 
            border: 2px solid #eee; 
            padding: 15px 10px; 
            border-radius: 12px; 
            text-align: center; 
            cursor: pointer; 
            transition: 0.3s; 
            background: #fff; 
            display: flex; 
            flex-direction: column; 
            align-items: center; 
            justify-content: center;
            gap: 8px;
            box-shadow: 0 4px 6px rgba(0,0,0,0.02);
        }
        .shipping-method:hover { border-color: #ccc; background: #fdfdfd; }
        .shipping-method.active { 
            border-color: #8b0000; 
            background: #fffafa; 
            color: #8b0000; 
            box-shadow: 0 8px 15px rgba(139,0,0,0.1);
        }
        .shipping-method i { 
            font-size: 1.6rem; 
            color: #666; 
            transition: 0.3s;
        }
        .shipping-method.active i { color: #8b0000; }
        .shipping-method span { font-size: 0.95rem; font-weight: 700; line-height: 1.2; }

        .address-field-wrap { display: none; margin-bottom: 15px; }

        /* Bundle & Save Styles - Refined */
        .bundle-save-section { margin-top: 30px; direction: rtl; text-align: right; font-family: 'Cairo', sans-serif; }
        .bundle-save-title { font-size: 1.4rem; font-weight: 800; margin-bottom: 20px; color: #333; display: flex; align-items: center; gap: 10px; border-bottom: 1px solid #eee; padding-bottom: 10px; }
        .bundle-save-title i { color: #8b0000; font-size: 1.2rem; }
        
        .bundle-option { position: relative; border: 2px solid #ddd; border-radius: 12px; padding: 20px; margin-bottom: 20px; cursor: pointer; transition: 0.3s; background: #fff; box-shadow: 0 4px 6px rgba(0,0,0,0.02); }
        .bundle-option:hover { border-color: #aaa; }
        .bundle-option.active { border-color: #8b0000; background: #fffafb; box-shadow: 0 8px 20px rgba(139,0,0,0.08); }
        
        .bundle-header { display: flex; justify-content: space-between; align-items: center; }
        .bundle-info { flex: 1; display: flex; align-items: center; }
        .bundle-label { position: absolute; top: -14px; left: 20px; background: #8b0000; color: #fff; padding: 4px 15px; border-radius: 4px; font-size: 0.85rem; font-weight: bold; box-shadow: 0 4px 10px rgba(139,0,0,0.2); }
        .faq-header h1 { font-size: 3.5rem; font-weight: 900; color: #111; margin: 0; background: linear-gradient(45deg, #000, #8b0000); -webkit-background-clip: text; -webkit-text-fill-color: transparent; }
        .faq-header p { color: #666; font-size: 1.3rem; margin-top: 15px; font-weight: 500; max-width: 600px; margin-left: auto; margin-right: auto; }
        .bundle-badge { display: inline-block; padding: 3px 10px; border-radius: 5px; margin-top: 5px; }
        
        .bundle-pricing { text-align: left; position: relative; }
        .bundle-discount-badge { position: absolute; top: -35px; left: 0; background: #fff; border: 1px solid #8b0000; color: #8b0000; padding: 2px 8px; border-radius: 4px; font-weight: bold; font-size: 0.9rem; }
        .bundle-price { font-size: 1.4rem; font-weight: 800; color: #8b0000; display: block; }
        .bundle-old-price { font-size: 1.1rem; color: #999; text-decoration: line-through; display: block; }
        
        .bundle-radio { width: 24px; height: 24px; border: 2px solid #ddd; border-radius: 50%; display: flex; align-items: center; justify-content: center; margin-left: 15px; position: relative; background: #fff; flex-shrink: 0; }
        .bundle-option.active .bundle-radio { border-color: #8b0000; }
        .bundle-option.active .bundle-radio::after { content: ''; width: 14px; height: 14px; background: #8b0000; border-radius: 50%; }

        .bundle-variations { margin-top: 15px; padding-top: 15px; border-top: 1px dotted #ccc; display: none; }
        .bundle-option.active .bundle-variations { display: block; }
        
        .var-row { margin-bottom: 20px; display: flex; align-items: center; justify-content: flex-start; gap: 15px; flex-wrap: wrap; }
        .var-label { font-weight: bold; font-size: 1rem; color: #222; min-width: 40px; }
        .var-selectors { flex: 1; display: flex; gap: 15px; align-items: center; justify-content: space-between; }
        .var-select { padding: 10px; border-radius: 8px; border: 1px solid #ccc; font-family: 'Cairo', sans-serif; font-size: 1rem; width: 120px; background: #fff; }
        
        .color-swatches, .size-tiles { display: flex; gap: 10px; flex-wrap: wrap; margin-top: 10px; }
        .color-swatch, .size-tile { 
            min-width: 80px; height: 48px; border: 2px solid #eee; border-radius: 12px !important; 
            display: flex; align-items: center; justify-content: center; padding: 0 20px;
            cursor: pointer; transition: 0.3s; font-weight: 700; background: #fff;
            font-size: 1rem; color: #444; box-shadow: 0 2px 5px rgba(0,0,0,0.02);
            text-align: center;
        }
        .color-swatch:hover, .size-tile:hover { border-color: var(--ast-global-color-0); color: var(--ast-global-color-0); background: #fdfafb; }
        .color-swatch.active, .size-tile.active { border-color: var(--ast-global-color-0); background: var(--ast-global-color-0); color: #fff !important; box-shadow: 0 8px 20px rgba(139,0,0,0.15); }


        .qty-wrap { display: flex; align-items: center; gap: 15px; margin-top: 25px; border-top: 1px solid #eee; padding-top: 20px; }
        .qty-label { font-weight: 800; font-size: 1.1rem; color: #111; }
        .qty-controls { display: flex; align-items: center; background: #f8f8f8; border-radius: 40px; padding: 6px; border: 1px solid #eee; }
        .qty-btn { width: 38px; height: 38px; border-radius: 50%; display: flex; align-items: center; justify-content: center; cursor: pointer; background: #fff; transition: 0.3s; border: none; font-size: 1.1rem; box-shadow: 0 2px 5px rgba(0,0,0,0.05); }
        .qty-btn:hover { background: #8b0000; color: #fff; }
        .qty-val { width: 45px; text-align: center; font-weight: 900; font-size: 1.3rem; border: none; background: transparent; -moz-appearance: textfield; pointer-events: none; }

        /* Hide Astra Sticky Bar */
        .ast-sticky-add-to-cart, .ast-sticky-add-to-cart-wrapper, #ast-sticky-add-to-cart { display: none !important; }
        
        /* Hide default price if it appears twice or in wrong place */
        .summary .price { font-size: 1.8rem; color: var(--brand-red); font-weight: 800; margin-bottom: 20px; display: block; }
        .summary .woocommerce-product-details__short-description { margin-bottom: 25px; line-height: 1.6; font-size: 1.1rem; }

        .embedded-cod-form { display: none; background: #fff; padding: 25px; border-radius: 20px; border: 2px solid var(--ast-global-color-0); margin-top: 20px; box-shadow: 0 10px 30px rgba(0,0,0,0.1); }
        .embedded-cod-form.show-it { display: block !important; }
        .embedded-cod-title { font-weight: 800; font-size: 1.2rem; margin-bottom: 20px; color: #111; border-right: 4px solid var(--ast-global-color-0); padding-right: 15px; }
        .embedded-input-group { position: relative; margin-bottom: 15px; }
        .embedded-input-group i { position: absolute; right: 15px; top: 50%; transform: translateY(-50%); color: #888; }
        .embedded-input-group select { 
            width: 100%; border: 1px solid #ddd; border-radius: 12px; padding: 12px 40px 12px 15px; 
            font-family: inherit; appearance: none; -webkit-appearance: none; background-color: #fdfdfd;
            background-image: url("data:image/svg+xml;charset=UTF-8,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 24 24' fill='none' stroke='%23888' stroke-width='2' stroke-linecap='round' stroke-linejoin='round'%3E%3Cpolyline points='6 9 12 15 18 9'%3E%3C/polyline%3E%3C/svg%3E");
            background-repeat: no-repeat; background-position: left 15px center; background-size: 1.2em;
        }
        .confirm-order-btn { width: 100%; background: var(--ast-global-color-0); color: #fff; border: none; padding: 18px; border-radius: 40px; font-size: 1.3rem; font-weight: 800; cursor: pointer; margin-top: 20px; box-shadow: 0 10px 20px rgba(139,0,0,0.2); }

        /* Disabled Button State */
        .btn-disabled { 
            opacity: 0.4 !important; 
            pointer-events: none !important; 
            filter: grayscale(1) !important;
            cursor: not-allowed !important;
            box-shadow: none !important;
        }
        
        /* Highlight Missing Selections (Optional) */
        .selection-missing { border: 2px solid #ff4d4d !important; animation: shake 0.5s; }
        @keyframes shake {
            0%, 100% { transform: translateX(0); }
            25% { transform: translateX(-5px); }
            75% { transform: translateX(5px); }
        }
    </style>

    <?php 
        $offers = get_post_meta($product->get_id(), '_bundle_offers', true);
        $has_prices = false;
        if ($offers && is_array($offers)) {
            foreach ($offers as $o) { if (!empty($o['price'])) { $has_prices = true; break; } }
        }
    ?>

    <?php if ($has_prices) : ?>
    <div class="bundle-save-section">
        <div class="bundle-save-title">
            <i class="fa fa-gift"></i>
            حزم التوفير المتاحة
        </div>
        
        <?php 
        $attributes = $product->get_attributes();
        $var_options = array();
        foreach ($attributes as $attr_name => $attr) {
            $name = wc_attribute_label($attr_name);
            $options = $attr->get_options();
            if ($attr->is_taxonomy()) {
                $terms = get_terms($attr_name, array('include' => $options));
                $var_options[$name] = array();
                foreach ($terms as $term) {
                    $var_options[$name][] = $term->name;
                }
            } else {
                $var_options[$name] = $options;
            }
        }
        ?>

        <div class="qty-wrap" style="margin-bottom:20px; border-bottom:1px solid #eee; padding-bottom:15px;">
            <div class="qty-label">الكمية المطلوبة:</div>
            <div class="qty-controls">
                <div class="qty-btn minus"><i class="fa fa-minus"></i></div>
                <input type="text" class="qty-val" name="exp_qty" value="1" readonly>
                <div class="qty-btn plus"><i class="fa fa-plus"></i></div>
            </div>
        </div>

        <!-- No Bundle Option -->
        <?php 
        $hide_main = get_post_meta($product->get_id(), '_hide_main_product_offer', true);
        if ($hide_main !== 'yes') :
        ?>
        <div class="bundle-option active no-bundle-choice" data-items="0" data-price="0" data-name="none">
            <div class="bundle-header">
                <div class="bundle-info">
                    <div class="bundle-radio"></div>
                    <div>
                        <div class="bundle-name">شراء المنتج الأساسي فقط</div>
                        <div class="bundle-badge">بدون عروض إضافية</div>
                    </div>
                </div>
                <div class="bundle-pricing">
                    <span class="bundle-price">0 د.ج</span>
                </div>
            </div>
        </div>
        <?php endif; ?>

        <?php
        $first_active = true;
        foreach ($offers as $index => $offer) : 
            if (empty($offer['price']) || (isset($offer['hide']) && $offer['hide'] == 'yes')) continue;
            
            $items_count = (!empty($offer['items'])) ? intval($offer['items']) : 0;
            
            // Explicit user logic: Offer 1 (Index 0) = 2 pieces, Others = 3 pieces
            if ($items_count <= 0) {
                if ($index == 0) $items_count = 2; // الأول = قطعتين
                else $items_count = 3; // الباقي = 3 قطع
            }
            if ($items_count < 1) $items_count = 2;
            if ($items_count < 1) $items_count = 2; // Safety fallback
            // Ensure no negative or zero items for bundles
            if ($items_count < 1) $items_count = 2;
            
            $is_active = ($hide_main == 'yes' && $first_active) ? 'active' : '';
            if ($is_active) $first_active = false;
            ?>
            <div class="bundle-option <?php echo $is_active; ?>" data-items="<?php echo $items_count; ?>" data-price="<?php echo esc_attr($offer['price']); ?>" data-name="<?php echo esc_attr($offer['name']); ?>">
                <div class="bundle-label"><?php echo esc_html($offer['label']); ?></div>
                <div class="bundle-header">
                    <div class="bundle-info">
                        <div class="bundle-radio"></div>
                        <div>
                            <div class="bundle-name"><?php echo esc_html($offer['name']); ?></div>
                            <div class="bundle-badge"><?php echo esc_html($offer['badge']); ?></div>
                        </div>
                    </div>
                    <div class="bundle-pricing">
                        <?php if (!empty($offer['discount'])) : ?>
                            <div class="bundle-discount-badge"><?php echo esc_html($offer['discount']); ?>-</div>
                        <?php endif; ?>
                        <span class="bundle-price"><?php echo esc_html($offer['price']); ?> د.ج</span>
                        <span class="bundle-old-price"><?php echo esc_html($offer['old_price']); ?> د.ج</span>
                    </div>
                </div>

                <div class="bundle-variations">
                    <?php for ($i = 1; $i <= $items_count; $i++) : ?>
                        <div class="var-row">
                            <div class="var-label">القطعة <?php echo $i; ?>:</div>
                            <div class="var-selectors">
                                <?php foreach ($var_options as $label => $options) : 
                                    $is_color = (stripos($label, 'اللون') !== false || stripos($label, 'لون') !== false || stripos($label, 'color') !== false);
                                    ?>
                                    <div class="size-tiles color-as-text" data-attr="<?php echo $label; ?>">
                                        <?php if (empty($options)) : ?>
                                            <span style="color:red; font-size:0.8rem;">يرجى إضافة خيارات لهذا التباين في صفحة المنتج</span>
                                        <?php else : ?>
                                            <?php foreach ($options as $opt) : ?>
                                                <div class="size-tile" data-value="<?php echo $opt; ?>"><?php echo esc_html($opt); ?></div>
                                            <?php endforeach; ?>
                                        <?php endif; ?>
                                        <input type="hidden" class="var-select" data-attr="<?php echo $label; ?>" name="bundle_var[<?php echo $index; ?>][<?php echo $i; ?>][<?php echo $label; ?>]" value="">
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    <?php endfor; ?>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
    <?php endif; ?>

    <!-- The Hidden COD Form -->
    <div class="embedded-cod-form" id="sliding-cod-form">
        <div class="embedded-cod-title">لإتمام الطلب، يرجى إدخال تفاصيل التوصيل:</div>
        <input type="hidden" name="express_checkout_direct_order" id="express_checkout_direct_order" value="1">
        
        <div class="embedded-input-group">
            <i class="fa fa-user"></i>
            <input type="text" name="exp_name" id="f_name" placeholder="الاسم الكامل" required>
        </div>
        <div class="embedded-input-group">
            <i class="fa fa-phone"></i>
            <input type="tel" name="exp_phone" id="f_phone" placeholder="رقم الهاتف الأساسي" required>
        </div>
        <div class="embedded-input-group">
            <i class="fa fa-plus-circle"></i>
            <input type="tel" name="exp_phone2" placeholder="هاتف إضافي (اختياري)">
        </div>
        <div class="embedded-input-group">
            <i class="fa fa-map-marker"></i>
            <select name="exp_state" id="f_state" required style="width:100%; border:1px solid #ddd; border-radius:12px; padding:12px 40px 12px 15px; background:#fdfdfd;">
                <option value="">اختر الولاية</option>
                <?php foreach (get_algeria_wilayas() as $code => $w) : ?>
                    <option value="<?php echo esc_attr($w['name']); ?>" data-office="<?php echo $w['office']; ?>" data-home="<?php echo $w['home']; ?>">
                        <?php echo $code . ' - ' . esc_html($w['name']); ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="embedded-input-group">
            <i class="fa fa-location-arrow"></i>
            <input type="text" name="exp_city" id="f_city" placeholder="البلدية" required>
        </div>

        <div class="embedded-cod-title">اختيار طريقة التوصيل:</div>
        <div class="shipping-options">
            <div class="shipping-method active" data-method="post">
                <i class="fa fa-envelope-o"></i>
                <span>التوصيل للمكتب<br>(Yalidine)</span>
            </div>
            <div class="shipping-method" data-method="home">
                <i class="fa fa-home"></i>
                <span>التوصيل لباب<br>المنزل</span>
            </div>
            <input type="hidden" name="exp_shipping" id="f_shipping" value="post">
        </div>

        <div class="address-field-wrap" id="home-address-wrap">
            <div class="embedded-input-group">
                <i class="fa fa-map-marker"></i>
                <input type="text" name="exp_address" id="f_address" placeholder="عنوان المنزل بضبط (اختياري)">
            </div>
        </div>

        <!-- Price Summary Section -->
        <div class="price-summary-box" style="background:#f9f9f9; padding:15px; border-radius:12px; margin-top:20px; border:1px dashed #ddd;">
            <div style="display:flex; justify-content:space-between; margin-bottom:10px; font-weight:600;">
                <span>سعر المنتج:</span>
                <span id="summary-prod-price">0 د.ج</span>
            </div>
            <div style="display:flex; justify-content:space-between; margin-bottom:10px; font-weight:600;">
                <span>سعر التوصيل:</span>
                <span id="summary-ship-price">0 د.ج</span>
            </div>
            <div style="display:flex; justify-content:space-between; margin-top:10px; padding-top:10px; border-top:2px solid #eee; font-weight:900; font-size:1.4rem; color:#8b0000;">
                <span>المجموع الكلي:</span>
                <span id="summary-total-price">0 د.ج</span>
            </div>
        </div>
        
        <button type="button" class="confirm-order-btn" id="final-confirm-order">تأكيد الطلب 🚚</button>
    </div>

    <script>
    jQuery(document).ready(function($) {
        var cartForm = $('form.cart');
        
        // Stop hiding variations - let them show at the top for single product choice
        // $('.variations, .single_variation_wrap').hide();
        
        // But style them to match our premium look
        $('.variations_form select').each(function() {
            var select = $(this);
            // Don't skip if hidden, WooCommerce often hides them initially!
            var label = select.closest('tr').find('.label label').text() || select.closest('.variations').find('.label label').text() || 'Option';
            if (!label || label.trim() == '') label = select.attr('data-attribute_name') || 'Option';
            
            var is_color = (label.indexOf('اللون') !== -1 || label.indexOf('لون') !== -1 || label.toLowerCase().indexOf('color') !== -1);
            
            var container = $('<div class="' + (is_color ? 'color-swatches' : 'size-tiles') + ' main-vars-wrap" data-attr="' + label + '"></div>');
            select.find('option').each(function() {
                var val = $(this).val();
                if (!val) return;
                var tileClass = is_color ? 'color-swatch' : 'size-tile';
                container.append('<div class="' + tileClass + '" data-value="' + val + '">' + val + '</div>');
            });
            // Append and hide select
            select.after(container).hide();
        });

        $(document).on('click', '.main-vars-wrap .color-swatch, .main-vars-wrap .size-tile', function() {
            var val = $(this).data('value');
            var container = $(this).parent();
            container.find('.color-swatch, .size-tile').removeClass('active');
            $(this).addClass('active');
            
            // Find the select that is literally before the container
            var select = container.prev('select');
            select.val(val).trigger('change');
            
            // Trigger ALL variation events for maximum compatibility
            var form = $(this).closest('form');
            form.trigger('check_variations');
            form.trigger('woocommerce_variation_has_changed');
            
            // Recalculate total
            calculateFinalTotal();
            
            // Small delay to allow WooCommerce to resolve variation_id
            setTimeout(function() {
                var var_id = form.find('input[name="variation_id"]').val();
                console.log('Resolved Variation ID: ' + var_id);
            }, 200);
        });

        $('.bundle-option').on('click', function() {
            $('.bundle-option').removeClass('active');
            $(this).addClass('active');
            $('#express_checkout_direct_order').val('1');
            
            // Smart UI: If bundle is active, HIDE main variations
            if ($(this).data('name') !== 'none') {
                $('.variations_form .variations, .main-vars-wrap').slideUp();
                // Also hide main product price to avoid confusion
                $('.summary .price:first').slideUp();
                $('.qty-wrap').hide();
                $('.qty-val').val(1);
            } else {
                $('.variations_form .variations, .main-vars-wrap').slideDown();
                $('.summary .price:first').slideDown();
                $('.qty-wrap').show();
            }
            
            updateShippingVisibility();
            validateSelection();
        });

    function validateSelection() {
        var activeBundle = $('.bundle-option.active');
        var isBundle = (activeBundle.length > 0 && activeBundle.data('name') !== 'none');
        var isValid = true;
        
        if (!isBundle) {
            // Check Main Variations Only
            $('.main-vars-wrap').each(function() {
                if ($(this).find('.size-tile.active, .color-swatch.active').length === 0) {
                    isValid = false;
                }
            });
            // Also check if WooCommerce resolved a variation ID
            var variation_id = $('input[name="variation_id"]').val();
            if (!variation_id || variation_id == '0') isValid = false;
        } else {
            // Check ALL internal bundle variations
            activeBundle.find('.var-row').each(function() {
                $(this).find('.var-selectors .size-tiles').each(function() {
                    if ($(this).find('.size-tile.active').length === 0) {
                        isValid = false;
                    }
                });
            });
        }
        
        // Update Buttons
        var btns = $('#trigger-buy-now, #trigger-add-cart');
        if (isValid) {
            btns.removeClass('btn-disabled');
        } else {
            btns.addClass('btn-disabled');
        }
    }

    function updateShippingVisibility() {
        var activeBundle = $('.bundle-option.active');
        var bundleName = activeBundle.data('name');
        var badge = activeBundle.find('.bundle-badge').text() || '';
        
        if (bundleName !== 'none') {
            if (badge.indexOf('مجاني للبيت') !== -1) {
                $('.shipping-method[data-method="post"]').hide();
                $('.shipping-method[data-method="home"]').show().addClass('active').siblings().removeClass('active');
                $('#f_shipping').val('home');
                $('#home-address-wrap').show();
            } else {
                $('.shipping-method').show();
            }
        } else {
            $('.shipping-method').show();
        }
        calculateFinalTotal();
    }

    $('.shipping-method').on('click', function() {
        var activeBundle = $('.bundle-option.active');
        var badge = activeBundle.find('.bundle-discount-badge').text() || '';
        
        // If "Free to Home" is forced, don't allow switching
        if (badge.indexOf('مجاني للبيت') !== -1 && $(this).data('method') === 'post') {
            return;
        }

        $('.shipping-method').removeClass('active');
        $(this).addClass('active');
        var method = $(this).data('method');
        $('#f_shipping').val(method);
        if(method === 'home') {
            $('#home-address-wrap').slideDown();
        } else {
            $('#home-address-wrap').slideUp();
        }
    });

        $(document).on('click', '.color-swatch', function(e) {
            e.stopPropagation();
            var parent = $(this).closest('.color-swatches');
            parent.find('.color-swatch').removeClass('active');
            $(this).addClass('active');
            parent.find('input.var-select').val($(this).data('value')).trigger('change');
        });

        $(document).on('click', '.size-tile', function(e) {
            e.stopPropagation();
            var parent = $(this).closest('.size-tiles');
            parent.find('.size-tile').removeClass('active');
            $(this).addClass('active');
            parent.find('input.var-select').val($(this).data('value')).trigger('change');
            validateSelection();
        });

        function calculateFinalTotal() {
            var activeBundle = $('.bundle-option.active');
            var bundlePrice = parseFloat(activeBundle.data('price')) || 0;
            var bundleName = activeBundle.data('name');
            var qty = parseInt($('.qty-val').val()) || 1;
            
            var subtotal = 0;

            if (bundleName === 'none') {
                // Main Product Only
                var mainPrice = parseFloat("<?php echo $product->get_price(); ?>") || 0;
                subtotal = mainPrice * qty;
                $('.qty-wrap').show();
            } else {
                // Bundle Selected
                subtotal = bundlePrice * qty;
            }
            
            var method = $('#f_shipping').val();
            var stateOpt = $('#f_state option:selected');
            var shipPrice = 0;
            
            // Always Free for any bundle
            var is_free = (bundleName !== 'none');
            
            if (!is_free && stateOpt.val() !== '') {
                shipPrice = parseFloat(stateOpt.data(method)) || 0;
            }
            
            var total = subtotal + shipPrice;
            
            $('#summary-prod-price').text(subtotal + ' د.ج');
            $('#summary-ship-price').text(shipPrice === 0 ? 'مجاني 🚚' : shipPrice + ' د.ج');
            $('#summary-total-price').text(total + ' د.ج');
        }

        $('#f_state, #f_shipping').on('change', calculateFinalTotal);
        $('.bundle-option').on('click', function() {
            // Already called updateShippingVisibility, now calc total
            setTimeout(calculateFinalTotal, 100);
        });

        // Qty Controls
        $('.qty-btn.plus').on('click', function() {
            var input = $(this).siblings('.qty-val');
            input.val(parseInt(input.val()) + 1);
            calculateFinalTotal();
        });
        $('.qty-btn.minus').on('click', function() {
            var input = $(this).siblings('.qty-val');
            var val = parseInt(input.val());
            if (val > 1) input.val(val - 1);
            calculateFinalTotal();
        });

        if($('.bundle-save-section').closest('form.cart').length === 0) {
            $('.embedded-cod-form').appendTo(cartForm.first());
        }

        // Initial Calculation
        calculateFinalTotal();
        updateShippingVisibility();
        validateSelection();

        $('#trigger-buy-now').on('click', function(e) {
            e.preventDefault();
            console.log('Buy Now Clicked - Action Started');
            
            var isBundle = $('#express_checkout_direct_order').val() === '1';
            
            if (!isBundle) {
                // Check if ALL main variations are selected
                var allSelected = true;
                $('.main-vars-wrap').each(function() {
                    // Correctly check if any variation tile is active in THIS wrap
                    if ($(this).find('.size-tile.active').length === 0) {
                        allSelected = false;
                    }
                });
                
                var variation_id = $('input[name="variation_id"]').val();
                var variationsResolved = variation_id !== '0' && variation_id !== '';
                
                if (!allSelected || !variationsResolved) {
                    alert('⚠️ يرجى اختيار المقاس واللون أولاً!');
                    // Pulse the variations section to highlight it
                    $('.main-vars-wrap').css('border', '2px solid red').delay(500).queue(function(next){
                        $(this).css('border', 'none');
                        next();
                    });
                    return false;
                }
            } else {
                var activeBundle = $('.bundle-option.active');
                if (activeBundle.length === 0) {
                     alert('يرجى اختيار أحد العروض المتاحة.');
                     return false;
                }
                var bundleValid = true;
                activeBundle.find('.var-select').each(function() {
                    if ($(this).val() === '') bundleValid = false;
                });
                if (!bundleValid) {
                    alert('⚠️ يرجى اختيار المقاس واللون لكل القطع في العرض المختار!');
                    return false;
                }
            }

            $('.bundle-save-section, .main-vars, .custom-buttons-wrapper').slideUp();
            $('#sliding-cod-form').addClass('show-it').hide().delay(400).fadeIn(600, function() {
                var target = $('#sliding-cod-form');
                if (target.length) {
                    $('html, body').animate({
                        scrollTop: target.offset().top - 120
                    }, 800);
                }
            });
        });

        $('#trigger-add-cart').on('click', function(e) {
            e.preventDefault();
            var activeBundle = $('.bundle-option.active');
            var isBundle = activeBundle.length > 0 && activeBundle.data('name') !== 'none';
            
            if (!isBundle) {
                 var allSelected = true;
                 $('.main-vars-wrap').each(function() {
                     if ($(this).find('.size-tile.active').length === 0) allSelected = false;
                 });
                 
                 var variation_id = $('input[name="variation_id"]').val();
                 var variationsResolved = variation_id !== '0' && variation_id !== '';
                 
                 if (!allSelected || !variationsResolved) {
                     alert('⚠️ يرجى اختيار المقاس واللون أولاً!');
                     return false;
                 }
                 // Standard Add to Cart: Submit the variation form
                 $('form.cart').append('<input type="hidden" name="add-to-cart" value="<?php the_ID(); ?>">');
                 $('form.cart').submit();
            } else {
                // Bundle AJAX Add to Cart
                var activeBundle = $('.bundle-option.active');
                var selections = [];
                activeBundle.find('.var-row').each(function() {
                    var itemSel = {};
                    $(this).find('.var-select').each(function() {
                        itemSel[$(this).data('attr')] = $(this).val();
                    });
                    selections.push(itemSel);
                });

                if (selections.some(s => Object.values(s).some(v => v === ''))) {
                    alert('يرجى اختيار المقاس واللون لكل القطع في العرض.');
                    return false;
                }

                $(this).text('جاري إضافة للعربة...').prop('disabled', true);
                
                var data = {
                    action: 'bundle_add_to_cart_ajax',
                    product_id: '<?php the_ID(); ?>',
                    qty: $('.qty-val').val(),
                    bundle_info: JSON.stringify({
                        name: activeBundle.data('name'),
                        items_count: activeBundle.data('items'),
                        price: activeBundle.data('price'),
                        selections: selections
                    }),
                    nonce: '<?php echo wp_create_nonce("express_checkout_action"); ?>'
                };

                $.post('<?php echo admin_url("admin-ajax.php"); ?>', data, function(response) {
                    if (response.success) {
                        window.location.reload(); // Refresh to show cart items or update counts
                    } else {
                        alert('خطأ: ' + response.data.message);
                        $('#trigger-add-cart').text('أضف للسلة 🛒 وإكمال التسوق').prop('disabled', false);
                    }
                });
            }
        });
        $('#final-confirm-order').on('click', function(e) {
            e.preventDefault();
            
            var name = $('input[name="exp_name"]').val();
            var phone = $('input[name="exp_phone"]').val();
            var state = $('#f_state').val();
            var city = $('input[name="exp_city"]').val();

            if (!name || !phone || !state || !city) {
                alert('يرجى ملأ جميع الخانات المطلوبة');
                return;
            }

            $(this).text('جاري تأكيد طلبك...').css('opacity', '0.7').prop('disabled', true);
            
            if ($('#express_checkout_direct_order').val() === '1') {
                // Bundle AJAX Loop
                var activeBundle = $('.bundle-option.active');
                var bundleData = {
                    name: activeBundle.data('name'),
                    items_count: activeBundle.data('items'),
                    price: activeBundle.data('price'),
                    selections: []
                };
                activeBundle.find('.var-row').each(function() {
                    var itemSel = {};
                    $(this).find('.var-select').each(function() {
                        itemSel[$(this).data('attr')] = $(this).val();
                    });
                    bundleData.selections.push(itemSel);
                });

                var data = {
                    action: 'process_express_checkout',
                    nonce: '<?php echo wp_create_nonce("express_checkout_action"); ?>',
                    product_id: '<?php the_ID(); ?>',
                    name: name,
                    phone: phone,
                    phone2: $('input[name="exp_phone2"]').val(),
                    state: state,
                    city: city,
                    shipping: $('#f_shipping').val(),
                    address: $('#f_address').val(),
                    qty: $('.qty-val').val(),
                    // Main variations
                    main_vars: JSON.stringify($('form.cart').serializeArray()),
                    bundle_info: JSON.stringify(bundleData)
                };

                $.post('<?php echo admin_url("admin-ajax.php"); ?>', data, function(response){
                    if(response.success) {
                        window.location.href = response.data.redirect;
                    } else {
                        alert('Error: ' + response.data.message);
                        $('#final-confirm-order').text('تأكيد الطلب 🚚').prop('disabled', false).css('opacity', '1');
                    }
                });
            } else {
                // Single Product Purchase: Trigger standard form submission
                // Our php filter (custom_express_co_to_thank_you) will handle the redirect.
                // We just need to add the fields to the cartForm.
                cartForm.append('<input type="hidden" name="express_checkout_direct_order" value="1">');
                cartForm.append('<input type="hidden" name="exp_name" value="' + name + '">');
                cartForm.append('<input type="hidden" name="exp_phone" value="' + phone + '">');
                cartForm.append('<input type="hidden" name="exp_phone2" value="' + $('input[name="exp_phone2"]').val() + '">');
                cartForm.append('<input type="hidden" name="exp_state" value="' + state + '">');
                cartForm.append('<input type="hidden" name="exp_city" value="' + city + '">');
                cartForm.append('<input type="hidden" name="exp_shipping" value="' + $('#f_shipping').val() + '">');
                cartForm.append('<input type="hidden" name="exp_address" value="' + $('#f_address').val() + '">');
                
                var activeBundle = $('.bundle-option.active');
                if (activeBundle.length > 0) {
                    cartForm.append('<input type="hidden" name="bundle_badge" value="' + activeBundle.find('.bundle-discount-badge').text() + '">');
                }

                cartForm.submit();
            }
        });
    });
    </script>
    <?php
}
// Hook at the very end of the cart form (after variations, after add to cart button)
add_action( 'woocommerce_after_add_to_cart_button', 'custom_product_page_layout', 99 );

// Store Bundle Badge in Cart Item Data
add_filter('woocommerce_add_cart_item_data', function($cart_item_data, $product_id, $variation_id) {
    if (isset($_REQUEST['bundle_badge'])) {
        $cart_item_data['bundle_badge'] = sanitize_text_field($_REQUEST['bundle_badge']);
    }
    return $cart_item_data;
}, 10, 3);

add_filter('woocommerce_get_item_data', function($item_data, $cart_item) {
    if (isset($cart_item['bundle_badge'])) {
        $item_data[] = array('key' => 'العرض', 'value' => $cart_item['bundle_badge']);
    }
    return $item_data;
}, 10, 2);



// 5. Intercept Add To Cart and Create Order Immediately! (The Holy Grail)
function custom_express_co_to_thank_you($url) {
    if( isset($_REQUEST['express_checkout_direct_order']) && $_REQUEST['express_checkout_direct_order'] == '1' ) {
        // At this specific point, native WooCommerce (and any third party plugin) 
        // has successfully added all bundle logics and variations to the Cart.
        
        $checkout = WC()->checkout();
        
        // Force POST data for standard create_order() logic
        $_POST['payment_method'] = 'cod';
        $_POST['billing_first_name'] = sanitize_text_field($_REQUEST['exp_name']);
        $_POST['billing_phone'] = sanitize_text_field($_REQUEST['exp_phone']);
        $_POST['billing_state'] = sanitize_text_field($_REQUEST['exp_state']);
        $_POST['billing_city'] = sanitize_text_field($_REQUEST['exp_city']);
        $_POST['billing_country'] = 'DZ';
        
        try {
            $order_id = $checkout->create_order(array());
            if ( ! is_wp_error( $order_id ) ) {
                $order = wc_get_order( $order_id );
                if(!empty($_REQUEST['exp_phone2'])) {
                    $order->update_meta_data('_billing_phone_2', sanitize_text_field($_REQUEST['exp_phone2']));
                }
                
                $order->set_payment_method('cod');
                $order->set_payment_method_title('الدفع عند التسليم');

                // Apply Shipping Fee
                $method = sanitize_text_field($_REQUEST['exp_shipping']);
                $state_name = sanitize_text_field($_REQUEST['exp_state']);
                $wilayas = get_algeria_wilayas();
                $shipping_cost = 0;
                
                // Get Bundle Badge check (Free shipping?)
                // Since this is a redirect, the cart already has items.
                // We'll check if any item is a bundle and has free shipping in its badge.
                $is_free = false;
                foreach (WC()->cart->get_cart() as $cart_item) {
                    if (isset($cart_item['bundle_badge']) && strpos($cart_item['bundle_badge'], 'مجاني') !== false) {
                        $is_free = true;
                    }
                }

                if (!$is_free) {
                    foreach ($wilayas as $w) {
                        if ($w['name'] == $state_name) {
                            $shipping_cost = ($method == 'home') ? $w['home'] : $w['office'];
                            break;
                        }
                    }
                }

                if ($shipping_cost > 0) {
                    $item_fee = new WC_Order_Item_Fee();
                    $item_fee->set_name( ($method == 'home') ? 'توصيل للمنزل' : 'توصيل للمكتب' );
                    $item_fee->set_amount( $shipping_cost );
                    $item_fee->set_total( $shipping_cost );
                    $order->add_item( $item_fee );
                }

                $order->calculate_totals();
                $order->update_status('processing', 'Express Landing Pop-up Checkout');
                
                WC()->cart->empty_cart();
                
                // Return Standard WooCommerce Thank You URL
                return $order->get_checkout_order_received_url();
            }
        } catch (Exception $e) {
            // Fallback to normal checkout if error
            return wc_get_checkout_url();
        }
    }
    return $url;
}
add_filter('woocommerce_add_to_cart_redirect', 'custom_express_co_to_thank_you', 9999);


// 6. Force Custom Checkout Template
function override_checkout_template( $template ) {
    // Only override the main checkout page, not the Order Received endpoint
    if ( is_checkout() && ! is_wc_endpoint_url( 'order-received' ) ) {
        $new_template = get_stylesheet_directory() . '/custom-checkout.php';
        if ( file_exists( $new_template ) ) {
            return $new_template;
        }
    }
    return $template;
}
add_filter( 'template_include', 'override_checkout_template', 99 );

// 6b. Add Body Class for Custom Checkout
function add_custom_checkout_body_class( $classes ) {
    if ( is_checkout() && ! is_wc_endpoint_url( 'order-received' ) ) {
        $classes[] = 'page-template-custom-checkout';
    }
    return $classes;
}
add_filter( 'body_class', 'add_custom_checkout_body_class' );

// 7. Process Custom Express Checkout via AJAX
// 7. Process Custom Express Checkout via AJAX (Direct Injection or Cart Checkout)
function handle_process_express_checkout() {
    check_ajax_referer( 'express_checkout_action', 'nonce' );

    $product_id = isset($_POST['product_id']) ? intval($_POST['product_id']) : 0;
    
    // If no product_id, we check if the cart has items (Checkout Page flow)
    if (!$product_id && WC()->cart->is_empty()) {
        wp_send_json_error(array('message' => 'السلة فارغة أو المنتج غير متوفر.'));
    }

    $name    = isset($_POST['cod_name']) ? sanitize_text_field($_POST['cod_name']) : sanitize_text_field($_POST['name']);
    $phone   = isset($_POST['cod_phone']) ? sanitize_text_field($_POST['cod_phone']) : sanitize_text_field($_POST['phone']);
    $phone2  = isset($_POST['cod_phone2']) ? sanitize_text_field($_POST['cod_phone2']) : (isset($_POST['phone2']) ? sanitize_text_field($_POST['phone2']) : '');
    $state   = isset($_POST['cod_state']) ? sanitize_text_field($_POST['cod_state']) : sanitize_text_field($_POST['state']);
    $city    = isset($_POST['cod_city']) ? sanitize_text_field($_POST['cod_city']) : sanitize_text_field($_POST['city']);
    $address = isset($_POST['cod_address']) ? sanitize_text_field($_POST['cod_address']) : (isset($_POST['address']) ? sanitize_text_field($_POST['address']) : '');
    $shipping_method = (isset($_POST['shipping']) && !empty($_POST['shipping'])) ? sanitize_text_field($_POST['shipping']) : 'office';
    if (isset($_POST['cod_shipping'])) $shipping_method = sanitize_text_field($_POST['cod_shipping']);
    
    $main_qty = isset($_POST['qty']) ? intval($_POST['qty']) : 1;

    express_log("Processing Express Order. ProductID: $product_id, Name: $name, Source: " . ($product_id ? 'Direct' : 'Cart'));


    try {
        // 1. Create WooCommerce Order
        $order = wc_create_order();
        if (is_wp_error($order)) throw new Exception($order->get_error_message());

        if ($product_id) {
            // --- PATH A: Direct Buy (Product Page) ---
            $product = wc_get_product($product_id);
            if (!$product) throw new Exception('Product not found.');

            $bundle_info = isset($_POST['bundle_info']) ? json_decode(stripslashes($_POST['bundle_info']), true) : null;
            $main_vars_raw = isset($_POST['main_vars']) ? stripslashes($_POST['main_vars']) : '';
            $is_bundle = ($bundle_info && isset($bundle_info['name']) && $bundle_info['name'] !== 'none');

            if (!$is_bundle) {
                $variation_id = 0;
                $var_data = array();
                if (!empty($main_vars_raw)) {
                    $decoded = json_decode($main_vars_raw, true);
                    if (is_array($decoded)) {
                        foreach ($decoded as $item) {
                            if (isset($item['name']) && strpos($item['name'], 'attribute_') === 0) {
                                $var_data[$item['name']] = $item['value'];
                            }
                        }
                    }
                }
                if ($product->is_type('variable')) {
                    $data_store = WC_Data_Store::load( 'product' );
                    $variation_id = $data_store->find_matching_product_variation( new WC_Product_Variable( $product_id ), $var_data );
                }
                $order->add_product($product, $main_qty, array('variation_id' => $variation_id, 'variation' => $var_data));
            } else {
                for ($q = 0; $q < $main_qty; $q++) {
                    foreach ($bundle_info['selections'] as $selection) {
                        $b_var_data = array();
                        $attributes = $product->get_attributes();
                        foreach ($selection as $label => $attr_val) {
                            foreach ($attributes as $slug => $attr_obj) {
                                if (wc_attribute_label($slug) == $label) {
                                    $tax_name = strpos($slug, 'pa_') === 0 ? 'attribute_' . $slug : 'attribute_' . $slug;
                                    $b_var_data[$tax_name] = $attr_val;
                                    break;
                                }
                            }
                        }
                        $b_variation_id = 0;
                        if ($product->is_type('variable')) {
                            $data_store = WC_Data_Store::load( 'product' );
                            $b_variation_id = $data_store->find_matching_product_variation( new WC_Product_Variable( $product_id ), $b_var_data );
                        }
                        $order->add_product($product, 1, array('variation_id' => $b_variation_id, 'variation' => $b_var_data));
                    }
                }
                $order->update_meta_data('_is_bundle_order', 'yes');
                $order->update_meta_data('_bundle_name', sanitize_text_field($bundle_info['name']));
            }
        } else {
            // --- PATH B: Cart Checkout (Checkout Page) ---
            foreach (WC()->cart->get_cart() as $cart_item_key => $cart_item) {
                $order->add_product($cart_item['data'], $cart_item['quantity'], array(
                    'variation_id' => $cart_item['variation_id'],
                    'variation'    => $cart_item['variation']
                ));
            }
        }

        // 2. Set Customer Data
        $customer_address = array(
            'first_name' => $name,
            'phone'      => $phone,
            'state'      => $state,
            'city'       => $city,
            'country'    => 'DZ',
            'address_1'  => $address
        );
        $order->set_address($customer_address, 'billing');
        $order->set_address($customer_address, 'shipping');
        if (!empty($phone2)) $order->update_meta_data('_billing_phone_2', $phone2);

        // 3. Payment Method
        $order->set_payment_method('cod');
        $order->set_payment_method_title('الدفع عند التسليم (COD)');

        // 4. Shipping Calculation
        $shipping_cost = 0;
        $is_free = false;
        
        // Check for free shipping in cart or direct bundle
        if ($product_id && isset($is_bundle) && $is_bundle) {
            $is_free = true;
        } else {
            foreach (WC()->cart->get_cart() as $item) {
                if (isset($item['bundle_badge']) && strpos($item['bundle_badge'], 'مجاني') !== false) {
                    $is_free = true; break;
                }
            }
        }

        if (!$is_free) {
            $wilayas = get_algeria_wilayas();
            foreach ($wilayas as $w) {
                if ($w['name'] == $state) {
                    $shipping_cost = ($shipping_method == 'home') ? $w['home'] : $w['office'];
                    break;
                }
            }
        }

        if ($shipping_cost > 0) {
            $item_fee = new WC_Order_Item_Fee();
            $item_fee->set_name(($shipping_method == 'home') ? 'توصيل للمنزل' : 'توصيل للمكتب');
            $item_fee->set_amount($shipping_cost);
            $item_fee->set_total($shipping_cost);
            $order->add_item($item_fee);
        }

        // 5. Totals & Status
        $order->calculate_totals();
        
        // Force Bundle Price if Direct
        if ($product_id && isset($is_bundle) && $is_bundle && !empty($bundle_info['price'])) {
            $order->set_total((floatval($bundle_info['price']) * $main_qty) + $shipping_cost);
        }
        
        $order->update_status('processing', 'Express Checkout');
        $order->save();

        WC()->cart->empty_cart();
        express_log("Order #{$order->get_id()} created successfully.");
        
        wp_send_json_success(array('redirect' => $order->get_checkout_order_received_url()));

    } catch (Exception $e) {
        express_log("Express Order Error: " . $e->getMessage());
        wp_send_json_error(array('message' => $e->getMessage()));
    }
}

add_action( 'wp_ajax_process_express_checkout', 'handle_process_express_checkout' );

add_action( 'wp_ajax_nopriv_process_express_checkout', 'handle_process_express_checkout' );
add_action( 'wp_ajax_add_to_cart_bundle', 'handle_bundle_add_to_cart_ajax' );
add_action( 'wp_ajax_nopriv_add_to_cart_bundle', 'handle_bundle_add_to_cart_ajax' );
add_action( 'wp_ajax_submit_cod_order', 'handle_process_express_checkout' );
add_action( 'wp_ajax_nopriv_submit_cod_order', 'handle_process_express_checkout' );

function handle_bundle_add_to_cart_ajax() {

    check_ajax_referer( 'express_checkout_action', 'nonce' );

    $product_id = isset($_POST['product_id']) ? intval($_POST['product_id']) : 0;
    $bundle_info = isset($_POST['bundle_info']) ? json_decode(stripslashes($_POST['bundle_info']), true) : null;
    $qty = isset($_POST['qty']) ? intval($_POST['qty']) : 1;

    if (!$product_id || !$bundle_info) {
        wp_send_json_error(array('message' => 'Missing product or bundle data.'));
    }

    express_log("AJAX Add to Cart (Bundle). Product: $product_id, Bundle: {$bundle_info['name']}, Qty: $qty");

    try {
        $product = wc_get_product($product_id);
        if (!$product) throw new Exception('Product not found.');
        
        $attributes = $product->get_attributes();

        for ($q = 0; $q < $qty; $q++) {
            foreach ($bundle_info['selections'] as $selection) {
                $var_data = array();
                
                // Map labels to internal slugs (same as handle_process_express_checkout)
                foreach ($selection as $label => $v) {
                    $matched = false;
                    foreach ($attributes as $slug => $attr_obj) {
                        if (wc_attribute_label($slug) == $label) {
                            $tax_name = strpos($slug, 'pa_') === 0 ? 'attribute_' . $slug : 'attribute_' . $slug;
                            $var_data[$tax_name] = $v;
                            $matched = true;
                            break;
                        }
                    }
                    if (!$matched) {
                        // Fallback if no label match (maybe it was already a slug)
                        $tax_name = (strpos($label, 'attribute_') === 0) ? $label : 'attribute_' . $label;
                        $var_data[$tax_name] = $v;
                    }
                }

                $variation_id = 0;
                if ($product->is_type('variable')) {
                    $data_store = WC_Data_Store::load( 'product' );
                    $variation_id = $data_store->find_matching_product_variation( new WC_Product_Variable( $product_id ), $var_data );
                }

                // Metadata for the cart item
                $cart_item_data = array(
                    'bundle_name' => $bundle_info['name'],
                    'bundle_badge' => 'عرض خاص (Bundle)'
                );
                
                WC()->cart->add_to_cart($product_id, 1, $variation_id, $var_data, $cart_item_data);
            }
        }

        wp_send_json_success(array('message' => 'Added to cart successfully.'));
    } catch (Exception $e) {
        express_log("Bundle Add to Cart Error: " . $e->getMessage());
        wp_send_json_error(array('message' => $e->getMessage()));
    }
}

add_action( 'wp_ajax_bundle_add_to_cart_ajax', 'handle_bundle_add_to_cart_ajax' );
add_action( 'wp_ajax_nopriv_bundle_add_to_cart_ajax', 'handle_bundle_add_to_cart_ajax' );





// 8. Bundle & Save: Custom Meta Box for Product Offers
function add_bundle_offers_meta_box() {
    add_meta_box(
        'bundle_offers_meta_box',
        'نظام العروض المتقدم (Bundle & Save)',
        'render_bundle_offers_meta_box',
        'product',
        'normal',
        'high'
    );
}
add_action('add_meta_boxes', 'add_bundle_offers_meta_box');

// Enqueue Media Uploader scripts
function enqueue_admin_bundle_scripts($hook) {
    if ('post.php' != $hook && 'post-new.php' != $hook) return;
    wp_enqueue_media();
}
add_action('admin_enqueue_scripts', 'enqueue_admin_bundle_scripts');

function render_bundle_offers_meta_box($post) {
    $offers     = get_post_meta($post->ID, '_bundle_offers', true);
    $enable_bd  = get_post_meta($post->ID, '_enable_bundle_system', true);
    $top_text   = get_post_meta($post->ID, '_bundle_top_text', true);
    $badge_text = get_post_meta($post->ID, '_custom_badge_text', true);
    $hide_main  = get_post_meta($post->ID, '_hide_main_product_offer', true);

    if (empty($top_text)) {
        $top_text = 'اختر اللون والمقاس (إذا كنت بين مقاسين ½، اختر المقاس الأكبر لراحة أفضل، املأ معلوماتك كاملة، ثم اضغط على زر الشراء لإتمام طلبك 🚀';
    }

    if (!$offers || !is_array($offers)) {
        $offers = array(
            array('label' => 'الأكثر مبيعاً', 'name' => 'عرض قطعتين', 'items' => 2, 'price' => '', 'old_price' => '', 'badge' => 'توصيل مجاني لـ 58 ولاية'),
            array('label' => 'توفير أكبر', 'name' => 'عرض 3 قطع', 'items' => 3, 'price' => '', 'old_price' => '', 'badge' => 'توصيل سريع مجاني لـ 58 ولاية')
        );
    }
    $offers = array_slice($offers, 0, 2);
    wp_nonce_field('bundle_offers_nonce_action', 'bundle_offers_nonce');
    ?>
    <style>
        .b-panel { background: #fff; border: 1px solid #ccd0d4; padding: 20px; border-radius: 8px; margin-bottom: 20px; }
        .b-section-ttl { font-size: 1.1rem; font-weight: 800; color: #1d2327; margin-bottom: 15px; border-bottom: 2px solid #2271b1; display: inline-block; padding-bottom: 5px; }
        .bundle-offer-row { border: 1px solid #ddd; padding: 15px; margin-bottom: 15px; background: #fdfdfd; border-radius: 8px; border-right: 5px solid #2271b1; }
        .bundle-field-group { display: flex; gap: 15px; flex-wrap: wrap; }
        .bundle-field { margin-bottom: 12px; flex: 1; min-width: 200px; }
        .bundle-field label { display: block; font-weight: bold; margin-bottom: 5px; }
        .bundle-field input[type="text"], .bundle-field input[type="number"] { width: 100%; height: 35px; border-radius: 4px; }
        .b-toggle-row { background: #e7f5ff; padding: 15px; border-radius: 6px; margin-bottom: 20px; border: 1px solid #b8e1ff; }
        .b-banner-preview { max-width: 300px; margin-top: 10px; border: 1px dashed #ccc; padding: 5px; border-radius: 4px; }
        .b-banner-preview img { width: 100%; height: auto; display: block; }
    </style>

    <div class="b-toggle-row">
        <label style="font-size: 1.1rem; cursor: pointer;">
            <input type="checkbox" name="enable_bundle_system" value="yes" <?php checked($enable_bd, 'yes'); ?>> 
            <strong>✅ تفعيل نظام العروض المتقدم (Bundle & Save) لهذا المنتج</strong>
        </label>
        <p class="description" style="margin-top: 5px;">إذا لم يتم تفعيل هذا الزر، لن يظهر أي شيء متعلق بالعروض في صفحة المنتج.</p>
    </div>

    <div class="b-panel">
        <div class="b-section-ttl">إعدادات الشارة (Lightning Badge)</div>
        <div class="bundle-field" style="max-width:300px">
            <label>نص الشارة:</label>
            <input type="text" name="custom_badge_text" value="<?php echo esc_attr($badge_text); ?>" placeholder="مثال: الأكثر مبيعاً">
            <p class="description">تظهر مع أيقونة البرق في الصفحة الرئيسية. (نص فقط)</p>
        </div>
    </div>

    <div class="b-panel">
        <div class="b-section-ttl">النص العلوي للإرشاد (Top Instruction Text)</div>
        <div class="bundle-field">
            <textarea name="bundle_top_text" rows="3" style="width:100%"><?php echo esc_textarea($top_text); ?></textarea>
            <p class="description">يظهر في الصندوق الرمادي بجمال في أعلى قسم الطلب.</p>
        </div>
    </div>

    <div class="b-panel">
        <label style="margin-top: 10px; display: block; font-weight:700; color:#d63638">
            <input type="checkbox" name="hide_main_product_offer" value="yes" <?php checked($hide_main, 'yes'); ?>> 
            🚫 إخفاء خيار "شراء المنتج الأساسي فقط" (إجبار الزبون على العروض)
        </label>
    </div>

    <div class="b-panel">
        <div class="b-section-ttl">قائمة العروض</div>
        <div id="bundle-offers-container">
            <?php foreach ($offers as $index => $offer) : ?>
                <div class="bundle-offer-row">
                    <h4>عرض رقم <?php echo $index + 1; ?></h4>
                    <div class="bundle-field-group">
                        <div class="bundle-field">
                            <label>تسمية العرض (Badge)</label>
                            <input type="text" name="bundle_offers[<?php echo $index; ?>][label]" value="<?php echo esc_attr($offer['label'] ?? ''); ?>" placeholder="مثال: الأكثر مبيعاً">
                        </div>
                        <div class="bundle-field">
                            <label>اسم العبوة (Inside name)</label>
                            <input type="text" name="bundle_offers[<?php echo $index; ?>][name]" value="<?php echo esc_attr($offer['name'] ?? ''); ?>" placeholder="مثال: قطعتين">
                        </div>
                        <div class="bundle-field" style="max-width: 100px;">
                            <label>عدد القطع</label>
                            <input type="number" name="bundle_offers[<?php echo $index; ?>][items]" value="<?php echo esc_attr($offer['items'] ?? ( $index === 0 ? 2 : 3 )); ?>" min="1" max="50">
                        </div>
                        <div class="bundle-field">
                            <label>السعر الجديد (DZD)</label>
                            <input type="text" name="bundle_offers[<?php echo $index; ?>][price]" value="<?php echo esc_attr($offer['price'] ?? ''); ?>">
                        </div>
                        <div class="bundle-field">
                            <label>السعر القديم (DZD)</label>
                            <input type="text" name="bundle_offers[<?php echo $index; ?>][old_price]" value="<?php echo esc_attr($offer['old_price'] ?? ''); ?>">
                        </div>
                        <div class="bundle-field">
                            <label>النص الصغير (Shipping text)</label>
                            <input type="text" name="bundle_offers[<?php echo $index; ?>][badge]" value="<?php echo esc_attr($offer['badge'] ?? ''); ?>" placeholder="توصيل مجاني">
                        </div>
                        <div class="bundle-field" style="width: 100%; flex: 100%;">
                            <label><input type="checkbox" name="bundle_offers[<?php echo $index; ?>][hide]" value="yes" <?php checked(isset($offer['hide']) && $offer['hide'] == 'yes'); ?>> إخفاء هذا العرض</label>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>

    <script>
    jQuery(document).ready(function($){
        var frame;
        $('#b-upload-btn').on('click', function(e){
            e.preventDefault();
            if(frame){ frame.open(); return; }
            frame = wp.media({ title: 'اختر أو ارفع بنر إعلاني', button: { text: 'استخدام كبنر للمنتج' }, multiple: false });
            frame.on('select', function(){
                var attachment = frame.state().get('selection').first().toJSON();
                $('#product_promo_banner').val(attachment.url);
                $('#b-preview img').attr('src', attachment.url);
                $('#b-preview, #b-remove-btn').show();
            });
            frame.open();
        });
        $('#b-remove-btn').on('click', function(e){
            e.preventDefault();
            $('#product_promo_banner').val('');
            $('#b-preview, #b-remove-btn').hide();
        });
    });
    </script>
    <?php
}

function save_bundle_offers_meta_box($post_id) {
    if (!isset($_POST['bundle_offers_nonce']) || !wp_verify_nonce($_POST['bundle_offers_nonce'], 'bundle_offers_nonce_action')) {
        return;
    }
    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
        return;
    }
    if (isset($_POST['bundle_offers'])) {
        update_post_meta($post_id, '_bundle_offers', $_POST['bundle_offers']);
    }
    if (isset($_POST['custom_badge_text'])) {
        update_post_meta($post_id, '_custom_badge_text', sanitize_text_field($_POST['custom_badge_text']));
    }
    if (isset($_POST['bundle_top_text'])) {
        update_post_meta($post_id, '_bundle_top_text', sanitize_text_field($_POST['bundle_top_text']));
    }
    $enable_bundles = isset($_POST['enable_bundle_system']) ? 'yes' : 'no';
    update_post_meta($post_id, '_enable_bundle_system', $enable_bundles);
    
    $hide_main = isset($_POST['hide_main_product_offer']) ? 'yes' : 'no';
    update_post_meta($post_id, '_hide_main_product_offer', $hide_main);
}
add_action('save_post_product', 'save_bundle_offers_meta_box');

// 9. Register FAQ Custom Post Type
function register_faq_cpt() {
    $labels = array(
        'name'               => 'الأسئلة الشائعة',
        'singular_name'      => 'سؤال',
        'menu_name'          => 'الأسئلة الشائعة',
        'add_new'           => 'إضافة سؤال جديد',
        'add_new_item'      => 'إضافة سؤال جديد',
        'edit_item'         => 'تعديل السؤال',
        'new_item'          => 'سؤال جديد',
        'view_item'         => 'عرض السؤال',
        'all_items'         => 'جميع الأسئلة',
    );
    $args = array(
        'labels'             => $labels,
        'public'             => true,
        'has_archive'        => false,
        'menu_icon'          => 'dashicons-editor-help',
        'supports'           => array('title', 'editor'),
        'show_in_rest'       => true,
    );
    register_post_type('faq', $args);
}
add_action('init', 'register_faq_cpt');

// 10. Register Homepage Hero Slider CPT
function register_hero_slider_cpt() {
    $labels = [
        'name' => 'الشرائح الرئيسية (Slider)',
        'singular_name' => 'شريحة',
        'add_new' => 'إضافة شريحة جديدة',
        'all_items' => 'جميع الشرائح',
        'edit_item' => 'تعديل الشريحة',
    ];
    $args = [
        'labels' => $labels,
        'public' => true,
        'has_archive' => false,
        'menu_icon' => 'dashicons-images-alt2',
        'supports' => ['title'],
        'show_in_rest' => true,
    ];
    register_post_type('hero_slide', $args);
}
add_action('init', 'register_hero_slider_cpt');

// 11. Custom Meta Box for Hero Slide
function add_hero_slide_meta_box() {
    add_meta_box('hero_slide_meta', 'إعدادات الشريحة', 'render_hero_slide_meta_box', 'hero_slide', 'normal', 'high');
}
add_action('add_meta_boxes', 'add_hero_slide_meta_box');

function render_hero_slide_meta_box($post) {
    $img   = get_post_meta($post->ID, '_slide_img', true);
    $sub   = get_post_meta($post->ID, '_slide_subtitle', true);
    $desc  = get_post_meta($post->ID, '_slide_desc', true);
    $btn_t = get_post_meta($post->ID, '_slide_btn_text', true);
    $btn_u = get_post_meta($post->ID, '_slide_btn_url', true);
    wp_nonce_field('hero_slide_nonce_action', 'hero_slide_nonce');
    ?>
    <style>
        .s-field { margin-bottom: 20px; }
        .s-field label { display: block; font-weight: bold; margin-bottom: 5px; }
        .s-field input, .s-field textarea { width: 100%; }
        .s-preview { max-width: 400px; margin-top: 10px; border: 1px dashed #ccc; padding: 5px; }
        .s-preview img { width: 100%; height: auto; display: block; }
    </style>
    <div class="s-field">
        <label>صورة الخلفية (Background Image):</label>
        <input type="hidden" name="slide_img" id="slide_img_val" value="<?php echo esc_attr($img); ?>">
        <button type="button" class="button button-secondary" id="s-upload-btn">اختر صورة</button>
        <div id="s-preview-box" class="s-preview" <?php echo empty($img) ? 'style="display:none"' : ''; ?>>
            <img src="<?php echo esc_url($img); ?>" alt="">
        </div>
    </div>
    <div class="s-field"><label>النص الصغير (Subtitle - خلفية حمراء):</label><input type="text" name="slide_subtitle" value="<?php echo esc_attr($sub); ?>" placeholder="مثال: الراحة في كل خطوة"></div>
    <div class="s-field"><label>العنوان الرئيسي (Main Title):</label><input type="text" name="post_title_manual" value="<?php echo esc_attr($post->post_title); ?>" placeholder="مثال: Lacoste Nitro"></div>
    <div class="s-field"><label>الوصف (Description):</label><textarea name="slide_desc" rows="3"><?php echo esc_textarea($desc); ?></textarea></div>
    <div class="s-field"><label>نص الزر (Button Text):</label><input type="text" name="slide_btn_text" value="<?php echo esc_attr($btn_t); ?>" placeholder="احصل على العروض"></div>
    <div class="s-field"><label>رابط الزر (Button URL):</label><input type="text" name="slide_btn_url" value="<?php echo esc_attr($btn_u); ?>" placeholder="#offers"></div>

    <script>
    jQuery(document).ready(function($){
        var frame;
        $('#s-upload-btn').on('click', function(e){
            e.preventDefault();
            if(frame){ frame.open(); return; }
            frame = wp.media({ title: 'اختر صورة للشريحة', button: { text: 'استخدام كخلفية' }, multiple: false });
            frame.on('select', function(){
                var attachment = frame.state().get('selection').first().toJSON();
                $('#slide_img_val').val(attachment.url);
                $('#s-preview-box img').attr('src', attachment.url);
                $('#s-preview-box').show();
            });
            frame.open();
        });
    });
    </script>
    <?php
}

function save_hero_slide_meta_box($post_id) {
    if (!isset($_POST['hero_slide_nonce']) || !wp_verify_nonce($_POST['hero_slide_nonce'], 'hero_slide_nonce_action')) return;
    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) return;

    $fields = ['slide_img', 'slide_subtitle', 'slide_desc', 'slide_btn_text', 'slide_btn_url'];
    foreach ($fields as $f) {
        if (isset($_POST[$f])) update_post_meta($post_id, '_' . $f, sanitize_text_field($_POST[$f]));
    }

    // Update Post Title if provided
    if (isset($_POST['post_title_manual'])) {
        remove_action('save_post_hero_slide', 'save_hero_slide_meta_box');
        wp_update_post([
            'ID' => $post_id,
            'post_title' => sanitize_text_field($_POST['post_title_manual'])
        ]);
        add_action('save_post_hero_slide', 'save_hero_slide_meta_box');
    }
}
add_action('save_post_hero_slide', 'save_hero_slide_meta_box');

// 12. Global Store Settings (Social Links & Contacts)
function add_store_settings_meta_box() {
    $front_id = get_option('page_on_front');
    add_meta_box('store_settings_meta', 'إعدادات المتجر العامة (روابط التواصل)', 'render_store_settings_meta_box', 'page', 'side', 'high');
}
add_action('add_meta_boxes', 'add_store_settings_meta_box');

function render_store_settings_meta_box($post) {
    if ($post->ID != get_option('page_on_front')) return;
    
    $fb   = get_post_meta($post->ID, '_store_fb', true);
    $ig   = get_post_meta($post->ID, '_store_ig', true);
    $tk   = get_post_meta($post->ID, '_store_tk', true);
    $wa   = get_post_meta($post->ID, '_store_wa', true);
    $phone = get_post_meta($post->ID, '_store_phone', true);
    $addr = get_post_meta($post->ID, '_store_addr', true);
    $f_logo = get_post_meta($post->ID, '_store_footer_logo', true);
    
    // SEO & Social Fields
    $seo_title = get_post_meta($post->ID, '_store_seo_title', true);
    $seo_desc  = get_post_meta($post->ID, '_store_seo_desc', true);
    $og_img    = get_post_meta($post->ID, '_store_og_img', true);
    $favicon   = get_post_meta($post->ID, '_store_favicon', true);
    
    wp_nonce_field('store_settings_nonce_action', 'store_settings_nonce');
    ?>
    <style>
        .contact-box {
            background: #f0f8ff;
            padding: 15px;
            border-radius: 8px;
            text-align: center;
            margin-bottom: 20px;
        }
        .contact-btn {
            display: inline-block;
            background: #25d366;
            color: #fff;
            padding: 10px 20px;
            border-radius: 5px;
            text-decoration: none;
            margin-top: 10px;
        }
        .st-fld { margin-bottom: 15px; border-bottom: 1px solid #eee; padding-bottom: 10px; } 
        .st-fld label { display: block; font-weight: bold; margin-bottom: 5px; font-size: 13px; color: #2271b1; } 
        .st-fld input { width: 100%; padding: 8px; border-radius: 4px; border: 1px solid #ccc; }
        .f-preview { max-width: 150px; margin-top: 10px; border: 1px dashed #ccc; padding: 5px; background: #333; display: <?php echo empty($f_logo) ? 'none' : 'block'; ?>; }
        .f-preview img { width: 100%; height: auto; filter: brightness(0) invert(1); }
    </style>

    <div style="background: #f9f9f9; padding: 15px; border-radius: 8px; margin-bottom: 20px; border: 1px solid #e5e5e5;">
        <h4 style="margin-top:0; color:#d63638;"><i class="dashicons dashicons-admin-site"></i> إعدادات الهوية والشريط السفلي</h4>
        
        <div class="st-fld">
            <label>لوجو الشريط السفلي (Footer Logo):</label>
            <input type="hidden" name="store_footer_logo" id="store_footer_logo" value="<?php echo esc_attr($f_logo); ?>">
            <button type="button" class="button button-secondary" id="st-upload-btn">اختيار لوجو السفلية</button>
            <div id="f-preview" class="f-preview">
                <img src="<?php echo esc_url($f_logo); ?>" alt="">
            </div>
            <p class="description">سيظهر في أسفل كل صفحات الموقع.</p>
        </div>

        <div class="st-fld">
            <label>رقم الهاتف الأساسي (Phone):</label>
            <input type="text" name="store_phone" value="<?php echo esc_attr($phone); ?>" placeholder="مثال: 0555 12 34 56">
        </div>

        <div class="st-fld">
            <label>رقم الواتساب (WhatsApp Num):</label>
            <input type="text" name="store_wa" value="<?php echo esc_attr($wa); ?>" placeholder="213XXXXXXXXX">
            <p class="description">استخدم الصيغة الدولية بدون علامة +</p>
        </div>

        <div class="st-fld">
            <label>عنوان المتجر / المقر (Address):</label>
            <input type="text" name="store_addr" value="<?php echo esc_attr($addr); ?>" placeholder="الجزائر، العاصمة">
        </div>
    </div>

    <div style="background: #fff8e1; padding: 15px; border-radius: 8px; margin-bottom: 20px; border: 1px solid #ffd54f;">
        <h4 style="margin-top:0; color:#f57f17;"><i class="dashicons dashicons-google"></i> إعدادات البحث ووسائل التواصل (SEO)</h4>
        
        <div class="st-fld">
            <label>أيقونة التبويب (Favicon):</label>
            <input type="hidden" name="store_favicon" id="store_favicon" value="<?php echo esc_attr($favicon); ?>">
            <button type="button" class="button button-secondary media-upload-btn" data-target="#store_favicon" data-preview="#fav-preview">اختيار Favicon</button>
            <div id="fav-preview" style="margin-top:10px; <?php echo empty($favicon) ? 'display:none' : ''; ?>">
                <img src="<?php echo esc_url($favicon); ?>" style="width:32px; height:32px; border:1px solid #ddd;">
            </div>
            <p class="description">الأيقونة الصغيرة التي تظهر في المتصفح وجوجل (يفضل حجم 32x32).</p>
        </div>

        <div class="st-fld">
            <label>عنوان الموقع في البحث (SEO Title):</label>
            <input type="text" name="store_seo_title" value="<?php echo esc_attr($seo_title); ?>" placeholder="مثال: Gentle Shoes - أفخم الأحذية في الجزائر">
        </div>

        <div class="st-fld">
            <label>وصف الموقع (SEO Description):</label>
            <textarea name="store_seo_desc" style="width:100%;" rows="3"><?php echo esc_textarea($seo_desc); ?></textarea>
            <p class="description">هذا الوصف يظهر تحت اسم الموقع في نتائج بحث جوجل.</p>
        </div>

        <div class="st-fld">
            <label>صورة المشاركة (Social Share Image):</label>
            <input type="hidden" name="store_og_img" id="store_og_img" value="<?php echo esc_attr($og_img); ?>">
            <button type="button" class="button button-secondary media-upload-btn" data-target="#store_og_img" data-preview="#og-preview">اختيار صورة المشاركة</button>
            <div id="og-preview" style="margin-top:10px; <?php echo empty($og_img) ? 'display:none' : ''; ?>">
                <img src="<?php echo esc_url($og_img); ?>" style="max-width:100%; height:auto; border:1px solid #ddd;">
            </div>
            <p class="description">الصورة التي تظهر عند إرسال رابط الموقع في واتساب أو فيسبوك.</p>
        </div>
    </div>

    <div style="background: #fff; padding: 15px; border-radius: 8px; border: 1px solid #e5e5e5;">
        <h4 style="margin-top:0; color:#3b5998;"><i class="dashicons dashicons-share"></i> روابط التواصل الاجتماعي</h4>
        <div class="st-fld"><label>Facebook URL:</label><input type="text" name="store_fb" value="<?php echo esc_attr($fb); ?>"></div>
        <div class="st-fld"><label>Instagram URL:</label><input type="text" name="store_ig" value="<?php echo esc_attr($ig); ?>"></div>
        <div class="st-fld"><label>TikTok URL:</label><input type="text" name="store_tk" value="<?php echo esc_attr($tk); ?>"></div>
    </div>

    <script>
    jQuery(document).ready(function($){
        var frame;
        $('body').on('click', '.media-upload-btn, #st-upload-btn', function(e){
            e.preventDefault();
            var btn = $(this);
            var target = btn.data('target') || '#store_footer_logo';
            var preview = btn.data('preview') || '#f-preview';

            frame = wp.media({ title: 'اختيار صورة', button: { text: 'استخدام الصورة' }, multiple: false });
            frame.on('select', function(){
                var attachment = frame.state().get('selection').first().toJSON();
                $(target).val(attachment.url);
                if (preview && $(preview).length) {
                    $(preview + ' img').attr('src', attachment.url);
                    $(preview).show();
                }
            });
            frame.open();
        });
    });
    </script>
    <?php
}

function save_store_settings_meta_box($post_id) {
    if (!isset($_POST['store_settings_nonce']) || !wp_verify_nonce($_POST['store_settings_nonce'], 'store_settings_nonce_action')) return;
    $fields = [
        'store_fb', 'store_ig', 'store_tk', 'store_wa', 'store_addr', 
        'store_phone', 'store_footer_logo', 'store_seo_title', 
        'store_seo_desc', 'store_og_img', 'store_favicon'
    ];
    foreach ($fields as $f) {
        if (isset($_POST[$f])) update_post_meta($post_id, '_' . $f, sanitize_text_field($_POST[$f]));
    }
}
add_action('save_post', 'save_store_settings_meta_box');

// 12b. Meta Box for About Us Page Template
function add_about_page_meta_box() {
    $post_id = isset($_GET['post']) ? $_GET['post'] : (isset($_POST['post_ID']) ? $_POST['post_ID'] : 0);
    $template = get_post_meta($post_id, '_wp_page_template', true);
    
    if ($template == 'page-about.php') {
        add_meta_box(
            'about_page_meta',
            'إعدادات صفحة "من نحن" الفاخرة',
            'render_about_page_meta_box',
            'page',
            'normal',
            'high'
        );
    }
}
add_action('add_meta_boxes', 'add_about_page_meta_box');

function render_about_page_meta_box($post) {
    $title    = get_post_meta($post->ID, '_about_hero_title', true);
    $subtitle = get_post_meta($post->ID, '_about_hero_subtitle', true);
    $logo     = get_post_meta($post->ID, '_about_custom_logo', true);
    $desc     = get_post_meta($post->ID, '_about_extra_desc', true);
    
    wp_nonce_field('about_page_nonce_action', 'about_page_nonce');
    ?>
    <style>
        .ab-field { margin-bottom: 20px; }
        .ab-field label { display: block; font-weight: bold; margin-bottom: 8px; font-size: 14px; color: #2271b1; }
        .ab-field input[type="text"], .ab-field textarea { width: 100%; padding: 10px; border-radius: 4px; border: 1px solid #ccc; }
        .ab-preview { max-width: 250px; margin-top: 10px; border: 1px dashed #ccc; padding: 5px; background: #f0f0f0; display: <?php echo empty($logo) ? 'none' : 'block'; ?>; }
        .ab-preview img { width: 100%; height: auto; }
    </style>

    <div class="ab-field">
        <label>عنوان الصفحة (Hero Title):</label>
        <input type="text" name="about_hero_title" value="<?php echo esc_attr($title ?: 'من نحن'); ?>" placeholder="مثال: قصة الأناقة والفخامة">
    </div>

    <div class="ab-field">
        <label>نص تعريفي جذاب (Hero Subtitle):</label>
        <input type="text" name="about_hero_subtitle" value="<?php echo esc_attr($subtitle); ?>" placeholder="مثال: بدأنا بشغف لنقدم لك أفضل الأحذية من أرقى الماركات">
    </div>

    <div class="ab-field">
        <label>لوجو الصفحة الكبير (Large Logo):</label>
        <input type="hidden" name="about_custom_logo" id="about_custom_logo" value="<?php echo esc_attr($logo); ?>">
        <button type="button" class="button button-secondary" id="ab-upload-btn">اختيار لوجو كبير</button>
        <div id="ab-preview" class="ab-preview">
            <img src="<?php echo esc_url($logo); ?>" alt="">
        </div>
        <p class="description">يفضل أن يكون اللوجو شفافاً وبحجم كبير ليظهر بشكل فخم في مقدمة الصفحة.</p>
    </div>

    <div class="ab-field">
        <label>نبذة إضافية (اختياري):</label>
        <textarea name="about_extra_desc" rows="4" placeholder="هنا يمكنك كتابة كلام إضافي يظهر فوق روابط التواصل"><?php echo esc_textarea($desc); ?></textarea>
    </div>

    <script>
    jQuery(document).ready(function($){
        var frame;
        $('#ab-upload-btn').on('click', function(e){
            e.preventDefault();
            if(frame){ frame.open(); return; }
            frame = wp.media({ title: 'اختر لوجو فخم لصفحة من نحن', button: { text: 'استخدام كلوجو' }, multiple: false });
            frame.on('select', function(){
                var attachment = frame.state().get('selection').first().toJSON();
                $('#about_custom_logo').val(attachment.url);
                $('#ab-preview img').attr('src', attachment.url);
                $('#ab-preview').show();
            });
            frame.open();
        });
    });
    </script>
    <?php
}

function save_about_page_meta_box($post_id) {
    if (!isset($_POST['about_page_nonce']) || !wp_verify_nonce($_POST['about_page_nonce'], 'about_page_nonce_action')) return;
    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) return;

    $fields = ['about_hero_title', 'about_hero_subtitle', 'about_custom_logo', 'about_extra_desc'];
    foreach ($fields as $f) {
        if (isset($_POST[$f])) update_post_meta($post_id, '_' . $f, sanitize_text_field($_POST[$f]));
    }
}
add_action('save_post', 'save_about_page_meta_box');

// 13. Helper: Get WooCommerce Categories for Header
function get_header_categories_dropdown() {
    $args = [ 'taxonomy' => 'product_cat', 'hide_empty' => true, 'parent' => 0 ];
    return get_terms($args);
}

// 13b. Force Full Width for Specific Pages
add_filter( 'astra_page_layout', 'force_custom_full_width_layout' );
function force_custom_full_width_layout( $layout ) {
    if ( is_page('faq') || is_page_template('page-faq.php') || is_page('shop') || is_page('about-us') || is_page('من نحن') ) {
        return 'no-sidebar';
    }
    return $layout;
}

add_filter( 'astra_get_content_layout', 'force_custom_content_layout' );
function force_custom_content_layout( $layout ) {
    if ( is_page('faq') || is_page_template('page-faq.php') || is_page('shop') || is_page('about-us') || is_page('من نحن') ) {
        return 'page-builder'; // Astra's edge-to-edge layout
    }
    return $layout;
}

// Add CSS to ensure edge-to-edge
add_action('wp_head', 'force_full_width_css_fix');
function force_full_width_css_fix() {
    if ( is_page('faq') || is_page_template('page-faq.php') || is_page('shop') || is_page('about-us') || is_page('من نحن') ) {
        echo '<style>
            .ast-container, #content .ast-container { max-width: 100% !important; padding-left: 0 !important; padding-right: 0 !important; }
            #primary { margin: 0 !important; padding: 0 !important; width: 100% !important; max-width: 100% !important; }
            .site-content { padding-bottom: 0 !important; }
            .ast-plain-container.ast-no-sidebar #primary { padding: 0 !important; margin: 0 !important; }
        </style>';
    }
}

// 14. Auto-create FAQ Page if it doesn't exist
function auto_create_faq_page() {
    $check_page = get_page_by_path('faq');
    
    if (!$check_page) {
        $page_id = wp_insert_post(array(
            'post_title'    => 'الأسئلة المتكررة',
            'post_content'  => '',
            'post_status'   => 'publish',
            'post_type'     => 'page',
            'post_name'     => 'faq',
        ));
        
        if ($page_id) {
            update_post_meta($page_id, '_wp_page_template', 'page-faq.php');
        }
    } else {
        // Ensure template is set
        update_post_meta($check_page->ID, '_wp_page_template', 'page-faq.php');
    }
}
add_action('init', 'auto_create_faq_page');

// 15. Auto-create About Us Page if it doesn't exist
function auto_create_about_page() {
    $check_page = get_page_by_path('about-us');
    if (!$check_page) {
        $page_id = wp_insert_post(array(
            'post_title'    => 'من نحن',
            'post_content'  => 'أهلاً بكم في Gentle Shoes! نحن نفتخر بتقديم أرقى الأحذية الجزائرية التي تجمع بين الفخامة والراحة. هنا يمكنك كتابة قصة نجاح متجرك وهويتك التجارية.',
            'post_status'   => 'publish',
            'post_type'     => 'page',
            'post_name'     => 'about-us',
        ));
        if ($page_id) update_post_meta($page_id, '_wp_page_template', 'page-about.php');
    } else {
        update_post_meta($check_page->ID, '_wp_page_template', 'page-about.php');
    }
}
add_action('init', 'auto_create_about_page');

// 11. Algeria Wilayas & Shipping Rates Helper
function get_algeria_wilayas() {
    return array(
        '1' => array('name' => 'أدرار', 'office' => 800, 'home' => 1200),
        '2' => array('name' => 'الشلف', 'office' => 400, 'home' => 700),
        '3' => array('name' => 'الأغواط', 'office' => 500, 'home' => 800),
        '4' => array('name' => 'أم البواقي', 'office' => 450, 'home' => 750),
        '5' => array('name' => 'باتنة', 'office' => 450, 'home' => 750),
        '6' => array('name' => 'بجاية', 'office' => 400, 'home' => 700),
        '7' => array('name' => 'بسكرة', 'office' => 500, 'home' => 800),
        '8' => array('name' => 'بشار', 'office' => 800, 'home' => 1200),
        '9' => array('name' => 'البليدة', 'office' => 300, 'home' => 500),
        '10' => array('name' => 'البويرة', 'office' => 350, 'home' => 600),
        '11' => array('name' => 'تمنراست', 'office' => 1000, 'home' => 1500),
        '12' => array('name' => 'تبسة', 'office' => 500, 'home' => 800),
        '13' => array('name' => 'تلمسان', 'office' => 450, 'home' => 750),
        '14' => array('name' => 'تيارت', 'office' => 450, 'home' => 750),
        '15' => array('name' => 'تيزي وزو', 'office' => 350, 'home' => 600),
        '16' => array('name' => 'الجزائر العاصمة', 'office' => 300, 'home' => 450),
        '17' => array('name' => 'الجلفة', 'office' => 450, 'home' => 750),
        '18' => array('name' => 'جيجل', 'office' => 450, 'home' => 750),
        '19' => array('name' => 'سطيف', 'office' => 400, 'home' => 700),
        '20' => array('name' => 'سعيدة', 'office' => 500, 'home' => 800),
        '21' => array('name' => 'سكيكدة', 'office' => 450, 'home' => 750),
        '22' => array('name' => 'سيدي بلعباس', 'office' => 450, 'home' => 750),
        '23' => array('name' => 'عنابة', 'office' => 450, 'home' => 750),
        '24' => array('name' => 'قالمة', 'office' => 450, 'home' => 750),
        '25' => array('name' => 'قسنطينة', 'office' => 400, 'home' => 700),
        '26' => array('name' => 'المدية', 'office' => 350, 'home' => 600),
        '27' => array('name' => 'مستغانم', 'office' => 450, 'home' => 750),
        '28' => array('name' => 'المسيلة', 'office' => 450, 'home' => 750),
        '29' => array('name' => 'معسكر', 'office' => 450, 'home' => 750),
        '30' => array('name' => 'ورقلة', 'office' => 600, 'home' => 900),
        '31' => array('name' => 'وهران', 'office' => 450, 'home' => 700),
        '32' => array('name' => 'البيض', 'office' => 600, 'home' => 900),
        '33' => array('name' => 'إليزي', 'office' => 1000, 'home' => 1500),
        '34' => array('name' => 'برج بوعريريج', 'office' => 400, 'home' => 700),
        '35' => array('name' => 'بومرداس', 'office' => 300, 'home' => 500),
        '36' => array('name' => 'الطارف', 'office' => 500, 'home' => 800),
        '37' => array('name' => 'تندوف', 'office' => 1000, 'home' => 1500),
        '38' => array('name' => 'تيسمسيلت', 'office' => 450, 'home' => 750),
        '39' => array('name' => 'الوادي', 'office' => 500, 'home' => 800),
        '40' => array('name' => 'خنشلة', 'office' => 500, 'home' => 800),
        '41' => array('name' => 'سوق أهراس', 'office' => 500, 'home' => 800),
        '42' => array('name' => 'تيبازة', 'office' => 350, 'home' => 600),
        '43' => array('name' => 'ميلة', 'office' => 450, 'home' => 750),
        '44' => array('name' => 'عين الدفلة', 'office' => 400, 'home' => 700),
        '45' => array('name' => 'النعامة', 'office' => 600, 'home' => 900),
        '46' => array('name' => 'عين تموشنت', 'office' => 500, 'home' => 800),
        '47' => array('name' => 'غرداية', 'office' => 550, 'home' => 850),
        '48' => array('name' => 'غليزان', 'office' => 450, 'home' => 750),
        '49' => array('name' => 'تيميمون', 'office' => 900, 'home' => 1300),
        '50' => array('name' => 'برج باجي مختار', 'office' => 1200, 'home' => 1800),
        '51' => array('name' => 'أولاد جلال', 'office' => 550, 'home' => 850),
        '52' => array('name' => 'بني عباس', 'office' => 900, 'home' => 1300),
        '53' => array('name' => 'عين صالح', 'office' => 1000, 'home' => 1500),
        '54' => array('name' => 'عين قزام', 'office' => 1200, 'home' => 1800),
        '55' => array('name' => 'تقرت', 'office' => 600, 'home' => 900),
        '56' => array('name' => 'جانت', 'office' => 1100, 'home' => 1600),
        '57' => array('name' => 'المغير', 'office' => 600, 'home' => 900),
        '58' => array('name' => 'المنيعة', 'office' => 600, 'home' => 900),
    );
}

// 12. Homepage Theme Customizer Settings
add_action( 'customize_register', 'gentle_shoes_customize_register' );
function gentle_shoes_customize_register( $wp_customize ) {
    $wp_customize->add_section( 'gentle_shoes_homepage', array(
        'title'    => 'إعدادات الصفحة الرئيسية',
        'priority' => 30,
    ) );

    // Banner Settings
    $wp_customize->add_setting( 'home_banner_image', array('default' => '') );
    $wp_customize->add_control( new WP_Customize_Image_Control( $wp_customize, 'home_banner_image', array(
        'label'    => 'صورة البنر الرئيسية',
        'section'  => 'gentle_shoes_homepage',
    ) ) );

    $wp_customize->add_setting( 'home_banner_title', array('default' => 'Lacoste Nitro') );
    $wp_customize->add_control( 'home_banner_title', array(
        'label'    => 'عنوان البنر',
        'section'  => 'gentle_shoes_homepage',
        'type'     => 'text',
    ) );

    $wp_customize->add_setting( 'home_banner_desc', array('default' => 'الراحة في كل خطوة... لدينا كل ما تحتاجه.') );
    $wp_customize->add_control( 'home_banner_desc', array(
        'label'    => 'وصف البنر',
        'section'  => 'gentle_shoes_homepage',
        'type'     => 'textarea',
    ) );

    $wp_customize->add_setting( 'home_banner_btn_text', array('default' => 'تسوق الآن') );
    $wp_customize->add_control( 'home_banner_btn_text', array(
        'label'    => 'نص الزر',
        'section'  => 'gentle_shoes_homepage',
        'type'     => 'text',
    ) );

    $wp_customize->add_setting( 'home_banner_show_btn', array('default' => true) );
    $wp_customize->add_control( 'home_banner_show_btn', array(
        'label'    => 'إظهار الزر؟',
        'section'  => 'gentle_shoes_homepage',
        'type'     => 'checkbox',
    ) );
}

// 13. Enqueue Swiper.js for Slider
add_action( 'wp_enqueue_scripts', 'enqueue_swiper_assets' );
function enqueue_swiper_assets() {
    if ( is_front_page() || is_home() ) {
        wp_enqueue_style( 'swiper-css', 'https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.css' );
        wp_enqueue_script( 'swiper-js', 'https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.js', array(), null, true );
    }
}

// 14. Clear Stock Display: Show simple status instead of quantity
add_filter( 'woocommerce_get_stock_html', 'gentle_shoes_custom_stock_html', 10, 2 );
function gentle_shoes_custom_stock_html( $html, $product ) {
    if ( $product->is_in_stock() ) {
        return '<p class="stock in-stock" style="font-weight:700; color:#27ae60; margin:10px 0;">متوفر حالياً في المخزن ✓</p>';
    } else {
        return '<p class="stock out-of-stock" style="font-weight:700; color:#e74c3c; margin:10px 0;">غير متوفر حالياً ✗</p>';
    }
}

// --- AUTO-GENERATION: ENSURE PREMIUM SHOP PAGE EXISTS ---
function ensure_premium_shop_page_active() {
    $slug  = 'shop';
    $title = 'كل المنتجات';
    
    // Check if page exists by slug
    $existing_page = get_page_by_path($slug);
    
    if ( ! $existing_page ) {
        // Create the page if missing
        $page_id = wp_insert_post(array(
            'post_title'   => $title,
            'post_name'    => $slug,
            'post_content' => '',
            'post_status'  => 'publish',
            'post_type'    => 'page',
        ));
        
        if ( $page_id ) {
            update_post_meta($page_id, '_wp_page_template', 'page-products.php');
            // Flush rewrite rules to avoid 404
            flush_rewrite_rules();
        }
    } else {
        // Force the template on the existing shop page
        update_post_meta($existing_page->ID, '_wp_page_template', 'page-products.php');
    }
}
// Run on theme activation and admin init to be safe
add_action('after_switch_theme', 'ensure_premium_shop_page_active');
add_action('admin_init', 'ensure_premium_shop_page_active');

// Ensure search results for products also use a clean template if possible
function override_search_template_for_shop( $template ) {
    if ( is_search() && isset($_GET['s_name']) ) {
        $new_template = get_stylesheet_directory() . '/page-products.php';
        if ( file_exists( $new_template ) ) {
            return $new_template;
        }
    }
    return $template;
}
add_filter( 'template_include', 'override_search_template_for_shop', 99 );

/**
 * 🚀 AUTOMATED DESIGN INJECTOR & LAYOUT FIX
 * Automatically applies premium designs and forces FULL WIDTH for Astra.
 */
function inject_premium_designs($template) {
    if (is_admin()) return $template;

    $page_id = get_the_ID();
    $page_title = get_the_title($page_id);
    
    // Check if it's our target pages (Cart or Checkout)
    $is_checkout_custom = ($page_title === 'صفحة خروج' || is_checkout() || $page_id == get_option('woocommerce_checkout_page_id')) && !is_wc_endpoint_url('order-received');
    $is_cart_custom = ($page_title === 'عربة تسوق' || is_cart() || $page_id == get_option('woocommerce_cart_page_id'));
    $is_thankyou = is_wc_endpoint_url('order-received');

    if ($is_checkout_custom || $is_cart_custom || $is_thankyou) {
        // Force Astra Full Width via CSS injection
        add_action('wp_head', function() {
            echo '<style>
                /* Aggressive Astra Layout Force */
                .ast-container, #content .ast-container, .site-content .ast-container { max-width: 100% !important; width: 100% !important; padding: 0 !important; margin: 0 !important; }
                .site-content { padding-top: 0 !important; padding-bottom: 0 !important; }
                #primary { margin: 0 !important; padding: 0 !important; width: 100% !important; }
                .entry-content { margin: 0 !important; max-width: 100% !important; }
                .ast-separate-container .ast-article-post, .ast-separate-container .ast-article-single { padding: 0 !important; margin: 0 !important; }
                .ast-plain-container .site-content .ast-container { max-width: 100% !important; padding: 0 !important; }
            </style>';
        });

        // Strip the ast-container class from the main div for these pages
        add_filter( 'astra_attr_container', function( $attr ) {
            $attr['class'] = str_replace( 'ast-container', 'ast-container-full', $attr['class'] );
            return $attr;
        }, 99 );

        if ($is_checkout_custom) {
            $checkout_tpl = get_stylesheet_directory() . '/custom-checkout.php';
            if (file_exists($checkout_tpl)) return $checkout_tpl;
        }

        if ($is_thankyou) {
            $thanks_tpl = get_stylesheet_directory() . '/custom-thankyou.php';
            if (file_exists($thanks_tpl)) return $thanks_tpl;
        }
    }



    return $template;
}
add_filter('template_include', 'inject_premium_designs', 100);

// Force Astra to use Full-Width / Stretched layout for our special pages
add_filter('astra_get_option_content-layout', function($layout) {
    if (is_cart() || is_checkout() || is_wc_endpoint_url('order-received') || get_the_title() === 'عربة تسوق' || get_the_title() === 'صفحة خروج') {
        return 'page-builder'; // Full Width / Stretched
    }
    return $layout;
}, 99);

add_filter('astra_get_option_single-page-content-layout', function($layout) {
    if (is_cart() || is_checkout() || is_wc_endpoint_url('order-received') || get_the_title() === 'عربة تسوق' || get_the_title() === 'صفحة خروج') {
        return 'page-builder';
    }
    return $layout;
}, 99);

add_filter('astra_page_layout', function($layout) {
    if (is_cart() || is_checkout() || is_wc_endpoint_url('order-received') || get_the_title() === 'عربة تسوق' || get_the_title() === 'صفحة خروج') {
        return 'no-sidebar';
    }
    return $layout;
}, 99);

add_filter( 'astra_the_title_enabled', function( $enabled ) {
    if ( is_cart() || is_checkout() || is_wc_endpoint_url('order-received') || get_the_title() === 'عربة تسوق' || get_the_title() === 'صفحة خروج' ) {
        return false;
    }
    return $enabled;
});


// Ensure WooCommerce knows these are the correct pages and they contain the shortcodes
add_action('init', function() {
    // Cart Page
    $cart_page = get_page_by_title('عربة تسوق');
    if ($cart_page) {
        update_option('woocommerce_cart_page_id', $cart_page->ID);
        if (strpos($cart_page->post_content, '[woocommerce_cart]') === false) {
            wp_update_post(array('ID' => $cart_page->ID, 'post_content' => '[woocommerce_cart]'));
        }
    }

    // Checkout Page
    $checkout_page = get_page_by_title('صفحة خروج');
    if ($checkout_page) {
        update_option('woocommerce_checkout_page_id', $checkout_page->ID);
        if (strpos($checkout_page->post_content, '[woocommerce_checkout]') === false) {
            wp_update_post(array('ID' => $checkout_page->ID, 'post_content' => '[woocommerce_checkout]'));
        }
    }
});



/**
 * 📝 Debug Logger for Express Checkout
 */
function express_log($msg) {
    // Use WordPress uploads directory for compatibility with all hostings
    $upload_dir = wp_upload_dir();
    $log_dir = $upload_dir['basedir'] . '/express_logs';
    
    if (!file_exists($log_dir)) {
        wp_mkdir_p($log_dir);
    }
    
    $file = $log_dir . '/express_debug.log';
    $date = date('Y-m-d H:i:s');
    file_put_contents($file, "[$date] $msg\n", FILE_APPEND);
}


