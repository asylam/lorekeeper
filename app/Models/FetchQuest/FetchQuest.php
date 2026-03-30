<?php

namespace App\Models\FetchQuest;

use App\Models\Model;
use App\Models\Item\Item;

class FetchQuest extends Model
{
    /** 
     * The attributes that are mass assignable.
     * 
     * @var array 
    */
    protected $fillable = [
        'name', 'is_active', 'completed', 'request_item_id', 'reward_item_id',
    ];

     /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'fetch_quests';

    /**
     * Validation rules for quest creation.
     *
     * @var array
     */
    public static $createRules = [
        'name'               => 'required|unique:fetch_quests|between:3,100',
        'request_item_id'    => 'required|exists:items,id',
        'reward_item_id'     => 'required|exists:items,id',
    ];

    /**
     * Validation rules for quest updating.
     *
     * @var array
     */
    public static $updateRules = [
        'name'               => 'required|between:3,100',
        'request_item_id'    => 'required|exists:items,id',
        'reward_item_id'     => 'required|exists:items,id',
    ];

    /**
    * The relationships that should always be loaded.
    *
    * @var array
    */
    protected $with = ['requestItem', 'rewardItem'];

    /**********************************************************************************************

        RELATIONS

    **********************************************************************************************/

    /**
     * Get the item that is requested.
     */
    public function requestItem()
    {
        return $this->belongsTo(Item::class, 'request_item_id');
    }

    /**
     * Get the item that is rewarded.
     */
    public function rewardItem()
    {
        return $this->belongsTo(Item::class, 'reward_item_id');
    }
}