<?php

namespace JanHerman\Tracy;

use Closure;
use JanHerman\Tracy\Panels\PagePanel;
use Kirby\Cms\Url;
use Kirby\Exception\Exception as KirbyException;
use Kirby\Filesystem\Dir;
use PHPMailer\PHPMailer\PHPMailer as Mailer;
use Throwable;
use Tracy\Debugger;
use Tracy\Helpers;
use Tracy\Logger;

class TracyPlugin
{
    /**
     * Initialize Tracy and register plugin panels.
     */
    public static function init(): void
    {
        $logDirectory = self::logDirectory();

        Debugger::enable(
            self::mode(),
            $logDirectory,
            self::option('logger.email')
        );

        self::setDebuggerOptions();
        self::setLoggerOptions();
        self::setBarVisibility();
        self::addPanels();
    }

    /**
     * Read a plugin option with the Tracy option prefix.
     */
    private static function option(string $key, mixed $default = null): mixed
    {
        return option('jan-herman.tracy.' . $key, $default);
    }

    /**
     * Resolve and create the Tracy log directory.
     */
    private static function logDirectory(): string
    {
        $logDirectory = self::option('logDirectory', kirby()->root('logs'));

        if (is_callable($logDirectory)) {
            $logDirectory = $logDirectory();
        }

        if (Dir::exists($logDirectory) === false) {
            try {
                Dir::make($logDirectory);
            } catch (Throwable) {
                throw new KirbyException($logDirectory . ' directory is not writable.');
            }
        }

        return $logDirectory;
    }

    /**
     * Resolve the configured Tracy mode.
     */
    private static function mode(): mixed
    {
        $mode = self::option('mode');

        return match ($mode) {
            'detect'      => Debugger::Detect,
            'development' => Debugger::Development,
            'staging'     => Debugger::Development,
            'production'  => Debugger::Production,
            default       => $mode,
        };
    }

    /**
     * Apply static Tracy debugger options.
     */
    private static function setDebuggerOptions(): void
    {
        $defaultOptions = [
            'editor' => 'vscode://file/%file:%line',
        ];
        $options = array_merge($defaultOptions, self::option('debugger', []));

        foreach ($options as $key => $value) {
            if (property_exists(Debugger::class, $key) === false) {
                trigger_error('Tracy: Unknown option "jan-herman.tracy.debugger.' . $key . '"', E_USER_NOTICE);
                continue;
            }

            Debugger::${$key} = $value;
        }
    }

    /**
     * Apply Tracy logger email options.
     */
    private static function setLoggerOptions(): void
    {
        $logger = Debugger::getLogger();

        $logger->mailer = function (mixed $message, string $email) use ($logger): void {
            self::mailer($message, $email, $logger->fromEmail);
        };

        if ($fromEmail = self::option('logger.fromEmail')) {
            $logger->fromEmail = $fromEmail;
        }

        if ($emailSnooze = self::option('logger.emailSnooze')) {
            $logger->emailSnooze = $emailSnooze;
        }
    }

    /**
     * Send Tracy logger emails through Kirby's mailer.
     */
    private static function mailer(mixed $message, string $email, ?string $fromEmail = null): void
    {
        $host = preg_replace('#[^\w.-]+#', '', $_SERVER['SERVER_NAME'] ?? php_uname('n'));
        $beforeSend = option('email.beforeSend');

        kirby()->email([
            'from' => $fromEmail ?? 'noreply@' . $host,
            'to' => self::emailRecipients($email),
            'subject' => 'PHP: An error occurred on the server ' . $host,
            'body' => Logger::formatMessage($message) . "\n\nsource: " . Helpers::getSource(),
            'beforeSend' => function (Mailer $mailer) use ($beforeSend): Mailer {
                $mailer->XMailer = 'Tracy';
                $mailer->Encoding = Mailer::ENCODING_8BIT;

                if ($beforeSend instanceof Closure) {
                    return $beforeSend->call($this, $mailer) ?? $mailer;
                }

                return $mailer;
            },
        ]);
    }

    /**
     * Convert Tracy's comma-separated recipient string for Kirby.
     */
    private static function emailRecipients(string $email): string|array
    {
        $recipients = array_filter(array_map('trim', explode(',', $email)));

        return count($recipients) === 1 ? reset($recipients) : $recipients;
    }

    /**
     * Hide the Tracy bar in the Kirby Panel when disabled.
     */
    private static function setBarVisibility(): void
    {
        if (!self::option('showBarInPanel') && self::isPanel()) {
            Debugger::$showBar = false;
        }
    }

    /**
     * Register configured Tracy bar panels.
     */
    private static function addPanels(): void
    {
        if (self::option('panels.page') && self::isPanel() === false && self::currentUrlBasePath() !== 'api') {
            Debugger::getBar()->addPanel(new PagePanel());
        }
    }

    /**
     * Get the first segment of the current URL path.
     */
    private static function currentUrlBasePath(): ?string
    {
        return Url::toObject()->path()->first();
    }

    /**
     * Check whether the current request targets the Kirby Panel.
     */
    private static function isPanel(): bool
    {
        return option('panel.slug', 'panel') === self::currentUrlBasePath();
    }
}
