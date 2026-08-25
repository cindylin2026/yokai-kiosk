<?php
declare(strict_types=1);
require_once __DIR__ . '/../src/config.php';

if (empty(Cart::items())) {
    header('Location: cart.php');
    exit;
}
$method = (string)($_GET['method'] ?? 'card');
$lang = $_SESSION['lang'] ?? 'en';
?>
<!doctype html>
<html lang="<?= $lang === 'zh' ? 'zh-Hant' : 'en' ?>">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>Yo-Kai Express — <?= $lang === 'zh' ? '發票' : 'Receipt' ?></title>
<link rel="stylesheet" href="<?= asset('assets/css/style.css') ?>">
</head>
<body>
<div class="light-screen">
    <?php render_topbar(false); ?>
    <?php render_stepper('payment'); ?>

    <div class="light-card">
        <div class="receipt-print">
            <div class="rp-check">✓</div>
            <h2><?= $lang === 'zh' ? '付款成功' : 'PAYMENT SUCCESSFUL' ?></h2>
            <?php foreach (Cart::lines() as $line): ?>
                <div class="rp-line"><span><?= htmlspecialchars(item_name($line['item'])) ?> ×<?= $line['qty'] ?></span><span><?= money($line['lineTotal']) ?></span></div>
            <?php endforeach; ?>
            <div class="rp-line rp-total"><span><?= $lang === 'zh' ? '總計' : 'TOTAL' ?></span><span><?= money(Cart::total()) ?></span></div>
        </div>
        <p style="color:var(--label-secondary);margin:var(--sp-4) 0 0;"><?= $lang === 'zh' ? '真實收據已列印完成' : 'Your receipt just printed out' ?></p>
    </div>

    <div class="modal-backdrop" id="emailModal" style="display:none;">
        <div class="modal-card keyboard-card">
            <h3 class="vk-title"><?= $lang === 'zh' ? '輸入 Email 收取電子收據' : 'Enter your email for a digital receipt' ?></h3>
            <div class="vk-field" id="vkField"><span id="vkText"></span><span class="vk-caret">|</span></div>
            <div class="vk-domain-row" id="vkDomainRow"></div>
            <div class="virtual-keyboard" id="virtualKeyboard"></div>
            <button type="button" class="btn btn-primary btn-block" style="margin-top:var(--sp-3);" onclick="submitEmail()">
                <?= $lang === 'zh' ? '完成' : 'Done' ?>
            </button>
        </div>
    </div>
    <div class="modal-backdrop" id="emailDoneModal" style="display:none;">
        <div class="modal-card" style="text-align:center;">
            <div class="rp-check" style="margin:0 auto var(--sp-3);">✓</div>
            <h3 style="margin:0 0 6px;"><?= $lang === 'zh' ? '收據已寄出！' : 'Receipt sent!' ?></h3>
            <p style="color:var(--label-secondary);margin:0;" id="vkSentTo"></p>
        </div>
    </div>
</div>

<script>
const vkLang = <?= json_encode($lang) ?>;
let emailValue = '';
const vkRows = [
    ['1','2','3','4','5','6','7','8','9','0'],
    ['q','w','e','r','t','y','u','i','o','p'],
    ['a','s','d','f','g','h','j','k','l'],
    ['z','x','c','v','b','n','m','@','.'],
];
// The four most common consumer email domains (Gmail dominant nearly
// everywhere, then Outlook/Hotmail, Yahoo, and iCloud for iPhone
// users) — quick-fill buttons above the keyboard so most guests never
// have to type their domain by hand.
const commonDomains = ['gmail.com', 'outlook.com', 'yahoo.com', 'icloud.com'];

function buildDomainRow(){
    const row = document.getElementById('vkDomainRow');
    row.innerHTML = '';
    commonDomains.forEach(domain => {
        const btn = document.createElement('button');
        btn.type = 'button';
        btn.className = 'vk-domain';
        btn.textContent = '@' + domain;
        btn.onclick = () => {
            emailValue = (emailValue.includes('@') ? emailValue.split('@')[0] : emailValue) + '@' + domain;
            renderEmail();
        };
        row.appendChild(btn);
    });
}

function buildKeyboard(){
    const kb = document.getElementById('virtualKeyboard');
    kb.innerHTML = '';
    vkRows.forEach(row => {
        const rowEl = document.createElement('div');
        rowEl.className = 'vk-row';
        row.forEach(ch => {
            const key = document.createElement('button');
            key.type = 'button';
            key.className = 'vk-key';
            key.textContent = ch;
            key.onclick = () => { emailValue += ch; renderEmail(); };
            rowEl.appendChild(key);
        });
        kb.appendChild(rowEl);
    });
    const bottomRow = document.createElement('div');
    bottomRow.className = 'vk-row';
    const dash = document.createElement('button');
    dash.type = 'button'; dash.className = 'vk-key'; dash.textContent = '-_';
    dash.onclick = () => { emailValue += '-'; renderEmail(); };
    const space = document.createElement('button');
    space.type = 'button'; space.className = 'vk-key vk-space'; space.textContent = vkLang === 'zh' ? '空白' : 'space';
    space.onclick = () => { emailValue += ' '; renderEmail(); };
    const back = document.createElement('button');
    back.type = 'button'; back.className = 'vk-key vk-back'; back.textContent = '⌫';
    back.onclick = () => { emailValue = emailValue.slice(0, -1); renderEmail(); };
    bottomRow.appendChild(dash);
    bottomRow.appendChild(space);
    bottomRow.appendChild(back);
    kb.appendChild(bottomRow);
}

function renderEmail(){
    document.getElementById('vkText').textContent = emailValue;
}

function openEmailKeyboard(){
    emailValue = '';
    renderEmail();
    buildKeyboard();
    buildDomainRow();
    document.getElementById('emailModal').style.display = 'flex';
}
function submitEmail(){
    if (!emailValue.includes('@')) {
        document.getElementById('vkField').style.animation = 'shake .4s';
        setTimeout(() => { document.getElementById('vkField').style.animation = ''; }, 400);
        return;
    }
    document.getElementById('emailModal').style.display = 'none';
    document.getElementById('vkSentTo').textContent = emailValue;
    document.getElementById('emailDoneModal').style.display = 'flex';
    setTimeout(function(){ window.location.href = 'cooking.php?method=<?= urlencode($method) ?>&email=1'; }, 1600);
}

// Email is mandatory on every order, not just offered — this modal has
// no close button and the underlying page has no other way forward, so
// entering a valid-looking email (submitEmail's own @-check) is the
// only path to "Done." Opens automatically the instant the receipt
// renders, right where a guest already expects to be asked "how do you
// want your receipt."
openEmailKeyboard();
</script>
</body>
</html>
