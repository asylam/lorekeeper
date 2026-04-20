<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void {
        // Create fetch_quests table
        Schema::create('fetch_quests', function (Blueprint $table) {
            $table->increments('id');

            $table->string('name');
            $table->boolean('is_active')->default(true);
            $table->boolean('has_image')->default(false);
            $table->text('description')->nullable();
            $table->text('parsed_description')->nullable();
            $table->text('greeting_message')->nullable();
            $table->text('request_message')->nullable();
            $table->text('completion_message')->nullable();
            $table->text('expired_message')->nullable();
            $table->string('hash')->nullable();

            $table->unsignedInteger('request_table_id');
            $table->unsignedInteger('reward_table_id');

            $table->index('request_table_id');
            $table->index('reward_table_id');

            $table->foreign('request_table_id')->references('id')->on('loot_tables');
            $table->foreign('reward_table_id')->references('id')->on('loot_tables');

            $table->timestamps();
        });

        // Create user_quests table
        Schema::create('user_quests', function (Blueprint $table) {
            $table->increments('id');

            $table->enum('status', ['accepted', 'completed', 'expired'])->default('accepted');
            $table->timestamp('due_at');
            $table->timestamp('completed_at')->nullable();
            $table->timestamp('expired_at')->nullable();

            $table->unsignedInteger('user_id');
            $table->unsignedInteger('fetch_quest_id');

            $table->foreign('user_id')
                ->references('id')->on('users')
                ->onDelete('cascade');

            $table->foreign('fetch_quest_id')
                ->references('id')->on('fetch_quests')
                ->onDelete('cascade');

            $table->index(['user_id', 'fetch_quest_id']);
            $table->index('status');

            $table->timestamps();
        });

        // Create quest_items table (replaces denormalized fields)
        Schema::create('quest_items', function (Blueprint $table) {
            $table->increments('id');

            $table->unsignedInteger('user_quest_id');
            $table->enum('type', ['request', 'reward']);
            $table->string('rewardable_type');
            $table->unsignedInteger('rewardable_id');
            $table->unsignedInteger('quantity')->default(1);

            $table->foreign('user_quest_id')->references('id')->on('user_quests')->onDelete('cascade');
            $table->index(['user_quest_id', 'type']);
            $table->index(['rewardable_type', 'rewardable_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void {
        Schema::dropIfExists('quest_items');
        Schema::dropIfExists('user_quests');
        Schema::dropIfExists('fetch_quests');
    }
};
