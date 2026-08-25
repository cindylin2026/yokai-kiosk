<?php
declare(strict_types=1);

function render_topbar(bool $showLang = true): void
{
    $enActive = ($_SESSION['lang'] ?? 'en') === 'en';
    $count = Cart::count();
    ?>
    <div class="kiosk-topbar">
        <a class="kiosk-logo-elegant" href="menu.php" aria-label="Yo-Kai Express">
            <span class="word">YO-KAI</span>
            <span class="sub">express</span>
        </a>
        <div class="topbar-tools">
            <?php if ($showLang): ?>
            <div class="lang-toggle">
                <button type="button" class="<?= $enActive ? 'active' : '' ?>" onclick="setLang('en')">EN</button>
                <button type="button" class="<?= !$enActive ? 'active' : '' ?>" onclick="setLang('zh')">中文</button>
            </div>
            <?php endif; ?>
            <a class="icon-btn" href="cart.php" title="<?= t('your_cart') ?>" aria-label="<?= t('your_cart') ?>">
                🛒
                <?php if ($count > 0): ?><span class="badge-count"><?= $count ?></span><?php endif; ?>
            </a>
        </div>
    </div>
    <script>
    function setLang(l){
        var u = new URL(window.location.href);
        u.searchParams.set('lang', l);
        window.location.href = u.toString();
    }
    </script>
    <?php
}

/** @return array{img:string,avatar:string,name:array<string,string>} */
function mascot(string $key): array
{
    $mascots = [
        'shiba' => ['img' => 'assets/brand/mascot-shiba-chef.webp', 'avatar' => 'assets/brand/mascot-shiba-avatar.webp', 'name' => ['en' => 'Shiba', 'zh' => '柴犬主廚']],
        'ramen-girl' => ['img' => 'assets/brand/mascot-ramengirl-chef.webp', 'avatar' => 'assets/brand/mascot-ramengirl-avatar.webp', 'name' => ['en' => 'Ramen Girl', 'zh' => '拉麵妹']],
    ];
    return $mascots[$key] ?? $mascots['shiba'];
}

/**
 * Chef commentary line for a single menu item. Every branch is derived
 * from real fields (stock count, badge, description) — never an
 * invented claim about the dish (see standing lesson: an earlier pivot
 * had a mascot claim an "extra egg" option that didn't exist).
 */
function chef_line(array $item): array
{
    $stock = (int)($item['stock'] ?? 0);
    $name = $item['name'];
    if ($stock <= 0) {
        return [
            'en' => "Sold out for now — come back and try {$name['en']} next time!",
            'zh' => "「{$name['zh']}」暫時售完了，下次再來嚐嚐吧！",
        ];
    }
    if ($stock <= 3) {
        return [
            'en' => "Uh oh! Only {$stock} left of {$name['en']} — better be quick!",
            'zh' => "糟糕！「{$name['zh']}」只剩 {$stock} 碗了，手腳要快！",
        ];
    }
    if (!empty($item['badge'])) {
        return [
            'en' => "This is our chef's pick today — don't miss {$name['en']}!",
            'zh' => "這是我們今天的主廚推薦，別錯過「{$name['zh']}」！",
        ];
    }
    $descEn = $item['desc']['en'] ?? '';
    $descZh = $item['desc']['zh'] ?? '';
    $firstEn = trim(explode('.', $descEn)[0] ?? '');
    $firstZh = trim(explode('。', $descZh)[0] ?? '');
    return [
        'en' => $firstEn !== '' ? "{$firstEn}. Freshly made just for you!" : "Freshly made just for you — enjoy {$name['en']}!",
        'zh' => $firstZh !== '' ? "{$firstZh}，現點現做給你！" : "「{$name['zh']}」現點現做，趕快試試看！",
    ];
}

function render_chef_widget(array $item, string $mascotKey = 'shiba'): void
{
    $lang = $_SESSION['lang'] ?? 'en';
    $m = mascot($mascotKey);
    $line = chef_line($item);
    $urgent = (int)($item['stock'] ?? 0) > 0 && (int)($item['stock'] ?? 0) <= 3;
    ?>
    <div class="chef-widget">
        <div class="chef-avatar"><img src="<?= asset($m['avatar']) ?>" alt="<?= htmlspecialchars($m['name'][$lang]) ?>"></div>
        <div class="chef-bubble <?= $urgent ? 'urgent' : '' ?>"><?= htmlspecialchars($line[$lang]) ?></div>
    </div>
    <?php
}

