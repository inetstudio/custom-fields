<?php

namespace InetStudio\CustomFields\Providers;

use Illuminate\Support\ServiceProvider;

/**
 * Class CustomFieldsBindingsServiceProvider.
 */
class CustomFieldsBindingsServiceProvider extends ServiceProvider
{
    /**
    * @var  bool
    */
    protected $defer = true;

    /**
    * @var  array
    */
    public $bindings = [
        'InetStudio\CustomFields\Contracts\Models\CustomFieldModelContract' => 'InetStudio\CustomFields\Models\CustomFieldModel',
        'InetStudio\CustomFields\Contracts\Services\Back\CustomFieldsServiceContract' => 'InetStudio\CustomFields\Services\Back\CustomFieldsService',
    ];

    /**
     * Получить сервисы от провайдера.
     *
     * @return  array
     */
    public function provides()
    {
        return array_keys($this->bindings);
    }
}
