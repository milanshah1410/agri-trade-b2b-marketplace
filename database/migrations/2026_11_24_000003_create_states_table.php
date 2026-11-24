<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('states', function (Blueprint $table) {
            $table->id();
            $table->string('name', 80)->unique();
            $table->string('code', 2)->unique();      // ISO 3166-2:IN suffix, e.g. GJ
            $table->string('gst_code', 2)->unique();  // GST state code, e.g. 24
            $table->string('type', 10)->default('state'); // state | ut
            $table->boolean('is_serviceable')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('states');
    }
};
