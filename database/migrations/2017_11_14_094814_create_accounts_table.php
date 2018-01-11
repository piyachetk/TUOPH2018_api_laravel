<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateAccountsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('accounts', function (Blueprint $table) {
            $table->increments('id');
            $table->enum('type', ['student', 'teacher', 'student-college', 'guardian']);
            $table->enum('prefix', ['mr', 'miss', 'mrs', 'master-boy', 'master-girl', 'other'])->nullable();
            $table->string('school')->nullable();
            $table->enum('studentYear', ['p1-3', 'p4-6', 'm1', 'm2', 'm3', 'm4', 'm5', 'm6'])->nullable();
            $table->string('firstName')->nullable();
            $table->string('lastName')->nullable();
            $table->string('email')->nullable();

            $table->mediumText('picture')->nullable();

            $table->mediumText('scanned')->nullable();
            $table->mediumText('interests')->nullable();

            $table->integer('points')->default(0);

            $table->boolean('registered')->default(false);
            $table->boolean('receivedCert')->default(false);

            $table->string('ref_no');

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
        Schema::dropIfExists('accounts');
    }
}
