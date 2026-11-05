<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Category Groupings for Recommendations
    |--------------------------------------------------------------------------
    |
    | Define which category slugs are related and should be recommended together.
    | When a user views products from one category in a group, products from
    | all categories in that group will be recommended.
    |
    */
    'category_groups' => [
        // Women's traditional wear
        ['womens-wear/sarees', 'womens-wear/blouses'],
        
        // Men's traditional wear
        ['mens-wear/punjabi', 'mens-wear/dhuti'],
        
        // Kid's wear - boys and girls separate (each recommends only their own)
        ['kids-wear/boys-wear'],   // Boy's wear only recommends boy's wear
        ['kids-wear/girls-wear'],  // Girl's wear only recommends girl's wear
        
        // Jewellery / Alankar - handcrafted silver jewellery and alankar recommend each other
        [
            'accessories/handcrafted-silver-jewellery',
            'accessories/alankar',
            'accessories/alankar/gold-plated-copper',
            'accessories/alankar/silver-polish-copper'
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Fallback Product Limit
    |--------------------------------------------------------------------------
    |
    | Number of random products to show when user has no browsing history.
    |
    */
    'fallback_limit' => 12,

    /*
    |--------------------------------------------------------------------------
    | Cookie Settings
    |--------------------------------------------------------------------------
    |
    | Cookie name and lifetime for tracking guest user views.
    |
    */
    'cookie_name' => 'jonakee_session_id',
    'cookie_days' => 30,

    /*
    |--------------------------------------------------------------------------
    | Recommendation Limits
    |--------------------------------------------------------------------------
    |
    | Maximum products to show in recommendation sections.
    |
    */
    'homepage_limit' => 12,
    'product_page_limit' => 8,
];
