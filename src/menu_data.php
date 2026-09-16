<?php
declare(strict_types=1);

function menu_categories(): array
{
    return [
        'ramen'  => ['en' => 'Ramen',      'zh' => '拉麵',   'icon' => '🍜'],
        'bowls'  => ['en' => 'Rice Bowls', 'zh' => '丼飯',   'icon' => '🍚'],
        'noodles'=> ['en' => 'Noodles',    'zh' => '麵食',   'icon' => '🍝'],
        'sides'  => ['en' => 'Sides',      'zh' => '小菜',   'icon' => '🥟'],
    ];
}

function menu_items(): array
{
    return [
        [
            'id' => 'chashu-tonkotsu-ramen',
            'category' => 'ramen',
            'name' => ['en' => 'Chashu Tonkotsu Ramen', 'zh' => '叉燒豚骨拉麵'],
            'desc' => ['en' => 'Rich pork bone broth with tender chashu pork belly, soft-boiled egg, nori, corn and scallion.',
                       'zh' => '濃郁豚骨湯底，搭配軟嫩叉燒、溏心蛋、海苔、玉米與蔥花。'],
            'price' => 10.99,
            'cook_time' => 170,
            'img' => 'assets/img/chashu-tonkotsu-ramen.png',
            'badge' => ['en' => "Chef's Pick", 'zh' => '主廚推薦'],
            'mascot' => 'shiba',
            'stock' => 5,
            'contains' => [
                ['icon' => '🥩', 'qty' => '2', 'label' => ['en' => 'slices of chashu pork', 'zh' => '叉燒片']],
                ['icon' => '🥚', 'qty' => '1/2', 'label' => ['en' => 'soft-boiled egg', 'zh' => '溏心蛋']],
                ['icon' => '🌽', 'qty' => '1', 'label' => ['en' => 'scoop of sweet corn', 'zh' => '玉米粒']],
            ],
            'allergens' => ['egg', 'gluten', 'soy'],
            'nutrition' => ['serving' => '24 oz (710ml)', 'calories' => 520, 'fat' => 18, 'fat_dv' => 23, 'sat_fat' => 6, 'sat_fat_dv' => 30, 'sodium' => 1780, 'sodium_dv' => 77, 'carbs' => 61, 'carbs_dv' => 22, 'sugar' => 4, 'protein' => 24, 'ingredients' => 'Pork bone broth, wheat noodles, pork belly, egg, corn, seaweed, scallion', 'contains' => 'Egg, Wheat, Soy'],
        ],
        [
            'id' => 'spicy-chashu-ramen',
            'category' => 'ramen',
            'name' => ['en' => 'Spicy Chashu Ramen', 'zh' => '辣味叉燒拉麵'],
            'desc' => ['en' => 'Spicy miso broth with chashu pork, chili oil, bean sprouts and a soft-boiled egg.',
                       'zh' => '辣味噌湯底，搭配叉燒、辣油、豆芽菜與溏心蛋。'],
            'price' => 11.49,
            'cook_time' => 165,
            'img' => 'assets/img/spicy-chashu-ramen.png',
            'badge' => ['en' => 'Spicy', 'zh' => '辣'],
            'mascot' => 'shiba',
            'stock' => 8,
            'contains' => [
                ['icon' => '🥩', 'qty' => '2', 'label' => ['en' => 'slices of chashu pork', 'zh' => '叉燒片']],
                ['icon' => '🥚', 'qty' => '1/2', 'label' => ['en' => 'soft-boiled egg', 'zh' => '溏心蛋']],
                ['icon' => '🌶️', 'qty' => '1', 'label' => ['en' => 'spoon of chili oil', 'zh' => '辣油']],
            ],
            'allergens' => ['egg', 'gluten', 'soy'],
            'nutrition' => ['serving' => '24 oz (710ml)', 'calories' => 560, 'fat' => 20, 'fat_dv' => 26, 'sat_fat' => 7, 'sat_fat_dv' => 35, 'sodium' => 1890, 'sodium_dv' => 82, 'carbs' => 63, 'carbs_dv' => 23, 'sugar' => 5, 'protein' => 25, 'ingredients' => 'Miso broth, wheat noodles, chashu pork, chili oil, egg, bean sprouts', 'contains' => 'Egg, Wheat, Soy'],
        ],
        [
            'id' => 'beef-noodle-soup',
            'category' => 'ramen',
            'name' => ['en' => 'Beef Noodle Soup', 'zh' => '紅燒牛肉麵'],
            'desc' => ['en' => 'Braised beef in a rich soy-spiced broth over thick wheat noodles with bok choy.',
                       'zh' => '紅燒牛腩搭配濃郁醬香湯底，厚麵條與青江菜。'],
            'price' => 12.49,
            'cook_time' => 180,
            'img' => 'assets/img/beef-noodle-soup.png',
            'badge' => null,
            'mascot' => 'shiba',
            'stock' => 6,
            'contains' => [
                ['icon' => '🥩', 'qty' => '4-5', 'label' => ['en' => 'slices of braised beef', 'zh' => '燉牛肉片']],
                ['icon' => '🥬', 'qty' => '1', 'label' => ['en' => 'portion of bok choy', 'zh' => '青江菜']],
            ],
            'allergens' => ['gluten', 'soy'],
            'nutrition' => ['serving' => '24 oz (710ml)', 'calories' => 580, 'fat' => 22, 'fat_dv' => 28, 'sat_fat' => 8, 'sat_fat_dv' => 40, 'sodium' => 1920, 'sodium_dv' => 83, 'carbs' => 58, 'carbs_dv' => 21, 'sugar' => 3, 'protein' => 30, 'ingredients' => 'Beef broth, wheat noodles, beef shank, soy sauce, bok choy, star anise', 'contains' => 'Wheat, Soy'],
        ],
        [
            'id' => 'udon',
            'category' => 'ramen',
            'name' => ['en' => 'Udon', 'zh' => '烏龍麵'],
            'desc' => ['en' => 'Thick chewy udon noodles in a dashi broth with tempura flakes, fish cake and scallion.',
                       'zh' => '彈牙烏龍麵條，搭配柴魚昆布高湯、天婦羅碎、魚板與蔥花。'],
            'price' => 9.99,
            'cook_time' => 120,
            'img' => 'assets/img/udon.png',
            'badge' => null,
            'mascot' => 'ramen-girl',
            'stock' => 10,
            'contains' => [
                ['icon' => '🐟', 'qty' => '2', 'label' => ['en' => 'slices of fish cake', 'zh' => '魚板片']],
                ['icon' => '🧅', 'qty' => '1', 'label' => ['en' => 'sprinkle of tempura flakes', 'zh' => '天婦羅碎']],
            ],
            'allergens' => ['gluten', 'soy'],
            'nutrition' => ['serving' => '20 oz (590ml)', 'calories' => 420, 'fat' => 8, 'fat_dv' => 10, 'sat_fat' => 2, 'sat_fat_dv' => 10, 'sodium' => 1340, 'sodium_dv' => 58, 'carbs' => 72, 'carbs_dv' => 26, 'sugar' => 3, 'protein' => 16, 'ingredients' => 'Udon noodles, dashi broth, fish cake, tempura flakes, scallion', 'contains' => 'Wheat, Soy'],
        ],
        [
            'id' => 'teriyaki-chicken-rice',
            'category' => 'bowls',
            'name' => ['en' => 'Teriyaki Chicken Rice', 'zh' => '照燒雞肉丼'],
            'desc' => ['en' => 'Grilled chicken thigh glazed with teriyaki sauce over steamed short-grain rice with sesame.',
                       'zh' => '照燒烤雞腿肉鋪於短米飯上，撒上芝麻，風味香甜。'],
            'price' => 10.49,
            'cook_time' => 140,
            'img' => 'assets/img/teriyaki-chicken-rice.png',
            'badge' => null,
            'mascot' => 'ramen-girl',
            'stock' => 7,
            'contains' => [
                ['icon' => '🍗', 'qty' => '1', 'label' => ['en' => 'grilled chicken thigh', 'zh' => '烤雞腿肉']],
                ['icon' => '🍚', 'qty' => '1', 'label' => ['en' => 'bowl of steamed rice', 'zh' => '蒸飯']],
            ],
            'allergens' => ['gluten', 'soy'],
            'nutrition' => ['serving' => '22 oz (295ml)', 'calories' => 540, 'fat' => 14, 'fat_dv' => 18, 'sat_fat' => 4, 'sat_fat_dv' => 20, 'sodium' => 1480, 'sodium_dv' => 64, 'carbs' => 68, 'carbs_dv' => 25, 'sugar' => 8, 'protein' => 30, 'ingredients' => 'White rice, chicken thigh, teriyaki sauce, sesame', 'contains' => 'Wheat, Soy'],
        ],
        [
            'id' => 'ropa-vieja-rice',
            'category' => 'bowls',
            'name' => ['en' => 'Ropa Vieja with Rice', 'zh' => '古巴燉牛肉丼'],
            'desc' => ['en' => 'Slow-braised shredded beef in tomato and pepper sofrito over steamed white rice.',
                       'zh' => '慢燉手撕牛肉佐番茄甜椒醬，搭配白飯。'],
            'price' => 11.99,
            'cook_time' => 150,
            'img' => 'assets/img/ropa-vieja-rice.png',
            'badge' => ['en' => 'New', 'zh' => '新品'],
            'mascot' => 'ramen-girl',
            'stock' => 4,
            'contains' => [
                ['icon' => '🥩', 'qty' => '1', 'label' => ['en' => 'portion of shredded beef', 'zh' => '手撕牛肉']],
                ['icon' => '🍚', 'qty' => '1', 'label' => ['en' => 'bowl of steamed rice', 'zh' => '蒸飯']],
            ],
            'allergens' => ['soy'],
            'nutrition' => ['serving' => '22 oz (295ml)', 'calories' => 570, 'fat' => 18, 'fat_dv' => 23, 'sat_fat' => 6, 'sat_fat_dv' => 30, 'sodium' => 1560, 'sodium_dv' => 68, 'carbs' => 62, 'carbs_dv' => 23, 'sugar' => 5, 'protein' => 32, 'ingredients' => 'White rice, beef, tomato, bell pepper, onion, garlic', 'contains' => 'Soy'],
        ],
        [
            'id' => 'dan-dan-noodles',
            'category' => 'noodles',
            'name' => ['en' => 'Spicy Vegetarian Dan Dan Noodles', 'zh' => '素食擔擔麵'],
            'desc' => ['en' => 'Wheat noodles in a spicy sesame-soy sauce with crispy tofu, bok choy and peanuts.',
                       'zh' => '麵條拌入香辣芝麻醬，搭配脆豆腐、青江菜與花生碎。'],
            'price' => 9.49,
            'cook_time' => 110,
            'img' => 'assets/img/dan-dan-noodles.png',
            'badge' => ['en' => 'Vegan', 'zh' => '純素'],
            'mascot' => 'ramen-girl',
            'stock' => 9,
            'contains' => [
                ['icon' => '🥬', 'qty' => '1', 'label' => ['en' => 'portion of bok choy', 'zh' => '青江菜']],
                ['icon' => '🥜', 'qty' => '1', 'label' => ['en' => 'sprinkle of crushed peanuts', 'zh' => '花生碎']],
            ],
            'allergens' => ['gluten', 'soy'],
            'nutrition' => ['serving' => '18 oz (510ml)', 'calories' => 480, 'fat' => 16, 'fat_dv' => 21, 'sat_fat' => 3, 'sat_fat_dv' => 15, 'sodium' => 1240, 'sodium_dv' => 54, 'carbs' => 66, 'carbs_dv' => 24, 'sugar' => 4, 'protein' => 18, 'ingredients' => 'Wheat noodles, tofu, sesame paste, soy sauce, bok choy, peanut, chili oil', 'contains' => 'Wheat, Soy, Peanut'],
        ],
        [
            'id' => 'mentaiko-pasta',
            'category' => 'noodles',
            'name' => ['en' => 'Mentaiko Pasta', 'zh' => '明太子義大利麵'],
            'desc' => ['en' => 'Al dente pasta tossed in a creamy spiced pollock roe sauce with butter and nori.',
                       'zh' => '彈牙義大利麵拌入奶油辣味明太子醬，佐以海苔絲。'],
            'price' => 11.99,
            'cook_time' => 125,
            'img' => 'assets/img/mentaiko-pasta.png',
            'badge' => ['en' => 'New', 'zh' => '新品'],
            'mascot' => 'ramen-girl',
            'stock' => 6,
            'contains' => [
                ['icon' => '🐟', 'qty' => '1', 'label' => ['en' => 'spoonful of mentaiko roe', 'zh' => '明太子']],
                ['icon' => '🧈', 'qty' => '1', 'label' => ['en' => 'pat of butter', 'zh' => '奶油']],
            ],
            'allergens' => ['egg', 'gluten', 'dairy'],
            'nutrition' => ['serving' => '18 oz (510g)', 'calories' => 550, 'fat' => 20, 'fat_dv' => 26, 'sat_fat' => 9, 'sat_fat_dv' => 45, 'sodium' => 1380, 'sodium_dv' => 60, 'carbs' => 70, 'carbs_dv' => 25, 'sugar' => 2, 'protein' => 22, 'ingredients' => 'Pasta, pollock roe, butter, cream, nori, scallion', 'contains' => 'Egg, Wheat, Dairy'],
        ],
        [
            'id' => 'seafood-pesto-pasta',
            'category' => 'noodles',
            'name' => ['en' => 'Seafood Pesto Pasta', 'zh' => '海鮮青醬義大利麵'],
            'desc' => ['en' => 'Linguine tossed with basil pesto, shrimp, scallops and cherry tomatoes.',
                       'zh' => '扁麵條拌入羅勒青醬，搭配鮮蝦、干貝與小番茄。'],
            'price' => 13.49,
            'cook_time' => 130,
            'img' => 'assets/img/seafood-pesto-pasta.png',
            'badge' => null,
            'mascot' => 'ramen-girl',
            'stock' => 5,
            'contains' => [
                ['icon' => '🍤', 'qty' => '3', 'label' => ['en' => 'shrimp', 'zh' => '鮮蝦']],
                ['icon' => '🍥', 'qty' => '2', 'label' => ['en' => 'scallops', 'zh' => '干貝']],
            ],
            'allergens' => ['gluten', 'shellfish'],
            'nutrition' => ['serving' => '20 oz (570g)', 'calories' => 590, 'fat' => 22, 'fat_dv' => 28, 'sat_fat' => 5, 'sat_fat_dv' => 25, 'sodium' => 1120, 'sodium_dv' => 49, 'carbs' => 68, 'carbs_dv' => 25, 'sugar' => 3, 'protein' => 28, 'ingredients' => 'Linguine, shrimp, scallop, basil pesto, cherry tomato, olive oil, parmesan', 'contains' => 'Wheat, Shellfish'],
        ],
        [
            'id' => 'wonton-soup',
            'category' => 'sides',
            'name' => ['en' => 'Wonton Soup', 'zh' => '雲吞湯'],
            'desc' => ['en' => 'Pork and shrimp wontons in a clear ginger-scallion broth.', 'zh' => '豬肉鮮蝦雲吞，搭配清淡薑蔥湯底。'],
            'price' => 5.99,
            'cook_time' => 90,
            'img' => 'assets/img/wonton-soup.png',
            'badge' => null,
            'mascot' => 'shiba',
            'stock' => 12,
            'contains' => [
                ['icon' => '🥟', 'qty' => '5', 'label' => ['en' => 'pork & shrimp wontons', 'zh' => '豬肉鮮蝦雲吞']],
            ],
            'allergens' => ['gluten', 'soy', 'shellfish'],
            'nutrition' => ['serving' => '12 oz (355ml)', 'calories' => 220, 'fat' => 7, 'fat_dv' => 9, 'sat_fat' => 2, 'sat_fat_dv' => 10, 'sodium' => 860, 'sodium_dv' => 37, 'carbs' => 28, 'carbs_dv' => 10, 'sugar' => 1, 'protein' => 12, 'ingredients' => 'Pork, shrimp, wheat wrapper, ginger, scallion, soy sauce', 'contains' => 'Wheat, Soy, Shellfish'],
        ],
        [
            'id' => 'soup-dumplings',
            'category' => 'sides',
            'name' => ['en' => 'Soup Dumplings (6pc)', 'zh' => '小籠包（6顆）'],
            'desc' => ['en' => 'Steamed pork soup dumplings with a rich broth filling and ginger dipping sauce.', 'zh' => '豬肉湯包，一口咬下鮮美湯汁，附薑絲沾醬。'],
            'price' => 7.99,
            'cook_time' => 115,
            'img' => 'assets/img/soup-dumplings.png',
            'badge' => null,
            'mascot' => 'shiba',
            'stock' => 8,
            'contains' => [
                ['icon' => '🥟', 'qty' => '6', 'label' => ['en' => 'pork soup dumplings', 'zh' => '豬肉小籠包']],
                ['icon' => '🫚', 'qty' => '1', 'label' => ['en' => 'side of ginger dipping sauce', 'zh' => '薑絲沾醬']],
            ],
            'allergens' => ['gluten', 'soy'],
            'nutrition' => ['serving' => '6 pieces (180g)', 'calories' => 300, 'fat' => 10, 'fat_dv' => 13, 'sat_fat' => 3, 'sat_fat_dv' => 15, 'sodium' => 720, 'sodium_dv' => 31, 'carbs' => 38, 'carbs_dv' => 14, 'sugar' => 2, 'protein' => 15, 'ingredients' => 'Pork, wheat wrapper, ginger, soy sauce, scallion', 'contains' => 'Wheat, Soy'],
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
    'egg'       => ['en' => 'Egg',       'zh' => '蛋',     'icon' => '🥚'],
    'gluten'    => ['en' => 'Wheat',     'zh' => '麩質',   'icon' => '🌾'],
    'soy'       => ['en' => 'Soy',       'zh' => '大豆',   'icon' => '🌱'],
    'dairy'     => ['en' => 'Dairy',     'zh' => '乳製品', 'icon' => '🥛'],
    'shellfish' => ['en' => 'Shellfish', 'zh' => '甲殼類', 'icon' => '🦐'],
];
