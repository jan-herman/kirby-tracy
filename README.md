# Kirby Tracy

> [Kirby](https://getkirby.com) implementation of [Tracy debugger](https://tracy.nette.org/en/).

## Options

All options are configured under the `jan-herman.tracy` prefix.

```php
return [
    'jan-herman.tracy' => [
        'mode' => 'detect',
        'logDirectory' => null,
        'debugger' => [
            'editor' => 'vscode://file/%file:%line',
        ],
        'logger' => [
            'email' => null,
            'fromEmail' => null,
            'emailSnooze' => null,
        ],
        'showBarInPanel' => false,
        'panels' => [
            'page' => true,
        ],
    ],
];
```

### mode

Default: `'detect'`

[Tracy mode](https://tracy.nette.org/en/guide#toc-development-vs-production-mode). Can be one of the following: `'detect'`, `'development'`, `'staging'`, `'production'`, IP address or array of IP addresses.

### logDirectory

Default: `$kirby->root('logs')`

Where to keep Tracy logs. The value can also be a callable that returns the directory path.

### debugger

Default:

```php
[
    'editor' => 'vscode://file/%file:%line',
]
```

Special option for configuring static properties on `Tracy\Debugger`.

Each key in this array is assigned directly to the matching [debugger property](https://tracy.nette.org/en/configuring). For example:

```php
'jan-herman.tracy' => [
    'debugger' => [
        'editor' => 'phpstorm://open?file=%file&line=%line',
        'maxDepth' => 4,
        'maxLength' => 200,
    ],
],
```

This is equivalent to setting:

```php
Debugger::$editor = 'phpstorm://open?file=%file&line=%line';
Debugger::$maxDepth = 4;
Debugger::$maxLength = 200;
```

For editor URL formats, see the [Tracy documentation](https://tracy.nette.org/en/open-files-in-ide).

### logger.email

Default: `null`

E-mail address to send error notifications to. Error notifications are sent through Kirby's email system and use your configured Kirby email transport.

### logger.fromEmail

Default: `null`

E-mail address to send error notifications from.

### logger.emailSnooze

Default: `null`

How long Tracy should wait before sending another e-mail notification for the same error.

### showBarInPanel

Default: `false`

Whether to show the Tracy bar in the Kirby Panel area.

### panels.page

Default: `true`

Whether to enable the page panel.
