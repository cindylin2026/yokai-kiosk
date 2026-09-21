<?php
declare(strict_types=1);
require_once __DIR__ . '/../src/config.php';

$lang = $_SESSION['lang'] ?? 'en';

if (isset($_GET['lang']) && in_array($_GET['lang'], ['en', 'zh', 'ja', 'ko', 'es'], true)) {
    $_SESSION['lang'] = $_GET['lang'];
    $lang = $_SESSION['lang'];
    $qs = $_GET; unset($qs['lang']);
    header('Location: item.php?' . http_build_query($qs));
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_bowl'])) {
    Cart::clear();
    Cart::add((string)$_POST['add_bowl'], 1);
    header('Location: cart.php');
    exit;
}

$itemId  = $_GET['id'] ?? '';
$backCat = $_GET['cat'] ?? '';
$item    = menu_item($itemId);

if (!$item) {
    header('Location: menu.php');
    exit;
}

$soldOut      = $item['stock'] <= 0;
$allergenList = array_map(
    fn($a) => ALLERGEN_LABELS[$a] ?? ['en' => $a, 'zh' => $a, 'icon' => '⚠️'],
    $item['allergens'] ?? []
);
$n = $item['nutrition'];
$backUrl = 'menu.php' . ($backCat ? '?cat=' . urlencode($backCat) : '');

$langLabels = ['en'=>'English','zh'=>'中文','ja'=>'日本語','ko'=>'한국어','es'=>'Español'];
$currentLangLabel = $langLabels[$lang] ?? 'English';
?>
<!doctype html>
<html lang="<?= $lang === 'zh' ? 'zh-Hant' : 'en' ?>">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>Yo-Kai Express — <?= htmlspecialchars($item['name'][$lang] ?? $item['name']['en']) ?></title>
<link rel="stylesheet" href="<?= asset('assets/css/style.css') ?>">
<style>
.item-screen {
  min-height: 100vh; width: 100%;
  background: #ece8e1;
  display: flex; align-items: center; justify-content: center;
  padding: 18px; box-sizing: border-box;
}
.item-frame {
  width: calc(100vw - 36px); max-width: 1400px;
  height: calc((100vw - 36px) * 9 / 16); max-height: 787px;
  background: #f6f4f0;
  border-radius: 22px; border: 1px solid rgba(197,160,89,0.28);
  box-shadow: 0 0 0 1px rgba(197,160,89,0.1), 0 30px 70px rgba(25,27,30,0.12);
  display: flex; flex-direction: column;
  overflow: hidden;
}
.item-topbar {
  display: flex; align-items: center; justify-content: space-between;
  padding: 14px 28px 0; flex: 0 0 auto;
}
.item-topbar .t-logo img { height: 30px; width: auto; }
.item-topbar .t-right { display: flex; align-items: center; gap: 12px; }

