<?php

namespace App\Models\FetchQuest;

use App\Models\FetchQuest\UserQuest;
use App\Models\Item\Item;
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
        'request_item_id', 'reward_item_id', 'hash',
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
    protected $with = ['requestItem', 'rewardItem'];

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
        'request_item_id'    => 'required|exists:items,id',
        'reward_item_id'     => 'required|exists:items,id',
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
        'request_item_id'    => 'required|exists:items,id',
        'reward_item_id'     => 'required|exists:items,id',
        'image'              => 'mimes:png',
    ];

    /**********************************************************************************************

        RELATIONS

    **********************************************************************************************/

    /**
     * Get the item that is requested.
     */
    public function requestItem() {
        return $this->belongsTo(Item::class, 'request_item_id');
    }

    /**
     * Get the item that is rewarded.
     */
    public function rewardItem() {
        return $this->belongsTo(Item::class, 'reward_item_id');
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
        $userQuest = UserQuest::where('fetch_quest_id', $this->id)
            ->where('user_id', Auth::user()->id)
            ->latest()->first();

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
