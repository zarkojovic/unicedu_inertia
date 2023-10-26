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
        Schema::create('intakes', function(Blueprint $table) {
            $table->id('intake_id');
            $table->boolean('active')->default(0);
            $table->string('intake_name');
            $table->unsignedBigInteger('intake_bitrix_id');
            $table->timestamps();
        });

        Artisan::call('db:seed', [
            '--class' => 'IntakeSeeder',
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void {
        Schema::dropIfExists('intakes');
    }

};
