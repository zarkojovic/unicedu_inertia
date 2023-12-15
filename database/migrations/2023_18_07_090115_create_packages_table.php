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
        Schema::create('packages', function(Blueprint $table) {
            $table->id('package_id');
            $table->string('package_name');
            $table->string('package_bitrix_id');
            $table->string('primary_color')->default('#000000');
            $table->string('secondary_color')->default('#000000');
            $table->string('text_color')->default('#ffffff');
            $table->timestamps();
        });

        Artisan::call('db:seed', [
            '--class' => 'PackageSeeder',
        ]);
        //wirte me a code

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void {
        Schema::dropIfExists('packages');
    }

};
