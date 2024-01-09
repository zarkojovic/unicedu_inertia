<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {

    /**
     * Run the migrations.
     */
    public function up(): void {
        Schema::create('user_infos', function(Blueprint $table) {
            $table->id('user_info_id');
            $table->unsignedBigInteger('user_id')->nullable();
            $table->unsignedBigInteger('deal_id')->nullable();
            $table->unsignedBigInteger('field_id');
            $table->string('value')->nullable();
            $table->string('display_value')->nullable();
            $table->string('file_name')->nullable();
            $table->string('file_path')->nullable();
            $table->string('file_id')->nullable();
            $table->timestamps();

            // Unique index for user_id and field_id (required for outbound webhooks)
            $table->unique(['user_id', 'field_id']);

            // Unique index for deal_id and field_id (required for outbound webhooks)
            $table->unique(['deal_id', 'field_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void {
        Schema::dropIfExists('user_infos');
    }

};
