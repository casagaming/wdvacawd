<?php
/**
 * Custom Single Product Template — PRECISE REPLICATION & LOGIC FIX
 * Reference: https://gentleshoes.vip/gentleshoes/products/LacosteNitro
 */
defined('ABSPATH') || exit;

global $product;
if (!$product || !($product instanceof WC_Product)) return;

$pid        = $product->get_id();
$main_img   = $product->get_image_id();
$gallery    = $product->get_gallery_image_ids();
$all_imgs   = array_filter(array_merge([$main_img], $gallery));
$attrs      = $product->get_attributes();
$variations = $product->get_available_variations();
$price      = floatval($product->get_price());
$is_var     = $product->is_type('variable');
$offers     = get_post_meta($pid, '_bundle_offers', true) ?: [];
$enable_bd  = get_post_meta($pid, '_enable_bundle_system', true);
$hide_main  = get_post_meta($pid, '_hide_main_product_offer', true);

// Build attribute options
$var_opts = [];
foreach ($attrs as $aname => $attr) {
    $label = rawurldecode(rawurldecode(wc_attribute_label($aname)));
    if (strpos($label, '%') !== false) { $label = urldecode(urldecode($label)); }
    $options = $attr->get_options();
    $wc_key  = 'attribute_' . $aname;
    if ($attr->is_taxonomy()) {
        $terms = get_terms(['taxonomy' => $aname, 'include' => $options, 'orderby' => 'include']);
        if (!is_wp_error($terms)) {
            $vals = [];
            foreach ($terms as $t) { $vals[] = ['slug' => $t->slug, 'name' => rawurldecode($t->name)]; }
            $var_opts[$label] = ['key' => $wc_key, 'vals' => $vals];
        }
    } else {
        $vals = [];
        foreach ($options as $opt) { $vals[] = ['slug' => $opt, 'name' => rawurldecode($opt)]; }
        $var_opts[$label] = ['key' => $wc_key, 'vals' => $vals];
    }
}

$has_bundles = false;
if ($enable_bd === 'yes' && is_array($offers)) {
    foreach ($offers as $o) {
        if (!empty($o['price']) && (empty($o['hide']) || $o['hide'] !== 'yes')) { $has_bundles = true; break; }
    }
}

$nonce    = wp_create_nonce('express_checkout_action');
$ajax_url = admin_url('admin-ajax.php');
$cart_url = wc_get_cart_url();
$p_img    = wp_get_attachment_image_url($main_img, 'thumbnail');
?>

