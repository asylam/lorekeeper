<?php

namespace App\Console\Commands;

use App\Models\FetchQuest\UserQuest;
use Illuminate\Console\Command;

class ResetFetchQuests extends Command {
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'reset-fetch-quests';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Resets all fetch quests.';

    /**
     * Create a new command instance.
     */
    public function __construct() {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle() {
        UserQuest::truncate();
        $this->info('Fetch quests reset successfully.');
    }
}
