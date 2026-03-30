<?php

namespace App\Services;

use App\Models\FetchQuest\FetchQuest;
use App\Models\User\User;
use Illuminate\Support\Facades\DB;

class FetchQuestService extends Service {
    /*
    |--------------------------------------------------------------------------
    | Fetch Quest Service
    |--------------------------------------------------------------------------
    |
    | Handles the creation and editing of fetch quests.
    |
    */

    /**********************************************************************************************

        FETCH QUESTS

    **********************************************************************************************/

    /**
     * Creates a new fetch quest.
     *
     * @param array $data
     *
     * @return \App\Models\FetchQuest\FetchQuest|bool
     */
    public function createFetchQuest($data, User $user) {
        DB::beginTransaction();

        try {
            $quest = FetchQuest::create($data);
            if (!$this->logAdminAction($user, 'Created Fetch Quest', 'Created '.$quest->name)) {
                throw new \Exception('Failed to log admin action.');
            }

            return $this->commitReturn($quest);
        } catch (\Exception $e) {
            $this->setError('error', $e->getMessage());
        }

        return $this->rollbackReturn(false);
    }

    /**
     * Updates a fetch quest.
     *
     * @param \App\Models\FetchQuest\FetchQuest $quest
     * @param array                             $data
     *
     * @return \App\Models\FetchQuest\FetchQuest|bool
     */
    public function updateFetchQuest($quest, $data, User $user) {
        DB::beginTransaction();

        try {
            if (FetchQuest::where('name', $data['name'])->where('id', '!=', $quest->id)->exists()) {
                throw new \Exception('The name has already been taken.');
            }

            $quest->update($data);

            if (!$this->logAdminAction($user, 'Updated Fetch Quest', 'Updated '.$quest->name)) {
                throw new \Exception('Failed to log admin action.');
            }

            return $this->commitReturn($quest);
        } catch (\Exception $e) {
            $this->setError('error', $e->getMessage());
        }

        return $this->rollbackReturn(false);
    }

    /**
     * Deletes a fetch quest.
     *
     * @param \App\Models\FetchQuest\FetchQuest $quest
     *
     * @return bool
     */
    public function deleteFetchQuest($quest, User $user) {
        DB::beginTransaction();

        try {
            $quest->delete();

            if (!$this->logAdminAction($user, 'Deleted Fetch Quest', 'Deleted '.$quest->name)) {
                throw new \Exception('Failed to log admin action.');
            }

            return $this->commitReturn(true);
        } catch (\Exception $e) {
            $this->setError('error', $e->getMessage());
        }

        return $this->rollbackReturn(false);
    }
}
