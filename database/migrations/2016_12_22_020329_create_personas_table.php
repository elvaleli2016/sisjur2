<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePersonasTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('personas', function (Blueprint $table) {
            $table->increments('id');
            $table->string('dni')->unique();
            $table->string('nombre');
            $table->string('apellido');
            $table->date('fecha_nac');
            $table->string('telefono')->nullable();
            $table->string('celular')->nullable();
            $table->string('correo')->unique();
            $table->string('password');
            $table->enum('tipo',['administrador','abogado','cliente']);
            $table->string('foto')->nullable();
            $table->rememberToken();
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
        Schema::dropIfExists('personas');
    }

}
