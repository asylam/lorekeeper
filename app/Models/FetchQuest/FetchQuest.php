<?php

namespace App\Models\FetchQuest;

use App\Models\FetchQuest\UserQuest;
use App\Models\Loot\LootTable;
use App\Models\User\User;
use App\Models\Model;
use Illuminate\Support\Facades\Auth;

class FetchQuest extends Model {
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'is_active', 'has_image', 'description', 'parsed_description', 
        'greeting_message', 'request_message', 'completion_message', 'expired_message',
        'request_table_id', 'reward_table_id', 'hash',
    ];

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'fetch_quests';

    /**
     * The relationships that should always be loaded.
     *
     * @var array
     */
    protected $with = ['requestTable', 'rewardTable'];

    /**
     * Validation rules for quest creation.
     *
     * @var array
     */
    public static $createRules = [
        'name'               => 'required|unique:fetch_quests|between:3,100',
        'description'        => 'nullable',
        'parsed_description' => 'nullable',
        'greeting_message'   => 'nullable',
        'request_message'    => 'nullable',
        'completion_message' => 'nullable',
        'expired_message'    => 'nullable',
        'request_table_id'    => 'required|exists:loot_tables,id',
        'reward_table_id'     => 'required|exists:loot_tables,id',
        'image'              => 'mimes:png',
    ];

    /**
     * Validation rules for quest updating.
     *
     * @var array
     */
    public static $updateRules = [
        'name'               => 'required|between:3,100',
        'description'        => 'nullable',
        'parsed_description' => 'nullable',
        'greeting_message'   => 'nullable',
        'request_message'    => 'nullable',
        'completion_message' => 'nullable',
        'expired_message'    => 'nullable',
        'request_table_id'    => 'required|exists:loot_tables,id',
        'reward_table_id'     => 'required|exists:loot_tables,id',
        'image'              => 'mimes:png',
    ];

    /**********************************************************************************************

        RELATIONS

    **********************************************************************************************/

    /**
     * Get the loot table for requested items.
     */
    public function requestTable() {
        return $this->belongsTo(LootTable::class, 'request_table_id');
    }

    /**
     * Get the loot table for rewarded items.
     */
    public function rewardTable() {
        return $this->belongsTo(LootTable::class, 'reward_table_id');
    }

    /**********************************************************************************************

        ACCESSORS

    **********************************************************************************************/

    /**
     * Gets the current user's active quest for this quest, if it exists.
     * 
     * @return App\Models\FetchQuest\UserQuest|null
     */
    public function getActiveUserQuestAttribute() {
        $user = Auth::user();

        return $user ? $this->getActiveUserQuestForUser($user) : null;
    }

    /**
     * Gets the specified user's active quest for this fetch quest, if it exists.
     *
     * @param \App\Models\User\User $user
     *
     * @return \App\Models\FetchQuest\UserQuest|null
     */
    public function getActiveUserQuestForUser(User $user) {
        $userQuest = UserQuest::where('fetch_quest_id', $this->id)
            ->where('user_id', $user->id)
            ->latest()
            ->first();

        return $userQuest && $userQuest->active ? $userQuest : null;
    }
    
    /**
     * Gets the file directory containing the model's image.
     *
     * @return string
     */
    public function getImageDirectoryAttribute() {
        return 'images/data/fetch-quests';
    }

    /**
     * Gets the file name of the model's image.
     *
     * @return string
     */
    public function getFetchQuestImageFileNameAttribute() {
        return $this->hash.$this->id.'-image.png';
    }

    /**
     * Gets the path to the file directory containing the model's image.
     *
     * @return string
     */
    public function getFetchQuestImagePathAttribute() {
        return public_path($this->imageDirectory);
    }

    /**
     * Gets the URL of the model's image.
     *
     * @return string
     */
    public function getFetchQuestImageUrlAttribute() {
        if (!$this->has_image) {
            return null;
        }

        return asset($this->imageDirectory.'/'.$this->fetchQuestImageFileName);
    }
}
