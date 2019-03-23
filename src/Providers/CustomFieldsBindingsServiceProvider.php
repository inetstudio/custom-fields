<?php

namespace InetStudio\CustomFields\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Contracts\Support\DeferrableProvider;

/**
 * Class CustomFieldsBindingsServiceProvider.
 */
class CustomFieldsBindingsServiceProvider extends ServiceProvider implements DeferrableProvider
{
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
