<?php
declare(strict_types=1);

/**
 * Sample menu catalogue. In production this would come from the POS
 * database; kept as a flat array here so the redesign is easy to run
 * standalone. Two items ship with real product photography (cropped
 * from the current kiosk's own catalogue); the rest are clearly
 * illustrated with line-icon placeholders rather than fabricated
 * photos, since no photography exists for them yet.
 *
 * 'mascot' only drives the small "picked by" badge on chef's-pick /
 * new items — no invented dialogue.
 *
 * 'stock' is the real-time-inventory number shown on the card so a
 * guest never taps into something that turns out to be sold out —
 * one of the core "extreme reassurance" requirements. 0 = sold out.
 *
 * 'contains' is a structured, countable ingredient breakdown (icon +
 * label + quantity) shown as a simple list on the item page — no
 * guessing what's actually in the bowl.
 */
function menu_categories(): array
{
    return [
        'ramen'  => ['en' => 'Ramen',      'zh' => '拉麵',   'icon' => '🍜'],
        'bowls'  => ['en' => 'Rice Bowls', 'zh' => '丼飯',   'icon' => '🍚'],
        'sides'  => ['en' => 'Sides',      'zh' => '小菜',   'icon' => '🥟'],
    ];
}

function menu_items(): array
{
    return [
        [
            'id' => 'tonkotsu-ramen',
            'category' => 'ramen',
            'name' => ['en' => 'Tonkotsu Ramen with Soft Boiled Egg', 'zh' => '豚骨拉麵（溏心蛋）'],
            'desc' => ['en' => 'Japanese ramen noodles, slices of cooked chashu (Japanese style pork belly marinated with sauces), slices of fish cake, fresh corn, seaweed, fresh green onion.',
                       'zh' => '日式拉麵麵條，搭配叉燒、魚板、玉米、海苔與蔥花，湯頭濃郁香醇。'],
            'price' => 10.99,
            'cook_time' => 170,
            'img' => 'assets/img/dish-ramen.webp',
            'badge' => ['en' => "Chef's Pick", 'zh' => '主廚推薦'],
            'mascot' => 'shiba',
            'stock' => 5,
            'contains' => [
                ['icon' => '🥩', 'qty' => '2', 'label' => ['en' => 'slices of chashu pork', 'zh' => '叉燒片']],
                ['icon' => '🥚', 'qty' => '1/2', 'label' => ['en' => 'soft-boiled egg', 'zh' => '溏心蛋']],
                ['icon' => '🐟', 'qty' => '2', 'label' => ['en' => 'slices of fish cake', 'zh' => '魚板片']],
                ['icon' => '🌽', 'qty' => '1', 'label' => ['en' => 'scoop of sweet corn', 'zh' => '玉米粒']],
            ],
            'allergens' => ['egg', 'gluten', 'soy'],
            'nutrition' => ['serving' => '24 oz (710ml)', 'calories' => 520, 'fat' => 18, 'fat_dv' => 23, 'sat_fat' => 6, 'sat_fat_dv' => 30, 'sodium' => 1780, 'sodium_dv' => 77, 'carbs' => 61, 'carbs_dv' => 22, 'sugar' => 4, 'protein' => 24, 'ingredients' => 'Pork bone broth, wheat noodles, pork belly, egg, fish cake, corn, seaweed, scallion', 'contains' => 'Egg, Wheat, Soy'],
        ],
        [
            'id' => 'chashu-don',
            'category' => 'bowls',
            'name' => ['en' => 'Japanese Pork Chashu Don', 'zh' => '日式叉燒丼'],
            'desc' => ['en' => 'Tender braised pork belly chashu over steamed short-grain rice, finished with pickled ginger and daikon.',
                       'zh' => '軟嫩叉燒豬肉片鋪於蒸好的短米飯上，佐以醃薑與蘿蔔，風味十足。'],
            'price' => 9.99,
            'cook_time' => 130,
            'img' => 'assets/img/dish-chashu.webp',
            'badge' => null,
            'mascot' => 'ramen-girl',
            'stock' => 3,
            'contains' => [
                ['icon' => '🥩', 'qty' => '6-8', 'label' => ['en' => 'slices of braised pork belly', 'zh' => '燉叉燒片']],
                ['icon' => '🍚', 'qty' => '1', 'label' => ['en' => 'bowl of short-grain rice', 'zh' => '短米飯']],
                ['icon' => '🫚', 'qty' => '1', 'label' => ['en' => 'side of pickled ginger', 'zh' => '醃薑']],
            ],
            'allergens' => ['soy'],
            'nutrition' => ['serving' => '24 oz (295ml)', 'calories' => 590, 'fat' => 32, 'fat_dv' => 42, 'sat_fat' => 12, 'sat_fat_dv' => 59, 'sodium' => 1940, 'sodium_dv' => 84, 'carbs' => 59, 'carbs_dv' => 22, 'sugar' => 0, 'protein' => 13, 'ingredients' => 'White rice, pork belly, soy sauce', 'contains' => 'Soy'],
        ],
        [
            'id' => 'spicy-miso-ramen',
            'category' => 'ramen',
            'name' => ['en' => 'Spicy Miso Ramen', 'zh' => '辣味噌拉麵'],
            'desc' => ['en' => 'Miso broth with a chili kick, ground pork, bean sprouts, scallion and a soft boiled egg.',
                       'zh' => '味噌辣湯底，搭配豬肉末、豆芽菜、蔥花與溏心蛋。'],
            'price' => 11.49,
            'cook_time' => 165,
            'img' => null,
            'placeholder_icon' => '🌶️',
            'badge' => ['en' => 'Spicy', 'zh' => '辣'],
            'mascot' => 'shiba',
            'stock' => 8,
            'contains' => [
                ['icon' => '🥩', 'qty' => '1', 'label' => ['en' => 'scoop of ground pork', 'zh' => '豬肉末']],
                ['icon' => '🥚', 'qty' => '1/2', 'label' => ['en' => 'soft-boiled egg', 'zh' => '溏心蛋']],
                ['icon' => '🌶️', 'qty' => '1', 'label' => ['en' => 'spoon of chili oil', 'zh' => '辣油']],
            ],
            'allergens' => ['egg', 'gluten', 'soy'],
            'nutrition' => ['serving' => '24 oz (710ml)', 'calories' => 560, 'fat' => 20, 'fat_dv' => 26, 'sat_fat' => 7, 'sat_fat_dv' => 35, 'sodium' => 1890, 'sodium_dv' => 82, 'carbs' => 63, 'carbs_dv' => 23, 'sugar' => 5, 'protein' => 25, 'ingredients' => 'Miso broth, wheat noodles, ground pork, chili oil, egg, bean sprouts, scallion', 'contains' => 'Egg, Wheat, Soy'],
        ],
        [
            'id' => 'curry-chicken-don',
            'category' => 'bowls',
            'name' => ['en' => 'Curry Chicken Don', 'zh' => '咖哩雞肉丼'],
            'desc' => ['en' => 'Japanese-style curry with grilled chicken thigh over steamed rice.', 'zh' => '日式咖哩搭配烤雞腿肉，鋪於蒸飯上。'],
            'price' => 10.49,
            'cook_time' => 140,
            'img' => null,
            'placeholder_icon' => '🍛',
            'badge' => null,
            'mascot' => 'ramen-girl',
            'stock' => 0,
            'contains' => [
                ['icon' => '🍗', 'qty' => '1', 'label' => ['en' => 'grilled chicken thigh', 'zh' => '烤雞腿肉']],
                ['icon' => '🍚', 'qty' => '1', 'label' => ['en' => 'bowl of steamed rice', 'zh' => '蒸飯']],
            ],
            'allergens' => ['gluten'],
            'nutrition' => ['serving' => '24 oz (295ml)', 'calories' => 540, 'fat' => 16, 'fat_dv' => 21, 'sat_fat' => 5, 'sat_fat_dv' => 25, 'sodium' => 1620, 'sodium_dv' => 70, 'carbs' => 70, 'carbs_dv' => 25, 'sugar' => 6, 'protein' => 28, 'ingredients' => 'White rice, chicken thigh, curry roux, carrot, onion, potato', 'contains' => 'Wheat'],
        ],
        [
            'id' => 'edamame',
            'category' => 'sides',
            'name' => ['en' => 'Sea Salt Edamame', 'zh' => '海鹽毛豆'],
            'desc' => ['en' => 'Steamed edamame tossed with flaky sea salt.', 'zh' => '蒸毛豆佐海鹽。'],
            'price' => 3.49,
            'cook_time' => 95,
            'img' => null,
            'placeholder_icon' => '🫛',
            'badge' => null,
            'mascot' => 'shiba',
            'stock' => 14,
            'contains' => [
                ['icon' => '🫛', 'qty' => '1', 'label' => ['en' => 'bowl of steamed edamame', 'zh' => '蒸毛豆']],
            ],
            'allergens' => ['soy'],
            'nutrition' => ['serving' => '4 oz (113g)', 'calories' => 120, 'fat' => 5, 'fat_dv' => 6, 'sat_fat' => 1, 'sat_fat_dv' => 5, 'sodium' => 260, 'sodium_dv' => 11, 'carbs' => 9, 'carbs_dv' => 3, 'sugar' => 2, 'protein' => 11, 'ingredients' => 'Edamame, sea salt', 'contains' => 'Soy'],
        ],
        [
            'id' => 'gyoza',
            'category' => 'sides',
            'name' => ['en' => 'Steamed Pork Dumplings (6pc)', 'zh' => '豬肉蒸餃（6顆）'],
            'desc' => ['en' => 'Delicate steamed pork and cabbage dumplings with ponzu dipping sauce.', 'zh' => '軟嫩豬肉高麗菜蒸餃，附柚子醋沾醬。'],
            'price' => 6.99,
            'cook_time' => 110,
            'img' => null,
            'placeholder_icon' => '🥟',
            'badge' => null,
            'mascot' => 'ramen-girl',
            'stock' => 2,
            'contains' => [
                ['icon' => '🥟', 'qty' => '6', 'label' => ['en' => 'pork & cabbage dumplings', 'zh' => '豬肉高麗菜蒸餃']],
                ['icon' => '🥢', 'qty' => '1', 'label' => ['en' => 'cup of ponzu dipping sauce', 'zh' => '柚子醋沾醬']],
            ],
            'allergens' => ['gluten', 'soy'],
            'nutrition' => ['serving' => '6 pieces (170g)', 'calories' => 280, 'fat' => 8, 'fat_dv' => 10, 'sat_fat' => 2, 'sat_fat_dv' => 10, 'sodium' => 640, 'sodium_dv' => 28, 'carbs' => 36, 'carbs_dv' => 13, 'sugar' => 2, 'protein' => 14, 'ingredients' => 'Pork, cabbage, wheat wrapper, soy sauce, ginger', 'contains' => 'Wheat, Soy'],
        ],
    ];
}

function menu_item(string $id): ?array
{
    foreach (menu_items() as $item) {
        if ($item['id'] === $id) {
            return $item;
        }
    }
    return null;
}

const ALLERGEN_LABELS = [
    'egg'    => ['en' => 'Egg',    'zh' => '蛋',   'icon' => '🥚'],
    'gluten' => ['en' => 'Wheat',  'zh' => '麩質', 'icon' => '🌾'],
    'soy'    => ['en' => 'Soy',    'zh' => '大豆', 'icon' => '🌱'],
    'dairy'  => ['en' => 'Dairy',  'zh' => '乳製品', 'icon' => '🥛'],
    'shellfish' => ['en' => 'Shellfish', 'zh' => '甲殼類', 'icon' => '🦐'],
];
