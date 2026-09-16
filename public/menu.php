<?php
declare(strict_types=1);
require_once __DIR__ . '/../src/config.php';

$lang = $_SESSION['lang'] ?? 'en';

if (isset($_GET['lang']) && in_array($_GET['lang'], ['en', 'zh'], true)) {
    $_SESSION['lang'] = $_GET['lang'];
    $lang = $_SESSION['lang'];
    $qs = $_GET; unset($qs['lang']);
    header('Location: menu.php?' . http_build_query($qs));
    exit;
}

$categories = menu_categories();
$catKeys    = array_keys($categories);
$activeCat  = $_GET['cat'] ?? $catKeys[0];
if (!isset($categories[$activeCat])) $activeCat = $catKeys[0];
$_SESSION['last_cat'] = $activeCat;

$items = array_values(array_filter(menu_items(), fn($i) => $i['category'] === $activeCat));
?>
<!doctype html>
<html lang="<?= $lang === 'zh' ? 'zh-Hant' : 'en' ?>">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>Yo-Kai Express — <?= $lang === 'zh' ? '菜單' : 'Menu' ?></title>
<link rel="stylesheet" href="<?= asset('assets/css/style.css') ?>">
<style>
/* ── Full-page menu ── */
.menu-screen {
  min-height: 100vh; width: 100%;
  background: #ece8e1;
  display: flex; align-items: center; justify-content: center;
  padding: 18px; box-sizing: border-box;
}
.menu-frame {
  width: calc(100vw - 36px); max-width: 1400px;
  height: calc((100vw - 36px) * 9 / 16); max-height: 787px;
  background: #f6f4f0;
  border-radius: 22px; border: 1px solid rgba(197,160,89,0.28);
  box-shadow: 0 0 0 1px rgba(197,160,89,0.1), 0 30px 70px rgba(25,27,30,0.12);
  display: flex; flex-direction: column;
  overflow: hidden;
}

