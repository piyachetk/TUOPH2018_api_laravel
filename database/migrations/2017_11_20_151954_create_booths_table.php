<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateBoothsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('booths', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name');
            $table->enum('type', ['program', 'club', 'organization', 'event', 'show', 'concert', 'competition','other']);
            $table->mediumText('description');
            $table->mediumText('preview');
            $table->mediumText('picture');
            $table->mediumText('location');
            $table->mediumText('time')->nullable();
            $table->unsignedInteger('points')->default(0);
            $table->unsignedInteger('scanCount')->default(0);
            $table->mediumText('admin')->nullable();
            $table->mediumText('tags')->nullable();
            $table->boolean('isHighlight')->default(false);

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
        Schema::dropIfExists('booths');
    }
}
