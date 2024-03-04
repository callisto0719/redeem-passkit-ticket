<?php

namespace App\Libs;

use App\Enums\LogSeverity;

class Logging
{
    /** @var resource */
    private $log;

    public function __construct() {
        $this->log = \fopen('php://stderr', 'wb');
    }

    public function write(
        string $message,
        LogSeverity $severity = LogSeverity::DEFAULT
    ): void {
        \fwrite($this->log, \json_encode([
            'message' => $message,
            'severity' => $severity->value
        ], \JSON_UNESCAPED_UNICODE | \JSON_UNESCAPED_SLASHES) . PHP_EOL);
    }
}
