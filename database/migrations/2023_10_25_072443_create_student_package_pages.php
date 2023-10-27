<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {

    /**
     * Run the migrations.
     */
    public function up(): void {
        Schema::create('student_package_pages', function(Blueprint $table) {
            $table->id('student_package_page_id');
            $table->unsignedBigInteger('package_id');
            $table->unsignedBigInteger('page_id');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void {
        Schema::dropIfExists('student_package_pages');
    }

};
