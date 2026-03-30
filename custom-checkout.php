<?php
/**
 * Custom High-Conversion COD Checkout Template - STANDALONE FULL-WIDTH Edition
 * Renders as a completely independent page - no Astra sidebar/layout
 */

if ( ! defined( 'ABSPATH' ) ) { exit; }

// We need WP environment but bypass Astra layout completely
?>
<!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
    <meta charset="<?php bloginfo('charset'); ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>إتمام الطلب - <?php bloginfo('name'); ?></title>
    <?php wp_head(); ?>
    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@400;600;700;900&family=Outfit:wght@400;700;900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    <style>
    /* === Reset & Base === */
    *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

    :root {
        --red: #8b0000;
        --red-grad: linear-gradient(135deg, #8b0000 0%, #d91c1c 100%);
        --bg: #f4f4f7;
        --card: #ffffff;
        --text: #1a1a1a;
        --muted: #888;
        --radius: 20px;
        --shadow: 0 8px 40px rgba(0,0,0,0.07);
    }

    html, body {
        font-family: 'Cairo', sans-serif;
        background: var(--bg);
        color: var(--text);
        direction: rtl;
        min-height: 100vh;
        overflow-x: hidden;
    }

    /* === Top Nav Bar === */
    .co-navbar {
        background: #fff;
        padding: 16px 30px;
        display: flex;
        align-items: center;
        justify-content: space-between;
        box-shadow: 0 2px 20px rgba(0,0,0,0.06);
        position: sticky;
        top: 0;
        z-index: 100;
    }
    .co-logo {
        font-family: 'Outfit', sans-serif;
        font-weight: 900;
        font-size: 1.5rem;
        color: var(--red);
        text-decoration: none;
    }
    .co-back-btn {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        color: var(--red);
        border: 2px solid var(--red);
        border-radius: 30px;
        padding: 8px 22px;
        font-weight: 700;
        font-size: 0.95rem;
        text-decoration: none;
        transition: 0.3s;
    }
    .co-back-btn:hover { background: var(--red); color: #fff; }

    /* === Main Container === */
    .co-page {
        max-width: 1300px;
        margin: 0 auto;
        padding: 40px 20px 80px;
    }

    /* === Header === */
    .co-header {
        text-align: center;
        margin-bottom: 50px;
    }
    .co-header h1 {
        font-family: 'Outfit', sans-serif;
        font-size: 2.8rem;
        font-weight: 950;
        color: var(--text);
        margin-bottom: 25px;
        letter-spacing: -1px;
    }
    .co-steps {
        display: flex;
        justify-content: center;
        align-items: center;
        gap: 0;
        margin-bottom: 10px;
    }
    .co-step {
        display: flex;
        align-items: center;
        gap: 10px;
        color: #ccc;
        font-weight: 700;
        font-size: 0.95rem;
    }
    .co-step.active { color: var(--red); }
    .co-step-num {
        width: 32px; height: 32px;
        border-radius: 50%; border: 2px solid currentColor;
        display: flex; align-items: center; justify-content: center;
        font-size: 0.85rem; font-weight: 900;
    }
    .co-step.active .co-step-num { background: var(--red-grad); color: #fff; border-color: transparent; }
    .co-step-line { width: 60px; height: 2px; background: #eee; margin: 0 10px; }

    /* === Grid Layout === */
    .co-grid {
        display: grid;
        grid-template-columns: 1.5fr 1fr;
        gap: 30px;
        align-items: start;
    }
    @media (max-width: 1024px) {
        .co-grid { grid-template-columns: 1fr; }
        .co-header h1 { font-size: 2rem; }
    }

    /* === Cards === */
    .co-card {
        background: var(--card);
        border-radius: var(--radius);
        padding: 35px;
        box-shadow: var(--shadow);
        border: 1px solid #f0f0f0;
        margin-bottom: 25px;
    }
    .co-card-title {
        font-size: 1.3rem;
        font-weight: 900;
        margin-bottom: 28px;
        color: var(--text);
        display: flex;
        align-items: center;
        gap: 12px;
        border-right: 4px solid var(--red);
        padding-right: 15px;
    }
    .co-card-title i { color: var(--red); }

    /* === Form Fields === */
    .fld-grp { margin-bottom: 20px; }
    .fld-grp label {
        display: block;
        font-weight: 700;
        font-size: 0.9rem;
        color: #555;
        margin-bottom: 8px;
    }
    .input-wrap {
        position: relative;
        display: flex;
        align-items: center;
    }
    .input-wrap i {
        position: absolute;
        right: 16px;
        color: #bbb;
        font-size: 1rem;
        pointer-events: none;
    }
    .input-wrap input,
    .input-wrap select {
        width: 100%;
        padding: 14px 45px 14px 18px;
        border: 2px solid #eee;
        border-radius: 12px;
        font-family: 'Cairo', sans-serif;
        font-size: 0.95rem;
        font-weight: 600;
        color: var(--text);
        background: #fafafa;
        transition: border-color 0.3s;
        -webkit-appearance: none;
        appearance: none;
        outline: none;
    }
    .input-wrap input:focus,
    .input-wrap select:focus {
        border-color: var(--red);
        background: #fff;
    }
    .two-cols {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 20px;
    }
    @media (max-width: 600px) { .two-cols { grid-template-columns: 1fr; } }

    /* === Shipping Options === */
    .ship-grid {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 15px;
        margin-bottom: 20px;
    }
    .ship-opt {
        border: 2px solid #eee;
        border-radius: 16px;
        padding: 20px 15px;
        cursor: pointer;
        text-align: center;
        transition: 0.3s;
        position: relative;
    }
    .ship-opt.active {
        border-color: var(--red);
        background: #fff5f5;
    }
    .ship-opt i { font-size: 1.8rem; color: #ccc; margin-bottom: 10px; display: block; }
    .ship-opt.active i { color: var(--red); }
    .ship-opt h4 { font-size: 1rem; font-weight: 900; margin-bottom: 5px; }
    .ship-opt p { font-size: 0.8rem; color: var(--muted); }
    .ship-check {
        position: absolute;
        top: 10px;
        left: 10px;
        width: 20px; height: 20px;
        border-radius: 50%;
        border: 2px solid #eee;
        background: #fff;
        transition: 0.3s;
    }
    .ship-opt.active .ship-check {
        background: var(--red);
        border-color: var(--red);
    }

    /* === Order Summary Side === */
    .sum-item {
        display: flex;
        align-items: center;
        gap: 15px;
        padding: 15px 0;
        border-bottom: 1px solid #f5f5f5;
    }
    .sum-item:last-child { border-bottom: none; }
    .sum-img {
        width: 70px; height: 70px;
        border-radius: 12px;
        overflow: hidden;
        border: 1px solid #eee;
        flex-shrink: 0;
    }
    .sum-img img { width: 100%; height: 100%; object-fit: cover; }
    .sum-info { flex: 1; }
    .sum-name { font-weight: 800; font-size: 0.95rem; margin-bottom: 4px; }
    .sum-qty { font-size: 0.82rem; color: var(--muted); font-weight: 600; }
    .sum-price { font-weight: 900; color: var(--red); white-space: nowrap; }

    .total-rows { margin-top: 20px; }
    .t-row {
        display: flex;
        justify-content: space-between;
        padding: 10px 0;
        font-weight: 700;
        color: #555;
        border-bottom: 1px solid #f5f5f5;
    }
    .t-row.final {
        border-bottom: none;
        font-size: 1.3rem;
        font-weight: 950;
        color: var(--text);
        padding-top: 15px;
        margin-top: 5px;
    }

    /* === Submit Button === */
    .co-submit {
        width: 100%;
        background: var(--red-grad);
        color: #fff;
        border: none;
        border-radius: 16px;
        padding: 20px;
        font-family: 'Cairo', sans-serif;
        font-size: 1.2rem;
        font-weight: 950;
        cursor: pointer;
        transition: 0.3s;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 12px;
        margin-top: 25px;
        box-shadow: 0 10px 30px rgba(139,0,0,0.25);
    }
    .co-submit:hover { transform: translateY(-3px); box-shadow: 0 15px 40px rgba(139,0,0,0.35); }
    .co-submit:disabled { opacity: 0.7; cursor: not-allowed; transform: none; }

    /* === Trust Badges === */
    .trust-bar {
        display: flex;
        justify-content: center;
        gap: 30px;
        margin-top: 20px;
        flex-wrap: wrap;
    }
    .trust-item { display: flex; align-items: center; gap: 8px; color: var(--muted); font-size: 0.85rem; font-weight: 700; }
    .trust-item i { color: #28a745; }

    /* === Empty Cart State === */
    .co-empty-card {
        text-align: center;
        padding: 100px 20px;
    }
    .co-empty-card i { font-size: 5rem; color: #eee; display: block; margin-bottom: 25px; }
    .co-empty-card h2 { font-weight: 950; margin-bottom: 25px; font-size: 1.5rem; }

    @keyframes fadeInUp {
        from { opacity: 0; transform: translateY(20px); }
        to { opacity: 1; transform: translateY(0); }
    }
    .co-page { animation: fadeInUp 0.6s ease-out; }
    </style>
</head>
<body>

<!-- Top Navigation Bar -->
<nav class="co-navbar">
    <a href="<?php echo home_url('/'); ?>" class="co-logo">
        <?php bloginfo('name'); ?>
    </a>
    <a href="<?php echo home_url('/'); ?>" class="co-back-btn">
        <i class="fa fa-arrow-right"></i> العودة للرئيسية
    </a>
</nav>

<div class="co-page">

    <!-- Header with Steps -->
    <header class="co-header">
        <h1>إتمام الطلب</h1>
        <div class="co-steps">
            <div class="co-step active">
                <div class="co-step-num">1</div>
                <span>المعلومات</span>
            </div>
            <div class="co-step-line"></div>
            <div class="co-step">
                <div class="co-step-num">2</div>
                <span>الشحن</span>
            </div>
            <div class="co-step-line"></div>
            <div class="co-step">
                <div class="co-step-num">3</div>
                <span>التأكيد</span>
            </div>
        </div>
    </header>

    <?php if ( WC()->cart->is_empty() ) : ?>
        <div class="co-card co-empty-card">
            <i class="fa fa-shopping-basket"></i>
            <h2>سلة مشترياتك فارغة!</h2>
            <a href="<?php echo home_url('/'); ?>" class="co-submit" style="display:inline-flex; width:auto; padding:15px 50px; text-decoration:none;">
                العودة للرئيسية
            </a>
        </div>
    <?php else : ?>

    <form id="express_checkout_form">
        <div class="co-grid">

            <!-- LEFT: Customer Info Form -->
            <div class="co-main-col">

                <div class="co-card">
                    <div class="co-card-title"><i class="fa fa-user"></i> معلومات الإستلام</div>

                    <div class="fld-grp">
                        <label>الاسم الكامل *</label>
                        <div class="input-wrap">
                            <i class="fa fa-user"></i>
                            <input type="text" name="co_name" placeholder="مثال: أحمد محمد" required>
                        </div>
                    </div>

                    <div class="two-cols">
                        <div class="fld-grp">
                            <label>رقم الهاتف *</label>
                            <div class="input-wrap">
                                <i class="fa fa-phone"></i>
                                <input type="tel" name="co_phone" id="co_phone" placeholder="05XX XXX XXX" required>
                            </div>
                        </div>
                        <div class="fld-grp">
                            <label>رقم ثانٍ (اختياري)</label>
                            <div class="input-wrap">
                                <i class="fa fa-phone-square"></i>
                                <input type="tel" name="co_phone2" placeholder="06XX XXX XXX">
                            </div>
                        </div>
                    </div>

                    <div class="two-cols">
                        <div class="fld-grp">
                            <label>الولاية *</label>
                            <div class="input-wrap">
                                <i class="fa fa-globe"></i>
                                <select name="co_state" id="co_state" required>
                                    <option value="">اختر الولاية</option>
                                    <?php foreach (get_algeria_wilayas() as $code => $w) : ?>
                                        <option value="<?php echo esc_attr($w['name']); ?>" data-office="<?php echo $w['office']; ?>" data-home="<?php echo $w['home']; ?>">
                                            <?php echo $code . ' - ' . esc_html($w['name']); ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>
                        <div class="fld-grp">
                            <label>البلدية *</label>
                            <div class="input-wrap">
                                <i class="fa fa-map-marker"></i>
                                <input type="text" name="co_city" placeholder="اسم البلدية" required>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="co-card">
                    <div class="co-card-title"><i class="fa fa-truck"></i> طريقة التوصيل</div>

                    <div class="ship-grid">
                        <div class="ship-opt active" data-method="office">
                            <div class="ship-check"></div>
                            <i class="fa fa-envelope-o"></i>
                            <h4>توصيل للمكتب</h4>
                            <p>الاستلام من أقرب مكتب ياليدين</p>
                        </div>
                        <div class="ship-opt" data-method="home">
                            <div class="ship-check"></div>
                            <i class="fa fa-home"></i>
                            <h4>لباب المنزل</h4>
                            <p>توصيل لباب عنوانك مباشرة</p>
                        </div>
                    </div>

                    <input type="hidden" name="co_shipping" id="co_shipping" value="office">

                    <div id="home_address_wrap" style="display:none; animation: fadeInUp 0.3s;">
                        <div class="fld-grp">
                            <label>العنوان بالتدقيق</label>
                            <div class="input-wrap">
                                <i class="fa fa-location-arrow"></i>
                                <input type="text" name="co_address" placeholder="الحي، رقم المنزل، الشارع...">
                            </div>
                        </div>
                    </div>

                    <?php wp_nonce_field( 'express_checkout_action', 'express_checkout_nonce' ); ?>
                </div>

            </div>

            <!-- RIGHT: Order Summary -->
            <div class="co-sidebar-col">

                <div class="co-card" style="position: sticky; top: 90px;">
                    <div class="co-card-title"><i class="fa fa-shopping-bag"></i> سلة طلباتك</div>

                    <div class="co-items-list">
                        <?php
                        foreach ( WC()->cart->get_cart() as $cart_item_key => $cart_item ) {
                            $_product = $cart_item['data'];
                            if ( $_product && $_product->exists() && $cart_item['quantity'] > 0 ) {
                                $img = wp_get_attachment_image_src( $_product->get_image_id(), 'thumbnail' );
                                $img_url = $img ? $img[0] : wc_placeholder_img_src();
                                ?>
                                <div class="sum-item">
                                    <div class="sum-img"><img src="<?php echo $img_url; ?>" alt=""></div>
                                    <div class="sum-info">
                                        <div class="sum-name"><?php echo $_product->get_name(); ?></div>
                                        <div class="sum-qty"><?php echo $cart_item['quantity']; ?> قطعة</div>
                                    </div>
                                    <div class="sum-price"><?php echo WC()->cart->get_product_subtotal( $_product, $cart_item['quantity'] ); ?></div>
                                </div>
                                <?php
                            }
                        }
                        ?>
                    </div>

                    <div class="total-rows">
                        <div class="t-row">
                            <span>مجموع المنتجات:</span>
                            <span><?php echo WC()->cart->get_cart_subtotal(); ?></span>
                        </div>
                        <div class="t-row">
                            <span>تكلفة الشحن:</span>
                            <span id="co_ship_price" style="color:#28a745; font-weight:900;">يُحسب لاحقاً</span>
                        </div>
                        <div class="t-row final">
                            <span>الإجمالي:</span>
                            <span id="co_total_price"><?php wc_cart_totals_order_total_html(); ?></span>
                        </div>
                    </div>

                    <button type="submit" id="main_submit_btn" class="co-submit">
                        <i class="fa fa-check-circle"></i>
                        <span>تأكيد الطلب الآن</span>
                    </button>

                    <div class="trust-bar">
                        <div class="trust-item"><i class="fa fa-shield"></i> دفع عند الاستلام</div>
                        <div class="trust-item"><i class="fa fa-map-marker"></i> توصيل 58 ولاية</div>
                        <div class="trust-item"><i class="fa fa-lock"></i> تسوق آمن 100%</div>
                    </div>
                </div>

            </div>
        </div>
    </form>

    <?php endif; ?>
</div>

<script type="text/javascript">
jQuery(document).ready(function($){

    // Check bundle free shipping
    var isBundleFree = false;
    var currentBadge = "";
    <?php
    foreach (WC()->cart->get_cart() as $item) {
        if (isset($item['bundle_name']) || (isset($item['bundle_badge']) && strpos($item['bundle_badge'], 'مجاني') !== false)) { ?>
            isBundleFree = true;
            currentBadge = "<?php echo isset($item['bundle_badge']) ? esc_js($item['bundle_badge']) : ''; ?>";
        <?php break; }
    } ?>

    var prodSubtotal = parseFloat("<?php echo WC()->cart->get_subtotal(); ?>");

    function updateSummary() {
        var stateOpt = $('#co_state option:selected');
        var method = $('#co_shipping').val() || 'office';
        var methodKey = (method === 'office') ? 'office' : 'home';
        var shipCost = 0;

        if (isBundleFree) {
            if (currentBadge.indexOf('للبيت') !== -1) {
                $('.ship-opt[data-method="office"]').hide();
                $('.ship-opt[data-method="home"]').addClass('active').siblings().removeClass('active');
                $('#co_shipping').val('home');
                method = 'home';
                $('#home_address_wrap').show();
            }
            shipCost = 0;
        } else if (stateOpt.val() !== '') {
            shipCost = parseFloat(stateOpt.data(methodKey)) || 0;
        }

        var total = prodSubtotal + shipCost;
        $('#co_ship_price').text(shipCost === 0 ? 'مجاني 🎉' : shipCost + ' د.ج');
        $('#co_total_price').text(total.toLocaleString('fr-DZ', {minimumFractionDigits: 2}) + ' د.ج');
    }

    $(document).on('click', '.ship-opt', function(){
        if (isBundleFree && currentBadge.indexOf('مجاني للبيت') !== -1 && $(this).data('method') === 'office') return;
        $('.ship-opt').removeClass('active');
        $(this).addClass('active');
        var method = $(this).data('method');
        $('#co_shipping').val(method);
        if (method === 'home') { $('#home_address_wrap').slideDown(300); }
        else { $('#home_address_wrap').slideUp(300); }
        updateSummary();
    });

    $('#co_state').on('change', updateSummary);
    updateSummary();

    // Form Submit
    $('#express_checkout_form').on('submit', function(e){
        e.preventDefault();
        var btn = $('#main_submit_btn');
        var phone = $('#co_phone').val();

        if (phone.length < 10) { alert('يرجى إدخال رقم هاتف صحيح (10 أرقام)'); return; }
        var state = $('#co_state').val();
        if (!state) { alert('يرجى اختيار الولاية'); return; }
        var shipping = $('#co_shipping').val();
        if (shipping === 'home' && !$('input[name="co_address"]').val()) {
            alert('يرجى إدخال عنوان المنزل.');
            return;
        }

        btn.prop('disabled', true).find('span').text('جارِ المعالجة...');

        var data = {
            action: 'process_express_checkout',
            nonce: $('#express_checkout_nonce').val(),
            name: $('input[name="co_name"]').val(),
            phone: phone,
            phone2: $('input[name="co_phone2"]').val(),
            state: state,
            city: $('input[name="co_city"]').val(),
            shipping: shipping,
            address: $('input[name="co_address"]').val()
        };

        $.post('<?php echo admin_url("admin-ajax.php"); ?>', data, function(response){
            if (response.success) {
                window.location.href = response.data.redirect;
            } else {
                alert('خطأ: ' + (response.data ? response.data.message : 'يرجى المحاولة مرة أخرى'));
                btn.prop('disabled', false).find('span').text('تأكيد الطلب الآن');
            }
        }).fail(function(){
            alert('حدث خطأ في الاتصال، يرجى المحاولة مرة أخرى.');
            btn.prop('disabled', false).find('span').text('تأكيد الطلب الآن');
        });
    });
});
</script>

<?php wp_footer(); ?>
</body>
</html>
