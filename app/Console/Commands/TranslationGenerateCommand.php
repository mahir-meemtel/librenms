<?php
namespace App\Console\Commands;

use App\Console\LnmsCommand;

class TranslationGenerateCommand extends LnmsCommand
{
    protected $name = 'translation:generate';

    public function handle(): int
    {
        \Artisan::call('vue-i18n:generate', ['--multi-locales' => 'true', '--format' => 'umd']);

        // update hashes manually
        $this->updateManifest();

        return 0;
    }

    private function updateManifest(): void
    {
        $manifest_file = public_path('js/lang/manifest.json');

        if (file_exists($manifest_file)) {
            $manifest = json_decode(file_get_contents($manifest_file), true);
        } else {
            $manifest = [];
        }

        foreach (glob(public_path('js/lang/*.js')) as $file) {
            $file_name = str_replace(public_path(), '', $file);
            $locale = basename(str_replace('/js/lang/', '', $file_name), '.js');
            $manifest[$locale] = $file_name . '?id=' . substr(md5(file_get_contents($file)), 0, 20);
        }

        file_put_contents($manifest_file, json_encode($manifest, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES) . PHP_EOL);
    }
}
