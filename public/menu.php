<?php
declare(strict_types=1);
require_once __DIR__ . '/../src/config.php';

$browseMode = isset($_GET['browse']);

if (!$browseMode && $_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_bowl'])) {
    Cart::clear();
    Cart::add((string)$_POST['add_bowl'], 1);
    $redirect = 'menu.php' . (isset($_POST['cat']) ? '?cat=' . urlencode((string)$_POST['cat']) : '');
    header('Location: ' . $redirect);
    exit;
}

$lang = $_SESSION['lang'] ?? 'en';
$categories = menu_categories();
$catKeys = array_keys($categories);
$activeCat = $_GET['cat'] ?? $catKeys[0];
if (!isset($categories[$activeCat])) {
    $activeCat = $catKeys[0];
}
$_SESSION['last_cat'] = $activeCat;

$items = array_values(array_filter(menu_items(), fn($i) => $i['category'] === $activeCat));
$cartCount = Cart::count();
$cartTotal = Cart::total();

$payload = array_map(function ($item) {
    return [
        'id' => $item['id'],
        'name' => $item['name'],
        'desc' => $item['desc'],
        'price' => money($item['price']),
        'thumb' => item_thumb_html($item),
        'stock' => $item['stock'],
        'badge' => $item['badge'],
        'allergens' => array_map(fn($a) => ALLERGEN_LABELS[$a] ?? ['en' => $a, 'zh' => $a, 'icon' => '⚠️'], $item['allergens'] ?? []),
    ];
}, $items);
?>
<!doctype html>
<html lang="<?= $lang === 'zh' ? 'zh-Hant' : 'en' ?>">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>Yo-Kai Express — <?= t('main_menu') ?></title>
<link rel="stylesheet" href="<?= asset('assets/css/style.css') ?>">
</head>
<body>
<div class="explore-screen<?= $browseMode ? ' browse-mode' : '' ?>">
    <div class="explore-frame">
        <?php if ($browseMode): ?>
            <div class="browse-banner">📱 <?= $lang === 'zh' ? '瀏覽模式 — 準備好後請至機台完成點餐' : 'Browsing mode — order at the kiosk when ready' ?></div>
        <?php endif; ?>
        <div class="explore-topbar">
            <?php if ($browseMode): ?>
                <span></span>
            <?php else: ?>
                <a class="explore-back" href="index.php">‹ <?= $lang === 'zh' ? '回到首頁' : 'Back to Home' ?></a>
            <?php endif; ?>
            <div class="explore-logo">
                <span class="word">YO-KAI</span>
                <span class="sub">express</span>
            </div>
            <div style="display:flex; align-items:center; gap:10px;">
                <?php if (!$browseMode): ?>
                    <a class="explore-lang" href="cart.php">🛒 <?= $cartCount > 0 ? $cartCount . ' · ' . money($cartTotal) : ($lang === 'zh' ? '購物車' : 'Cart') ?></a>
                <?php endif; ?>
                <div class="explore-lang" onclick="toggleLang()">🌐 <?= $lang === 'zh' ? '中文' : 'English' ?> ▾</div>
            </div>
        </div>

        <div class="explore-body">
            <div class="explore-main">
                <div class="explore-heading">
                    <span class="ico">🍜</span>
                    <div>
                        <h1><?= $lang === 'zh' ? '探索菜單' : 'Explore Our Menu' ?></h1>
                        <p><?= $lang === 'zh' ? '現點現做，新鮮出爐' : 'Hot. Fresh. Made to order.' ?></p>
                    </div>
                </div>

                <div class="explore-tabs">
                    <?php foreach ($categories as $key => $cat): ?>
                        <a class="explore-tab<?= $key === $activeCat ? ' active' : '' ?>" href="menu.php?cat=<?= urlencode($key) ?>&lang=<?= $lang ?><?= $browseMode ? '&browse=1' : '' ?>">
                            <?= $cat['icon'] ?> <?= htmlspecialchars($cat[$lang]) ?>
                        </a>
                    <?php endforeach; ?>
                    <a class="explore-tab allergen-tab" href="#" onclick="event.preventDefault(); openModal();">
                        🌿 <?= $lang === 'zh' ? '營養標示' : 'Nutritional Facts' ?>
                    </a>
                </div>

                <div class="explore-grid" id="exploreGrid">
                    <?php foreach ($items as $i => $item): $soldOut = $item['stock'] <= 0; $badgeText = $item['badge'][$lang] ?? ($item['badge']['en'] ?? null); $isSpicy = ($item['badge']['en'] ?? '') === 'Spicy'; ?>
                        <div class="explore-card<?= $i === 0 ? ' selected' : '' ?><?= $soldOut ? ' sold-out' : '' ?>" data-idx="<?= $i ?>" onclick="selectItem(<?= $i ?>)">
                            <?php if ($badgeText): ?>
                                <span class="badge-tag<?= $isSpicy ? ' spicy' : '' ?>"><?= htmlspecialchars($badgeText) ?></span>
                            <?php elseif ($soldOut): ?>
                                <span class="badge-tag out"><?= $lang === 'zh' ? '售罄' : 'Sold Out' ?></span>
                            <?php elseif ($item['stock'] <= 3): ?>
                                <span class="badge-tag low"><?= $lang === 'zh' ? "剩 {$item['stock']}" : "{$item['stock']} left" ?></span>
                            <?php endif; ?>
                            <div class="thumb"><?= item_thumb_html($item) ?></div>
                            <h3><?= htmlspecialchars(item_name($item)) ?></h3>
                            <div class="price"><?= money($item['price']) ?></div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>

            <div class="explore-detail" id="exploreDetail">
                <!-- filled by JS on load -->
            </div>
        </div>

        <div class="explore-footer" id="exploreFooter">
            <?php if ($browseMode): ?>
                <a class="f-link" href="#" onclick="event.preventDefault(); openModal();"><?= $lang === 'zh' ? '營養標示' : 'Nutritional Facts' ?></a>
                <span class="f-note"><?= $lang === 'zh' ? '準備好後請至機台點餐' : 'Order at the kiosk when you\'re ready' ?></span>
            <?php elseif ($cartCount > 0): ?>
                <span class="f-link">🛒 <?= $lang === 'zh' ? "{$cartCount} 件商品 · " . money($cartTotal) : "{$cartCount} items · " . money($cartTotal) ?></span>
                <a class="f-link" style="background:linear-gradient(180deg,#d9c08a,#c5a059); color:#fff; padding:10px 22px; border-radius:999px; font-weight:800;" href="cart.php"><?= $lang === 'zh' ? '前往結帳 →' : 'Proceed to Checkout →' ?></a>
            <?php endif; ?>
        </div>
    </div>
