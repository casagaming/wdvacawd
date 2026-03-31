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
.gs-fld select:disabled{background:#f5f5f5;color:#aaa;cursor:not-allowed;border-color:#eee;}
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

/* Step-by-step wizard */
.gs-wizard{display:flex;flex-direction:column;gap:10px;width:100%}
.gs-step{border:2px solid #eee;border-radius:16px;overflow:hidden;transition:border-color .25s,box-shadow .25s;background:#fff}
.gs-step.active{border-color:var(--red);box-shadow:0 6px 20px var(--red-glow)}
.gs-step.done{border-color:#27ae60;background:#f6fdf8}
.gs-step-hd{display:flex;align-items:center;gap:10px;padding:13px 16px;cursor:default;user-select:none}
.gs-step-num{width:26px;height:26px;border-radius:50%;background:#eee;color:#888;font-size:.75rem;font-weight:900;display:flex;align-items:center;justify-content:center;flex-shrink:0;transition:.25s}
.gs-step.active .gs-step-num{background:var(--red);color:#fff}
.gs-step.done .gs-step-num{background:#27ae60;color:#fff}
.gs-step-title{flex:1;font-size:.85rem;font-weight:900;color:#333}
.gs-step.active .gs-step-title{color:var(--red)}
.gs-step.done .gs-step-title{color:#27ae60}
.gs-step-val{font-size:.8rem;font-weight:900;color:#27ae60;background:#e8fdf0;padding:3px 10px;border-radius:8px;display:none}
.gs-step.done .gs-step-val{display:block}
.gs-step-check{font-size:.9rem;color:#27ae60;display:none;margin-right:auto}
.gs-step.done .gs-step-check{display:block}
.gs-step-bd{overflow:hidden;max-height:0;transition:max-height .35s cubic-bezier(0.4,0,0.2,1),padding .3s}
.gs-step.active .gs-step-bd{max-height:400px;padding:0 14px 16px}
.gs-step-tiles{display:flex;flex-wrap:wrap;gap:8px;margin-top:2px}

.gs-t-wrap{display:flex;align-items:center;gap:8px;border:2px solid #eee;padding:7px 12px;border-radius:12px;cursor:pointer;background:#fff;transition:.25s;min-width:48px;justify-content:center}
.gs-t-wrap.on{border-color:var(--red);background:#fff8f8;box-shadow:0 4px 12px var(--red-glow)}
.gs-t-lbl{font-size:.82rem;color:#333;font-weight:800}

.gs-t-missing{border-color:#ff4d4d!important;animation:gsPulse 0.8s infinite}
@keyframes gsPulse{0%{box-shadow:0 0 0 0 rgba(255,77,77,0.4)}70%{box-shadow:0 0 0 10px rgba(255,77,77,0)}100%{box-shadow:0 0 0 0 rgba(255,77,77,0)}}

/* Summary bar after all steps done */
.gs-wizard-done{display:none;background:linear-gradient(135deg,#e8fdf0,#f0fff8);border:2px solid #27ae60;border-radius:14px;padding:12px 16px;margin-top:6px;text-align:center;color:#27ae60;font-weight:900;font-size:.9rem}
.gs-wizard-done i{margin-left:6px}

.gs-attr-label{font-size:0.8rem;font-weight:700;color:#777;margin-bottom:8px;display:block}
.gs-vars-ttl{font-weight:900;font-size:.85rem;color:#222;margin-bottom:10px;display:flex;align-items:center;gap:8px}

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

/* Gallery thumbnails */
.gsp-thumbs{display:flex;gap:10px;margin-top:12px;flex-wrap:wrap;justify-content:flex-start}
.gsp-thumb{width:72px;height:72px;border-radius:14px;overflow:hidden;cursor:pointer;border:2.5px solid transparent;background:var(--bg);transition:.2s;box-shadow:0 2px 8px rgba(0,0,0,0.07)}
.gsp-thumb.on{border-color:var(--red);box-shadow:0 4px 14px var(--red-glow)}
.gsp-thumb img{width:100%;height:100%;object-fit:cover;display:block}

/* Mobile: title & price overlay on TOP of image */
@media(max-width:768px){
  .gsp{flex-direction:column;gap:20px}
  .gsp-gal{flex:none;position:relative}
  .gsp-main{border-radius:24px;overflow:hidden}
  .gsp-mobile-overlay{
    position:absolute;top:0;left:0;right:0;
    background:linear-gradient(to bottom, rgba(0,0,0,0.72) 0%, rgba(0,0,0,0.18) 70%, transparent 100%);
    border-radius:24px 24px 0 0;
    padding:14px 16px 22px;
    display:flex;flex-direction:column;gap:4px;
    pointer-events:none;
  }
  .gsp-mobile-overlay .gsp-title{color:#fff;font-size:1.3rem;margin:0;text-shadow:0 2px 8px rgba(0,0,0,0.5)}
  .gsp-mobile-overlay .gsp-price{margin:0}
  .gsp-mobile-overlay .gsp-price .new-p{color:#fff;font-size:1.6rem;text-shadow:0 2px 8px rgba(0,0,0,0.4)}
  .gsp-mobile-overlay .gsp-price .old-p{color:rgba(255,255,255,0.75)}
  /* Hide title/price from info section on mobile */
  .gsp-info .gsp-title-desktop,.gsp-info .gsp-price-desktop{display:none}
  .gs-grid-2{grid-template-columns:1fr}
}
@media(min-width:769px){
  .gsp-mobile-overlay{display:none}
}
</style>

<div id="product-<?php the_ID(); ?>" <?php wc_product_class('gsp'); ?>>
    <div class="gsp-gal">
        <div class="gsp-main">
            <?php $main_img_src = $main_img ? wp_get_attachment_image_url($main_img, 'large') : wc_placeholder_img_src('large'); ?>
            <img id="gs-main-img" src="<?php echo esc_url($main_img_src); ?>" alt="<?php the_title_attribute(); ?>" onerror="this.src='<?php echo esc_url(wc_placeholder_img_src()); ?>'">
        </div>
        <!-- Mobile overlay: title & price on image -->
        <div class="gsp-mobile-overlay">
            <h1 class="gsp-title"><?php the_title(); ?></h1>
            <div class="gsp-price">
                <?php if ($product->get_regular_price()) : ?><span class="old-p"><?php echo $product->get_regular_price(); ?> دج</span><?php endif; ?>
                <span class="new-p"><?php echo $price; ?> دج</span>
            </div>
        </div>
        <!-- Gallery thumbnails -->
        <?php if (count($all_imgs) > 1) : ?>
        <div class="gsp-thumbs">
            <?php foreach ($all_imgs as $img_id) :
                $thumb_url = wp_get_attachment_image_url($img_id, 'thumbnail');
                $full_url  = wp_get_attachment_image_url($img_id, 'large');
                if (!$thumb_url) continue;
            ?>
            <div class="gsp-thumb <?php echo $img_id === $main_img ? 'on' : ''; ?>" data-full="<?php echo esc_url($full_url); ?>">
                <img src="<?php echo esc_url($thumb_url); ?>" alt="">
            </div>
            <?php endforeach; ?>
        </div>
        <?php endif; ?>
    </div>
    <div class="gsp-info">
        <h1 class="gsp-title gsp-title-desktop"><?php the_title(); ?></h1>
        <div class="gsp-price gsp-price-desktop">
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
                        <option value="<?php echo esc_attr($w['name']); ?>" data-code="<?php echo esc_attr($code); ?>" data-office="<?php echo $w['office']; ?>" data-home="<?php echo $w['home']; ?>"><?php echo $code . ' - ' . esc_html($w['name']); ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="gs-fld">
                    <label>البلدية *</label>
                    <i class="fa fa-map-marker-alt"></i>
                    <select id="gf-city" required disabled>
                        <option value="">اختر الولاية أولاً</option>
                    </select>
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
                        <div class="gs-bvars-nested"></div>
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

    /* Gallery thumbnail switcher */
    $(document).on('click', '.gsp-thumb', function(){
        var fullUrl = $(this).data('full');
        if(!fullUrl) return;
        $('#gs-main-img').attr('src', fullUrl);
        $('.gsp-thumb').removeClass('on');
        $(this).addClass('on');
    });
    var VARS=<?php echo json_encode($variations);?>, ISVAR=<?php echo $is_var ? 'true' : 'false'; ?>, selVars={};
    var Wilayas=<?php echo json_encode(get_algeria_wilayas()); ?>;
    var Communes=<?php echo json_encode(get_algeria_communes()); ?>;

    /* Populate communes when wilaya changes */
    function populateCommunes(){
        var $stateOpt = $('#gf-state option:selected');
        var code = parseInt($stateOpt.data('code'));
        var $city = $('#gf-city');
        $city.empty();
        if(!code || !Communes[code]){
            $city.prop('disabled', true).append('<option value="">اختر الولاية أولاً</option>');
            return;
        }
        $city.prop('disabled', false).append('<option value="">اختر البلدية</option>');
        $.each(Communes[code], function(_, name){
            $city.append('<option value="'+name+'">'+name+'</option>');
        });
    }
    $('#gf-state').on('change', function(){
        populateCommunes();
        updateSummary();
    });

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

    /* ===== STEP-BY-STEP WIZARD FOR BUNDLE SELECTION ===== */
    var VAR_OPTS = <?php echo json_encode(array_map(function($label, $data) use ($var_opts) {
        $isColor = strpos(strtolower($label), 'color') !== false || strpos($label, 'لون') !== false;
        return [
            'label' => $isColor ? 'اللون' : 'المقاس',
            'key'   => $data['key'],
            'vals'  => $data['vals'],
        ];
    }, array_keys($var_opts), $var_opts)); ?>;

    /* Build wizard steps for a bundle item ($nested el, itemCount) */
    function buildWizard($item){
        var $nested = $item.find('.gs-bvars-nested');
        $nested.empty();

        if(!VAR_OPTS.length){ $nested.hide(); return; }

        var items = parseInt($item.data('items')) || 1;
        var steps = [];

        for(var i=0; i<items; i++){
            for(var a=0; a<VAR_OPTS.length; a++){
                steps.push({ itemIdx: i, attrIdx: a, attr: VAR_OPTS[a] });
            }
        }

        var $wizard = $('<div class="gs-wizard"></div>');
        var totalSteps = steps.length;

        $.each(steps, function(idx, step){
            var itemLabel = (items > 1) ? ' — القطعة '+(step.itemIdx+1) : '';
            var $step = $('<div class="gs-step" data-step="'+idx+'" data-bitem="'+step.itemIdx+'" data-battr="'+step.attr.key+'"></div>');

            // Header
            var $hd = $('<div class="gs-step-hd"></div>');
            $hd.append('<div class="gs-step-num">'+(idx+1)+'</div>');
            $hd.append('<div class="gs-step-title">'+step.attr.label+itemLabel+'</div>');
            $hd.append('<div class="gs-step-val"></div>');
            $hd.append('<div class="gs-step-check"><i class="fa fa-check-circle"></i></div>');

            // Body with tiles
            var $bd = $('<div class="gs-step-bd"></div>');
            var $tiles = $('<div class="gs-step-tiles"></div>');
            $.each(step.attr.vals, function(_, v){
                $tiles.append('<div class="gs-t-wrap gs-wiz-tile" data-value="'+v.slug+'"><div class="gs-t-lbl">'+v.name+'</div></div>');
            });
            $bd.append($tiles);
            $step.append($hd).append($bd);
            $wizard.append($step);
        });

        $wizard.append('<div class="gs-wizard-done"><i class="fa fa-check-circle"></i> تم اختيار جميع المقاسات والألوان</div>');

        $nested.append($wizard).show();
        activateStep($nested, 0);
    }

    function activateStep($nested, idx){
        var $steps = $nested.find('.gs-step');
        $steps.removeClass('active');
        if(idx < $steps.length){
            $steps.eq(idx).addClass('active');
            // Scroll to step smoothly
            var $s = $steps.eq(idx);
            setTimeout(function(){
                var top = $s.offset().top - 80;
                $('html,body').animate({scrollTop: top}, 350);
            }, 120);
        }
    }

    function wizardAllDone($nested){
        var $steps = $nested.find('.gs-step');
        return $steps.length > 0 && $steps.filter('.done').length === $steps.length;
    }

    /* Click a tile inside wizard */
    $(document).on('click', '.gs-wiz-tile', function(e){
        e.stopPropagation();
        var $tile = $(this);
        var $step = $tile.closest('.gs-step');
        if(!$step.hasClass('active')) return;

        // Mark selection
        $step.find('.gs-wiz-tile').removeClass('on');
        $tile.addClass('on');

        var valName = $tile.find('.gs-t-lbl').text();
        $step.find('.gs-step-val').text(valName);

        // Animate done after short delay
        setTimeout(function(){
            $step.removeClass('active').addClass('done');
            var $nested = $step.closest('.gs-bvars-nested');
            var idx = parseInt($step.data('step'));
            
            if(wizardAllDone($nested)){
                $nested.find('.gs-wizard-done').fadeIn(300);
                updateSummary();
            } else {
                activateStep($nested, idx+1);
            }
        }, 220);
    });

    /* Clicking a done step re-opens it to change selection */
    $(document).on('click', '.gs-step.done .gs-step-hd', function(e){
        e.stopPropagation();
        var $step = $(this).parent();
        var $nested = $step.closest('.gs-bvars-nested');
        var idx = parseInt($step.data('step'));

        // Close all steps from this one onwards
        var $steps = $nested.find('.gs-step');
        $steps.each(function(i){
            if(i >= idx){
                $(this).removeClass('done active');
                $(this).find('.gs-wiz-tile').removeClass('on');
                $(this).find('.gs-step-val').text('');
            }
        });
        $nested.find('.gs-wizard-done').hide();
        activateStep($nested, idx);
    });

    /* Bundle item click — activate it and build wizard */
    $(document).on('click', '.gs-bdl-item', function(e){
        if($(e.target).closest('.gs-step,.gs-wiz-tile').length) return;

        var $item = $(this);
        if($item.hasClass('on')) return;

        // Deactivate all bundles
        $('.gs-bdl-item').removeClass('on').find('.gs-bvars-nested').slideUp(250).empty();
        $('.gs-wizard-done').hide();

        $item.addClass('on');
        buildWizard($item);
        $item.find('.gs-bvars-nested').hide().slideDown(300);

        updateSummary();
    });

        function submitOrder(isDirectBuy){
            var name = $('#gf-name').val(), phone = $('#gf-phone').val(), state = $('#gf-state').val(), city = $('#gf-city').val();
            
            if(isDirectBuy){
                if(!name || !phone || !state || !city){ alert('يرجى ملء جميع الحقول المطلوبة.'); return; }
                if($('#gf-shipping').val() === 'home' && !$('#gf-addr').val()){ alert('يرجى إدخال عنوان المنزل.'); return; }
            }
            
            var $b = $('.gs-bdl-item.on'), isBundle = ($b.data('name') !== 'none');
            var bd = {}, ok = true;

            // Read selections from wizard steps
            $b.find('.gs-step').each(function(){
                var $step = $(this);
                var itemIdx = parseInt($step.data('bitem'));
                var attrKey = $step.data('battr');
                var $selected = $step.find('.gs-wiz-tile.on');
                if(!bd[itemIdx]) bd[itemIdx] = {};
                if(!$selected.length){
                    $step.removeClass('done').addClass('active');
                    ok = false;
                } else {
                    bd[itemIdx][attrKey] = $selected.data('value');
                }
            });

            if(!ok){ alert('⚠️ يرجى إكمال اختيار اللون والمقاس لجميع القطع!'); return; }
            if(VAR_OPTS.length && !$b.find('.gs-step').length){ alert('⚠️ يرجى اختيار اللون والمقاس.'); return; }

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

    // Build wizard for initially selected bundle (if has attributes)
    var $initItem = $('.gs-bdl-item.on');
    if($initItem.length && VAR_OPTS.length){
        buildWizard($initItem);
    }

    updateSummary();
});
</script>
