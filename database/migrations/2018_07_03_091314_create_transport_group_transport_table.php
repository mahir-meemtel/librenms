<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up(): void
    {
        Schema::create('transport_group_transport', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('transport_group_id');
            $table->unsignedInteger('transport_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down(): void
    {
        Schema::drop('transport_group_transport');
    }
};
