<?php
declare(strict_types=1);
require_once __DIR__ . '/../src/config.php';

if (!empty(Cart::items())) {
    $method = (string)($_GET['method'] ?? 'card');
    Cart::finalizeOrder($method);
    $_SESSION['stamps'] = min(5, (int)($_SESSION['stamps'] ?? 0) + 1);
}
$order = $_SESSION['last_order'] ?? null;
if (!$order) {
    header('Location: menu.php');
    exit;
}
$lang      = $_SESSION['lang'] ?? 'en';
$item      = $order['lines'][0]['item'] ?? null;
$totalSecs = isset($_GET['fast']) ? 10 : ($item['cook_time'] ?? 120);
$browseUrl = KIOSK_BASE_URL . '/menu.php?browse=1';
?>
<!doctype html>
<html lang="<?= $lang === 'zh' ? 'zh-Hant' : 'en' ?>">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>Yo-Kai Express</title>
<link rel="stylesheet" href="<?= asset('assets/css/style.css') ?>">
<style>
* { box-sizing: border-box; }
.pickup-screen {
  min-height: 100vh; width: 100%;
  background: #ece8e1;
  display: flex; align-items: center; justify-content: center;
  padding: 18px;
}
.pickup-frame {
  width: calc(100vw - 36px); max-width: 1400px;
  height: calc((100vw - 36px) * 9 / 16); max-height: 787px;
  background: #f6f4f0;
  border-radius: 22px; border: 1px solid rgba(197,160,89,0.28);
  box-shadow: 0 0 0 1px rgba(197,160,89,0.1), 0 30px 70px rgba(25,27,30,0.12);
  display: flex; flex-direction: column;
  position: relative; overflow: hidden;
}

/* Topbar */
.pickup-topbar {
  display: flex; align-items: center; justify-content: space-between;
  padding: 14px 28px 0; flex: 0 0 auto;
}
.pickup-topbar img { height: 28px; width: auto; }
.pickup-order-no {
  font-family: var(--font-mono); font-weight: 700; font-size: .82rem;
  letter-spacing: .06em; color: #8a7040;
  background: rgba(197,160,89,0.1); border: 1px solid rgba(197,160,89,0.25);
  border-radius: 999px; padding: 5px 16px;
}

/* Two-column body */
.pickup-body {
  flex: 1; display: flex; min-height: 0;
  padding: 16px 28px 22px; gap: 24px;
}

