<?php
declare(strict_types=1);

/**
 * Session-backed cart. The original kiosk could only ever hold a
 * single item with no quantity control — this adds multi-item carts,
 * quantity edits, and a promo code slot that a scanned QR (see
 * QrPromo) can pre-fill via ?promo=CODE on the menu screen.
 */
final class Cart
{
    public static function items(): array
    {
        return $_SESSION['cart'] ?? [];
    }

    /**
     * Never lets a cart quantity exceed real-time stock — a machine
     * that could take payment for something it can't actually make is
     * exactly the "will it eat my money" fear the whole UI is built
     * to avoid.
     */
    public static function add(string $itemId, int $qty = 1): void
    {
        $item = menu_item($itemId);
        if (!$item || $item['stock'] <= 0) {
            return;
        }
        $qty = max(1, $qty);
        $current = $_SESSION['cart'][$itemId] ?? 0;
        $_SESSION['cart'][$itemId] = min($item['stock'], $current + $qty);
    }

    public static function setQty(string $itemId, int $qty): void
    {
        if ($qty <= 0) {
            unset($_SESSION['cart'][$itemId]);
            return;
        }
        $item = menu_item($itemId);
        $_SESSION['cart'][$itemId] = $item ? min($qty, max(0, $item['stock'])) : $qty;
    }

    public static function remove(string $itemId): void
    {
        unset($_SESSION['cart'][$itemId]);
    }

    public static function clear(): void
    {
        unset($_SESSION['cart'], $_SESSION['promo']);
    }

    public static function count(): int
    {
        return array_sum(self::items());
    }

    /** @return array<int, array{item: array, qty: int, lineTotal: float}> */
    public static function lines(): array
    {
        $lines = [];
        foreach (self::items() as $id => $qty) {
            $item = menu_item($id);
            if (!$item) {
                continue;
            }
            $lines[] = [
                'item' => $item,
                'qty' => $qty,
                'lineTotal' => $item['price'] * $qty,
            ];
        }
        return $lines;
    }

    public static function subtotal(): float
    {
        $sum = 0.0;
        foreach (self::lines() as $line) {
            $sum += $line['lineTotal'];
        }
        return $sum;
    }

    public static function promo(): ?array
    {
        return $_SESSION['promo'] ?? null;
    }

    public static function applyPromo(string $code): bool
    {
        $valid = [
            'WELCOME10' => 0.10,
            'YOKAI15'   => 0.15,
        ];
        $code = strtoupper(trim($code));
        if (!isset($valid[$code])) {
            return false;
        }
        $_SESSION['promo'] = ['code' => $code, 'rate' => $valid[$code]];
        return true;
    }

    public static function discount(): float
    {
        $promo = self::promo();
        return $promo ? self::subtotal() * $promo['rate'] : 0.0;
    }

    public static function tax(): float
    {
        return max(0, self::subtotal() - self::discount()) * TAX_RATE;
    }

    public static function total(): float
    {
        return max(0, self::subtotal() - self::discount()) + self::tax();
    }

    /**
     * Called once payment succeeds: snapshots the order for the
     * pickup screens, assigns an order number, then empties the cart.
     */
    public static function finalizeOrder(string $method): array
    {
        $orderNumber = 'YK-' . random_int(40000, 49999);
        $_SESSION['order_number'] = $orderNumber;
        $snapshot = [
            'number' => $orderNumber,
            'method' => $method,
            'lines' => self::lines(),
            'total' => self::total(),
            'placed_at' => (new DateTimeImmutable())->format('Y-m-d H:i'),
        ];
        $_SESSION['last_order'] = $snapshot;
        self::clear();
        return $snapshot;
    }
}
