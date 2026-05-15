# Changelog

## [2.0.0] - 2026-05-15
### Added
- `tracy.debugger` option for setting `Tracy\Debugger` static properties
- `tracy.logger.email`, `tracy.logger.fromEmail` and `tracy.logger.emailSnooze` options
- `tracy.logDirectory` option can now be a callable
- emails are now sent through Kirby's email system

### Changed
- plugin initialization moved to `JanHerman\Tracy\TracyPlugin`
- Kirby hook now only calls `TracyPlugin::init()`
- Tracy now runs in panel (Debugger::$showBar is set to false instead of interupting Tracy initialization)
- `tracy.enableInPanel` option renamed to `tracy.showBarInPanel`
- `tracy.logsDirectory` option renamed to `tracy.logDirectory`
- `editor` option moved under `tracy.debugger.editor`

### Removed
- need for non-standard `kirby.render:before` hook (uses `system.loadPlugins:after` instead)
- old top-level `adminEmail`, `fromEmail` and `editor` options


## [1.4.0] - 2024-08-19
### Added
- mode option


## [1.3.0] - 2024-08-08
### Added
- fromEmail option


## [1.2.3 - 1.2.4] - 2024-07-30
### Fixed
- Page panel error when using field with reserved names (i.e. image, videos, model, etc.)


## [1.2.2] - 2024-04-15
### Added
- support for synced-structure field in page panel


## [1.2.0] - 2024-04-14
### Added
- Page panel


## [1.1.1] - 2024-01-19
### Changed
- code refactoring


## [1.1.0] - 2023-08-12
### Added
- 'logsDirectory' option
- check if the logs directory exists before initialising plugin


## [1.0.1] - 2023-02-19
### Added
- README.md
- CHANGELOG.md

### Changed
- Plugin name changed from `kirby-tracy` to `tracy`


## [1.0.0] - 2023-02-06
### Added
- Initial release
