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
        Schema::table('classes', function (Blueprint $table) {
            //ici on ajoute la cle etrangere
            $table->foreignId('vague_id')->nullable()->after('id')->constrained('vagues')->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('classes', function (Blueprint $table) {
            //en cas de retour en arriere , on supprime la cle etrangere
            $table->dropForeign(['vague_id']);
            $table->dropColumn(['vague_id']);
        });
    }
};