/* LEFT — QR column */
.pickup-left {
  flex: 1; display: flex; flex-direction: column;
  align-items: center; justify-content: center; gap: 18px;
}
.pickup-headline {
  font-family: var(--font-display); font-weight: 900;
  font-size: clamp(2rem, 4.5vw, 3.8rem);
  color: #2b2b2a; text-align: center; line-height: 1.15;
}
.pickup-headline .arrow { color: #c5a059; }
.pickup-sub {
  font-size: clamp(1.1rem, 1.8vw, 1.5rem);
  color: #6b6862; text-align: center; font-weight: 600;
}
.pickup-qr {
  background: #fff; border-radius: 16px;
  border: 2px solid rgba(197,160,89,0.3);
  padding: 14px;
  box-shadow: 0 8px 28px rgba(25,27,30,0.08);
}
.pickup-qr img {
  display: block;
  width: clamp(160px, 20vw, 260px);
  height: auto;
}

/* RIGHT — Shiba chef animation */
.pickup-right {
  flex: 0 0 38%; display: flex; flex-direction: column;
  align-items: center; justify-content: center;
  gap: 12px;
}
.shiba-stage {
  position: relative;
  width: 100%; max-width: 300px;
  height: 260px;
  display: flex; align-items: flex-end; justify-content: center;
}

.shiba-chef-img {
  height: 200px; width: auto;
  filter: drop-shadow(0 8px 16px rgba(25,27,30,0.15));
  transform-origin: bottom center;
  animation: shiba-cycle 9s ease-in-out infinite;
}
/* 3 poses cycling: bob → stir (lean forward) → taste (lean back + flip) → celebrate (jump) */
@keyframes shiba-cycle {
  /* idle bob — pose 1 */
  0%   { transform: translateY(0)     rotate(-2deg) scaleX(1); }
  4%   { transform: translateY(-12px) rotate(-2deg) scaleX(1); }
  8%   { transform: translateY(0)     rotate(-2deg) scaleX(1); }
  12%  { transform: translateY(-12px) rotate(-2deg) scaleX(1); }
  16%  { transform: translateY(0)     rotate(-2deg) scaleX(1); }
  /* transition to stir */
  20%  { transform: translateY(0)     rotate(0deg)  scaleX(1); }
  /* stirring — lean forward — pose 2 */
  24%  { transform: translateY(-5px)  rotate(10deg) scaleX(1); }
  28%  { transform: translateY(0)     rotate(10deg) scaleX(1); }
  32%  { transform: translateY(-5px)  rotate(10deg) scaleX(1); }
  36%  { transform: translateY(0)     rotate(10deg) scaleX(1); }
  40%  { transform: translateY(-5px)  rotate(10deg) scaleX(1); }
  44%  { transform: translateY(0)     rotate(10deg) scaleX(1); }
  /* transition to taste */
  48%  { transform: translateY(0)     rotate(0deg)  scaleX(1); }
  /* tasting — lean back + mirror flip — pose 3 */
  52%  { transform: translateY(-4px)  rotate(-12deg) scaleX(-1); }
  56%  { transform: translateY(0)     rotate(-12deg) scaleX(-1); }
  60%  { transform: translateY(-4px)  rotate(-12deg) scaleX(-1); }
  64%  { transform: translateY(0)     rotate(-12deg) scaleX(-1); }
  /* transition to celebrate */
  68%  { transform: translateY(0)     rotate(0deg)  scaleX(1); }
  /* celebrate — jump! */
  74%  { transform: translateY(-22px) rotate(0deg)  scaleX(1) scaleY(1.08); }
  78%  { transform: translateY(0)     rotate(0deg)  scaleX(1) scaleY(0.92); }
  82%  { transform: translateY(-10px) rotate(0deg)  scaleX(1) scaleY(1.04); }
  86%  { transform: translateY(0)     rotate(0deg)  scaleX(1) scaleY(1); }
  /* back to idle */
  100% { transform: translateY(0)     rotate(-2deg) scaleX(1); }
}

.shiba-label {
  font-size: clamp(1rem, 1.4vw, 1.2rem);
  color: #8a7040; font-weight: 700; text-align: center;
}
.shiba-pot {
  position: absolute; bottom: 0; left: 50%;
  transform: translateX(-50%);
  font-size: clamp(2.5rem, 5vw, 4rem);
  animation: pot-bubble 1.2s ease-in-out infinite alternate;
}
@keyframes pot-bubble {
  from { transform: translateX(-50%) scale(1); }
  to   { transform: translateX(-50%) scale(1.06); }
}

/* Steam from pot */
.shiba-steam {
  position: absolute; bottom: 52px; left: 50%;
  transform: translateX(-50%);
  width: 80px; height: 60px; pointer-events: none;
}
.shiba-steam .s {
  position: absolute; bottom: 0;
  width: 10px; border-radius: 50% 50% 20% 20%;
  background: rgba(255,255,255,0.65);
  filter: blur(4px);
  animation: steam-rise 2.4s ease-in-out infinite;
  transform-origin: bottom center;
}
.shiba-steam .s:nth-child(1){ left:5px;  height:30px; animation-delay:0s;   animation-duration:2.4s; }
.shiba-steam .s:nth-child(2){ left:28px; height:40px; animation-delay:0.5s; animation-duration:2.8s; }
.shiba-steam .s:nth-child(3){ left:52px; height:34px; animation-delay:1s;   animation-duration:2.2s; }
@keyframes steam-rise {
  0%   { transform: translateY(0)    scaleX(1)   opacity: 0; }
  15%  { opacity: 1; }
  60%  { transform: translateY(-40px) scaleX(1.5); opacity: 0.5; }
  100% { transform: translateY(-70px) scaleX(2);   opacity: 0; }
}

.shiba-label {
  color: #8a7040; font-weight: 700; text-align: center;
}

/* Countdown overlay */
.pickup-countdown {
  position: absolute; inset: 0; z-index: 10;
  background: #f6f4f0;
  display: none; align-items: center; justify-content: center;
  font-family: var(--font-display); font-weight: 900;
  font-size: clamp(8rem, 20vw, 14rem); color: #c5a059;
}
</style>
</head>
<body>
<div class="pickup-screen">
  <div class="pickup-frame">

    <div class="pickup-topbar">
      <img src="<?= asset('assets/brand/logo.png') ?>" alt="Yo-Kai Express">
      <div class="pickup-order-no">#<?= htmlspecialchars($order['number']) ?></div>
    </div>

    <div class="pickup-body">

      <!-- LEFT: headlines + QR -->
      <div class="pickup-left">
        <div class="pickup-headline">
          <?= $lang === 'zh'
            ? '取餐請至右側 <span class="arrow">→</span>'
            : 'Pick up on the right <span class="arrow">→</span>' ?>
        </div>
        <div class="pickup-sub">
          <?= $lang === 'zh'
            ? '下一位顧客：掃描 QR Code 搶先看菜單'
            : 'Next guest: scan to browse the menu' ?>
        </div>
        <div class="pickup-qr">
          <img id="qrImg" alt="QR code">
        </div>
      </div>

      <!-- RIGHT: Shiba chef cooking animation -->
      <div class="pickup-right">
        <div class="shiba-stage">
          <img class="shiba-chef-img"
               src="<?= asset('assets/brand/mascot-shiba-chef.webp') ?>"
               alt="Shiba chef cooking">
          <div class="shiba-steam">
            <div class="s"></div>
            <div class="s"></div>
            <div class="s"></div>
          </div>
          <div class="shiba-pot">🍜</div>
        </div>
        <div class="shiba-label">
          <?= $lang === 'zh' ? '正在為您烹調…' : 'Cooking your order…' ?>
        </div>
      </div>

    </div><!-- .pickup-body -->

    <div class="pickup-countdown" id="countdown"></div>

  </div>
</div>

<script>
const total     = <?= (int)$totalSecs ?>;
const browseUrl = <?= json_encode($browseUrl) ?>;
let remaining   = total;

document.getElementById('qrImg').src =
  'https://api.qrserver.com/v1/create-qr-code/?size=280x280&margin=2&data=' +
  encodeURIComponent(browseUrl);

const countdown = document.getElementById('countdown');

function tick() {
  if (remaining <= 0) { window.location.href = 'ready.php'; return; }

  if (remaining <= 3) {
    countdown.style.display = 'flex';
    countdown.textContent   = remaining;
  }

  remaining--;
  setTimeout(tick, 1000);
}
tick();
</script>
</body>
</html>
