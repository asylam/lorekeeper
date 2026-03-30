<?php

namespace App\Services;

use App\Models\FetchQuest\FetchQuest;
use App\Models\Item\Item;
use App\Models\User\User;
use App\Models\User\UserItem;
use Illuminate\Support\Facades\DB;

class FetchQuestManager extends Service {
    /*
    |--------------------------------------------------------------------------
    | Fetch Quest Manager
    |--------------------------------------------------------------------------
    |
    | Handles the mechanics of fetch quests.
    |
    */

    /**
     * Completes a fetch quest.
     *
     * @param App\Models\FetchQuest\FetchQuest $quest
     * @param \App\Models\User\User            $user
     *
     * @return App\Models\FetchQuest\FetchQuest|bool
     */
    public function completeFetchQuest($quest, $user) {
        DB::beginTransaction();

        try {
            $quest = FetchQuest::where('id', $quest->id)->where('is_active', 1)->first();
            if (!$quest) {
                throw new \Exception('Invalid quest selected.');
            }

            // Remove the requested item from the user's inventory
            $requestItem = UserItem::where(['user_id' => $user->id,
                'item_id'                             => $quest->request_item_id,
            ])->where('count', '>', 0)->first();
            if (!$requestItem) {
                throw new \Exception('You do not have the item required to complete this quest.');
            }

            if (!(new InventoryManager())->debitStack($user, 'Fetch Quest Completion ('.$quest->name.')', ['data' => ''], $requestItem, 1)) {
                throw new \Exception('An error occurred while removing the item from your inventory. Please try again.');
            }

            // Give the user their reward
            if ($rewardItem = Item::find($quest->reward_item_id)) {
                $rewards = createAssetsArray();
                addAsset($rewards, $rewardItem, 1);
                fillUserAssets($rewards, null, $user, 'Fetch Quest Rewards ('.$quest->name.')', ['data' => '']);
            } else {
                throw new \Exception('The reward for this quest is invalid.');
            }
            $quest->update(['completed' => 1]);

            return $this->commitReturn($quest);
        } catch (\Exception $e) {
            $this->setError('error', $e->getMessage());
        }

        return $this->rollbackReturn(false);
    }
}
