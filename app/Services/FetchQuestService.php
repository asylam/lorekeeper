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
            $data = $this->populateQuestData($data);

            $image = null;
            if (isset($data['image']) && $data['image']) {
                $data['has_image'] = 1;
                $data['hash'] = randomString(10);
                $image = $data['image'];
                unset($data['image']);
            } else {
                $data['has_image'] = 0;
            }

            $quest = FetchQuest::create($data);
            
            if (!$this->logAdminAction($user, 'Created Fetch Quest', 'Created '.$quest->name)) {
                throw new \Exception('Failed to log admin action.');
            }

            if ($image) {
                $this->handleImage($image, $quest->fetchQuestImagePath, $quest->fetchQuestImageFileName);
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

            $data = $this->populateQuestData($data, $quest);

            $image = null;
            if (isset($data['image']) && $data['image']) {
                $data['has_image'] = 1;
                $data['hash'] = randomString(10);
                $image = $data['image'];
                unset($data['image']);
            }

            $quest->update($data);

            if ($quest) {
                $this->handleImage($image, $quest->fetchQuestImagePath, $quest->fetchQuestImageFileName);
            }

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

    /**
     * Processes user input for creating/updating a quest.
     *
     * @param array                             $data
     * @param \App\Models\FetchQuest\FetchQuest $quest
     *
     * @return array
     */
    private function populateQuestData($data, $quest = null) {
        if (isset($data['description']) && $data['description']) {
            $data['parsed_description'] = parse($data['description']);
        } else {
            $data['parsed_description'] = null;
        }   
        
        if (isset($data['remove_image'])) {
        if ($quest && $quest->has_image && $data['remove_image']) {
            $data['has_image'] = 0;
            $this->deleteImage($quest->fetchQuestImagePath, $quest->fetchQuestImageFileName);
        }
        unset($data['remove_image']);
        }

        return $data;
    }

}
