<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Symfony\Component\Process\Process;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

Artisan::command('swipe:mock-bootstrap {name=my-test} {--scopes=wallet:balance,payments:qr}', function () {
    $name = $this->argument('name');
    $scopes = $this->option('scopes');

    $this->comment('Starting Swipe mock server...');
    $startProcess = Process::fromShellCommandline('swipe mock start > /dev/null 2>&1 & echo $!');
    $startProcess->setTimeout(20);
    $startProcess->run();

    if (! $startProcess->isSuccessful()) {
        $this->warn('Failed to launch swipe mock server: ' . trim($startProcess->getErrorOutput()));
    } else {
        $pid = trim($startProcess->getOutput());
        $this->info('Swipe mock server started. PID: ' . $pid);
    }

    $this->comment('Creating Swipe API key...');
    $keyProcess = new Process([
        'swipe',
        'keys',
        'create',
        '--name', $name,
        '--scopes', $scopes,
        '--output', 'json',
    ]);
    $keyProcess->setTimeout(20);
    $keyProcess->run();

    if (! $keyProcess->isSuccessful()) {
        $this->error('Failed to create Swipe API key: ' . trim($keyProcess->getErrorOutput()));
        return;
    }

    $keyPayload = json_decode(trim($keyProcess->getOutput()), true);

    if (json_last_error() !== JSON_ERROR_NONE || empty($keyPayload['client_id']) || empty($keyPayload['client_secret'])) {
        $this->error('Unable to parse key creation response. Output: ' . trim($keyProcess->getOutput()));
        return;
    }

    $this->info('Swipe API key created successfully.');
    $this->line('Client ID: ' . $keyPayload['client_id']);
    $this->line('Client Secret: ' . $keyPayload['client_secret']);

    $this->comment('Authenticating with Swipe...');
    $authProcess = new Process([
        'swipe',
        'auth',
        'login',
        '--client-id', $keyPayload['client_id'],
        '--client-secret', $keyPayload['client_secret'],
    ]);
    $authProcess->setTimeout(20);
    $authProcess->run();

    if (! $authProcess->isSuccessful()) {
        $this->warn('Swipe auth login failed: ' . trim($authProcess->getErrorOutput()));
        $this->warn('You can retry login with the printed credentials.');
        return;
    }

    $this->info('Swipe auth login completed successfully.');
    $this->line(trim($authProcess->getOutput()));
})->purpose('Bootstrap the local Swipe mock server and create/authenticate API keys.');
