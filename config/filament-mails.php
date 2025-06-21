<?php

use Vormkracht10\FilamentMails\Resources\EventResource;
use Vormkracht10\FilamentMails\Resources\MailResource;
use Vormkracht10\FilamentMails\Resources\SuppressionResource;

return [
    'resources' => [
        'mail' => MailResource::class,
        'event' => EventResource::class,
        'suppression' => SuppressionResource::class,
    ],

    'navigation' => [
        'group' => 'Communication',
        'icon' => 'heroicon-o-envelope',
        'sort' => 1,
    ],

    'middleware' => [
        'web',
        'auth',
    ],

    'features' => [
        'mail_preview' => true,
        'mail_download' => true,
        'mail_resend' => true,
        'mail_delete' => true,
        'event_logging' => true,
        'suppression_list' => true,
    ],
];
