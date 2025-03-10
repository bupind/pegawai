<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('files', function(Blueprint $table) {
            $table->id();
            $table->string('type');
            $table->string('originalName');
            $table->string('alias')->nullable();
            $table->string('path');
            $table->string('filename');
            $table->integer('size');
            $table->string('extension');
            $table->string('mimeType');
            $table->text('additionalInformation')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('files');
    }
};
