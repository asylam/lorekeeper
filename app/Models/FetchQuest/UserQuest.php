<?php

namespace App\Models\FetchQuest;

use App\Models\Item\Item;
use App\Models\User\User;
use App\Models\Model;

class UserQuest extends Model {
    const STATUS_ACCEPTED = 1;
    const STATUS_COMPLETED = 2;
    const STATUS_EXPIRED = 3;

    public $timestamps = true;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'fetch_quest_id', 'user_id', 'status', 'due_at', 
        'completed_at', 'expired_at', 'created_at'
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
     * Validation rules for quest creation.
     *
     * @var array
     */
    public static $createRules = [
        'fetch_quest_id'     => 'required|exists:fetch_quests,id',
        'user_id'            => 'required|exists:users,id',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'due_at' => 'datetime',
        'completed_at'   => 'datetime',
        'expired_at'   => 'datetime',
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

    /**********************************************************************************************

        ACCESSORS

    **********************************************************************************************/

    

    /**
     * Gets whether or not the quest is accepted.
     *  
     * @return bool
     */
    public function getAcceptedAttribute() {
        return $this->status == self::STATUS_ACCEPTED;
    }
    
    /**
     * Gets whether or not the quest is completed.
     *
     * @return bool
     */
    public function getCompletedAttribute() {
        return $this->status == self::STATUS_COMPLETED;
    }

    /**
     * Gets whether or not the quest is expired.
     *  
     * @return bool
     */
    public function getExpiredAttribute() {
        return $this->status == self::STATUS_EXPIRED;
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
     * active = created today or needs to be abandoned
     *  
     * @return bool
     */
    public function getActiveAttribute() {
        return $this->created_at->isToday() || $this->past_due;
    }
}
