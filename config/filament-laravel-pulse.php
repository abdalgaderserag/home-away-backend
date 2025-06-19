<?php

declare(strict_types=1);

return [
    'components' => [
        'cache' => [
            'columnSpan' => 'full',
            'rows' => 2,
            'cols' => 'full',
            'ignoreAfter' => '1 day',
            'isDiscovered' => true,
            'isLazy' => true,
            'sort' => null,
            'canView' => true,
            'columnStart' => [],
        ],

        'exceptions' => [
            'columnSpan' => 'full',
            'rows' => 2,
            'cols' => 'full',
            'ignoreAfter' => '1 day',
            'isDiscovered' => true,
            'isLazy' => true,
            'sort' => null,
            'canView' => true,
            'columnStart' => [],
        ],

        'queues' => [
            'columnSpan' => 'full',
            'cols' => 'full',
            'ignoreAfter' => '1 day',
            'isDiscovered' => true,
            'isLazy' => true,
            'sort' => null,
            'canView' => true,
            'columnStart' => [],
        ],

        'servers' => [
            'columnSpan' => 'full',
            'cols' => 'full',
            'ignoreAfter' => '1 day',
            'isDiscovered' => true,
            'isLazy' => true,
            'sort' => null,
            'canView' => true,
            'columnStart' => [],
        ],

        'slow-out-going-requests' => [
            'columnSpan' => 'full',
            'cols' => 'full',
            'ignoreAfter' => '1 day',
            'isDiscovered' => true,
            'isLazy' => true,
            'sort' => null,
            'canView' => true,
            'columnStart' => [],
        ],

        'slow-queries' => [
            'columnSpan' => 'full',
            'cols' => 'full',
            'ignoreAfter' => '1 day',
            'isDiscovered' => true,
            'isLazy' => true,
            'sort' => null,
            'canView' => true,
            'columnStart' => [],
        ],

        'slow-requests' => [
            'columnSpan' => 'full',
            'cols' => 'full',
            'ignoreAfter' => '1 day',
            'isDiscovered' => true,
            'isLazy' => true,
            'sort' => null,
            'canView' => true,
            'columnStart' => [],
        ],

        'usage' => [
            'columnSpan' => [
                'md' => 6,
                'xl' => 6,
            ],
            'rows' => 2,
            'cols' => 'full',
            'ignoreAfter' => '1 day',
            'isDiscovered' => true,
            'isLazy' => true,
            'sort' => 1, // ensure it comes first
            'canView' => true,
            'columnStart' => [],
        ],

        'slow-jobs' => [
            'columnSpan' => 'full',
            'rows' => 2,
            'cols' => 'full',
            'ignoreAfter' => '1 day',
            'isDiscovered' => true,
            'isLazy' => true,
            'sort' => null,
            'canView' => true,
            'columnStart' => [],
        ],
    ],
];
