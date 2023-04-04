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
        Schema::create('people', function (Blueprint $table) {
            $table->increments('id');
            $table->string('identifier', 20)->nullable()->unique('funcionario_identidad_unique');
            $table->string('first_name', 191)->nullable();
            $table->string('full_name')->nullable();
            $table->string('second_name', 191)->nullable();
            $table->string('first_surname', 191)->nullable();
            $table->string('second_surname', 191)->nullable();
            $table->date('birth_date')->nullable();
            $table->string('direction')->nullable();
            $table->text('place_of_birth')->nullable();
            $table->enum('blood_type', ['A+', 'A-', 'B+', 'B-', 'O+', 'O-', 'AB+', 'AB-'])->nullable();
            $table->string('phone', 191)->nullable();
            $table->string('cell_phone', 191)->nullable();
            $table->string('email', 191)->nullable();
            $table->integer('eps_id')->nullable()->default(0);
            $table->text('address')->nullable();
            $table->enum('marital_status', ['Soltero(a)', 'Casado(a)', 'Divorciado(a)', 'Viudo(a)', 'Union Libre'])->nullable();
            $table->string('degree_instruction', 191)->nullable();
            $table->string('title', 191)->nullable();
            $table->string('talla_pantalon', 191)->nullable();
            $table->string('talla_bata', 191)->nullable();
            $table->string('talla_botas', 191)->nullable();
            $table->string('talla_camisa', 191)->nullable();
            $table->mediumText('image')->nullable();
            $table->integer('company_id')->nullable()->default(0);
            $table->integer('location_id')->nullable()->default(0);
            $table->string('shirt_size', 50)->nullable()->default('0');
            $table->string('pants_size', 50)->nullable()->default('0');
            $table->string('shue_size', 50)->nullable()->default('0');
            $table->integer('type_document_id')->nullable()->default(0);
            $table->integer('department_id')->nullable()->default(0);
            $table->integer('compensation_fund_id')->nullable()->default(0);
            $table->integer('severance_fund_id')->nullable()->default(0);
            $table->integer('arl_id');
            $table->integer('pension_fund_id')->nullable()->default(0);
            $table->integer('municipality_id')->nullable()->default(0);
            $table->integer('people_type_id')->nullable();
            $table->string('personId', 191)->nullable();
            $table->string('persistedFaceId', 191)->nullable();
            $table->enum('gener', ['Femenino', 'Masculino'])->nullable();
            $table->integer('Columna 50')->nullable();
            $table->enum('degree', ['Primaria', 'Secundaria', 'Técnica', 'Tecnológica', 'Profesional', 'Especialización', 'Maestría'])->nullable()->default('Primaria');
            $table->enum('status', ['Activo', 'Inactivo', 'Liquidado', 'PreLiquidado'])->default('Activo');
            $table->string('signature')->nullable();
            $table->string('color', 20)->nullable();
            $table->string('medical_record', 20)->nullable();
            $table->string('date_last_session', 20)->nullable();
            $table->timestamps();
            $table->integer('number_of_children')->nullable();
            $table->integer('work_contract_id')->nullable();
            $table->string('passport_number')->nullable();
            $table->enum('visa', ['Si', 'No'])->nullable()->default('No');
            $table->integer('payroll_risks_arl_id')->nullable();
            $table->integer('company_worked_id')->nullable()->default(1);
            $table->integer('folder_id')->nullable();
            $table->integer('apu_profile_id')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('people');
    }
};
