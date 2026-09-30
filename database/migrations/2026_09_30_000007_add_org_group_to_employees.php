<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('employees', function (Blueprint $table) {
            $table->unsignedBigInteger('organization_id')->nullable()->after('is_active');
            $table->unsignedBigInteger('group_id')->nullable()->after('organization_id');
            $table->foreign('organization_id')->references('id')->on('organizations')->nullOnDelete();
            $table->foreign('group_id')->references('id')->on('groups')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('employees', function (Blueprint $table) {
            $table->dropForeign(['organization_id']);
            $table->dropForeign(['group_id']);
            $table->dropColumn(['organization_id', 'group_id']);
        });
    }
};
