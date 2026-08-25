<?php
/**
 * Yo-Kai Express kiosk — bootstrap.
 * Session, store identity, and the tiny EN/中文 dictionary used across
 * every screen (a self-service kiosk should not force one language).
 */

declare(strict_types=1);

/**
 * Two separate systems share this bootstrap: the physical kiosk
 * (public/, one browser that's always open on the machine) and the
 * customer-facing mobile ordering site (mobile/, reached by scanning
 * a QR on the kiosk — a different device entirely). Each entry script
 * defines YK_APP before requiring this file so sessions never collide
 * even when both are tested on localhost in the same browser.
 */
if (!defined('YK_APP')) {
    define('YK_APP', 'kiosk');
}
if (session_status() !== PHP_SESSION_ACTIVE) {
    session_name(YK_APP === 'mobile' ? 'yk_mobile_sess' : 'yk_kiosk_sess');
    session_start();
}

// Where the *other* system lives — used to build cross-system links
// (the kiosk's attract-screen QR points at MOBILE_BASE_URL; the
// mobile order's pickup QR points at KIOSK_BASE_URL/scan.php). Override
// via env vars for a real multi-host deploy; these are just the local
// dev defaults matching .claude/launch.json.
const KIOSK_BASE_URL  = 'http://localhost:8990';
const MOBILE_BASE_URL = 'http://localhost:8992';

const STORE_ID   = 'SF-UNION-01';
const KIOSK_ID   = 'K-104';
const TAX_RATE   = 0.0875;

if (!isset($_SESSION['lang'])) {
    $_SESSION['lang'] = 'en';
}
if (isset($_GET['lang']) && in_array($_GET['lang'], ['en', 'zh'], true)) {
    $_SESSION['lang'] = $_GET['lang'];
}

$GLOBALS['__i18n'] = [
    'main_menu'        => ['en' => 'Main Menu',            'zh' => '主餐點'],
    'tap_to_start'      => ['en' => 'Tap to Start Your Order', 'zh' => '點擊開始點餐'],
    'attract_tagline'   => ['en' => 'Handcrafted ramen & rice bowls, ready in minutes', 'zh' => '職人手作拉麵與丼飯，數分鐘即可享用'],
    'nutrition_facts'   => ['en' => 'Nutrition Facts',      'zh' => '營養標示'],
    'add_to_cart'       => ['en' => 'Add to Cart',          'zh' => '加入購物車'],
    'your_cart'         => ['en' => 'Your Cart',            'zh' => '購物車'],
    'proceed_checkout'  => ['en' => 'Proceed to Checkout',  'zh' => '前往結帳'],
    'payment'           => ['en' => 'Payment',              'zh' => '付款方式'],
    'credit_card'       => ['en' => 'Credit Card',          'zh' => '信用卡'],
    'mobile_payment'    => ['en' => 'Mobile Payment',       'zh' => '行動支付'],
    'gift_code'         => ['en' => 'Gift Code',            'zh' => '禮品代碼'],
    'subtotal'          => ['en' => 'Subtotal',             'zh' => '小計'],
    'tax'               => ['en' => 'Tax',                  'zh' => '稅金'],
    'promo_discount'    => ['en' => 'Promo Discount',       'zh' => '優惠折扣'],
    'total'             => ['en' => 'Total',                'zh' => '總計'],
    'empty_cart'        => ['en' => 'Your cart is empty',   'zh' => '購物車是空的'],
    'browse_menu'       => ['en' => 'Browse Menu',          'zh' => '瀏覽菜單'],
    'meal_ready'        => ['en' => 'Meal is Ready!',       'zh' => '餐點已完成！'],
    'preparing'         => ['en' => "We're preparing your meal", 'zh' => '正在為您準備餐點'],
];

function t(string $key): string
{
    $lang = $_SESSION['lang'] ?? 'en';
    return $GLOBALS['__i18n'][$key][$lang] ?? $GLOBALS['__i18n'][$key]['en'] ?? $key;
}

function money(float $amount): string
{
    return '$' . number_format($amount, 2);
}

/**
 * The kiosk (public/) and the mobile site (mobile/) are separate
 * systems on separate ports, but share one set of product photos/brand
 * art. Rather than duplicate those files, the mobile app treats the
 * kiosk as the asset host and loads them cross-origin; the kiosk keeps
 * using plain same-origin paths as before.
 */
function asset(string $path): string
{
    $full = __DIR__ . '/../public/' . ltrim($path, '/');
    $v = is_file($full) ? filemtime($full) : time();
    $base = YK_APP === 'mobile' ? KIOSK_BASE_URL : '';
    return $base . '/' . ltrim($path, '/') . '?v=' . $v;
}

require_once __DIR__ . '/menu_data.php';
require_once __DIR__ . '/Cart.php';
require_once __DIR__ . '/QrPromo.php';
require_once __DIR__ . '/OrderStore.php';
require_once __DIR__ . '/partials.php';
