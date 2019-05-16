<?php

namespace InetStudio\CustomFieldsPackage\Fields\Providers;

use Illuminate\Contracts\Support\DeferrableProvider;
use Illuminate\Support\ServiceProvider as BaseServiceProvider;

/**
 * Class BindingsServiceProvider.
 */
class BindingsServiceProvider extends BaseServiceProvider implements DeferrableProvider
{
    /**
     * @var array
     */
    public $bindings = [
        'InetStudio\CustomFieldsPackage\Fields\Contracts\Models\FieldModelContract' => 'InetStudio\CustomFieldsPackage\Fields\Models\FieldModel',
        'InetStudio\CustomFieldsPackage\Fields\Contracts\Services\Back\ItemsServiceContract' => 'InetStudio\CustomFieldsPackage\Fields\Services\Back\ItemsService',
    ];

    /**
     * Получить сервисы от провайдера.
     *
     * @return array
     */
    public function provides()
    {
        return array_keys($this->bindings);
    }
}
