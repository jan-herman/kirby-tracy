<?php

use Kirby\Cms\App as Kirby;
use Kirby\Cms\Url;
use Kirby\Filesystem\Dir;
use Kirby\Toolkit\Config;
use Kirby\Exception\Exception as KirbyException;
use Tracy\Debugger;
use JanHerman\Tracy\Panels\PagePanel;

@include_once __DIR__ . '/vendor/autoload.php';

Kirby::plugin('jan-herman/tracy', [
    'options' => [
        'mode'          => 'detect',
        'adminEmail'    => null,
        'fromEmail'     => null,
        'editor'        => 'vscode://file/%file:%line',
        'enableInPanel' => false,
        'logsDirectory' => null,
        'panels' => [
            'page' => true,
        ],
    ],
    'hooks' => [
        'kirby.render:before' => function () {
            $kirby = kirby();

            // disable in panel
            $panel_slug = Config::get('panel.slug') ?: 'panel';
            $current_url_base_path = Url::toObject()->path()->first();
            $is_panel = $panel_slug === $current_url_base_path;

            if (!option('jan-herman.tracy.enableInPanel') && $is_panel) {
                return;
            }

            // check if the logs directory exists
            $logs_directory = option('jan-herman.tracy.logsDirectory', $kirby->root('logs'));

            if (Dir::exists($logs_directory) === false) {
                try {
                    Dir::make($logs_directory);
                } catch (Exception $e) {
                    throw new KirbyException($logs_directory . ' directory is not writable.');
                }
            }

            // init & settings
            $mode = option('jan-herman.tracy.mode');
            if ($mode === 'detect') {
                Debugger::enable(Debugger::Detect);
            } elseif ($mode === 'development' || $mode === 'staging') {
                Debugger::enable(Debugger::Development);
            } elseif ($mode === 'production') {
                Debugger::enable(Debugger::Production);
            } else {
                Debugger::enable($mode);
            }

            Debugger::$logDirectory = $logs_directory;
            Debugger::$email = option('jan-herman.tracy.adminEmail');
            Debugger::$editor = option('jan-herman.tracy.editor');

            if (option('jan-herman.tracy.fromEmail')) {
                $logger = Debugger::getLogger();
                $logger->fromEmail = option('jan-herman.tracy.fromEmail');
            }

            // add panels
            if (option('jan-herman.tracy.panels.page') && !$is_panel && $current_url_base_path !== 'api') {
                Debugger::getBar()->addPanel(new PagePanel());
            }
        }
    ]
]);
