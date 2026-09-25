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
* { box-sizing: border-box; }
.error-screen {
  min-height: 100vh; width: 100%;
  background: #ece8e1;
  display: flex; align-items: center; justify-content: center;
  padding: 18px;
}
.error-frame {
  width: calc(100vw - 36px); max-width: 1400px;
  height: calc((100vw - 36px) * 9 / 16); max-height: 787px;
  background: #f6f4f0;
  border-radius: 22px; border: 1px solid rgba(197,160,89,0.28);
  box-shadow: 0 0 0 1px rgba(197,160,89,0.1), 0 30px 70px rgba(25,27,30,0.12);
  display: flex; flex-direction: column;
  overflow: hidden; position: relative;
}

/* Logo top-left */
.error-logo {
  position: absolute; top: 4%; left: 4%; z-index: 2;
}
.error-logo img { height: 28px; width: auto; }

/* Two-column body */
.error-body {
  flex: 1; display: flex; min-height: 0;
  padding: 6% 6% 6% 4%;
  align-items: center;
  gap: 0;
}

/* LEFT — sad chef */
.error-left {
  flex: 0 0 40%;
  display: flex; align-items: flex-end; justify-content: center;
  padding-bottom: 2%;
}
.error-chef {
  /* Grayscale + slight droop = sad, dejected look */
  height: clamp(160px, 36%, 300px);
  width: auto;
  filter: grayscale(1) brightness(0.72) drop-shadow(0 8px 20px rgba(25,27,30,0.18));
  transform: rotate(-8deg) translateY(6%);
  transform-origin: bottom center;
}

/* RIGHT — message */
.error-right {
  flex: 1; min-width: 0;
  display: flex; flex-direction: column;
  justify-content: center;
  padding-left: 5%;
}

.error-badge {
  display: inline-flex; align-items: center; gap: 8px;
  background: rgba(197,57,42,0.08); border: 1.5px solid rgba(197,57,42,0.25);
  border-radius: 999px; padding: 6px 16px;
  font-size: clamp(.72rem, 1vw, .88rem); font-weight: 800;
  color: #c5392a; letter-spacing: .04em; text-transform: uppercase;
  margin-bottom: 5%;
  align-self: flex-start;
}

.error-headline {
  font-family: var(--font-display); font-weight: 900;
  font-size: clamp(1.6rem, 3.5vw, 3rem);
  color: #2b2b2a; line-height: 1.2;
  margin: 0 0 3%;
}

.error-sub {
  font-size: clamp(.9rem, 1.4vw, 1.15rem);
  color: #6b6862; line-height: 1.6; font-weight: 500;
  margin: 0 0 7%;
}

/* Phone card — prominent */
.error-phone-card {
  display: flex; align-items: center; gap: 18px;
  background: #fff;
  border: 2px solid rgba(197,160,89,0.4);
  border-radius: 18px; padding: 18px 24px;
  box-shadow: 0 8px 28px rgba(25,27,30,0.08);
  align-self: flex-start;
}
.error-phone-icon {
  width: 48px; height: 48px; border-radius: 12px;
  background: rgba(197,57,42,0.08);
  display: flex; align-items: center; justify-content: center;
  font-size: 1.4rem; flex: 0 0 auto;
}
.error-phone-text {}
.error-phone-label {
  font-size: clamp(.68rem, .9vw, .8rem);
  font-weight: 800; text-transform: uppercase; letter-spacing: .08em;
  color: #8a8378; margin-bottom: 4px;
}
.error-phone-number {
  font-family: var(--font-mono); font-weight: 900;
  font-size: clamp(1.3rem, 2.5vw, 2rem);
  color: #2b2b2a; letter-spacing: .06em;
}
</style>
</head>
<body>
<div class="error-screen">
  <div class="error-frame">

    <div class="error-logo">
      <img src="<?= asset('assets/brand/logo.png') ?>" alt="Yo-Kai Express">
    </div>

    <div class="error-body">

      <!-- Left: sad shiba chef -->
      <div class="error-left">
        <img class="error-chef"
             src="<?= asset('assets/brand/mascot-shiba-chef.webp') ?>"
             alt="Sad Shiba Chef">
      </div>

      <!-- Right: message + phone -->
      <div class="error-right">
        <div class="error-badge">⚠️ System Error</div>

        <h1 class="error-headline">Uh Oh!<br>Something is not right.</h1>

        <p class="error-sub">
          Please contact customer service for immediate assistance.
        </p>

        <div class="error-phone-card">
          <div class="error-phone-icon">📞</div>
          <div class="error-phone-text">
            <div class="error-phone-label">Customer Service Hotline</div>
            <div class="error-phone-number">1-855-965-2439</div>
          </div>
        </div>
      </div>

    </div>
  </div>
</div>
</body>
</html>
