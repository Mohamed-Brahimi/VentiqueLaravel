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
        Schema::create('offers', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            $table->decimal("price");
            $table->dateTime("dateOffered");
            $table->boolean("erased");
            $table->foreignId("antique_id")->references("id")->on("antiques")->onDelete(action: "cascade")->onUpdate("cascade");
            $table->foreignId("user_id")->references("id")->on("users")->onDelete(action: "cascade")->onUpdate("cascade");

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('offers');
    }
};
