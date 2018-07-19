<?php

namespace InetStudio\CustomFields\Services\Back;

use InetStudio\CustomFields\Contracts\Services\Back\CustomFieldsServiceContract;

/**
 * Class CustomFieldsService.
 */
class CustomFieldsService implements CustomFieldsServiceContract
{
    /**
     * Сохраняем кастомные поля.
     *
     * @param $request
     * @param $item
     */
    public function attachToObject($request, $item): void
    {
        if ($request->filled('custom')) {
            foreach ($request->get('custom') as $key => $value) {
                $item->updateCustomField($key, $value);
            }
        } else {
            $item->deleteAllCustomFields();
        }
    }
}
