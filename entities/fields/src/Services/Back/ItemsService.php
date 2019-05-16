<?php

namespace InetStudio\CustomFieldsPackage\Fields\Services\Back;

use Illuminate\Http\Request;
use InetStudio\AdminPanel\Base\Services\BaseService;
use InetStudio\CustomFieldsPackage\Fields\Contracts\Models\FieldModelContract;
use InetStudio\CustomFieldsPackage\Fields\Contracts\Services\Back\ItemsServiceContract;

/**
 * Class ItemsService.
 */
class ItemsService extends BaseService implements ItemsServiceContract
{
    /**
     * ItemsService constructor.
     *
     * @param  FieldModelContract  $model
     */
    public function __construct(FieldModelContract $model)
    {
        parent::__construct($model);
    }

    /**
     * Присваиваем кастомные поля объекту.
     *
     * @param $customFields
     * @param $item
     */
    public function attachToObject($customFields, $item): void
    {
        if ($customFields instanceof Request) {
            $customFields = $customFields->get('custom_fields', []);
        } else {
            $customFields = (array) $customFields;
        }

        if (! empty($customFields)) {
            $item->syncCustomFields($customFields);
        } else {
            $item->detachCustomFields($item->custom_fields);
        }
    }
}