</div>

<div class="modal-backdrop explore-nutrition-scope" id="nutritionModal" style="display:none;">
    <div class="modal-card">
        <button class="modal-close" onclick="closeModal()" aria-label="Close">✕</button>
        <table class="nutrition-table" id="nutritionTable">
            <tr><th colspan="2"><?= t('nutrition_facts') ?></th></tr>
        </table>
        <button class="btn btn-primary btn-block" style="margin-top:var(--sp-4);" onclick="closeModal()">Close</button>
    </div>
</div>

<script>
const lang = <?= json_encode($lang) ?>;
const browseMode = <?= json_encode($browseMode) ?>;
const activeCat = <?= json_encode($activeCat) ?>;
const items = <?= json_encode($payload, JSON_UNESCAPED_UNICODE) ?>;
const nutrition = <?= json_encode(array_map(fn($i) => $i['nutrition'], $items), JSON_UNESCAPED_UNICODE) ?>;
let selectedIdx = 0;

function toggleLang(){
    const u = new URL(window.location.href);
    u.searchParams.set('lang', lang === 'zh' ? 'en' : 'zh');
    window.location.href = u.toString();
}

function renderDetail(idx){
    const item = items[idx];
    if (!item) return;
    const detail = document.getElementById('exploreDetail');
    const soldOut = item.stock <= 0;
    const allergenHtml = item.allergens.length
        ? '<div class="d-allergen-label">' + (lang === 'zh' ? '過敏原' : 'Allergens') + '</div><div class="d-allergen-row">' +
          item.allergens.map(a => '<span class="d-allergen-chip"><span class="ico">' + a.icon + '</span>' + (a[lang] || a.en) + '</span>').join('') + '</div>'
        : '<div class="d-allergen-label">' + (lang === 'zh' ? '過敏原' : 'Allergens') + '</div><div class="d-allergen-none">' + (lang === 'zh' ? '無已知過敏原' : 'No known allergens') + '</div>';
    detail.innerHTML =
        '<div class="d-photo">' + item.thumb + '</div>' +
        '<h2>' + item.name[lang] + '</h2>' +
        '<div class="d-desc">' + item.desc[lang] + '</div>' +
        allergenHtml +
        '<div class="d-price">' + item.price + '</div>' +
        (browseMode
            ? '<div class="d-browse-note">' + (lang === 'zh' ? '記住這道菜，到機台上就能點囉！' : 'Remember this one — order it at the kiosk!') + '</div>'
            : soldOut
                ? '<div class="d-sold-out">' + (lang === 'zh' ? '目前售罄' : 'Currently Sold Out') + '</div>'
                : '<form method="post"><input type="hidden" name="add_bowl" value="' + item.id + '"><input type="hidden" name="cat" value="' + activeCat + '"><button type="submit" class="d-select">' + (lang === 'zh' ? '選擇這碗' : 'Select This Bowl') + ' <span class="arrow">→</span></button></form>') +
        '<div class="d-note">' + (soldOut || browseMode ? '' : (lang === 'zh' ? `剩餘 ${item.stock} 份` : `${item.stock} remaining right now`)) + '</div>';
}

