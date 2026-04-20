<?php

namespace App\Console\Commands;

use App\Models\FetchQuest\UserQuest;
use App\Models\FetchQuest\QuestItem;
use Illuminate\Console\Command;

/**
 * Resets all fetch quests by deleting all user quests and their related quest items.
 * Helpful for testing. Run 'php artisan reset-fetch-quests' to execute.
 */
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
        // Delete all user quests - this will trigger cascade deletes for related quest_items
        UserQuest::query()->delete();
        
        $this->info('Fetch quests reset successfully.');
    }
}
