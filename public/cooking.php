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
$lang = $_SESSION['lang'] ?? 'en';
$item = $order['lines'][0]['item'] ?? null;

// Real cook time varies 90s-3min by dish (see 'cook_time' in
// menu_data.php) — the wait is NOT a fixed 45s. Three phases fill it:
//   1) A picture-in-picture split: a big QR code (left, majority of the
//      width) for whoever is next in line to scan and start browsing
//      (menu.php?browse=1), running alongside the "Catch the
//      Ingredients" game in a smaller pane (right) — both visible at
//      once from the start, not QR-then-game. Game is capped at 45s
//      regardless of real cook time — tapping for 3 minutes straight
//      isn't fun, it's exhausting.
//   2) At 45s the game pane and its "ingredients caught" counter are
//      gone outright (no final-score recap) and the QR pane expands to
//      fill the whole stage — bigger and easier to read from a step or
//      two back. Heading switches to one fixed line pointing the
//      current guest at the physical pickup window, plus a smaller
//      line about grabbing utensils from the compartment below while
//      they wait.
//   3) A bare 3-2-1 in the final 3 seconds as a "get ready" beat.
// No running numeric countdown anywhere else on screen (Amanda: a
// ticking number the whole time reads as "excessive wait").
$totalSeconds = isset($_GET['fast']) ? 12 : ($item['cook_time'] ?? 120);
$gameSeconds = min(45, max(0, $totalSeconds - 3));
?>
<!doctype html>
<html lang="<?= $lang === 'zh' ? 'zh-Hant' : 'en' ?>">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>Yo-Kai Express — <?= $lang === 'zh' ? '烹飪中' : 'Cooking' ?></title>
<link rel="stylesheet" href="<?= asset('assets/css/style.css') ?>">
</head>
<body>
<div class="cook-screen">
    <div class="cook-order">#<?= htmlspecialchars($order['number']) ?></div>
    <div class="cook-countdown" id="cookCountdown"><?= $lang === 'zh' ? '我們煮麵，你來玩遊戲！' : 'We cook the food, you play the game!' ?></div>
    <div class="cook-subtext" id="cookSubtext" style="display:none;"></div>

    <div class="cook-stage-wrap" id="cookStageWrap">
        <div class="stage-qr-pane" id="stageQrPane">
            <img class="qr-big-img" id="qrImgBig" alt="QR code">
            <div class="qr-big-caption"><?= $lang === 'zh' ? '下一位客人，請掃碼看菜單' : 'Next guest, please scan here to browse the menu' ?></div>
        </div>
        <div class="stage-game-pane" id="stageGamePane">
            <img class="game-chef" id="gameChef" src="<?= asset(mascot('shiba')['img']) ?>" alt="">
            <div class="game-bowl" id="gameBowl">🍜</div>
        </div>
    </div>

    <div class="catch-counter" id="catchCounter"><?= $lang === 'zh' ? '已接住食材：' : 'Ingredients caught: ' ?><span class="n" id="catchCount">0</span></div>
</div>
<script>
const lang = <?= json_encode($lang) ?>;
const total = <?= (int)$totalSeconds ?>;
const gameSeconds = <?= (int)$gameSeconds ?>;
let remaining = total;
const cookCountdown = document.getElementById('cookCountdown');
const cookSubtext = document.getElementById('cookSubtext');
const cookingText = lang === 'zh' ? '我們煮麵，你來玩遊戲！' : 'We cook the food, you play the game!';
const waitMessage = lang === 'zh' ? '掃碼看菜單<br>右側取餐 ➡️' : "Scan below to browse the menu<br>Pickup on the right ➡️";
const utensilNote = lang === 'zh' ? '餐具可以先到下方小門拿取' : 'Grab your chopsticks and napkins below';
const stageGamePane = document.getElementById('stageGamePane');
const stageQrPane = document.getElementById('stageQrPane');
const gameStage = stageGamePane;
const gameChef = document.getElementById('gameChef');
const gameBowl = document.getElementById('gameBowl');
const catchCountEl = document.getElementById('catchCount');
const catchCounter = document.getElementById('catchCounter');
let catchCount = 0;
let gamePhaseOver = false;

