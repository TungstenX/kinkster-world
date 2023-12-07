<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('friends', function (Blueprint $table) {
          $table->dropForeign(['friend_id']);

          $table->foreign('friend_id')
            ->references('id')
            ->on('users')
            ->onDelete('cascade');
//             $table->foreignId('friend_id')->constrained(table: 'users')->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
    }
};
