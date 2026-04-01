<?php

namespace App\Services;

use App\Models\FetchQuest\FetchQuest;
use App\Models\FetchQuest\UserQuest;
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
     * Accepts a fetch quest for a user.
     * 
     * @param App\Models\FetchQuest\FetchQuest $quest
     * @param \App\Models\User\User            $user
     */
    public function acceptFetchQuest($quest, $user) {
        DB::beginTransaction();

        try {
            $quest = FetchQuest::where('id', $quest->id)->where('is_active', 1)->first();
            if (!$quest) {
                throw new \Exception('Invalid quest selected.');
            }
            if ($quest->activeUserQuest) {
                throw new \Exception('Quest already accepted.');
            }
            if ($quest->completed_at || $quest->expired_at) {
                throw new \Exception('Quest already completed or expired.');
            }

            UserQuest::create([
                'fetch_quest_id'     => $quest->id,
                'user_id'            => $user->id,
                'status'             => UserQuest::STATUS_ACCEPTED,
                'due_at'             => now()->addDay(),
            ]);

            return $this->commitReturn($quest);
        } catch (\Exception $e) {
            $this->setError('error', $e->getMessage());
        }

        return $this->rollbackReturn(false);
    }

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
            if (!$userquest = $quest->activeUserQuest) {
                throw new \Exception('Quest not accepted.');
            }
            if ($userquest -> past_due ||$userquest->expired || $quest->completed) {
                throw new \Exception('Quest already completed or expired.');
            }
            // Remove the requested item from the user's inventory
            $requestItem = UserItem::where(['user_id' => $user->id, 'item_id' => $quest->request_item_id,])
                ->where('count', '>', 0)->first();
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

            $userquest->update(['completed_at' => now(), 'status' => UserQuest::STATUS_COMPLETED]);

            return $this->commitReturn($quest);
        } catch (\Exception $e) {
            $this->setError('error', $e->getMessage());
        }

        return $this->rollbackReturn(false);
    }

    /**
     * Abandons a fetch quest.
     *
     * @param App\Models\FetchQuest\FetchQuest $quest
     * @param \App\Models\User\User            $user
     *
     * @return App\Models\FetchQuest\FetchQuest|bool
     */
    public function abandonFetchQuest($quest, $user) {
       DB::beginTransaction();

        try {
            $quest = FetchQuest::where('id', $quest->id)->where('is_active', 1)->first();
            if (!$quest) {
                throw new \Exception('Invalid quest selected.');
            }
            if (!$userQuest = $quest->activeUserQuest) {
                throw new \Exception('Quest not accepted.');
            }
            if ($userQuest->expired || $userQuest->completed) {
                throw new \Exception('Quest already completed or expired.');
            }

            $userQuest->update(['expired_at' => now(), 'status' => UserQuest::STATUS_EXPIRED]);

            return $this->commitReturn($quest);
        } catch (\Exception $e) {
            $this->setError('error', $e->getMessage());
        }

        return $this->rollbackReturn(false);
    }
}
