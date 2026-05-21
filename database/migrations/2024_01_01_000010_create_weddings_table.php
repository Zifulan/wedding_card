<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('weddings', function (Blueprint $table) {
            $table->id();
            $table->string('groom_name');
            $table->string('bride_name');
            $table->date('akad_date')->nullable();
            $table->time('akad_time')->nullable();
            $table->date('resepsi_date')->nullable();
            $table->time('resepsi_time')->nullable();
            $table->string('venue_name')->nullable();
            $table->text('venue_address')->nullable();
            $table->string('gmaps_link')->nullable();
            $table->string('rsvp_whatsapp')->nullable();
            $table->text('love_quote')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('weddings');
    }
};
