<?php
declare(strict_types=1);
require_once __DIR__ . '/../src/config.php';
$lang = $_SESSION['lang'] ?? 'en';
?>
<!doctype html>
<html lang="<?= $lang === 'zh' ? 'zh-Hant' : 'en' ?>">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>Yo-Kai Express — Error</title>
<link rel="stylesheet" href="<?= asset('assets/css/style.css') ?>">
<style>
.error-screen {
  min-height: 100vh; width: 100%;
  background: #ece8e1;
  display: flex; align-items: center; justify-content: center;
  padding: 18px; box-sizing: border-box;
}
.error-frame {
  width: calc(100vw - 36px); max-width: 1400px;
  height: calc((100vw - 36px) * 9 / 16); max-height: 787px;
  background: #f6f4f0;
  border-radius: 22px; border: 1px solid rgba(197,160,89,0.28);
  box-shadow: 0 0 0 1px rgba(197,160,89,0.1), 0 30px 70px rgba(25,27,30,0.12);
  display: flex; flex-direction: column;
  align-items: center; justify-content: center;
  text-align: center; padding: 6% 8%;
  position: relative; overflow: hidden;
}

/* Logo top-left */
.error-logo {
  position: absolute; top: 4%; left: 4%;
}
.error-logo img { height: 28px; width: auto; }

/* Mascot */
.error-mascot {
  width: clamp(80px, 12vw, 140px); height: auto;
  margin-bottom: 4%;
  animation: error-shake 0.6s ease-in-out 1s 3;
}
@keyframes error-shake {
  0%,100% { transform: translateX(0) rotate(0deg); }
  20%      { transform: translateX(-8px) rotate(-4deg); }
  40%      { transform: translateX(8px) rotate(4deg); }
  60%      { transform: translateX(-6px) rotate(-3deg); }
  80%      { transform: translateX(6px) rotate(3deg); }
}

/* Error icon */
.error-icon {
  font-size: clamp(2.5rem, 5vw, 4rem);
  margin-bottom: 3%;
  animation: icon-pulse 2s ease-in-out infinite;
}
@keyframes icon-pulse {
  0%,100% { transform: scale(1); }
  50%      { transform: scale(1.1); }
}

/* Text */
.error-oops {
  font-family: var(--font-display); font-weight: 900;
  font-size: clamp(1.8rem, 4vw, 3.2rem);
  color: #c5392a; margin: 0 0 2%;
  line-height: 1.15;
}
.error-msg {
  font-size: clamp(.95rem, 1.6vw, 1.3rem);
  color: #6b6862; font-weight: 500;
  max-width: 600px; line-height: 1.6;
  margin: 0 0 5%;
}

/* Phone number */
.error-phone {
  display: inline-flex; align-items: center; gap: 12px;
  background: #fff; border: 2px solid rgba(197,160,89,0.35);
  border-radius: 16px; padding: 14px 28px;
  box-shadow: 0 6px 20px rgba(25,27,30,0.08);
}
.error-phone .phone-label {
  font-size: clamp(.75rem, 1.1vw, .9rem);
  color: #8a8378; font-weight: 700;
  text-transform: uppercase; letter-spacing: .06em;
}
.error-phone .phone-number {
  font-family: var(--font-mono); font-weight: 900;
  font-size: clamp(1.2rem, 2.2vw, 1.8rem);
  color: #2b2b2a; letter-spacing: .04em;
}

/* Back button */
.error-back {
  position: absolute; bottom: 5%; left: 50%; transform: translateX(-50%);
  display: inline-flex; align-items: center; gap: 8px;
  font-size: clamp(.75rem, 1vw, .88rem); font-weight: 700;
  color: #8a8378; text-decoration: none;
  border: 1px solid rgba(197,160,89,0.25); border-radius: 999px;
  padding: 6px 18px; background: rgba(197,160,89,0.05);
  transition: all .18s;
}
.error-back:hover { border-color: #c5a059; color: #6b6862; }
</style>
</head>
<body>
<div class="error-screen">
  <div class="error-frame">

    <div class="error-logo">
      <img src="<?= asset('assets/brand/logo.png') ?>" alt="Yo-Kai Express">
    </div>

    <img class="error-mascot"
         src="<?= asset('assets/brand/mascot-shiba-avatar.webp') ?>"
         alt="Shiba mascot">

    <div class="error-icon">⚠️</div>

    <h1 class="error-oops">Uh Oh! Something is not right.</h1>

    <p class="error-msg">
      Please contact customer service for immediate assistance.
    </p>

    <div class="error-phone">
      <div>
        <div class="phone-label">Customer Service</div>
        <div class="phone-number">1-855-965-2439</div>
      </div>
    </div>

    <a class="error-back" href="index.php">← Return to Start</a>

  </div>
</div>
</body>
</html>
