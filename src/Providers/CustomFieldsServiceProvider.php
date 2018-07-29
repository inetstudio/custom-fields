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
}