<style>
:root{--red:#c0392b;--red-glow:rgba(192,57,43,0.3);--border:#ececec;--bg:#f9f9f9}
.gsp{display:flex;gap:40px;direction:rtl;max-width:1100px;margin:0 auto;padding:30px 20px;font-family:'Cairo',sans-serif;box-sizing:border-box;background:#fff}
.gsp-gal{flex:0 0 45%}
.gsp-main{border-radius:24px;overflow:hidden;background:var(--bg);aspect-ratio:1;position:relative;box-shadow:0 10px 30px rgba(0,0,0,0.05)}
.gsp-main img{width:100%;height:100%;object-fit:cover;display:block}
.gsp-info{flex:1;min-width:0}
.gsp-title{font-size:2rem;font-weight:900;color:#111;margin:0;line-height:1.2}
.gsp-title span{background:#ffc107;color:#fff;font-size:.7rem;padding:3px 10px;border-radius:8px;vertical-align:middle;margin-right:10px}
.gsp-rating{color:#ffc107;font-size:.9rem;margin:10px 0;display:flex;align-items:center;gap:4px}
.gsp-price{margin:18px 0;display:flex;align-items:center;gap:15px}
.gsp-price .old-p{color:#999;text-decoration:line-through;font-size:1.1rem}
.gsp-price .new-p{color:var(--red);font-size:2.4rem;font-weight:900}

.gs-top-alert{background:#fdfdfe;border-radius:16px;padding:16px 20px;border:1.5px solid #eee;color:#555;font-size:.9rem;line-height:1.7;text-align:center;margin-bottom:20px;box-shadow:0 4px 12px rgba(0,0,0,0.02)}

/* ---- Premium Mini-Checkout Form ---- */
.gs-form-section{
    background:#fff;
    border-radius:24px;
    padding:28px;
    border:1.5px solid #f0f0f0;
    box-shadow:0 15px 45px rgba(0,0,0,.06);
}
.gs-form-card-title{
    font-size:1rem;
    font-weight:900;
    color:#111;
    margin-bottom:20px;
    display:flex;
    align-items:center;
    gap:10px;
    border-right:4px solid var(--red);
    padding-right:12px;
    padding-bottom:14px;
    border-bottom:1px solid #f5f5f5;
}
.gs-form-card-title i{color:var(--red);}
.gs-fld{
    position:relative;
    margin-bottom:14px;
}
.gs-fld label{
    display:block;
    font-size:0.82rem;
    font-weight:800;
    color:#666;
    margin-bottom:6px;
}
.gs-fld input,.gs-fld select{
    width:100%;
    padding:13px 44px 13px 14px;
    border-radius:14px;
    border:2px solid #eee;
    background:#fafafa;
    font-family:'Cairo',sans-serif;
    font-size:.95rem;
    color:#333;
    transition:.25s;
    outline:none;
    -webkit-appearance:none;
    appearance:none;
}
.gs-fld input:focus,.gs-fld select:focus{
    border-color:var(--red);
    background:#fff;
    box-shadow:0 0 0 4px rgba(192,57,43,0.08);
}
.gs-fld i{
    position:absolute;
    right:15px;
    bottom:14px;
    color:#bbb;
    font-size:1rem;
    pointer-events:none;
}
.gs-grid-2{display:grid;grid-template-columns:1fr 1fr;gap:12px}

/* Delivery Options */
.gs-delivery-choice{display:grid;grid-template-columns:1fr 1fr;gap:12px;margin:18px 0 14px}
.gs-delivery-opt{
    border:2px solid #eee;
    border-radius:16px;
    padding:14px 12px;
    text-align:center;
    cursor:pointer;
    transition:.25s;
    background:#fff;
    position:relative;
}
.gs-delivery-opt.on{border-color:var(--red);background:#fff8f8;box-shadow:0 6px 18px var(--red-glow)}
.gs-delivery-opt i{display:block;font-size:1.4rem;color:#bbb;margin-bottom:7px}
.gs-delivery-opt.on i{color:var(--red)}
.gs-delivery-opt span{display:block;font-weight:900;font-size:.82rem;color:#333}
.gs-delivery-opt small{display:block;color:#999;font-size:.72rem;margin-top:3px}
.gs-dot{position:absolute;top:9px;right:9px;width:14px;height:14px;border-radius:50%;border:2px solid #ddd;background:#fff;transition:.25s}
.gs-delivery-opt.on .gs-dot{border-color:var(--red);background:var(--red)}

.gs-ship-price-row{
    display:flex;justify-content:space-between;align-items:center;
    background:#f8f8f8;border-radius:12px;padding:12px 16px;
    font-weight:800;font-size:.9rem;color:#555;
    margin-bottom:18px;
}
.gs-ship-val{background:var(--red);color:#fff;padding:4px 14px;border-radius:10px;font-size:.78rem;font-weight:900}

/* Bundle / Offer Selection */
.gs-bdl-list{margin:15px 0;display:flex;flex-direction:column;gap:12px}
.gs-bdl-item{background:#fff;border:2px solid #f0f0f0;border-radius:18px;padding:16px;display:flex;flex-wrap:wrap;align-items:center;cursor:pointer;position:relative;transition:.3s}
.gs-bdl-item.on{border-color:#111;background:#fafafa;box-shadow:0 8px 22px rgba(0,0,0,0.07)}
.gs-bdl-item.on::before{content:'\f058';font-family:'Font Awesome 5 Free';font-weight:900;position:absolute;top:10px;left:14px;color:#111;font-size:1.2rem;background:#fff;border-radius:50%;z-index:2}
.gs-bdl-price-side{width:90px;text-align:right;font-weight:900;font-size:1.25rem;color:#111;border-left:1.5px solid #f0f0f0;margin-left:16px}
.gs-bdl-info-side{flex:1}
.gs-bdl-name-txt{font-weight:800;font-size:.95rem;color:#333}
.gs-bdl-badge-val{background:#e8fdf0;color:#27ae60;font-size:.72rem;padding:3px 10px;border-radius:10px;font-weight:800;margin-bottom:5px;display:inline-block}

.gs-bvars-nested{width:100%;margin-top:18px;border-top:1.5px solid #f0f0f0;padding-top:18px;display:none}
.gs-bvars-grid{display:flex;flex-direction:row-reverse;gap:18px;overflow:visible}
.gs-bvar-unit{flex:1;min-width:0;border-left:1.5px solid #f5f5f5;padding-left:14px}
.gs-bvar-unit:last-child{border-left:none;padding-left:0}

.gs-attr-label{font-size:0.8rem;font-weight:700;color:#777;margin-bottom:8px;display:block}

.gs-vars-embedded{margin-top:18px}
.gs-vars-ttl{font-weight:900;font-size:.85rem;color:#222;margin-bottom:10px;display:flex;align-items:center;gap:8px}
.gs-t-wrap{display:flex;align-items:center;gap:8px;border:2px solid #eee;padding:7px 12px;border-radius:12px;cursor:pointer;background:#fff;transition:.25s;min-width:48px;justify-content:center}
.gs-t-wrap.on{border-color:var(--red);background:#fff8f8;box-shadow:0 4px 12px var(--red-glow)}
.gs-t-wrap.on .gs-t{background:var(--red);color:#fff;border-color:var(--red)}
.gs-t{width:22px;height:22px;border-radius:50%;display:flex;align-items:center;justify-content:center;border:1.5px solid #eee;font-size:.7rem;font-weight:900;transition:.2s;background:#f9f9f9;color:#777}
.gs-t-lbl{font-size:.82rem;color:#333;font-weight:800}

.gs-t-missing{border-color:#ff4d4d!important;animation:gsPulse 0.8s infinite}
@keyframes gsPulse{0%{box-shadow:0 0 0 0 rgba(255,77,77,0.4)}70%{box-shadow:0 0 0 10px rgba(255,77,77,0)}100%{box-shadow:0 0 0 0 rgba(255,77,77,0)}}

/* Main CTA Button */
.gs-final-checkout{
    background:var(--red);
    color:#fff;
    width:100%;
    padding:20px 18px;
    border-radius:20px;
    border:none;
    font-family:'Cairo',sans-serif;
    font-weight:900;
    font-size:1.1rem;
    cursor:pointer;
    box-shadow:0 10px 28px var(--red-glow);
    text-align:center;
    transition:.3s;
    position:relative;
    overflow:visible;
    display:flex;
    align-items:center;
    justify-content:center;
    gap:10px;
}
.gs-final-checkout:hover{transform:translateY(-3px);box-shadow:0 15px 35px var(--red-glow)}
.gs-final-checkout:disabled{opacity:.6;cursor:not-allowed;transform:none}
.gs-price-sticker{
    position:absolute;
    top:-12px;
    left:12px;
    background:#fff;
    color:var(--red);
    padding:4px 14px;
    border-radius:10px;
    font-weight:900;
    font-size:.9rem;
    box-shadow:0 4px 12px rgba(0,0,0,0.12);
    z-index:11;
    border:1.5px solid var(--red);
}

.gs-sticky-footer{position:fixed;bottom:0;left:0;right:0;background:rgba(255,255,255,0.98);backdrop-filter:blur(10px);border-top:1.5px solid #f0f0f0;z-index:1000;padding:12px 20px;display:flex;justify-content:space-between;align-items:center;box-shadow:0 -5px 20px rgba(0,0,0,0.05);cursor:pointer}
.gs-sticky-info{display:flex;align-items:center;gap:12px}
.gs-sticky-icon{width:40px;height:40px;background:#f9f9f9;border-radius:50%;display:flex;align-items:center;justify-content:center;color:#111;font-size:1.1rem}
.gs-sticky-txt{font-weight:900;font-size:.9rem;color:#333}
.gs-sticky-txt small{display:block;color:#888;font-size:.75rem}
.gs-sticky-arrow{color:#ccc;transition:.3s}
.gs-sticky-footer.on .gs-sticky-arrow{transform:rotate(180deg)}

@media(max-width:768px){.gsp{flex-direction:column;gap:20px}.gsp-gal{flex:none}.gs-grid-2{grid-template-columns:1fr}}
</style>

<div id="product-<?php the_ID(); ?>" <?php wc_product_class('gsp'); ?>>
    <div class="gsp-gal">
        <div class="gsp-main"><img id="gs-main-img" src="<?php echo esc_url(wp_get_attachment_image_url($main_img, 'large')); ?>"></div>
    </div>
    <div class="gsp-info">
        <h1 class="gsp-title"><?php the_title(); ?></h1>
        <div class="gsp-price">
            <?php if ($product->get_regular_price()) : ?><span class="old-p"><?php echo $product->get_regular_price(); ?> دج</span><?php endif; ?>
            <span class="new-p"><?php echo $price; ?> دج</span>
        </div>

        <?php $top_text = get_post_meta($pid, '_bundle_top_text', true) ?: 'اختر اللون والمقاس، املأ معلوماتك كاملة، ثم اضغط على زر الشراء لإتمام طلبك 🚀'; ?>
        <div class="gs-top-alert"><?php echo esc_html($top_text); ?></div>

        <div class="gs-form-section">

            <!-- Section: Customer Info -->
            <div class="gs-form-card-title"><i class="fa fa-user"></i> معلومات الاستلام</div>

            <div class="gs-fld">
                <label>الاسم الكامل *</label>
                <i class="fa fa-user"></i>
                <input type="text" id="gf-name" placeholder="مثال: أحمد محمد" required>
            </div>

            <div class="gs-grid-2" style="margin-bottom:12px">
                <div class="gs-fld">
                    <label>رقم الهاتف *</label>
                    <i class="fa fa-phone"></i>
                    <input type="tel" id="gf-phone" placeholder="05XX XXX XXX" required>
                </div>
                <div class="gs-fld">
                    <label>رقم ثانٍ (اختياري)</label>
                    <i class="fa fa-phone-square"></i>
                    <input type="tel" id="gf-phone2" placeholder="06XX XXX XXX">
                </div>
            </div>

            <div class="gs-grid-2" style="margin-bottom:14px">
                <div class="gs-fld">
                    <label>الولاية *</label>
                    <i class="fa fa-map-marker"></i>
                    <select id="gf-state" required>
                        <option value="">اختر الولاية</option>
                        <?php foreach (get_algeria_wilayas() as $code => $w) : ?>
                        <option value="<?php echo esc_attr($w['name']); ?>" data-office="<?php echo $w['office']; ?>" data-home="<?php echo $w['home']; ?>"><?php echo $code . ' - ' . esc_html($w['name']); ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="gs-fld">
                    <label>البلدية *</label>
                    <i class="fa fa-map-marker-alt"></i>
                    <input type="text" id="gf-city" placeholder="اسم البلدية" required>
                </div>
            </div>

            <!-- Section: Delivery Method -->
            <div class="gs-form-card-title" style="margin-top:20px;"><i class="fa fa-truck"></i> طريقة التوصيل</div>

            <div class="gs-delivery-choice">
                <div class="gs-delivery-opt on" data-method="office">
                    <div class="gs-dot"></div>
                    <i class="fa fa-envelope-o"></i>
                    <span>توصيل للمكتب</span>
                    <small>أقرب مكتب ياليدين</small>
                </div>
                <div class="gs-delivery-opt" data-method="home">
                    <div class="gs-dot"></div>
                    <i class="fa fa-home"></i>
                    <span>لباب المنزل</span>
                    <small>توصيل مباشر للعنوان</small>
                </div>
                <input type="hidden" id="gf-shipping" value="office">
            </div>

            <div id="gf-addr-wrap" style="display:none; margin-bottom:14px">
                <div class="gs-fld">
                    <label>العنوان بالتفصيل *</label>
                    <i class="fa fa-location-arrow"></i>
                    <input type="text" id="gf-addr" placeholder="الحي، رقم المنزل، الشارع...">
                </div>
            </div>

            <div class="gs-ship-price-row">
                <span>🚚 سعر التوصيل</span>
                <span class="gs-ship-val" id="gs-shipping-price">مجاناً</span>
            </div>

            <!-- Section: Offer Selection -->
            <?php if ($has_bundles || !empty($var_opts)) : ?>
            <div class="gs-form-card-title" style="margin-top:4px;"><i class="fa fa-gift"></i> اختر عرضك</div>
            <div class="gs-bdl-list">
                <?php if ($hide_main !== 'yes') : ?>
                    <div class="gs-bdl-item on" data-name="none" data-price="<?php echo $price; ?>" data-items="1">
                        <?php if ($has_bundles) : ?>
                        <div class="gs-bdl-price-side"><?php echo $price; ?> دج</div>
                        <div class="gs-bdl-info-side">
                            <span class="gs-bdl-badge-val">توصيل مجاني</span>
                            <div class="gs-bdl-name-txt">شراء قطعة واحدة فقط</div>
                        </div>
                        <?php endif; ?>
                        <div class="gs-bvars-nested" style="display:block">
                            <div class="gs-bvars-grid">
                                <div class="gs-bvar-unit">
                                    <?php if ($has_bundles) : ?>
                                    <div class="gs-vars-ttl" style="color:var(--red); font-size:0.85rem">📦 المنتج الأساسي:</div>
                                    <?php endif; ?>
                                    <?php foreach ($var_opts as $label => $data) : ?>
                                    <div class="gsp-attr" style="margin-bottom:12px">
                                        <?php $arLabel = (strpos(strtolower($label), 'color') !== false || strpos($label, 'لون') !== false) ? 'اللون' : 'المقاس'; ?>
                                        <span class="gs-attr-label"><?php echo $arLabel; ?>:</span>
                                        <div class="gsp-tiles gs-b-tiles" data-bitem="0" data-battr="<?php echo esc_attr($data['key']); ?>" style="display:flex; justify-content:flex-start; gap:6px; flex-wrap:wrap">
                                            <?php foreach ($data['vals'] as $v) : ?>
                                            <div class="gs-t-wrap" style="padding:4px 8px; min-width:50px; height:36px" data-value="<?php echo esc_attr($v['slug']); ?>">
                                                <div class="gs-t-lbl" style="font-size:0.75rem"><?php echo esc_html($v['name']); ?></div>
                                            </div>
                                            <?php endforeach; ?>
                                        </div>
                                    </div>
                                    <?php endforeach; ?>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endif; ?>
                <?php foreach ($offers as $i => $off) : ?>
                <div class="gs-bdl-item" data-name="<?php echo esc_attr($off['name']); ?>" data-price="<?php echo esc_attr($off['price']); ?>" data-items="<?php echo esc_attr($off['items']); ?>">
                    <div class="gs-bdl-price-side"><?php echo esc_html($off['price']); ?> دج</div>
                    <div class="gs-bdl-info-side">
                        <span class="gs-bdl-badge-val"><?php echo esc_html($off['label']); ?></span>
                        <div class="gs-bdl-name-txt"><?php echo esc_html($off['name']); ?> 🌟</div>
                    </div>
                    <div class="gs-bvars-nested"></div>
                </div>
                <?php endforeach; ?>
            </div>
            <?php endif; ?>

            <div id="gs-bundle-selectors" style="display:none"></div>

            <!-- CTA: Direct Order Button + Conditional Cart Button -->
            <div style="margin-top:28px; display:flex; flex-direction:column; gap:12px;">
                <button type="button" class="gs-final-checkout" id="gs-confirm-buy">
                    <div class="gs-price-sticker" id="gs-btn-tag"><?php echo $price; ?> دج</div>
                    <i class="fa fa-check-circle"></i>
                    اضغط لإرسال طلبك الآن 🚀
                </button>



                <div style="text-align:center; color:#aaa; font-size:0.82rem; font-weight:700;">
                    <i class="fa fa-shield" style="color:#28a745;"></i> دفع عند الاستلام — توصيل لـ 58 ولاية
                </div>
            </div>
        </div>
    </div>
</div>

<div class="gs-sticky-footer">
    <div class="gs-sticky-info">
        <div class="gs-sticky-icon"><i class="fa fa-shopping-cart"></i></div>
        <div class="gs-sticky-txt">ملخص الطلبية <small id="gs-sticky-sub">منتج واحد محدد</small></div>
    </div>
    <div id="gs-sticky-price" style="font-weight:900; color:var(--red); font-size:1.2rem">0 دج</div>
    <div class="gs-sticky-arrow"><i class="fa fa-chevron-up"></i></div>
</div>

<script>
jQuery(function($){
    'use strict';
    var PID=<?php echo $pid;?>, BASE=<?php echo $price;?>, NONCE='<?php echo $nonce;?>', AJAX='<?php echo esc_url($ajax_url);?>', CARTURL='<?php echo esc_url($cart_url);?>';
    var VARS=<?php echo json_encode($variations);?>, ISVAR=<?php echo $is_var ? 'true' : 'false'; ?>, selVars={};
    var Wilayas=<?php echo json_encode(get_algeria_wilayas()); ?>;

    function updateSummary(){
        var $b = $('.gs-bdl-item.on'), nm = $b.data('name')||'none';
        var bPrice = parseFloat($b.data('price')) || BASE;
        
        var stateName = $('#gf-state').val(), method = $('#gf-shipping').val();
        var ship = 0, isFree = (nm !== 'none');
        
        if(!isFree && stateName){
           for(var code in Wilayas){
               if(Wilayas[code].name === stateName){
                   ship = parseFloat(Wilayas[code][method]) || 0;
                   break;
               }
           }
        }
        
        var total = bPrice + ship;
        $('#gs-shipping-price').text(ship === 0 ? 'مجاناً' : ship + ' دج');
        $('#gs-sticky-price, #gs-btn-tag').text(total + ' دج');
        $('#gs-sticky-sub').text( (nm==='none' ? 'منتج واحد' : nm) + ' محدد' );
    }

    $(document).on('click', '.gs-delivery-opt', function(){
        $('.gs-delivery-opt').removeClass('on'); $(this).addClass('on');
        var method = $(this).data('method');
        $('#gf-shipping').val(method);
        
        if(method === 'home') {
            $('#gf-addr-wrap').slideDown();
        } else {
            $('#gf-addr-wrap').slideUp();
        }
        
        updateSummary();
    });

    $('#gf-state').on('change', updateSummary);

    $(document).on('click', '.gs-bdl-item', function(e){
        if($(e.target).closest('.gs-t-wrap, .gs-bvars-nested').length) return;
        $('.gs-t-wrap').removeClass('on');
        $('.gs-bdl-item').removeClass('on').find('.gs-bvars-nested').slideUp(300);
        $(this).addClass('on');
        
        var nm = $(this).data('name') || 'none';



        var items = parseInt($(this).data('items')), $nested = $(this).find('.gs-bvars-nested');
        
        if(!$nested.children().length && nm !== 'none'){
            var $grid = $('<div class="gs-bvars-grid"></div>');
            for(var i=0; i<items; i++){
                var $unit = $('<div class="gs-bvar-unit"></div>');
                $unit.append('<div class="gs-vars-ttl" style="color:var(--red); font-size:0.85rem">📦 القطعة رقم '+(i+1)+':</div>');
                <?php foreach ($var_opts as $label => $data) : ?>
                var $attr = $('<div class="gsp-attr" style="margin-bottom:12px"></div>');
                var arLabel = "<?php echo strpos(strtolower($label), 'color') !== false || strpos($label, 'لون') !== false ? 'اللون' : 'المقاس'; ?>";
                $attr.append('<span class="gs-attr-label">'+arLabel+':</span>');
                var $tiles = $('<div class="gsp-tiles gs-b-tiles" data-bitem="'+i+'" data-battr="<?php echo $data['key']; ?>" style="display:flex; justify-content:flex-start; gap:6px; flex-wrap:wrap"></div>');
                <?php foreach ($data['vals'] as $v) : ?>
                $tiles.append('<div class="gs-t-wrap" style="padding:4px 8px; min-width:50px; height:36px" data-value="<?php echo $v['slug']; ?>"><div class="gs-t-lbl" style="font-size:0.75rem"><?php echo $v['name']; ?></div></div>');
                <?php endforeach; ?>
                $attr.append($tiles); $unit.append($attr);
                <?php endforeach; ?>
                $grid.append($unit);
            }
            $nested.append($grid);
        }
        $nested.slideDown(300);
        updateSummary();
    });

    $(document).on('click', '.gs-t-wrap', function(){
        var $group = $(this).closest('.gsp-tiles');
        $group.find('.gs-t-wrap').removeClass('on gs-t-missing');
        $(this).addClass('on');
    });

        function submitOrder(isDirectBuy){
            var name = $('#gf-name').val(), phone = $('#gf-phone').val(), state = $('#gf-state').val(), city = $('#gf-city').val();
            
            if(isDirectBuy){
                if(!name || !phone || !state || !city){ alert('يرجى ملء جميع الحقول المطلوبة.'); return; }
                if($('#gf-shipping').val() === 'home' && !$('#gf-addr').val()){ alert('يرجى إدخال عنوان المنزل.'); return; }
            }
            
            var $b = $('.gs-bdl-item.on'), isBundle = ($b.data('name') !== 'none');
            var bd = {}, ok = true;
            $('.gs-t-wrap').removeClass('gs-t-missing');
            $b.find('.gs-bvar-unit').each(function(i){
                bd[i] = {};
                $(this).find('.gs-b-tiles').each(function(){
                    var attr = $(this).data('battr'), $on = $(this).find('.gs-t-wrap.on');
                    if(!$on.length){
                        $(this).find('.gs-t-wrap').addClass('gs-t-missing');
                        ok = false;
                    } else {
                        bd[i][attr] = $on.data('value');
                    }
                });
            });

            if(!ok){ alert('⚠️ يرجى اختيار اللون والمقاس لجميع المنتجات المحددة!'); return; }

            var $btn = isDirectBuy ? $('#gs-confirm-buy') : $('#gs-add-to-cart');
            var oldTxt = $btn.text();
            $btn.prop('disabled', true).css('opacity', '0.6').text(isDirectBuy ? 'جاري إرسال الطلب...' : 'جاري الإضافة...');

            var payload = {
                action: isDirectBuy ? 'process_express_checkout' : 'add_to_cart_bundle', 
                nonce: NONCE, product_id: PID,
                name: name, phone: phone, phone2: $('#gf-phone2').val(), state: state, city: city, address: $('#gf-addr').val(),
                shipping: $('#gf-shipping').val(),
                bundle_info: JSON.stringify({name:$b.data('name'), price:$b.data('price'), selections:Object.values(bd)}),
                total_price: parseFloat($b.data('price')) || BASE
            };

            console.log('Sending Payload:', payload);

            $.post(AJAX, payload, function(r){
                console.log('Server Response:', r);
                if(r.success){
                    if(isDirectBuy && r.data && r.data.redirect) window.location.href = r.data.redirect;
                    else {
                        // Trigger WooCommerce to refresh cart fragments (header cart count etc.)
                        $(document.body).trigger('wc_fragment_refresh');
                        $(document.body).trigger('added_to_cart', [r.data, '', $btn]);
                        alert('✅ تمت الإضافة إلى السلة بنجاح!');
                        $btn.prop('disabled', false).css('opacity', '1').text(oldTxt);
                    }
                } else {
                    alert('خطأ: ' + (r.data ? (r.data.message || r.data) : 'يرجى المحاولة لاحقاً'));
                    $btn.prop('disabled', false).css('opacity', '1').text(oldTxt);
                }
            }).fail(function(xhr){ 
                console.error('AJAX Error:', xhr.status, xhr.responseText);
                alert('عذراً، حدث خطأ في النظام. يرجى المحاولة لاحقاً.');
                $btn.prop('disabled', false).css('opacity', '1').text(oldTxt);
            });
        }

        $(document).on('click', '#gs-confirm-buy', function(){ submitOrder(true); });

    updateSummary();
});
</script>
