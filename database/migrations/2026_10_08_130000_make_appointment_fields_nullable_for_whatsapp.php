<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

/**
 * WhatsApp bookings (especially existing clients) don't collect every field the
 * website form requires, so the appointment can be created from chat with the
 * rest left for the Customer Care team to fill on the confirmation call.
 * The website form still validates these as required, so website bookings are
 * unaffected.
 */
return new class extends Migration
{
    public function up(): void
    {
        DB::statement("ALTER TABLE appointment_enquiries
            MODIFY email VARCHAR(255) NULL,
            MODIFY address VARCHAR(255) NULL,
            MODIFY pincode VARCHAR(10) NULL,
            MODIFY pet_type ENUM('dog','cat') NULL,
            MODIFY pet_gender ENUM('male','female') NULL,
            MODIFY appointment_date DATE NULL");
    }

    public function down(): void
    {
        DB::statement("ALTER TABLE appointment_enquiries
            MODIFY email VARCHAR(255) NOT NULL,
            MODIFY address VARCHAR(255) NOT NULL,
            MODIFY pincode VARCHAR(10) NOT NULL,
            MODIFY pet_type ENUM('dog','cat') NOT NULL,
            MODIFY pet_gender ENUM('male','female') NOT NULL,
            MODIFY appointment_date DATE NOT NULL");
    }
};