function render_stamp_card(): void
{
    $lang = $_SESSION['lang'] ?? 'en';
    $stamps = (int)($_SESSION['stamps'] ?? 0);
    $goal = 5;
    ?>
    <div class="stamp-card">
        <div class="head">
            <h3><?= $lang === 'zh' ? '妖怪集章卡' : 'Spirit Stamp Card' ?></h3>
            <span><?= $lang === 'zh' ? "再 " . max(0, $goal - $stamps) . " 次免費拉麵" : (max(0, $goal - $stamps)) . ' more for a free bowl' ?></span>
        </div>
        <div class="stamp-row">
            <?php for ($i = 1; $i <= $goal; $i++): ?>
                <div class="stamp-slot <?= $i <= $stamps ? 'filled' : '' ?> <?= $i === $goal ? 'reward' : '' ?>">
                    <?= $i <= $stamps ? '🍜' : ($i === $goal ? '🎁' : '') ?>
                </div>
            <?php endfor; ?>
        </div>
    </div>
    <?php
}

function render_stepper(string $active): void
{
    $steps = [
        'menu'    => ['en' => 'Menu',    'zh' => '菜單'],
        'cart'    => ['en' => 'Cart',    'zh' => '購物車'],
        'payment' => ['en' => 'Payment', 'zh' => '付款'],
        'pickup'  => ['en' => 'Pickup',  'zh' => '取餐'],
    ];
    $keys = array_keys($steps);
    $activeIdx = array_search($active, $keys, true);
    $lang = $_SESSION['lang'] ?? 'en';
    ?>
    <div class="stepper">
        <?php foreach ($keys as $i => $key):
            $state = $i < $activeIdx ? 'done' : ($i === $activeIdx ? 'active' : ''); ?>
            <div class="step <?= $state ?>">
                <span class="dot"></span>
                <span><?= htmlspecialchars($steps[$key][$lang]) ?></span>
            </div>
            <?php if ($i < count($keys) - 1): ?><div class="line"></div><?php endif; ?>
        <?php endforeach; ?>
    </div>
    <?php
}

function render_sticky_cart(): void
{
    $count = Cart::count();
    if ($count < 1) {
        return;
    }
    ?>
    <a class="sticky-cart" href="cart.php" style="text-decoration:none;">
        <div class="info">
            <span class="count"><?= $count ?></span>
            <span><?= t('your_cart') ?></span>
            <span class="total"><?= money(Cart::total()) ?></span>
        </div>
        <div class="cta"><?= t('proceed_checkout') ?> →</div>
    </a>
    <?php
}

function item_name(array $item): string
{
    $lang = $_SESSION['lang'] ?? 'en';
    return $item['name'][$lang] ?? $item['name']['en'];
}

function item_desc(array $item): string
{
    $lang = $_SESSION['lang'] ?? 'en';
    return $item['desc'][$lang] ?? $item['desc']['en'];
}

function item_badge(array $item): ?string
{
    if (!$item['badge']) {
        return null;
    }
    $lang = $_SESSION['lang'] ?? 'en';
    return $item['badge'][$lang] ?? $item['badge']['en'];
}

/**
 * Glossy stylized food emoji standing in for photography that doesn't
 * exist yet — the platform's native emoji glyphs (Apple/Google/etc.)
 * already render as polished 3D-ish icons, closer to the target look
 * than a flat line-art placeholder.
 */
const ITEM_ICON_EMOJI = [
    'spicy-miso-ramen' => '🍜',
    'curry-chicken-don' => '🍛',
    'edamame' => '🫛',
    'gyoza' => '🥟',
];

function item_thumb_html(array $item): string
{
    if (!empty($item['img'])) {
        $src = htmlspecialchars(asset($item['img']));
        $alt = htmlspecialchars(item_name($item));
        return "<img src=\"{$src}\" alt=\"{$alt}\" loading=\"lazy\">";
    }
    $emoji = ITEM_ICON_EMOJI[$item['id']] ?? '🍽️';
    return '<span class="item-icon-art">' . $emoji . '</span>';
}
