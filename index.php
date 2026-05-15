<?php

use Kirby\Cms\App as Kirby;
use JanHerman\Tracy\TracyPlugin;

@include_once __DIR__ . '/vendor/autoload.php';

Kirby::plugin('jan-herman/tracy', [
    'options' => [
        'mode' => 'detect',
        'logDirectory' => null,
        'debugger' => [],
        'logger' => [
            'email'     => null,
            'fromEmail' => null,
            'emailSnooze' => null,
        ],
        'showBarInPanel' => false,
        'panels' => [
            'page' => true,
        ],
    ],
    'hooks' => [
        'system.loadPlugins:after' => fn () => TracyPlugin::init(),
    ]
]);
