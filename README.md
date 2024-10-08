# Kirby Tracy

> [Kirby](https://getkirby.com) implementation of [Tracy debugger](https://tracy.nette.org/en/).

## Usage

Requires non-standard hook `kirby.render:before` to initialize Tracy before any other output.

Add the following snippet to your `public/index.php` before this line: `echo $kirby->render();`.

```php
$kirby->trigger('kirby.render:before');
```

## Options

### mode

Default: `'detect'`

[Tracy mode](https://tracy.nette.org/en/guide#toc-development-vs-production-mode). Can be one of the following: `'detect'`, `'development'`, `'staging'`, `'production'`, IP address or array of IP addresses.

### adminEmail

Default: `null`

E-mail address to send error notifications to.

### fromEmail

Default: `null`

E-mail address to send error notifications from.

### editor

Default: `'vscode://file/%file:%line'`

For more information see: [Tracy documentation](https://tracy.nette.org/en/open-files-in-ide)

### enableInPanel

Default: `false`

Whether to show show Tracy bar in the panel area.

### logsDirectory

Default: `$kirby->root('logs')`

Where to keep Tracy logs.

### panels.page

Default: `true`

Whether to enable the page panel.
