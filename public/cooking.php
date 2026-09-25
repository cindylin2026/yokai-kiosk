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

/* RIGHT — Shiba chef cooking animation */
.pickup-right {
  flex: 0 0 42%; display: flex; flex-direction: column;
  align-items: center; justify-content: center;
  gap: 14px;
}
.shiba-stage {
  position: relative;
  width: 100%; max-width: 360px;
  height: 340px;
  display: flex; align-items: flex-end; justify-content: center;
}

/* 3 images cycle via JS: chef (webp) + 2 gifs */
.shiba-img {
  position: absolute; bottom: 0;
  filter: drop-shadow(0 10px 20px rgba(25,27,30,0.18));
  transform-origin: bottom center;
  opacity: 0;
  transition: opacity .5s ease;
  max-height: 300px; width: auto;
}
.shiba-img.active { opacity: 1; }

/* chef webp: gentle bob */
.shiba-img-chef.active {
  animation: chef-bob-gentle 1.8s ease-in-out infinite;
}
@keyframes chef-bob-gentle {
  0%,100% { transform: translateY(0) rotate(-1deg); }
  50%      { transform: translateY(-10px) rotate(1deg); }
}

/* gif1 (stir): slight lean forward to match cooking context */
.shiba-img-gif1.active { animation: gif-lean 1s ease-in-out infinite alternate; }
@keyframes gif-lean {
  from { transform: rotate(0deg); }
  to   { transform: rotate(3deg) translateY(-4px); }
}

/* gif2 (flame): excited shake */
.shiba-img-gif2.active { animation: gif-shake .5s ease-in-out infinite alternate; }
@keyframes gif-shake {
  from { transform: rotate(-2deg) scale(1); }
  to   { transform: rotate(2deg) scale(1.03); }
}

.shiba-label {
  font-size: clamp(1rem, 1.4vw, 1.2rem);
  color: #8a7040; font-weight: 700; text-align: center;
}

/* Steam */
.shiba-steam {
  position: absolute; bottom: 30px; left: 50%;
  transform: translateX(-50%);
  width: 90px; height: 70px; pointer-events: none; z-index: 1;
}
.shiba-steam .s {
  position: absolute; bottom: 0;
  width: 12px; border-radius: 50% 50% 20% 20%;
  background: rgba(255,255,255,0.6);
  filter: blur(5px);
  animation: steam-rise 2.4s ease-in-out infinite;
  transform-origin: bottom center;
}
.shiba-steam .s:nth-child(1){ left:5px;  height:32px; animation-delay:0s;   animation-duration:2.4s; }
.shiba-steam .s:nth-child(2){ left:32px; height:44px; animation-delay:0.5s; animation-duration:2.8s; }
.shiba-steam .s:nth-child(3){ left:58px; height:36px; animation-delay:1s;   animation-duration:2.2s; }
@keyframes steam-rise {
  0%   { transform: translateY(0)     scaleX(1);   opacity: 0; }
  15%  { opacity: 1; }
  60%  { transform: translateY(-44px) scaleX(1.6); opacity: 0.5; }
  100% { transform: translateY(-75px) scaleX(2.1); opacity: 0; }
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
        <div class="shiba-stage" id="shibaStage">
          <!-- 3 images cycle: chef webp → gif stir → gif flame -->
          <img class="shiba-img shiba-img-chef active"
               src="<?= asset('assets/brand/mascot-shiba-chef.webp') ?>"
               alt="Shiba chef">
          <img class="shiba-img shiba-img-gif1"
               src="<?= asset('assets/brand/shiba-stir.gif') ?>"
               alt="Shiba stirring">
          <img class="shiba-img shiba-img-gif2"
               src="<?= asset('assets/brand/shiba-flame.gif') ?>"
               alt="Shiba flame cooking">
          <div class="shiba-steam">
            <div class="s"></div>
            <div class="s"></div>
            <div class="s"></div>
          </div>
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
// Cycle shiba images every 4s: chef webp → stir gif → flame gif → repeat
const shibaImgs = document.querySelectorAll('.shiba-img');
let shibaIdx = 0;
setInterval(function() {
  shibaImgs[shibaIdx].classList.remove('active');
  shibaIdx = (shibaIdx + 1) % shibaImgs.length;
  shibaImgs[shibaIdx].classList.add('active');
}, 4000);

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