function startWaitPhase(){
    if (gamePhaseOver) return;
    gamePhaseOver = true;
    clearInterval(spawnTimer);
    document.querySelectorAll('.falling-item').forEach(el => el.remove());
    stageGamePane.classList.add('hidden');
    stageQrPane.classList.add('expanded');
    catchCounter.style.display = 'none';
    cookCountdown.innerHTML = waitMessage;
    cookSubtext.textContent = utensilNote;
    cookSubtext.style.display = 'block';
}

function vibrate(ms) {
    if (navigator.vibrate) { try { navigator.vibrate(ms); } catch (e) {} }
}

const ingredients = ['🥚', '🌿', '🥩'];
const hazards = ['💣'];
const fallDuration = 3200;
const hazardChance = 0.25;

function spawnIngredient(){
    const isHazard = Math.random() < hazardChance;
    const stageWidth = gameStage.clientWidth;
    const item = document.createElement('div');
    item.className = 'falling-item';
    item.textContent = isHazard
        ? hazards[Math.floor(Math.random() * hazards.length)]
        : ingredients[Math.floor(Math.random() * ingredients.length)];
    const x = 40 + Math.random() * (stageWidth - 80);
    item.style.left = x + 'px';
    item.style.animationDuration = fallDuration + 'ms';
    gameStage.appendChild(item);

    let caught = false;
    function catchItem(){
        if (caught) return;
        caught = true;
        const rect = item.getBoundingClientRect();
        const stageRect = gameStage.getBoundingClientRect();
        const pop = document.createElement('div');
        pop.className = 'catch-pop' + (isHazard ? ' bad' : '');
        gameStage.appendChild(pop);
        setTimeout(() => pop.remove(), 700);
        item.classList.add('caught');
        gameBowl.classList.remove('bounce', 'shake');
        void gameBowl.offsetWidth;

        if (isHazard) {
            catchCount = Math.max(0, catchCount - 1);
            pop.textContent = '−1';
            gameBowl.classList.add('shake');
            vibrate([30, 40, 30]);
        } else {
            catchCount++;
            pop.textContent = '+1';
            gameBowl.classList.add('bounce');
            vibrate(12);
        }
        catchCountEl.textContent = catchCount;
        pop.style.left = (rect.left - stageRect.left) + 'px';
        pop.style.top = (rect.top - stageRect.top) + 'px';
        setTimeout(() => item.remove(), 420);
    }
    item.addEventListener('pointerdown', catchItem);
    item.addEventListener('animationend', () => { if (!caught) item.remove(); });
}

let spawnTimer = setInterval(spawnIngredient, 1300);

const browseUrl = location.origin + '/menu.php?browse=1';
document.getElementById('qrImgBig').src = 'https://api.qrserver.com/v1/create-qr-code/?size=200x200&margin=2&data=' + encodeURIComponent(browseUrl);

function tick(){
    const elapsed = total - remaining;

    if (remaining <= 0) {
        cookCountdown.innerHTML = lang === 'zh' ? '好了！' : 'Ready!';
        cookSubtext.style.display = 'none';
        clearInterval(spawnTimer);
        setTimeout(() => { window.location.href = 'ready.php'; }, 500);
        return;
    }

    if (elapsed >= gameSeconds && !gamePhaseOver) {
        startWaitPhase();
    }

    if (remaining <= 3) {
        cookSubtext.style.display = 'none';
        cookCountdown.innerHTML = '<span class="n">' + remaining + '</span>';
    } else if (!gamePhaseOver) {
        cookCountdown.textContent = cookingText;
    }
    remaining--;
    setTimeout(tick, 1000);
}
tick();
</script>
</body>
</html>
