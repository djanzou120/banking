<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTransactionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('transactions', function (Blueprint $table) {
            $table->string('id')->primary();
            $table->string('accountId');
            $table->string('recipientId')->nullable();
            $table->unsignedInteger('statusId');
            $table->foreign('accountId')->references('id')->on('accounts')
                ->onDelete('cascade')
                ->onUpdate('cascade');
            $table->foreign('recipientId')->references('id')->on('accounts')
                ->onDelete('cascade')
                ->onUpdate('cascade');
            $table->foreign('statusId')->references('id')->on('status')
                ->onDelete('cascade')
                ->onUpdate('cascade');
            $table->string('name', 120)->default("Default name");
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('transactions');
    }
}
