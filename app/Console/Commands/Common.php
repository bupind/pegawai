<?php

namespace App\Console\Commands;

use App\Models\Permission;
use App\Models\Role;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;
use function Termwind\{render};

class Common extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'common:generate {name}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate resource model app';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $name = ucfirst($this->argument('name'));
        $this->makeDir(resource_path("/js/Pages/{$name}"));
        $this->makeDir(app_path("/HTTP/Requests/{$name}"));
        $this->makeDir(base_path("/routes/common"));

//        Permission::create(['name' => strtolower($name) . ' create', 'guard_name' => 'web']);
//        Permission::create(['name' => strtolower($name) . ' read', 'guard_name' => 'web']);
//        Permission::create(['name' => strtolower($name) . ' update', 'guard_name' => 'web']);
//        Permission::create(['name' => strtolower($name) . ' delete', 'guard_name' => 'web']);

        $superadmin = Role::where(['name' => 'superuser'])->first();
        $superadmin->givePermissionTo([
            strtolower($name) . ' create',
            strtolower($name) . ' read',
            strtolower($name) . ' update',
            strtolower($name) . ' delete',
        ]);

        render(view('cli.common', [
            'controller'     => $this->controller($name),
            'indexRequest'   => $this->indexRequest($name),
            'storeRequest'   => $this->storeRequest($name),
            'updateRequest'  => $this->updateRequest($name),
            'pageIndex'      => $this->pageIndex($name),
            'pageCreate'     => $this->pageCreate($name),
            'pageDelete'     => $this->pageDelete($name),
            'pageDeleteBulk' => $this->pageDeleteBulk($name),
            'pageEdit'       => $this->pageEdit($name),
        ]));

        return self::SUCCESS;
    }

    protected function makeDir($path)
    {
        return is_dir($path) || mkdir($path);
    }

    protected function controller($name): string
    {
        $params = str_replace(
            [
                '{{modelName}}',
                '{{modelNamePlural}}',
                '{{modelNameLowerCase}}',
                '{{modelNamePluralLowerCase}}',
            ],
            [
                $name,
                Str::plural($name),
                strtolower($name),
                strtolower(Str::plural($name)),
            ],
            $this->getStub('Controller')
        );
        file_put_contents(app_path("/Http/Controllers/{$name}Controller.php"), $params);
        return "app/Http/Controllers/{$name}Controller.php";
    }

    protected function getStub($type)
    {
        return file_get_contents(resource_path("stubs/$type.stub"));
    }

    public function indexRequest($name): string
    {
        $params = str_replace(['{{modelName}}'], [
            $name,
        ], $this->getStub('Requests/Index')
        );
        $path   = "/HTTP/Requests/{$name}/{$name}IndexRequest.php";
        file_put_contents(app_path($path), $params);
        return "app" . $path;
    }

    public function storeRequest($name): string
    {
        $params = str_replace(
            [
                '{{modelName}}',
            ], [
            $name,
        ], $this->getStub('Requests/Store')
        );
        $path   = "/HTTP/Requests/{$name}/{$name}StoreRequest.php";
        file_put_contents(app_path($path), $params);
        return "app" . $path;
    }

    public function updateRequest($name): string
    {
        $params = str_replace(
            [
                '{{modelName}}',
            ], [
            $name,
        ], $this->getStub('Requests/Update')
        );
        $path   = "/HTTP/Requests/{$name}/{$name}UpdateRequest.php";
        file_put_contents(app_path($path), $params);
        return "app" . $path;
    }

    public function pageIndex($name): string
    {
        $params = str_replace(
            [
                '{{modelName}}',
                '{{modelNameLowerCase}}',
                '{{modelNamePluralLowerCase}}',
            ],
            [
                $name,
                strtolower($name),
                strtolower(Str::plural($name)),
            ], $this->getStub('Pages/Index')
        );
        $path   = "/js/Pages/{$name}/Index.vue";
        file_put_contents(resource_path($path), $params);
        return "resources" . $path;
    }

    public function pageCreate($name): string
    {
        $params = str_replace(
            [
                '{{modelName}}',
                '{{modelNameLowerCase}}',
            ],
            [
                $name,
                strtolower($name),
            ], $this->getStub('Pages/Create')
        );
        $path   = "/js/Pages/{$name}/Create.vue";
        file_put_contents(resource_path($path), $params);
        return "resources" . $path;
    }

    public function pageDelete($name): string
    {
        $params = str_replace(
            [
                '{{modelNameLowerCase}}',
            ],
            [
                strtolower($name),
            ], $this->getStub('Pages/Delete')
        );
        $path   = "/js/Pages/{$name}/Delete.vue";
        file_put_contents(resource_path($path), $params);
        return "resources" . $path;
    }

    public function pageDeleteBulk($name): string
    {
        $params = str_replace(
            [
                '{{modelNameLowerCase}}',
            ],
            [
                strtolower($name),
            ], $this->getStub('Pages/DeleteBulk')
        );
        $path   = "/js/Pages/{$name}/DeleteBulk.vue";
        file_put_contents(resource_path($path), $params);
        return "resources" . $path;
    }

    public function pageEdit($name): string
    {
        $params = str_replace(
            [
                '{{modelName}}',
                '{{modelNameLowerCase}}',
            ],
            [
                $name,
                strtolower($name),
            ], $this->getStub('Pages/Edit')
        );
        $path   = "/js/Pages/{$name}/Edit.vue";
        file_put_contents(resource_path($path), $params);
        return "resources" . $path;
    }
}
