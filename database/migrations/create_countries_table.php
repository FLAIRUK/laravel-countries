<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function getConnection(): ?string
    {
        return config('countries.connection');
    }

    public function up(): void
    {
        Schema::create(config('countries.table', 'countries'), function (Blueprint $table) {
            $table->unsignedSmallInteger('id')->primary();
            $table->char('iso_3166_2', 2)->unique();
            $table->char('iso_3166_3', 3)->unique();
            $table->char('numeric_code', 3)->unique();
            $table->string('name');
            $table->string('full_name')->nullable();
            $table->string('capital')->nullable();
            $table->string('citizenship')->nullable();
            $table->string('currency')->nullable();
            $table->char('currency_code', 3)->nullable()->index();
            $table->string('currency_sub_unit')->nullable();
            $table->string('currency_symbol', 8)->nullable();
            $table->unsignedTinyInteger('currency_decimals')->nullable();
            $table->string('calling_code', 8)->nullable();
            $table->char('region_code', 3)->nullable();
            $table->char('sub_region_code', 3)->nullable();
            $table->boolean('eea')->default(false);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists(config('countries.table', 'countries'));
    }
};