/* Language dropdown */
.lang-dd { position: relative; }
.lang-dd-btn {
  display: flex; align-items: center; gap: 8px;
  background: rgba(25,27,30,0.06); border: 1px solid rgba(197,160,89,0.3);
  border-radius: 999px; padding: 7px 16px;
  font-weight: 700; font-size: .8rem; color: #6b6862;
  cursor: pointer; white-space: nowrap; user-select: none;
}
.lang-chevron { font-size: .6rem; transition: transform .2s; }
.lang-dd.open .lang-chevron { transform: rotate(180deg); }
.lang-dd-menu {
  display: none; position: absolute; top: calc(100% + 8px); right: 0;
  background: #faf8f4; border: 1px solid rgba(197,160,89,0.3);
  border-radius: 14px; overflow: hidden;
  box-shadow: 0 8px 28px rgba(25,27,30,0.14);
  min-width: 150px; z-index: 50;
}
.lang-dd.open .lang-dd-menu { display: block; }
.lang-dd-opt {
  padding: 10px 16px; font-size: .84rem; font-weight: 600;
  color: #2b2b2a; cursor: pointer; transition: background .15s;
}
.lang-dd-opt:hover { background: rgba(197,160,89,0.1); }
.lang-dd-opt.active { color: #c5a059; font-weight: 800; }

.item-back {
  display: flex; align-items: center; gap: 6px;
  color: #6b6862; font-weight: 600; font-size: .82rem;
  text-decoration: none; padding: 6px 14px;
  border: 1px solid rgba(197,160,89,0.3); border-radius: 999px;
  background: #fff; transition: all .18s;
}
.item-back:hover { border-color: #c5a059; color: #2b2b2a; }

.item-body {
  flex: 1; min-height: 0;
  display: flex; gap: 0;
  padding: 20px 28px 24px;
}
.item-photo-col {
  flex: 0 0 42%;
  display: flex; align-items: center; justify-content: center;
  padding-right: 28px;
}
.item-photo-wrap {
  width: 100%; padding-bottom: 100%; height: 0;
  position: relative;
  border-radius: 20px; overflow: hidden;
  box-shadow: 0 16px 48px rgba(25,27,30,0.14);
  background: #edeef0;
}
.item-photo-wrap img {
  position: absolute; top: 0; left: 0;
  width: 100%; height: 100%; object-fit: cover;
}
.item-info-col {
  flex: 1; min-width: 0;
  display: flex; flex-direction: column;
  overflow-y: auto; scrollbar-width: thin;
  scrollbar-color: rgba(197,160,89,0.3) transparent;
  padding-right: 4px;
}
.item-info-col::-webkit-scrollbar { width: 4px; }
.item-info-col::-webkit-scrollbar-thumb { background: rgba(197,160,89,0.3); border-radius: 2px; }

.item-badge {
  display: inline-block; font-size: .75rem; font-weight: 800;
  letter-spacing: .04em; padding: 4px 12px; border-radius: 999px;
  color: #fff; background: #c5a059; text-transform: uppercase;
  margin-bottom: 10px; align-self: flex-start;
}
.item-badge.spicy { background: #dc3545; }
.item-badge.vegan { background: #4caf6d; }
.item-badge.new   { background: #7b5ea7; }

.item-name {
  font-family: var(--font-serif); font-weight: 900;
  font-size: clamp(1.1rem, 2.2vw, 1.7rem);
  color: #2b2b2a; margin: 0 0 6px; line-height: 1.25;
}
.item-price {
  font-family: var(--font-serif); font-weight: 900;
  font-size: clamp(1.1rem, 2vw, 1.5rem);
  color: #c5a059; margin: 0 0 12px;
}
.item-desc {
  font-size: clamp(.78rem, 1.1vw, .92rem);
  color: #6b6862; line-height: 1.6; margin: 0 0 16px;
}
.item-divider { height: 1px; background: #ddd6c8; margin: 0 0 14px; }

.item-section-title {
  font-size: .68rem; font-weight: 800; letter-spacing: .08em;
  text-transform: uppercase; color: #8a8378; margin-bottom: 8px;
}
.item-allergen-row { display: flex; flex-wrap: wrap; gap: 7px; margin-bottom: 16px; }
.item-allergen-chip {
  display: flex; align-items: center; gap: 5px;
  padding: 5px 12px; border-radius: 999px;
  background: rgba(220,53,69,0.06); border: 1px solid rgba(220,53,69,0.2);
  font-size: .76rem; font-weight: 700; color: #a82333;
}
.item-no-allergen { font-size: .78rem; color: #6b6862; margin-bottom: 16px; }

/* Nutrition toggle button */
.item-nut-toggle {
  display: flex; align-items: center; justify-content: space-between;
  width: 100%; background: rgba(197,160,89,0.08);
  border: 1px solid rgba(197,160,89,0.25); border-radius: 10px;
  padding: 9px 14px; font-size: .8rem; font-weight: 800;
  color: #8a7040; cursor: pointer; margin-bottom: 8px;
  font-family: var(--font-body); text-align: left;
}
.item-nut-toggle:hover { background: rgba(197,160,89,0.14); }
.nut-chevron { font-size: .6rem; transition: transform .2s; margin-left: auto; }
.item-nut-toggle.open .nut-chevron { transform: rotate(180deg); }

.item-nutrition {
  background: #fff; border: 1px solid #ddd6c8;
  border-radius: 12px; padding: 12px 14px;
  margin-bottom: 14px; display: none;
}
.item-nutrition.open { display: block; }
.item-nutrition table { width: 100%; border-collapse: collapse; }
.item-nutrition th {
  font-weight: 900; font-size: .82rem;
  border-bottom: 3px solid #2b2b2a; padding-bottom: 3px; text-align: left;
}
.item-nutrition .nut-cal td {
  font-size: 1rem; font-weight: 900;
  border-bottom: 3px solid #2b2b2a; padding: 3px 0;
}
.item-nutrition td { padding: 3px 0; border-bottom: 1px solid #ddd6c8; font-size: .74rem; color: #2b2b2a; }
.item-nutrition td.val { text-align: right; }

.item-stock-note { font-size: .74rem; color: #8a8378; margin-bottom: 12px; }
.item-stock-low  { color: #dc3545; font-weight: 700; }

.item-order-btn {
  display: flex; align-items: center; justify-content: center; gap: 10px;
  background: linear-gradient(180deg, #e8c96a, #c5a059);
  color: #1a1208; font-family: var(--font-serif); font-weight: 900;
  font-size: clamp(.9rem, 1.4vw, 1.15rem);
  border: none; border-radius: 999px; padding: 14px 28px;
  cursor: pointer; width: 100%;
  box-shadow: 0 6px 20px rgba(197,160,89,0.35);
  margin-top: auto;
  transition: transform .14s, box-shadow .14s;
}
.item-order-btn:active { transform: scale(0.97); }
.item-order-btn .arrow {
  width: 26px; height: 26px; border-radius: 50%;
  background: #1a1208; color: #c5a059;
  display: flex; align-items: center; justify-content: center;
  font-size: .8rem; flex: 0 0 auto;
}
.item-sold-out {
  width: 100%; text-align: center; padding: 14px;
  font-weight: 800; color: #dc3545; font-size: 1rem;
  border: 2px solid rgba(220,53,69,0.3); border-radius: 999px;
  margin-top: auto;
}
</style>
</head>
<body>
<div class="item-screen">
  <div class="item-frame">

    <div class="item-topbar">
      <a class="item-back" href="<?= htmlspecialchars($backUrl) ?>">
        ‹ <?= $lang === 'zh' ? '返回菜單' : 'Back to Menu' ?>
      </a>
      <a class="t-logo" href="index.php">
        <img src="<?= asset('assets/brand/logo.png') ?>" alt="Yo-Kai Express">
      </a>
      <div class="t-right">
        <div class="lang-dd" id="itemLangDd" onclick="event.stopPropagation()">
          <div class="lang-dd-btn" onclick="toggleDD('itemLangDd')">
            🌐 <span><?= htmlspecialchars($currentLangLabel) ?></span> <span class="lang-chevron">▼</span>
          </div>
          <div class="lang-dd-menu">
            <div class="lang-dd-opt <?= $lang==='en'?'active':'' ?>" onclick="setLang('en')">🇺🇸 English</div>
            <div class="lang-dd-opt <?= $lang==='zh'?'active':'' ?>" onclick="setLang('zh')">🇹🇼 中文</div>
            <div class="lang-dd-opt <?= $lang==='ja'?'active':'' ?>" onclick="setLang('ja')">🇯🇵 日本語</div>
            <div class="lang-dd-opt <?= $lang==='ko'?'active':'' ?>" onclick="setLang('ko')">🇰🇷 한국어</div>
            <div class="lang-dd-opt <?= $lang==='es'?'active':'' ?>" onclick="setLang('es')">🇪🇸 Español</div>
          </div>
        </div>
      </div>
    </div>

    <div class="item-body">
      <div class="item-photo-col">
        <div class="item-photo-wrap">
          <img src="<?= htmlspecialchars(asset($item['img'])) ?>"
               alt="<?= htmlspecialchars($item['name'][$lang] ?? $item['name']['en']) ?>">
        </div>
      </div>

      <div class="item-info-col">
        <?php
          $badgeEn   = $item['badge']['en'] ?? null;
          $badgeText = $item['badge'][$lang] ?? $badgeEn;
          $badgeClass = match($badgeEn) {
            'Spicy' => 'spicy', 'Vegan' => 'vegan', 'New' => 'new', default => '',
          };
        ?>
        <?php if ($badgeText): ?>
          <span class="item-badge <?= $badgeClass ?>"><?= htmlspecialchars($badgeText) ?></span>
        <?php endif; ?>

        <h1 class="item-name"><?= htmlspecialchars($item['name'][$lang] ?? $item['name']['en']) ?></h1>
        <p class="item-price"><?= money($item['price']) ?></p>
        <p class="item-desc"><?= htmlspecialchars($item['desc'][$lang] ?? $item['desc']['en']) ?></p>

        <div class="item-divider"></div>

        <div class="item-section-title"><?= $lang === 'zh' ? '過敏原' : 'Allergens' ?></div>
        <?php if ($allergenList): ?>
          <div class="item-allergen-row">
            <?php foreach ($allergenList as $a): ?>
              <span class="item-allergen-chip">
                <span><?= $a['icon'] ?></span>
                <?= htmlspecialchars($a[$lang] ?? $a['en']) ?>
              </span>
            <?php endforeach; ?>
          </div>
        <?php else: ?>
          <p class="item-no-allergen"><?= $lang === 'zh' ? '無已知過敏原' : 'No known allergens' ?></p>
        <?php endif; ?>

        <!-- Nutrition toggle -->
        <?php if ($n): ?>
        <button type="button" class="item-nut-toggle" id="nutToggle"
                onclick="this.classList.toggle('open'); document.getElementById('nutPanel').classList.toggle('open')">
          <?= $lang === 'zh' ? '營養標示' : 'Nutrition Facts' ?>
          <span class="nut-chevron">▼</span>
        </button>
        <div class="item-nutrition" id="nutPanel">
          <table>
            <tr><th colspan="2"><?= $lang === 'zh' ? '營養標示' : 'Nutrition Facts' ?></th></tr>
            <tr><td colspan="2" style="font-size:.68rem;color:#8a8378;padding-top:6px;">
              <?= $lang === 'zh' ? '每份' : 'Serving Size' ?> <?= htmlspecialchars($n['serving']) ?>
            </td></tr>
            <tr class="nut-cal">
              <td><?= $lang === 'zh' ? '熱量' : 'Calories' ?></td>
              <td class="val"><?= $n['calories'] ?></td>
            </tr>
            <tr><td style="font-weight:700;"><?= $lang === 'zh' ? '總脂肪' : 'Total Fat' ?> <?= $n['fat'] ?>g</td><td class="val"><?= $n['fat_dv'] ?>%</td></tr>
            <tr><td style="padding-left:12px;"><?= $lang === 'zh' ? '飽和脂肪' : 'Saturated Fat' ?> <?= $n['sat_fat'] ?>g</td><td class="val"><?= $n['sat_fat_dv'] ?>%</td></tr>
            <tr><td style="font-weight:700;"><?= $lang === 'zh' ? '鈉' : 'Sodium' ?> <?= $n['sodium'] ?>mg</td><td class="val"><?= $n['sodium_dv'] ?>%</td></tr>
            <tr><td style="font-weight:700;"><?= $lang === 'zh' ? '總碳水' : 'Total Carbohydrate' ?> <?= $n['carbs'] ?>g</td><td class="val"><?= $n['carbs_dv'] ?>%</td></tr>
            <tr><td style="padding-left:12px;"><?= $lang === 'zh' ? '糖' : 'Total Sugars' ?> <?= $n['sugar'] ?>g</td><td class="val">—</td></tr>
            <tr><td style="font-weight:700;"><?= $lang === 'zh' ? '蛋白質' : 'Protein' ?> <?= $n['protein'] ?>g</td><td class="val">—</td></tr>
          </table>
        </div>
        <?php endif; ?>

        <?php if (!$soldOut && $item['stock'] <= 5): ?>
          <p class="item-stock-note item-stock-low"><?= $lang === 'zh' ? "僅剩 {$item['stock']} 份！" : "Only {$item['stock']} left!" ?></p>
        <?php elseif (!$soldOut && $item['stock'] <= 10): ?>
          <p class="item-stock-note"><?= $lang === 'zh' ? "剩餘 {$item['stock']} 份" : "{$item['stock']} remaining" ?></p>
        <?php endif; ?>

        <?php if ($soldOut): ?>
          <div class="item-sold-out"><?= $lang === 'zh' ? '目前售罄' : 'Currently Sold Out' ?></div>
        <?php else: ?>
          <form method="post" style="margin-top:auto;">
            <input type="hidden" name="add_bowl" value="<?= htmlspecialchars($item['id']) ?>">
            <button type="submit" class="item-order-btn">
              <?= $lang === 'zh' ? '點這碗' : 'Order This Bowl' ?>
              <span class="arrow">→</span>
            </button>
          </form>
        <?php endif; ?>
      </div>
    </div>
  </div>
</div>
<script>
function setLang(l) {
  const u = new URL(window.location.href);
  u.searchParams.set('lang', l);
  window.location.href = u.toString();
}
function toggleDD(id) {
  document.getElementById(id).classList.toggle('open');
}
document.addEventListener('click', function() {
  document.querySelectorAll('.lang-dd').forEach(d => d.classList.remove('open'));
});
</script>
</body>
</html>
