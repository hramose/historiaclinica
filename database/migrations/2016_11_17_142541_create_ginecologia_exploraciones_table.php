<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateGinecologiaExploracionesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('ginecologia_exploraciones', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('historia_ginecologica_id')->unsigned(); $table->foreign('historia_ginecologica_id')->references('id')->on('historia_ginecologicas')->onDelete('restrict');
            $table->string('pa');
            $table->string('ta');
            $table->string('fc');
            $table->string('fr');
            $table->float('peso');
            $table->float('talla');
            $table->text('otros');
            $table->text('aspectogeneral');
            $table->softDeletes();
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
        Schema::dropIfExists('ginecologia_exploraciones');
    }
}
