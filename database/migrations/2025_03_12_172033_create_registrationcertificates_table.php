<?php

use App\Models\RegistrationCertificate;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('registration_certificate', function(Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('employeeId');
            $table->string('type', 50);
            $table->string('registrationNumber')->unique();
            $table->string('competence')->nullable();
            $table->string('certificateOfCompetenceNumber')->nullable();
            $table->date('validFrom');
            $table->date('validUntil');
            $table->unsignedBigInteger('registered_by')->nullable();
            $table->string('status', 50)->default(RegistrationCertificate::STATUS_ACTIVE);
            $table->timestamps();

            // Foreign Keys
            $table->foreign('employeeId')->references('id')->on('employee')->onDelete('cascade');
            $table->foreign('registered_by')->references('id')->on('users')->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('registration_certificate');
    }
};
