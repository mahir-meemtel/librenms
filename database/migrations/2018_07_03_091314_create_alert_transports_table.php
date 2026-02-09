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
        Schema::create('alert_transports', function (Blueprint $table) {
            $table->increments('transport_id');
            $table->string('transport_name', 30);
            $table->string('transport_type', 20)->default('mail');
            $table->boolean('is_default')->default(0);
            $table->text('transport_config')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down(): void
    {
        Schema::drop('alert_transports');
    }
};
