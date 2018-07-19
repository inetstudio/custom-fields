<?php

namespace InetStudio\CustomFields\Providers;

use Illuminate\Support\ServiceProvider;

/**
 * Class CustomFieldsServiceProvider.
 */
class CustomFieldsServiceProvider extends ServiceProvider
{
    /**
     * Загрузка сервиса.
     *
     * @return void
     */
    public function boot(): void
    {
        $this->registerConsoleCommands();
        $this->registerPublishes();
    }

    /**
     * Регистрация привязки в контейнере.
     *
     * @return void
     */
    public function register(): void
    {
        $this->registerBindings();
    }

    /**
     * Регистрация команд.
     *
     * @return void
     */
    protected function registerConsoleCommands(): void
    {
        if ($this->app->runningInConsole()) {
            $this->commands([
                'InetStudio\CustomFields\Console\Commands\SetupCommand',
            ]);
        }
    }

    /**
     * Регистрация ресурсов.
     *
     * @return void
     */
    protected function registerPublishes(): void
    {
        if ($this->app->runningInConsole()) {
            if (! class_exists('CreateCustomFieldsTables')) {
                $timestamp = date('Y_m_d_His', time());
                $this->publishes([
                    __DIR__.'/../../database/migrations/create_custom_fields_tables.php.stub' => database_path('migrations/'.$timestamp.'_create_custom_fields_tables.php'),
                ], 'migrations');
            }
        }
    }

    /**
     * Регистрация привязок, алиасов и сторонних провайдеров сервисов.
     *
     * @return void
     */
    protected function registerBindings(): void
    {
        // Models
        $this->app->bind('InetStudio\CustomFields\Contracts\Models\CustomFieldModelContract',  'InetStudio\CustomFields\Models\CustomFieldModel');

        // Services
        $this->app->bind('InetStudio\CustomFields\Contracts\Services\Back\CustomFieldsServiceContract', 'InetStudio\CustomFields\Services\Back\CustomFieldsService');
    }
}
