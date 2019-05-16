<?php

namespace InetStudio\CustomFieldsPackage\Fields\Models\Traits;

use ArrayAccess;
use Illuminate\Support\Collection;
use InetStudio\CustomFieldsPackage\Fields\Contracts\Models\FieldModelContract;

/**
 * Trait HasCustomFieldsCollection.
 */
trait HasCustomFieldsCollection
{
    /**
     * Determine if the model has any the given custom fields.
     *
     * @param  int|string|array|ArrayAccess|FieldModelContract  $customFields
     *
     * @return bool
     */
    public function hasCustomFields($customFields): bool
    {
        if ($this->isCustomFieldsStringBased($customFields)) {
            return ! $this->custom_fields->pluck('key')->intersect((array) $customFields)->isEmpty();
        }

        if ($this->isCustomFieldsIntBased($customFields)) {
            return ! $this->custom_fields->pluck('id')->intersect((array) $customFields)->isEmpty();
        }

        if ($customFields instanceof FieldModelContract) {
            return $this->custom_fields->contains('key', $customFields['key']);
        }

        if ($customFields instanceof Collection) {
            return ! $customFields->intersect($this->custom_fields->pluck('key'))->isEmpty();
        }

        return false;
    }

    /**
     * Determine if the model has any the given custom fields.
     *
     * @param  int|string|array|ArrayAccess|FieldModelContract  $customFields
     *
     * @return bool
     */
    public function hasAnyCustomField($customFields): bool
    {
        return $this->hasCustomFields($customFields);
    }

    /**
     * Determine if the model has all of the given custom fields.
     *
     * @param  int|string|array|ArrayAccess|FieldModelContract  $customFields
     *
     * @return bool
     */
    public function hasAllCustomFields($customFields): bool
    {
        if ($this->isCustomFieldsStringBased($customFields)) {
            $customFields = (array) $customFields;

            return $this->custom_fields->pluck('key')->intersect($customFields)->count() == count($customFields);
        }

        if ($this->isCustomFieldsIntBased($customFields)) {
            $customFields = (array) $customFields;

            return $this->custom_fields->pluck('id')->intersect($customFields)->count() == count($customFields);
        }

        if ($customFields instanceof FieldModelContract) {
            return $this->custom_fields->contains('key', $customFields['key']);
        }

        if ($customFields instanceof Collection) {
            return $this->custom_fields->intersect($customFields)->count() == $customFields->count();
        }

        return false;
    }

    /**
     * Determine if the given custom are string based.
     *
     * @param  int|string|array|ArrayAccess|FieldModelContract  $customFields
     *
     * @return bool
     */
    protected function isCustomFieldsStringBased($customFields): bool
    {
        return is_string($customFields) || (is_array($customFields) && isset($customFields[0]) && is_string($customFields[0]));
    }

    /**
     * Determine if the given custom are integer based.
     *
     * @param  int|string|array|ArrayAccess|FieldModelContract  $customFields
     *
     * @return bool
     */
    protected function isCustomFieldsIntBased($customFields): bool
    {
        return is_int($customFields) || (is_array($customFields) && isset($customFields[0]) && is_int($customFields[0]));
    }
}
