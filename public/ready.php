<?php
declare(strict_types=1);
require_once __DIR__ . '/../src/config.php';

$order = $_SESSION['last_order'] ?? null;
if (!$order) {
    header('Location: menu.php');
    exit;
}
$lang = $_SESSION['lang'] ?? 'en';
?>
<!doctype html>
<html lang="<?= $lang === 'zh' ? 'zh-Hant' : 'en' ?>">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>Yo-Kai Express — <?= t('meal_ready') ?></title>
<link rel="stylesheet" href="<?= asset('assets/css/style.css') ?>">
<style>
* { box-sizing: border-box; }
.ready-screen {
  min-height: 100vh; width: 100%;
  background: #ece8e1;
  display: flex; align-items: center; justify-content: center;
  padding: 18px;
}
.ready-frame {
  width: calc(100vw - 36px); max-width: 1400px;
  height: calc((100vw - 36px) * 9 / 16); max-height: 787px;
  background: #f6f4f0;
  border-radius: 22px; border: 1px solid rgba(197,160,89,0.28);
  box-shadow: 0 0 0 1px rgba(197,160,89,0.1), 0 30px 70px rgba(25,27,30,0.12);
  display: flex; flex-direction: column;
  align-items: center; justify-content: center;
  position: relative; overflow: hidden; text-align: center;
  padding: 5% 8%;
}

/* Logo */
.ready-logo {
  position: absolute; top: 4%; left: 4%;
}
.ready-logo img { height: 28px; width: auto; }

/* Order number */
.ready-order-no {
  font-family: var(--font-mono); font-weight: 700; font-size: clamp(.8rem, 1.2vw, 1rem);
  letter-spacing: .06em; color: #8a7040;
  background: rgba(197,160,89,0.1); border: 1px solid rgba(197,160,89,0.25);
  border-radius: 999px; padding: 6px 18px;
  margin-bottom: 4%;
}

/* Enjoy headline */
.ready-enjoy {
  font-family: var(--font-display); font-weight: 900;
  font-size: clamp(2.2rem, 5.5vw, 4.8rem);
  color: #c5a059; line-height: 1.1;
  margin-bottom: 3%;
}

/* Door open status */
.ready-door {
  display: inline-flex; align-items: center; gap: 10px;
  background: rgba(76,175,109,0.1); border: 1.5px solid rgba(76,175,109,0.35);
  border-radius: 999px; padding: 10px 24px;
  font-weight: 700; font-size: clamp(.95rem, 1.6vw, 1.3rem);
  color: #2b6b3e; margin-bottom: 5%;
}
.ready-door .dot {
  width: 10px; height: 10px; border-radius: 50%; background: #4caf6d;
  animation: dot-pulse 1.2s ease-in-out infinite;
}
@keyframes dot-pulse { 0%,100%{opacity:1;} 50%{opacity:.3;} }

/* Caution + stir — big and clear */
.ready-notices {
  display: flex; flex-direction: column; gap: 14px;
  width: 100%; max-width: 700px;
}
.ready-notice {
  display: flex; align-items: center; gap: 16px;
  background: #fff; border-radius: 14px;
  padding: 16px 24px;
  box-shadow: 0 4px 16px rgba(25,27,30,0.06);
  text-align: left;
}
.ready-notice .ni { font-size: clamp(1.4rem, 2.5vw, 2rem); flex: 0 0 auto; }
.ready-notice .nt {
  font-weight: 800;
  font-size: clamp(1rem, 1.8vw, 1.45rem);
  line-height: 1.3;
}
.ready-notice.hot  { border-left: 5px solid #c5392a; }
.ready-notice.hot .nt { color: #c5392a; }
.ready-notice.stir { border-left: 5px solid #c5a059; }
.ready-notice.stir .nt { color: #6b6862; }

/* Fireworks */
.fireworks-layer { position: absolute; top: 0; left: 0; right: 0; bottom: 0; pointer-events: none; overflow: hidden; z-index: 1; }
.firework { position: absolute; width: 6px; height: 6px; border-radius: 50%; animation: firework-burst 1.1s ease-out forwards; }
@keyframes firework-burst { 0%{transform:translate(0,0) scale(1);opacity:1;} 100%{transform:translate(var(--fx),var(--fy)) scale(.3);opacity:0;} }
</style>
</head>
<body>
<div class="ready-screen">
  <div class="ready-frame">

    <div class="fireworks-layer" id="fireworks"></div>

    <div class="ready-logo">
      <img src="<?= asset('assets/brand/logo.png') ?>" alt="Yo-Kai Express">
    </div>

    <div class="ready-order-no">#<?= htmlspecialchars($order['number']) ?></div>

    <div class="ready-enjoy">
      <?= $lang === 'zh' ? '祝您用餐愉快！' : 'Enjoy your meal!' ?>
    </div>

    <div class="ready-door">
      <span class="dot"></span>
      <?= $lang === 'zh' ? '出餐口已開啟，請取餐' : 'Pickup door is open — please collect your order' ?>
    </div>

    <div class="ready-notices">
      <div class="ready-notice hot">
        <span class="ni">🔥</span>
        <span class="nt"><?= $lang === 'zh' ? '注意：碗很燙，請小心' : 'Caution: bowl is hot — handle with care' ?></span>
      </div>
      <div class="ready-notice stir">
        <span class="ni">🥢</span>
        <span class="nt"><?= $lang === 'zh' ? '享用前請攪拌均勻' : 'Please give your bowl a good stir before eating' ?></span>
      </div>
    </div>

  </div>
</div>

<script>
function burstFireworks(){
  const layer = document.getElementById('fireworks');
  const colors = ['#c5a059','#d9c08a','#4caf6d','#2b2b2a'];
  for (let i = 0; i < 28; i++) {
    const f = document.createElement('div');
    f.className = 'firework';
    const angle = Math.random() * Math.PI * 2;
    const dist  = 80 + Math.random() * 180;
    f.style.setProperty('--fx', Math.cos(angle) * dist + 'px');
    f.style.setProperty('--fy', Math.sin(angle) * dist + 'px');
    f.style.left = (20 + Math.random() * 60) + '%';
    f.style.top  = (10 + Math.random() * 35) + '%';
    f.style.background = colors[i % colors.length];
    f.style.animationDelay = (Math.random() * 0.4) + 's';
    layer.appendChild(f);
  }
  setTimeout(() => { layer.innerHTML = ''; }, 1600);
}
burstFireworks();
setTimeout(() => { window.location.href = 'index.php'; }, 5000);
</script>
</body>
</html>