function selectItem(idx){
    selectedIdx = idx;
    document.querySelectorAll('.explore-card').forEach((el, i) => el.classList.toggle('selected', i === idx));
    renderDetail(idx);
}

function openModal(){
    const n = nutrition[selectedIdx];
    if (!n) return;
    const table = document.getElementById('nutritionTable');
    table.innerHTML =
        '<tr><th colspan="2">' + (lang === 'zh' ? '營養標示' : 'Nutrition Facts') + '</th></tr>' +
        '<tr><td colspan="2" style="font-size:var(--step-xs);color:var(--text-on-light-soft);padding-top:10px;">Serving Size ' + n.serving + '</td></tr>' +
        '<tr class="cal-row"><td>Calories</td><td class="val">' + n.calories + '</td></tr>' +
        '<tr><td style="font-weight:700;">Total Fat ' + n.fat + 'g</td><td class="val">' + n.fat_dv + '%</td></tr>' +
        '<tr><td style="padding-left:16px;">Saturated Fat ' + n.sat_fat + 'g</td><td class="val">' + n.sat_fat_dv + '%</td></tr>' +
        '<tr class="thick"><td style="font-weight:700;">Sodium ' + n.sodium + 'mg</td><td class="val">' + n.sodium_dv + '%</td></tr>' +
        '<tr><td style="font-weight:700;">Total Carbohydrate ' + n.carbs + 'g</td><td class="val">' + n.carbs_dv + '%</td></tr>' +
        '<tr class="thick"><td style="padding-left:16px;">Total Sugars ' + n.sugar + 'g</td><td class="val"></td></tr>' +
        '<tr><td style="font-weight:700;">Protein ' + n.protein + 'g</td><td class="val"></td></tr>';
    document.getElementById('nutritionModal').style.display = 'flex';
}
function closeModal(){ document.getElementById('nutritionModal').style.display = 'none'; }

renderDetail(0);
</script>
</body>
</html>
