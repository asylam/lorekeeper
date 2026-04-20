<?php

namespace App\Models\FetchQuest;

use App\Models\Item\Item;
use App\Models\Model;
use App\Models\User\User;

class UserQuest extends Model {
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'fetch_quest_id', 'user_id', 'status', 'due_at',
        'completed_at', 'expired_at', 'created_at',
    ];

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'user_quests';

    /**
     * The relationships that should always be loaded.
     *
     * @var array
     */
    protected $with = ['fetchQuest', 'user'];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'due_at'         => 'datetime',
        'completed_at'   => 'datetime',
        'expired_at'     => 'datetime',
    ];

    public $timestamps = true;

    /**
     * Validation rules for quest creation.
     *
     * @var array
     */
    public static $createRules = [
        'fetch_quest_id'     => 'required|exists:fetch_quests,id',
        'user_id'            => 'required|exists:users,id',
    ];

    /**********************************************************************************************

        RELATIONS

    **********************************************************************************************/

    /**
     * Get the fetch quest associated with this user quest.
     */
    public function fetchQuest() {
        return $this->belongsTo(FetchQuest::class);
    }

    /**
     * Get the user who owns this quest.
     */
    public function user() {
        return $this->belongsTo(User::class);
    }

    /**
     * Get all request quest items.
     */
    public function requestItems() {
        return $this->hasMany(QuestItem::class, 'user_quest_id')->where('type', 'request');
    }

    /**
     * Get all reward quest items.
     */
    public function rewardItems() {
        return $this->hasMany(QuestItem::class, 'user_quest_id')->where('type', 'reward');
    }

    /**
     * Get the fetch quest's request loot table (for display purposes).
     */
    public function getRequestLootTableAttribute() {
        return $this->fetchQuest->requestTable ?? null;
    }

    /**
     * Get the fetch quest's reward loot table (for display purposes).
     */
    public function getRewardLootTableAttribute() {
        return $this->fetchQuest->rewardTable ?? null;
    }

    /**********************************************************************************************

        ACCESSORS

    **********************************************************************************************/

    /**
     * Gets whether or not the quest is accepted.
     *
     * @return bool
     */
    public function getAcceptedAttribute() {
        return $this->status === 'accepted';
    }

    /**
     * Gets whether or not the quest is completed.
     *
     * @return bool
     */
    public function getCompletedAttribute() {
        return $this->status === 'completed';
    }

    /**
     * Gets whether or not the quest is expired.
     *
     * @return bool
     */
    public function getExpiredAttribute() {
        return $this->status === 'expired';
    }

    /**
     * Gets whether or not the quest is past due.
     *
     * @return bool
     */
    public function getPastDueAttribute() {
        return $this->due_at->isPast() && !$this->expired && !$this->completed;
    }

    /**
     * Gets whether or not the quest is active.
     * active = created today or needs to be abandoned.
     *
     * @return bool
     */
    public function getActiveAttribute() {
        return $this->created_at->isToday() || $this->past_due;
    }

    /**
     * Gets display names for all reward items.
     *
     * @return array
     */
    public function getRewardDisplayNamesAttribute() {
        return $this->rewardItems->map(fn ($item) => $item->display_name)->toArray();
    }

    /**********************************************************************************************

        EVENTS

    **********************************************************************************************/

    /**
     * Roll the loot tables from the fetch quest and assign quest items.
     * Called automatically on model creation.
     */
    public static function boot() {
        parent::boot();

        static::created(function ($userQuest) {
            $fetchQuest = FetchQuest::find($userQuest->fetch_quest_id);

            if ($fetchQuest) {
                // Roll and create request quest item
                QuestItem::createFromRolledLoot($userQuest, 'request', $fetchQuest->requestTable);

                // Roll and create reward quest item
                QuestItem::createFromRolledLoot($userQuest, 'reward', $fetchQuest->rewardTable);
            }
        });
    }
}
