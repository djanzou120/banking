<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Http\Controllers\Controller;

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
            $table->increments('id');
            $table->unsignedInteger('depositAgentId')->nullable();
            $table->unsignedInteger('accountId');
            $table->unsignedInteger('recipientId')->nullable();
            $table->unsignedInteger('fromId')->nullable();
            $table->enum('type', ['DEPOSIT', 'SEND', 'RECEIVE']);
            $table->enum('status', ['INIT', 'SUCCESS']);
            $table->float('amount');
            $table->foreign('depositAgentId')->references('id')->on('users')
                ->onDelete('cascade')
                ->onUpdate('cascade');
            $table->foreign('accountId')->references('id')->on('accounts')
                ->onDelete('cascade')
                ->onUpdate('cascade');
            $table->foreign('recipientId')->references('id')->on('accounts')
                ->onDelete('cascade')
                ->onUpdate('cascade');
            $table->foreign('fromId')->references('id')->on('accounts')
                ->onDelete('cascade')
                ->onUpdate('cascade');
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
