<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {

    /**
     * Run the migrations.
     */
    public function up(): void {
        Schema::create('field_categories', function(Blueprint $table) {
            $table->id('field_category_id');
            $table->string('category_name');
            $table->boolean('is_visible')->default(TRUE);
            $table->boolean('is_deal_category')->default(FALSE);
            $table->boolean('read_only')->default(FALSE);
            $table->timestamps();
        });

        Artisan::call('db:seed', [
            '--class' => 'CategoryFieldSeeder',
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void {
        Schema::dropIfExists('field_categories');
    }

};
