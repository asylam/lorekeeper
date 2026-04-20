<?php

namespace App\Services;

use App\Exceptions\FetchQuestException;
use App\Models\Currency\Currency;
use App\Models\FetchQuest\FetchQuest;
use App\Models\FetchQuest\UserQuest;
use App\Models\Item\Item;
use App\Models\User\User;
use App\Models\User\UserCurrency;
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
     * @param \App\Models\FetchQuest\FetchQuest|null $quest
     * @param \App\Models\User\User                  $user
     */
    public function acceptFetchQuest($quest, $user) {
        DB::beginTransaction();

        try {
            $quest = $this->getActiveQuest($quest);
            if ($quest->getActiveUserQuestForUser($user)) {
                throw FetchQuestException::alreadyAccepted();
            }

            UserQuest::create([
                'fetch_quest_id'     => $quest->id,
                'user_id'            => $user->id,
                'status'             => 'accepted',
                'due_at'             => now()->addDay(),
            ]);

            return $this->commitReturn($quest);
        } catch (\Throwable $e) {
            return $this->handleFailure($e, 'accept', $quest, $user);
        }
    }

    /**
     * Completes a fetch quest.
     *
     * @param \App\Models\FetchQuest\FetchQuest|null $quest
     * @param \App\Models\User\User                  $user
     *
     * @return \App\Models\FetchQuest\FetchQuest|bool
     */
    public function completeFetchQuest($quest, $user) {
        DB::beginTransaction();

        try {
            $quest = $this->getActiveQuest($quest);
            $userQuest = $quest->getActiveUserQuestForUser($user);
            if (!$userQuest) {
                throw FetchQuestException::notAccepted();
            }
            if ($userQuest->past_due || $userQuest->expired || $userQuest->completed) {
                throw FetchQuestException::alreadyCompletedOrExpired();
            }

            $requestItems = $userQuest->requestItems;
            if ($requestItems->isEmpty()) {
                throw FetchQuestException::requestItemsNotFound();
            }

            foreach ($requestItems as $requestQuestItem) {
                $this->processRequestItem($quest, $user, $requestQuestItem);
            }

            $rewardItems = $userQuest->rewardItems;
            if ($rewardItems->isEmpty()) {
                throw FetchQuestException::rewardItemsNotFound();
            }

            $rewards = $this->buildRewardAssets($rewardItems);
            if (!fillUserAssets($rewards, null, $user, 'Fetch Quest Rewards ('.$quest->name.')', ['data' => ''])) {
                throw FetchQuestException::rewardDistributionFailed();
            }

            $userQuest->update(['completed_at' => now(), 'status' => 'completed']);

            return $this->commitReturn($quest);
        } catch (\Throwable $e) {
            return $this->handleFailure($e, 'complete', $quest, $user);
        }
    }

    /**
     * Abandons a fetch quest.
     *
     * @param \App\Models\FetchQuest\FetchQuest|null $quest
     * @param \App\Models\User\User                  $user
     *
     * @return \App\Models\FetchQuest\FetchQuest|bool
     */
    public function abandonFetchQuest($quest, $user) {
        DB::beginTransaction();

        try {
            $quest = $this->getActiveQuest($quest);
            $userQuest = $quest->getActiveUserQuestForUser($user);
            if (!$userQuest) {
                throw FetchQuestException::notAccepted();
            }
            if ($userQuest->expired || $userQuest->completed) {
                throw FetchQuestException::alreadyCompletedOrExpired();
            }

            $userQuest->update(['expired_at' => now(), 'status' => 'expired']);

            return $this->commitReturn($quest);
        } catch (\Throwable $e) {
            return $this->handleFailure($e, 'abandon', $quest, $user);
        }
    }

    private function getActiveQuest(?FetchQuest $quest) {
        $activeQuest = $quest
            ? FetchQuest::where('id', $quest->id)->where('is_active', 1)->first()
            : null;

        if (!$activeQuest) {
            throw FetchQuestException::invalidQuest();
        }

        return $activeQuest;
    }

    private function processRequestItem($quest, $user, $requestQuestItem) {
        if ($requestQuestItem->rewardable_type == 'Item') {
            $requestItem = Item::find($requestQuestItem->rewardable_id);
            if (!$requestItem) {
                throw FetchQuestException::requestedItemMissing();
            }

            $userItemStack = UserItem::where(['user_id' => $user->id, 'item_id' => $requestItem->id])
                ->where('count', '>', 0)->first();
            if (!$userItemStack || $userItemStack->count < $requestQuestItem->quantity) {
                throw FetchQuestException::insufficientRequestedItems();
            }

            if (!(new InventoryManager())->debitStack($user, 'Fetch Quest Completion ('.$quest->name.')', ['data' => ''], $userItemStack, $requestQuestItem->quantity)) {
                throw FetchQuestException::requestedItemDebitFailed();
            }

            return;
        }

        if ($requestQuestItem->rewardable_type == 'Currency') {
            $requestCurrency = Currency::find($requestQuestItem->rewardable_id);
            if (!$requestCurrency) {
                throw FetchQuestException::requestedCurrencyMissing();
            }

            $userCurrency = UserCurrency::where(['user_id' => $user->id, 'currency_id' => $requestCurrency->id])->first();
            if (!$userCurrency || $userCurrency->quantity < $requestQuestItem->quantity) {
                throw FetchQuestException::insufficientRequestedCurrency();
            }

            if (!(new CurrencyManager())->debitCurrency($user, null, 'Fetch Quest Completion ('.$quest->name.')', 'Fetch Quest Completion', $requestCurrency, $requestQuestItem->quantity)) {
                throw FetchQuestException::requestedCurrencyDebitFailed();
            }

            return;
        }

        throw FetchQuestException::unsupportedRequestType();
    }

    private function buildRewardAssets($rewardItems) {
        $rewards = createAssetsArray();

        foreach ($rewardItems as $rewardQuestItem) {
            if ($rewardQuestItem->rewardable_type == 'Item') {
                $rewardItem = Item::find($rewardQuestItem->rewardable_id);
                if (!$rewardItem) {
                    throw FetchQuestException::rewardItemMissing();
                }

                addAsset($rewards, $rewardItem, $rewardQuestItem->quantity);
                continue;
            }

            if ($rewardQuestItem->rewardable_type == 'Currency') {
                $rewardCurrency = Currency::find($rewardQuestItem->rewardable_id);
                if (!$rewardCurrency) {
                    throw FetchQuestException::rewardCurrencyMissing();
                }

                addAsset($rewards, $rewardCurrency, $rewardQuestItem->quantity);
                continue;
            }

            throw FetchQuestException::unsupportedRewardType();
        }

        return $rewards;
    }

    private function handleFailure(\Throwable $e, $action, $quest = null, $user = null) {
        \Log::warning('Fetch quest action failed.', [
            'action'    => $action,
            'quest_id'  => $quest instanceof FetchQuest ? $quest->id : null,
            'user_id'   => $user ? $user->id : null,
            'exception' => get_class($e),
            'message'   => $e->getMessage(),
        ]);

        $this->setError('error', $e instanceof FetchQuestException ? $e->getMessage() : 'An unexpected error occurred. Please try again.');

        return $this->rollbackReturn(false);
    }
}
