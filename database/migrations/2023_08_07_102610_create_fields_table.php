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
        Schema::create('fields', function(Blueprint $table) {
            $table->id('field_id');
            $table->string('field_name');
            $table->string('title')->nullable();
            $table->boolean('is_active')->default(TRUE);
            $table->boolean('is_required')->default(FALSE);
            $table->boolean('is_contact_field')->default(FALSE);
            $table->string('type')->nullable();
            $table->string('custom_title')->nullable();
            $table->integer('order')->nullable();
            $table->unsignedBigInteger('field_category_id')->nullable();
            $table->timestamps();
        });

        Artisan::call('db:seed', [
            '--class' => 'FieldSeeder',
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void {
        Schema::dropIfExists('fields');
    }

};
