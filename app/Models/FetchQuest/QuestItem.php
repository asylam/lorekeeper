<?php

namespace App\Models\FetchQuest;

use App\Models\Model;

class QuestItem extends Model {
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'user_quest_id', 'type', 'rewardable_type', 'rewardable_id', 'quantity',
    ];

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'quest_items';

    /**
     * Timestamps are disabled for this model.
     *
     * @var bool
     */
    public $timestamps = false;

    /**********************************************************************************************

        FACTORY METHODS

    **********************************************************************************************/

    /**
     * Create quest items by rolling a loot table and storing all results.
     *
     * @param UserQuest $userQuest
     * @param string $type 'request' or 'reward'
     * @param \App\Models\Loot\LootTable $lootTable
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public static function createFromRolledLoot(UserQuest $userQuest, $type, $lootTable) {
        $createdItems = collect();

        try {
            // Roll the loot table and get consolidated results
            $rewards = $lootTable->roll(1);

            // Create quest items for all rolled items
            if (isset($rewards['items']) && is_array($rewards['items'])) {
                foreach ($rewards['items'] as $itemData) {
                    $item = self::create([
                        'user_quest_id' => $userQuest->id,
                        'type' => $type,
                        'rewardable_type' => 'Item',
                        'rewardable_id' => $itemData['asset']->id,
                        'quantity' => $itemData['quantity'] ?? 1,
                    ]);
                    $createdItems->push($item);
                }
            }

            // Create quest items for all rolled currencies
            if (isset($rewards['currencies']) && is_array($rewards['currencies'])) {
                foreach ($rewards['currencies'] as $currencyData) {
                    $item = self::create([
                        'user_quest_id' => $userQuest->id,
                        'type' => $type,
                        'rewardable_type' => 'Currency',
                        'rewardable_id' => $currencyData['asset']->id,
                        'quantity' => $currencyData['quantity'] ?? 1,
                    ]);
                    $createdItems->push($item);
                }
            }
        } catch (\Exception $e) {
            \Log::error("Error rolling loot table for quest item: " . $e->getMessage());
        }

        return $createdItems;
    }

    /**********************************************************************************************

        RELATIONS

    **********************************************************************************************/

    /**
     * Get the user quest this quest item belongs to.
     */
    public function userQuest() {
        return $this->belongsTo(UserQuest::class);
    }

    /**********************************************************************************************

        ACCESSORS

    **********************************************************************************************/

    /**
     * Gets the cast rewardable object (Item or Currency).
     *
     * @return mixed
     */
    public function getRewardableAttribute() {
        switch ($this->rewardable_type) {
            case 'Item':
                return \App\Models\Item\Item::find($this->rewardable_id);
            case 'Currency':
                return \App\Models\Currency\Currency::find($this->rewardable_id);
            default:
                return null;
        }
    }

    /**
     * Gets a display name for this quest item.
     *
     * @return string
     */
    public function getDisplayNameAttribute() {
        $quantity = $this->quantity;
        
        switch ($this->rewardable_type) {
            case 'Item':
                $item = \App\Models\Item\Item::find($this->rewardable_id);
                return ($quantity > 1 ? $quantity.'x ' : '') . ($item ? $item->name : 'Unknown Item');
            case 'Currency':
                $currency = \App\Models\Currency\Currency::find($this->rewardable_id);
                return ($quantity > 1 ? $quantity.'x ' : '') . ($currency ? $currency->name : 'Unknown Currency');
            default:
                return 'Unknown Reward';
        }
    }
}
