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
            $table->unsignedBigInteger('registrationCertificateId');
            $table->string('type', 50);
            $table->string('registrationNumber')->unique();
            $table->string('recommendationNumber')->nullable();
            $table->date('validFrom');
            $table->date('validUntil');
            $table->string('status', 50)->default(License::STATUS_ACTIVE);
            $table->timestamps();

            $table->foreign('employeeId')->references('id')->on('employee')->onDelete('cascade');
            $table->foreign('registrationCertificateId')->references('id')->on('registration_certificate')
                ->onDelete('cascade');
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
