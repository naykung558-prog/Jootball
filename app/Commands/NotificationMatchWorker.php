<?php

namespace App\Commands;

use App\Libraries\Notification\MatchNotificationWorker;
use CodeIgniter\CLI\BaseCommand;
use CodeIgniter\CLI\CLI;

class NotificationMatchWorker extends BaseCommand
{
    protected $group = 'Notifications';
    protected $name = 'notifications:match-worker';
    protected $description = 'Send automated match notifications for kickoff, lineups, match events, final scores, and standings.';
    protected $usage = 'notifications:match-worker [--event=all|kickoff|lineup|goal|card|substitution|final|standings] [--limit=N]';

    public function run(array $params)
    {
        $mode = (string) (CLI::getOption('event') ?: 'all');
        $limit = CLI::getOption('limit') !== null ? (int) CLI::getOption('limit') : 0;

        try {
            $worker = new MatchNotificationWorker();
            $result = $worker->run($mode, $limit);

            CLI::write('Match notification worker finished', 'green');
            CLI::write(json_encode($result, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT));
        } catch (\Throwable $e) {
            CLI::error($e->getMessage());
        }
    }
}
