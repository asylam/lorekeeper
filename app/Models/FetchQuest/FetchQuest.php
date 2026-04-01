<?php

namespace App\Models\FetchQuest;

use App\Models\Item\Item;
use App\Models\Model;

class FetchQuest extends Model {
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'is_active', 'has_image', 'description', 'parsed_description',
        'completed', 'greeting_message', 'request_message', 'completion_message',
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
