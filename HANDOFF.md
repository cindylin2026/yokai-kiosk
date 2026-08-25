# Yo-Kai Express — Engineering Handoff Notes

For Roger. This is a **design prototype**, not production code — it's a
fully clickable PHP app (real routing, real session state, real
business rules like stock limits) built so the design could be tested
end-to-end rather than reviewed as static frames. Some of it is real
logic worth porting as-is; a lot of it is a realistic-looking stand-in
for a backend service that doesn't exist yet. This doc tells you which
is which.

Run it locally with `php -S localhost:8990 -t public` from the repo
root.

---

## 1. What's real logic (safe to port as-is)

- **Stock enforcement** — `Cart::add()` in `src/Cart.php` hard-caps
  quantity at the item's real-time `stock` field. A guest can never
  order more than what's on hand.
- **One-bowl-per-order rule** — `menu.php`'s `add_bowl` handler calls
  `Cart::clear()` before `Cart::add()`, so selecting a new item always
  *replaces* the cart rather than accumulating. This is deliberate,
  not a bug — the kiosk only ever makes one bowl per transaction.
- **Session-based cart/order state** — `$_SESSION['cart']`,
  `$_SESSION['lang']`, `$_SESSION['last_order']`, `$_SESSION['stamps']`
  drive the whole flow. Straightforward and framework-agnostic.
- **Bilingual EN/中文** — every string goes through either the `t()`
  dictionary in `src/config.php` or an inline `$lang === 'zh' ? … : …`
  ternary. No hardcoded English anywhere in the live flow.
- **The CSS "light-scope" token pattern** — screens re-theme by
  redefining CSS custom properties (`--bg-0`, `--label`, `--accent`,
  etc.) inside a wrapper class (`.cart-screen`, `.light-screen`,
  `.cook-screen`, `.ready-screen`, `.explore-screen`/`.attract-screen`).
  Shared components (topbar, buttons, modals) consume the tokens via
  `var()`, so the whole palette can be swapped by editing ~4 token
  blocks instead of every component. **This system went through five
  full palette pivots during design review and this pattern is what
  made each one a same-day turnaround instead of a rewrite** — worth
  keeping the same architecture even in a real frontend stack (CSS
  variables, a theme object, whatever fits your stack).
- **Cook-time-driven wait screen** (`public/cooking.php`) — real
  per-dish `cook_time` (seconds) now lives in `src/menu_data.php`. The
  wait screen splits into three phases off that real number: a 45s-max
  mini-game, a "scan to browse" QR takeover for whoever's next in
  line, then a 3-2-1 in the final 3 seconds. The *values* for
  `cook_time` are placeholders I estimated (90–170s by dish
  complexity) — get real numbers from the kitchen/machine before
  shipping, the phase-timing logic itself doesn't need to change.

## 2. What's simulated — needs a real backend before launch

- **Payment is entirely fake.** `pay_card.php` / `pay_mobile.php` /
  `pay_giftcode.php` just show a spinner for ~2.6s and redirect —
  there is no payment gateway integration of any kind. `?simulate=decline`
  on `pay_card.php` shows the decline UI on demand for demo purposes.
- **Promo codes are a hardcoded array** (`Cart::applyPromo()` in
  `src/Cart.php`) — `WELCOME10` → 10% off, `YOKAI15` → 15% off. No
  validation service, no expiry, no per-user limits.
- **Email receipts are not actually sent.** `receipt.php`'s email
  keyboard validates for an `@` character, shows "Receipt sent!", and
  moves on — nothing is transmitted. Wire this to a real email service
  before launch; the UI (mandatory email capture, no skip, common-domain
  quick-fill buttons) is meant to ship as designed.
- **Menu content is a static PHP array** (`src/menu_data.php`) — prices,
  descriptions, stock counts, allergens, nutrition facts. In production
  this needs to come from a real menu/inventory system; stock in
  particular needs to be live, not a number I typed in.
- **The QR code on `cooking.php`** ("scan to browse the menu," for
  whoever's next in line) is generated via the public `api.qrserver.com`
  image API (`<img src="https://api.qrserver.com/...">`). Fine for a
  demo, but an external, non-guaranteed dependency for a kiosk that
  should work reliably offline/on a closed network — swap for a
  bundled QR-generation library.
- **`src/OrderStore.php`** persists order records as flat JSON files in
  `var/orders/` (auto-created on first write). It exists to support
  `public/scan.php`, a QR-redemption endpoint — currently unreachable
  in normal use since nothing generates a redemption QR yet, but the
  file-read logic itself is real and would need to become real
  database rows for anything that does trigger it later.

## 3. Dead files already cleaned up

Removed from this repo before handoff (all confirmed unreferenced by
the live flow first): `item.php`, `map.php`, `door.php`,
`design_palettes.php`, `palette_gallery.php` (superseded UI — an old
sidebar-catalog menu, an omikuji fortune-draw attract screen, a
category "world map," internal palette-comparison tools), the unused
`render_logo()` helper and its two logo image files, an unreferenced
`fx.js` confetti helper from an early palette generation, and a
handful of brand/machine images nothing pointed at. If you find
anything else in `public/assets/` you can't trace a reference to, it's
very likely the same story — grep for the filename before assuming
it's load-bearing.

## 4. Content/data caveats

- `menu_data.php`'s dish photos: only two items have real photography
  (`tonkotsu-ramen`, `chashu-don`); everything else falls back to a
  large styled emoji (`ITEM_ICON_EMOJI` in `src/partials.php`). Real
  photography for the rest is a content task, not an engineering one.
- Store/kiosk identity (`STORE_ID`, `KIOSK_ID`, `TAX_RATE` in
  `src/config.php`) are placeholder values — confirm real ones before
  launch.
- Every screen is designed at a fixed 16:9 (built/tested at
  1920×1080 and 1280×720) for the physical kiosk display, with one
  exception: `menu.php?browse=1` (the QR-scanned browse mode) is
  deliberately phone-responsive since it's the only screen ever
  viewed on a guest's own device.

---

Ping me (via this same design thread) if anything here doesn't match
what you find in the code — this doc reflects the state as of this
session.
