<?php

namespace App\Console\Commands;

use Illuminate\Foundation\Console\DownCommand as BaseDownCommand;

class DownCommand extends BaseDownCommand
{
    /**
     * The console command signature with default --secret=irc2026.
     *
     * @var string
     */
    protected $signature = 'down {--redirect= : The path that users should be redirected to}
                                 {--render= : The view that should be prerendered for display during maintenance mode}
                                 {--retry= : The number of seconds after which the request may be retried}
                                 {--refresh= : The number of seconds after which the browser may refresh}
                                 {--secret=irc2026 : The secret phrase that may be used to bypass maintenance mode}
                                 {--status=503 : The status code that should be used when returning the maintenance mode response}';
}
