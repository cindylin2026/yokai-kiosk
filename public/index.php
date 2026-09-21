<?php
declare(strict_types=1);
require_once __DIR__ . '/../src/config.php';

$lang = $_SESSION['lang'] ?? 'en';

if (isset($_GET['lang']) && in_array($_GET['lang'], ['en', 'zh'], true)) {
    $_SESSION['lang'] = $_GET['lang'];
    header('Location: index.php');
    exit;
}
?>
<!doctype html>
<html lang="<?= $lang === 'zh' ? 'zh-Hant' : 'en' ?>">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>Yo-Kai Express</title>
<link rel="stylesheet" href="<?= asset('assets/css/style.css') ?>">
<style>
.attract-screen {
  width: 100%; min-height: 100vh;
  background: #ece8e1;
  display: flex; align-items: center; justify-content: center;
  padding: 18px; box-sizing: border-box;
}
.attract-frame {
  position: relative;
  width: calc(100vw - 36px); max-width: 1920px;
  height: calc((100vw - 36px) * 9 / 16); max-height: 1080px;
  background: #f5f0e8;
  border: 2px solid rgba(197,160,89,0.35);
  border-radius: 20px;
  box-shadow: 0 20px 60px rgba(25,27,30,0.10);
  overflow: hidden;
  cursor: pointer;
}

/* Logo — top center */
.attract-logo {
  position: absolute;
  top: 5%; left: 50%; transform: translateX(-50%);
  height: clamp(32px, 5.5%, 60px);
  width: auto;
  display: block;
  filter: drop-shadow(0 1px 6px rgba(0,0,0,0.18));
  z-index: 1;
}

/* Bowl image fills entire frame */
.attract-bowl-wrap {
  position: absolute;
  inset: 0;
  display: flex;
  align-items: center;
  justify-content: center;
  z-index: 0;
}
.attract-bowl {
  width: 100%;
  height: 100%;
  object-fit: contain;
  object-position: center;
  display: block;
}

/* Steam */
.attract-steam {
  position: absolute;
  bottom: 55%;
  left: 50%;
  transform: translateX(-50%);
  width: 160px;
  height: 110px;
  pointer-events: none;
  z-index: 2;
}
.attract-steam .steam {
  position: absolute; bottom: 0;
  width: 22px; border-radius: 50% 50% 20% 20%;
  background: rgba(255, 255, 255, 0.55);
  filter: blur(8px);
  animation: steam-rise 3s ease-in-out infinite;
  transform-origin: bottom center;
}
.attract-steam .steam:nth-child(1){ left: 8px;   height: 58px; animation-delay: 0s;    animation-duration: 2.8s; }
.attract-steam .steam:nth-child(2){ left: 46px;  height: 78px; animation-delay: 0.65s; animation-duration: 3.3s; }
.attract-steam .steam:nth-child(3){ left: 84px;  height: 64px; animation-delay: 1.15s; animation-duration: 2.7s; }
.attract-steam .steam:nth-child(4){ left: 120px; height: 70px; animation-delay: 0.35s; animation-duration: 3.6s; }
@keyframes steam-rise {
  0%   { transform: translateY(0)     scaleX(1)   rotate(-3deg); opacity: 0; }
  15%  { opacity: 1; }
  60%  { transform: translateY(-80px)  scaleX(1.7) rotate(5deg);  opacity: 0.5; }
  100% { transform: translateY(-130px) scaleX(2.3) rotate(-4deg); opacity: 0; }
}

