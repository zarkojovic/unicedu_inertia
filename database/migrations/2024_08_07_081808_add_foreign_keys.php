<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {

    /**
     * Run the migrations.
     */
    public function up(): void {
        Schema::table('users', function(Blueprint $table) {
            $table->foreign('role_id')
                ->references('role_id')
                ->on('roles')
                ->onDelete('cascade');
            $table->foreign('package_id')
                ->references('package_id')
                ->on('packages')
                ->onDelete('cascade');
            $table->foreign('agency_id')
                ->references('agency_id')
                ->on('agencies')
                ->onDelete('cascade');
            $table->foreign('agent_id')
                ->references('user_id')
                ->on('users')
                ->onDelete('cascade');
        });
        Schema::table('deals', function(Blueprint $table) {
            $table->foreign('user_id')
                ->references('user_id')
                ->on('users')
                ->onDelete('cascade');
            $table->foreign('user_intake_package_id')
                ->references('user_intake_package_id')
                ->on('user_intake_packages')
                ->onDelete('cascade');
            $table->foreign('stage_id')
                ->references('stage_id')
                ->on('stages')
                ->onDelete('cascade');
        });
        Schema::table('fields', function(Blueprint $table) {
            $table->foreign('field_category_id')
                ->references('field_category_id')
                ->on('field_categories')
                ->onDelete('cascade');
        });
        Schema::table('user_infos', function(Blueprint $table) {
            $table->foreign('user_id')
                ->references('user_id')
                ->on('users')
                ->onDelete('cascade');
            $table->foreign('deal_id')
                ->references('deal_id')
                ->on('deals')
                ->onDelete('cascade');
            $table->foreign('field_id')
                ->references('field_id')
                ->on('fields')
                ->onDelete('cascade');
        });
        Schema::table('logs', function(Blueprint $table) {
            $table->foreign('user_id')
                ->references('user_id')
                ->on('users')
                ->onDelete('cascade');
        });

        Schema::table('agency_agent', function(Blueprint $table) {
            $table->foreign('agent_id')
                ->references('user_id')
                ->on('users')
                ->onDelete('cascade');
            $table->foreign('agency_id')
                ->references('agency_id')
                ->on('agencies')
                ->onDelete('cascade');
        });

        Schema::table('field_category_page', function(Blueprint $table) {
            $table->foreign('field_category_id')
                ->references('field_category_id')
                ->on('field_categories')
                ->onDelete('cascade');
            $table->foreign('page_id')
                ->references('page_id')
                ->on('pages')
                ->onDelete('cascade');
        });

        Schema::table('pages', function(Blueprint $table) {
            $table->foreign('role_id')
                ->references('role_id')
                ->on('roles')
                ->onDelete('cascade');
        });

        Schema::table('logs', function(Blueprint $table) {
            $table->foreign('action_id')
                ->references('action_id')
                ->on('actions')
                ->onDelete('cascade');
        });
        Schema::table('notifications', function(Blueprint $table) {
            $table->foreign('user_id')
                ->references('user_id')
                ->on('users')
                ->onDelete('cascade');
        });

        Schema::table('field_items', function(Blueprint $table) {
            $table->foreign('field_id')
                ->references('field_id')
                ->on('fields')
                ->onDelete('cascade');
        });
        Schema::table('student_package_pages', function(Blueprint $table) {
            $table->foreign('package_id')
                ->references('package_id')
                ->on('packages')
                ->onDelete('cascade');
            $table->foreign('page_id')
                ->references('page_id')
                ->on('pages')
                ->onDelete('cascade');
        });
        Schema::table('user_intake_packages', function(Blueprint $table) {
            $table->foreign('user_id')
                ->references('user_id')
                ->on('users')
                ->onDelete('cascade');
            $table->foreign('intake_id')
                ->references('intake_id')
                ->on('intakes')
                ->onDelete('cascade');
            $table->foreign('package_id')
                ->references('package_id')
                ->on('packages')
                ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void {
        Schema::table('users', function(Blueprint $table) {
            $table->dropForeign(['role_id']);
        });
    }

};
