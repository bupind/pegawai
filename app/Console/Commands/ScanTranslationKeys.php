<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;
use RegexIterator;
use Stichoza\GoogleTranslate\GoogleTranslate;

class ScanTranslationKeys extends Command
{
    protected $signature   = 'lang:scan {path=app} {locale=id}';
    protected $description = 'detect lang';

    public function handle()
    {
        $scanPath = base_path($this->argument('path'));
        $locale   = $this->argument('locale');
        $langFile = base_path("lang/{$locale}/app.php");

        if(!file_exists($langFile)) {
            file_put_contents($langFile, "<?php\n\nreturn [];\n");
        }
        $translations = include $langFile;
        $iterator     = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($scanPath));
        $phpFiles     = new RegexIterator($iterator, '/\.vue/');
        $pattern      = "/__\\('app\\.label\\.([a-zA-Z0-9_]+)'\\)/";
        $newKeys      = [];

        foreach($phpFiles as $file) {
            $content = file_get_contents($file->getPathname());
            preg_match_all($pattern, $content, $matches);

            foreach($matches[1] as $key) {
                if(!isset($translations['label'][$key])) {
                    $newKeys[$key] = ucfirst(str_replace('_', ' ', $key));
                }
            }
        }

        if(empty($newKeys)) {
            return;
        }
        $translator = new GoogleTranslate();
        $translator->setSource('en');
        $translator->setTarget($locale);

        foreach($newKeys as $key => $defaultValue) {
            $translatedValue             = $translator->translate($defaultValue);
            $translations['label'][$key] = $translatedValue;
            $this->info("added: '{$key}' => '{$translatedValue}'");
        }
        file_put_contents($langFile, "<?php\n\nreturn " . var_export($translations, true) . ";\n");

        $this->info("done /{$locale}/app.php.");
    }
}
