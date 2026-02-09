<?php
namespace Database\Seeders;

use App\Facades\ObzoraConfig;
use Illuminate\Database\Seeder;
use Illuminate\Support\Arr;
use Symfony\Component\Yaml\Yaml;

class ConfigSeeder extends Seeder
{
    /**
     * @var string[]
     */
    private $directories;

    public function __construct()
    {
        $this->directories = [dirname(__FILE__) . '/config'];

        if (is_dir('/data/config')) {
            $this->directories[] = '/data/config';
        }
    }

    public function run(): void
    {
        $files = array_merge(...array_map(function ($dir) {
            return glob("$dir/*.y*ml");  // both .yml and .yaml extensions
        }, $this->directories));

        if (empty($files)) {
            return; // nothing to do
        }

        $reapply = getenv('REAPPLY_YAML_CONFIG');

        if (ObzoraConfig::get('config_seeded') && ! $reapply) {
            if (! app()->runningInConsole() || ! $this->command->confirm(trans('commands.db:seed.existing_config'), false)) {
                return; // don't overwrite existing settings.
            }
        }

        $skipped_existing = false;

        foreach ($files as $file) {
            $settings = Yaml::parse(file_get_contents($file));
            foreach (Arr::wrap($settings) as $key => $value) {
                if (! is_string($key)) {
                    echo 'Skipped non-string config key: ' . json_encode($key) . PHP_EOL;
                    continue;
                }

                if (! $reapply && \App\Models\Config::where('config_name', $key)->exists()) {
                    if (! \App\Models\Config::where('config_name', $key)->value('config_value') == $value) {
                        echo 'Skipped existing config key: ' . json_encode($key) . PHP_EOL;
                        $skipped_existing = true;
                    }
                    continue;
                }

                ObzoraConfig::persist($key, $value);
            }
        }

        ObzoraConfig::persist('config_seeded', true);

        if ($skipped_existing) {
            echo 'Skipped overwriting existing config settings.  To overwrite them, run: REAPPLY_YAML_CONFIG=1 lnms db:seed' . PHP_EOL;
        }
    }
}