/* Topbar */
.menu-topbar {
  display: flex; align-items: center; justify-content: space-between;
  padding: 14px 28px 0; flex: 0 0 auto;
}
.menu-topbar .t-logo img { height: 30px; width: auto; }
.menu-topbar .t-right    { display: flex; align-items: center; gap: 12px; }
.menu-lang {
  display: flex; background: rgba(25,27,30,0.06);
  border: 1px solid rgba(197,160,89,0.3); border-radius: 999px; padding: 2px;
}
.menu-lang button {
  border: none; background: transparent; color: #6b6862;
  padding: 5px 14px; border-radius: 999px; font-weight: 700; font-size: .8rem;
  cursor: pointer; font-family: var(--font-body); transition: all .18s;
}
.menu-lang button.active { background: #c5a059; color: #fff; }
.menu-cart-btn {
  display: flex; align-items: center; gap: 7px;
  background: #2b2b2a; color: #fff; font-weight: 700; font-size: .82rem;
  padding: 8px 18px; border-radius: 999px; text-decoration: none;
}
.menu-cart-badge {
  background: #c5a059; color: #fff; font-size: 10px; font-weight: 900;
  width: 18px; height: 18px; border-radius: 50%;
  display: flex; align-items: center; justify-content: center;
}

/* Category tabs */
.menu-tabs {
  display: flex; gap: 8px; padding: 12px 28px 0;
  flex: 0 0 auto; overflow-x: auto; scrollbar-width: none;
}
.menu-tabs::-webkit-scrollbar { display: none; }
.menu-tab {
  padding: 8px 22px; border-radius: 999px;
  border: 1px solid rgba(197,160,89,0.3); color: #6b6862;
  font-weight: 700; font-size: .85rem; text-decoration: none;
  background: #fff; white-space: nowrap; transition: all .18s;
}
.menu-tab.active {
  background: linear-gradient(180deg,#d9c08a,#c5a059);
  color: #fff; border-color: transparent;
  box-shadow: 0 4px 12px rgba(197,160,89,0.3);
}

/* Grid — full width, 4 columns */
.menu-grid {
  flex: 1; min-height: 0;
  display: flex; flex-wrap: wrap;
  align-content: flex-start;
  gap: 16px;
  padding: 16px 28px 20px;
  overflow-y: auto; scrollbar-width: thin;
  scrollbar-color: rgba(197,160,89,0.3) transparent;
}
.menu-grid::-webkit-scrollbar { width: 4px; }
.menu-grid::-webkit-scrollbar-thumb { background: rgba(197,160,89,0.35); border-radius: 2px; }

.menu-card {
  /* 4 columns, 3 gaps of 16px */
  flex: 0 0 calc((100% - 48px) / 4);
  background: #fff;
  border: 1.5px solid rgba(197,160,89,0.15);
  border-radius: 16px; padding: 12px;
  cursor: pointer; position: relative;
  display: flex; flex-direction: column; align-items: center;
  text-align: center;
  text-decoration: none; color: inherit;
  transition: transform .18s, box-shadow .18s, border-color .18s;
}
.menu-card:hover {
  transform: translateY(-3px);
  box-shadow: 0 8px 24px rgba(197,160,89,0.2);
  border-color: #c5a059;
}
.menu-card.sold-out { opacity: .45; pointer-events: none; }

/* Square photo via padding-% trick */
.mc-photo {
  width: 100%; padding-bottom: 100%; height: 0;
  position: relative; border-radius: 12px; overflow: hidden;
  background: #edeef0; margin-bottom: 10px;
}
.mc-photo img {
  position: absolute; top: 0; left: 0;
  width: 100%; height: 100%; object-fit: cover;
}
.mc-name {
  font-family: var(--font-serif); font-size: 1rem; font-weight: 700;
  color: #2b2b2a; line-height: 1.3; margin: 0;
}
.mc-price {
  font-family: var(--font-serif); font-weight: 800;
  color: #c5a059; font-size: 1rem; margin-top: 5px;
}

/* Badges */
.mc-badge {
  position: absolute; top: 8px; left: 8px;
  font-size: 9px; font-weight: 800; letter-spacing: .04em;
  padding: 3px 9px; border-radius: 999px; color: #fff;
  background: #c5a059; text-transform: uppercase; z-index: 1;
}
.mc-badge.spicy   { background: #dc3545; }
.mc-badge.vegan   { background: #4caf6d; }
.mc-badge.new     { background: #7b5ea7; }
.mc-badge.soldout { background: #8a8378; }
.mc-badge.low     { background: #dc3545; }
</style>
</head>
<body>
<div class="menu-screen">
  <div class="menu-frame">

    <!-- Topbar -->
    <div class="menu-topbar">
      <a class="t-logo" href="index.php">
        <img src="<?= asset('assets/brand/logo.png') ?>" alt="Yo-Kai Express">
      </a>
      <div class="t-right">
        <div class="menu-lang">
          <button class="<?= $lang === 'en' ? 'active' : '' ?>" onclick="setLang('en')">EN</button>
          <button class="<?= $lang === 'zh' ? 'active' : '' ?>" onclick="setLang('zh')">中文</button>
        </div>
        <?php $cartCount = Cart::count(); if ($cartCount > 0): ?>
          <a class="menu-cart-btn" href="cart.php">
            🛒 <span class="menu-cart-badge"><?= $cartCount ?></span>
          </a>
        <?php endif; ?>
      </div>
    </div>

    <!-- Category tabs -->
    <div class="menu-tabs">
      <?php foreach ($categories as $key => $cat): ?>
        <a class="menu-tab <?= $key === $activeCat ? 'active' : '' ?>"
           href="menu.php?cat=<?= urlencode($key) ?>">
          <?= htmlspecialchars($cat[$lang]) ?>
        </a>
      <?php endforeach; ?>
    </div>

    <!-- Full-width item grid -->
    <div class="menu-grid">
      <?php foreach ($items as $item):
        $soldOut   = $item['stock'] <= 0;
        $badgeEn   = $item['badge']['en'] ?? null;
        $badgeText = $item['badge'][$lang] ?? $badgeEn;
        $badgeClass = match($badgeEn) {
          'Spicy'       => 'spicy',
          'Vegan'       => 'vegan',
          'New'         => 'new',
          "Chef's Pick" => '',
          default       => '',
        };
        $detailUrl = 'item.php?id=' . urlencode($item['id']) . '&cat=' . urlencode($activeCat);
      ?>
        <a class="menu-card <?= $soldOut ? 'sold-out' : '' ?>"
           href="<?= $soldOut ? '#' : $detailUrl ?>">

          <?php if ($soldOut): ?>
            <span class="mc-badge soldout"><?= $lang === 'zh' ? '售罄' : 'Sold Out' ?></span>
          <?php elseif ($item['stock'] <= 3): ?>
            <span class="mc-badge low"><?= $lang === 'zh' ? "剩 {$item['stock']}" : "{$item['stock']} left" ?></span>
          <?php elseif ($badgeText): ?>
            <span class="mc-badge <?= $badgeClass ?>"><?= htmlspecialchars($badgeText) ?></span>
          <?php endif; ?>

          <div class="mc-photo">
            <img src="<?= htmlspecialchars(asset($item['img'])) ?>"
                 alt="<?= htmlspecialchars($item['name'][$lang] ?? $item['name']['en']) ?>"
                 loading="lazy">
          </div>
          <p class="mc-name"><?= htmlspecialchars($item['name'][$lang] ?? $item['name']['en']) ?></p>
          <p class="mc-price"><?= money($item['price']) ?></p>
        </a>
      <?php endforeach; ?>
    </div>

  </div><!-- .menu-frame -->
</div><!-- .menu-screen -->

<script>
function setLang(l) {
  const u = new URL(window.location.href);
  u.searchParams.set('lang', l);
  window.location.href = u.toString();
}
</script>
</body>
</html>
