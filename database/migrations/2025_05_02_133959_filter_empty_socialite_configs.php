<?php
use App\Facades\ObzoraConfig;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        $configs = ObzoraConfig::get('auth.socialite.configs', []);
        $configs = array_filter($configs, function ($key) {
            if (is_string($key) && strlen(trim($key)) == 0) {
                return false;
            }

            return true;
        }, ARRAY_FILTER_USE_KEY);

        ObzoraConfig::persist('auth.socialite.configs', $configs);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
    }
};
