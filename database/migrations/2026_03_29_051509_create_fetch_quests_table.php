<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up() {
        Schema::create('fetch_quests', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->boolean('is_active')->default(true);
            $table->boolean('completed')->default(false);

            $table->unsignedInteger('request_item_id');
            $table->unsignedInteger('reward_item_id');

            $table->foreign('request_item_id')->references('id')->on('items');
            $table->foreign('reward_item_id')->references('id')->on('items');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void {
        Schema::dropIfExists('fetch_quests');
    }
};
