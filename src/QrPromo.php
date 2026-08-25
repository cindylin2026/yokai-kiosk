<?php
declare(strict_types=1);

/**
 * QrPromo — builds the promotional QR code shown on the "Meal is Ready"
 * screen (and reusable for printed receipts, table tents, or bag
 * stickers) and defines exactly what information it carries.
 *
 * Payload schema (encoded as the query string of a redemption URL,
 * which is what the QR actually contains — a URL scans reliably on
 * every phone camera with zero app required, unlike raw JSON/text):
 *
 *   https://order.yokaiexpress.com/promo
 *     ?code=WELCOME10        promo/coupon code, redeemable on next order
 *     &discount=10pct        human-readable discount (also shown as copy)
 *     &store=SF-UNION-01     store id -> which location drove the scan
 *     &kiosk=K-104           kiosk id -> which physical unit drove it
 *     &order=YK-48213        the order that generated this code, for
 *                            attribution/analytics and fraud checks
 *     &campaign=SUMMER26     marketing campaign tag
 *     &src=kiosk_ready_screen medium/source, mirrors utm_source
 *     &exp=2026-09-17        ISO expiry date, also rendered as copy
 *     &sig=<hmac>            HMAC-SHA256 signature over the fields above
 *                            so a screenshotted/edited URL can't be
 *                            forged into a different code or a longer
 *                            expiry — the redemption endpoint must
 *                            reject any payload whose signature doesn't
 *                            match before honoring the discount.
 *
 * Rendering: the demo calls the public api.qrserver.com image endpoint
 * so the prototype works with zero dependencies. A production kiosk
 * should self-host generation (e.g. endroid/qr-code via Composer) so
 * receipt printing never depends on third-party network availability.
 */
final class QrPromo
{
    private const SECRET = 'demo-secret-change-me-in-real-deploy';
    private const BASE_URL = 'https://order.yokaiexpress.com/promo';

    /** @return array<string,string> */
    public static function buildPayload(array $overrides = []): array
    {
        $payload = array_merge([
            'code'     => 'WELCOME10',
            'discount' => '10pct',
            'store'    => STORE_ID,
            'kiosk'    => KIOSK_ID,
            'order'    => self::orderNumber(),
            'campaign' => 'FALL26-KIOSK',
            'src'      => 'kiosk_ready_screen',
            'exp'      => (new DateTimeImmutable('+30 days'))->format('Y-m-d'),
        ], $overrides);

        ksort($payload);
        $payload['sig'] = self::sign($payload);

        return $payload;
    }

    private static function sign(array $payload): string
    {
        $canonical = http_build_query($payload);
        return substr(hash_hmac('sha256', $canonical, self::SECRET), 0, 16);
    }

    public static function redemptionUrl(array $overrides = []): string
    {
        $payload = self::buildPayload($overrides);
        return self::BASE_URL . '?' . http_build_query($payload);
    }

    public static function imageUrl(array $overrides = [], int $size = 240): string
    {
        $data = urlencode(self::redemptionUrl($overrides));
        return "https://api.qrserver.com/v1/create-qr-code/?size={$size}x{$size}&ecc=M&margin=8&data={$data}";
    }

    private static function orderNumber(): string
    {
        if (!isset($_SESSION['order_number'])) {
            $_SESSION['order_number'] = 'YK-' . random_int(40000, 49999);
        }
        return $_SESSION['order_number'];
    }
}
