<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('page_visits', function (Blueprint $table) {
            $table->id();
            $table->string('page_name');
            $table->string('page_url');
            $table->bigInteger('visit_count')->default(0);
            $table->timestamps();
            
            $table->unique('page_url');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('page_visits');
    }
};
