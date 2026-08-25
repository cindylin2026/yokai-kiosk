<?php
declare(strict_types=1);

/**
 * OrderStore — file-backed persistence for orders placed on a
 * customer's own phone (the menu.php?mobile=1 flow). A plain
 * $_SESSION value can't do this job: the phone that pays and the
 * kiosk that later scans the resulting QR are two different PHP
 * sessions entirely, so the handoff needs storage keyed by the
 * unguessable token that travels inside the QR/redemption URL
 * instead (see pickup_qr.php and scan.php).
 */
final class OrderStore
{
    private static function dir(): string
    {
        $dir = __DIR__ . '/../var/orders';
        if (!is_dir($dir)) {
            mkdir($dir, 0777, true);
        }
        return $dir;
    }

    private static function path(string $token): ?string
    {
        if (!preg_match('/^[a-f0-9]{20}$/', $token)) {
            return null;
        }
        return self::dir() . '/' . $token . '.json';
    }

    /** @param array<int, array{item: array, qty: int, lineTotal: float}> $lines */
    public static function create(array $lines, float $total, string $method): array
    {
        $token = bin2hex(random_bytes(10));
        $order = [
            'token'      => $token,
            'number'     => 'YK-' . random_int(40000, 49999),
            'method'     => $method,
            'lines'      => $lines,
            'total'      => $total,
            'placed_at'  => (new DateTimeImmutable())->format('Y-m-d H:i'),
            'status'     => 'awaiting_scan',
            'started_at' => null,
        ];
        file_put_contents(self::path($token), json_encode($order, JSON_UNESCAPED_UNICODE));
        return $order;
    }

    public static function find(string $token): ?array
    {
        $path = self::path($token);
        if (!$path || !is_file($path)) {
            return null;
        }
        $data = json_decode((string)file_get_contents($path), true);
        return is_array($data) ? $data : null;
    }

    /** Caller must have already confirmed the order is still 'awaiting_scan'. */
    public static function markCooking(string $token): ?array
    {
        $order = self::find($token);
        if (!$order) {
            return null;
        }
        $order['status'] = 'cooking';
        $order['started_at'] = (new DateTimeImmutable())->format('Y-m-d H:i:s');
        file_put_contents(self::path($token), json_encode($order, JSON_UNESCAPED_UNICODE));
        return $order;
    }
}
