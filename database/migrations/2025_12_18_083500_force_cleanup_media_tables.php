<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::disableForeignKeyConstraints();
        Schema::dropIfExists('media_files');
        Schema::dropIfExists('media_folders');
        Schema::enableForeignKeyConstraints();
    }

    public function down(): void
    {
        // No reverse
    }
};
