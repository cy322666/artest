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
        Schema::create('shows', function (Blueprint $table) {
            $table->id();
            $table->timestamps();

            $table->string('lead_id')->nullable();
            $table->integer('status')->default(0);
            $table->string('object')->nullable();
            $table->string('type')->nullable();
            $table->string('name')->nullable();
            $table->boolean('is_new')->nullable();
            $table->boolean('is_close')->default(false);
            $table->dateTime('datetime')->nullable();
            $table->integer('pipeline_id')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('shows');
    }
};
