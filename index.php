<?php

use Kirby\Cms\App as Kirby;
use Tracy\Debugger;

Kirby::plugin('jan-herman/kirby-tracy', [
    'options' => [
        'adminEmail'    => null,
        'editor'        => 'vscode://file/%file:%line',
        'enableInPanel' => false
	],
    'hooks' => [
        'kirby.render:before' => function () {
            $kirby = kirby();
            $panel_slug = C::get('panel.slug') ?: 'panel';
            $current_url_base_path = Url::toObject()->path()->first();

            // disable in panel
            if (!option('jan-herman.kirby-tracy.enableInPanel') && $panel_slug === $current_url_base_path) {
                return;
            }

            Debugger::enable(Debugger::DETECT, $kirby->root('logs'));
            Debugger::$email = option('jan-herman.kirby-tracy.adminEmail');
            Debugger::$editor = option('jan-herman.kirby-tracy.editor');
        }
    ]
]);
