<?php

namespace App\Commands;

use App\Libraries\Notification\FcmPublisher;
use CodeIgniter\CLI\BaseCommand;
use CodeIgniter\CLI\CLI;

class NotificationsTestSend extends BaseCommand
{
    protected $group = 'Notifications';
    protected $name = 'notifications:test';
    protected $description = 'Send a real FCM push notification to a device token or saved device.';
    protected $usage = 'notifications:test --token=FCM_TOKEN [--title="..."] [--body="..."]';

    public function run(array $params)
    {
        $token = (string) CLI::getOption('token');
        $title = (string) (CLI::getOption('title') ?: 'Jootball');
        $body = (string) (CLI::getOption('body') ?: 'Test push notification');
        $matchId = CLI::getOption('match-id');

        if ($token === '') {
            CLI::error('Missing --token');
            return;
        }

        $publisher = new FcmPublisher();
        $data = array_filter([
            'type' => 'test',
            'matchId' => $matchId !== null ? (string) $matchId : null,
        ], static fn ($value) => $value !== null && $value !== '');

        try {
            $result = $publisher->sendToToken($token, $title, $body, $data);
            CLI::write('Push sent successfully', 'green');
            CLI::write(json_encode($result, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT));
        } catch (\Throwable $e) {
            CLI::error($e->getMessage());
        }
    }
}