/* Bottom CTA */
.attract-bottom {
  position: absolute;
  bottom: 2%; left: 50%; transform: translateX(-50%);
  display: flex;
  flex-direction: column;
  align-items: center;
  z-index: 1;
}
.attract-cta {
  display: inline-flex; align-items: center; justify-content: center;
  background: linear-gradient(180deg, #e8c96a, #c5a059);
  color: #1a1208;
  font-family: var(--font-serif); font-weight: 900;
  font-size: clamp(.95rem, 1.6vw, 1.35rem);
  letter-spacing: .1em;
  padding: .75em 2.6em;
  border-radius: 999px;
  text-decoration: none;
  box-shadow: 0 0 0 4px rgba(197,160,89,0.25), 0 10px 30px rgba(197,160,89,0.35);
  animation: cta-pulse 2.2s ease-in-out infinite;
}
@keyframes cta-pulse {
  0%,100% { box-shadow: 0 0 0 4px rgba(197,160,89,0.25), 0 10px 30px rgba(197,160,89,0.35); }
  50%      { box-shadow: 0 0 0 10px rgba(197,160,89,0.10), 0 10px 30px rgba(197,160,89,0.5); }
}

/* Lang dropdown — top right */
.attract-lang {
  position: absolute; top: 4%; right: 3%; z-index: 4;
}
.lang-select-btn {
  display: flex; align-items: center; gap: 8px;
  background: rgba(236,232,225,0.9);
  border: 1px solid rgba(197,160,89,0.4);
  border-radius: 999px; padding: 7px 16px;
  font-weight: 700; font-size: .82rem; color: #6b6862;
  cursor: pointer; font-family: var(--font-body);
  backdrop-filter: blur(6px);
}
.lang-select-btn .globe { font-size: 1rem; }
.lang-select-btn .chevron { font-size: .6rem; transition: transform .2s; }
.lang-select-btn.open .chevron { transform: rotate(180deg); }
.lang-dropdown {
  display: none; position: absolute; top: calc(100% + 8px); right: 0;
  background: rgba(250,248,244,0.97);
  border: 1px solid rgba(197,160,89,0.3);
  border-radius: 14px; overflow: hidden;
  box-shadow: 0 8px 28px rgba(25,27,30,0.14);
  min-width: 160px; backdrop-filter: blur(12px);
}
.lang-dropdown.open { display: block; }
.lang-option {
  display: flex; align-items: center; gap: 10px;
  padding: 11px 18px; cursor: pointer;
  font-size: .84rem; font-weight: 600; color: #2b2b2a;
  transition: background .15s;
}
.lang-option:hover { background: rgba(197,160,89,0.1); }
.lang-option.active { color: #c5a059; font-weight: 800; }
.lang-option .flag { font-size: 1.1rem; }
</style>
</head>
<body>
<div class="attract-screen">
  <div class="attract-frame" onclick="location.href='menu.php'">

    <div class="attract-bowl-wrap">
      <img class="attract-bowl"
           src="<?= asset('assets/img/ramen-nobg.png') ?>"
           alt="Chashu Tonkotsu Ramen">
    </div>

    <div class="attract-steam">
      <div class="steam"></div>
      <div class="steam"></div>
      <div class="steam"></div>
      <div class="steam"></div>
    </div>

    <img class="attract-logo" src="<?= asset('assets/brand/logo.png') ?>" alt="Yo-Kai Express">

    <div class="attract-bottom">
      <a class="attract-cta" href="menu.php" onclick="event.stopPropagation()">
        <?= $lang === 'zh' ? '點此點餐' : 'TAP TO ORDER' ?>
      </a>
    </div>

    <div class="attract-lang" onclick="event.stopPropagation()">
      <div class="lang-select-btn" id="langBtn" onclick="toggleLangMenu()">
        <span class="globe">🌐</span>
        <span id="langLabel"><?= $lang === 'zh' ? '中文' : 'English' ?></span>
        <span class="chevron">▼</span>
      </div>
      <div class="lang-dropdown" id="langDropdown">
        <div class="lang-option <?= $lang === 'en' ? 'active' : '' ?>" onclick="pickLang('en')"><span class="flag">🇺🇸</span> English</div>
        <div class="lang-option <?= $lang === 'zh' ? 'active' : '' ?>" onclick="pickLang('zh')"><span class="flag">🇹🇼</span> 中文</div>
        <div class="lang-option" onclick="pickLang('ja')"><span class="flag">🇯🇵</span> 日本語</div>
        <div class="lang-option" onclick="pickLang('ko')"><span class="flag">🇰🇷</span> 한국어</div>
        <div class="lang-option" onclick="pickLang('es')"><span class="flag">🇪🇸</span> Español</div>
      </div>
    </div>

  </div>
</div>
<script>
function toggleLangMenu() {
  const btn = document.getElementById('langBtn');
  const dd  = document.getElementById('langDropdown');
  btn.classList.toggle('open');
  dd.classList.toggle('open');
}
function pickLang(l) {
  location.href = 'index.php?lang=' + l;
}
// Close dropdown if user taps elsewhere on the frame
document.querySelector('.attract-frame').addEventListener('click', function() {
  document.getElementById('langBtn').classList.remove('open');
  document.getElementById('langDropdown').classList.remove('open');
});
</script>
</body>
</html>
