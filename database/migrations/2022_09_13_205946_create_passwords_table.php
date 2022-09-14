<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('passwords', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger("user_id")
                ->nullable();
            $table->foreign("user_id")
                ->on("users")
                ->references("id");
            $table->string("name");
            $table->string("login")->nullable();
            $table->text("password");
            $table->unsignedBigInteger("category_id")
                ->nullable();
            $table->foreign("category_id")
                ->on("categories")
                ->references("id");
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('passwords');
    }
};
