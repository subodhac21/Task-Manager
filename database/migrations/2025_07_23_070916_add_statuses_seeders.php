<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
   public function up(): void
    {
        $statuses = [
            ['name' => 'todo', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'in-progress', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'done', 'created_at' => now(), 'updated_at' => now()]
        ];

        DB::table('statuses')->insert($statuses);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::table('statuses')->whereIn('name', ['todo', 'in-progress', 'done'])->delete();
    }
};
