<?php

use App\Models\License;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('license', function(Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('employeeId');
            $table->string('type', 50);
            $table->string('registrationNumber')->unique();
            $table->date('validFrom');
            $table->date('validUntil');
            $table->string('status', 50)->default(License::STATUS_ACTIVE);
            $table->timestamps();

            $table->foreign('employeeId')->references('id')->on('employee')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('license');
    }
};
