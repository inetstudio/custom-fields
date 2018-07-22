<?php

namespace InetStudio\CustomFields\Models\Traits;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Relations\MorphMany;

/**
 * Trait HasCustomFields.
 */
trait HasCustomFields
{
    /**
     * Get Custom Fields class name.
     *
     * @return string
     */
    public static function getCustomFieldsClassName(): string
    {
        $model = app()->make('InetStudio\CustomFields\Contracts\Models\CustomFieldModelContract');

        return get_class($model);
    }

    /**
     * Получаем все кастомные поля объекта.
     *
     * @return MorphMany
     */
    public function custom_fields(): MorphMany
    {
        return $this->morphMany(static::getCustomFieldsClassName(), 'customizable');
    }

    /**
     * Проверяем наличие кастомного поля.
     *
     * @param string $key
     *
     * @return mixed
     */
    public function hasCustomField(string $key): bool
    {
        return $this->custom_fields()->where('key', $key)->exists();
    }

    /**
     * Получаем все кастомные поля объекта в виде ключ-значение.
     *
     * @return mixed
     */
    public function getAllCustomFields()
    {
        return $this->custom_fields()->pluck('value', 'key')->toArray();
    }

    /**
     * Получаем кастомное поле.
     *
     * @param string $key
     * @param null $default
     *
     * @return mixed
     */
    public function getCustomField($key, $default = null, $returnObject = false)
    {
        $customField = $this->custom_fields()->where('key', $key)->first();

        if ($returnObject) {
            return $customField;
        }

        return ($customField) ? $customField->value : $default;
    }

    /**
     * Обновляем кастомное поле.
     *
     * @param string $key
     * @param $newValue
     *
     * @return mixed
     */
    public function updateCustomField($key, $newValue)
    {
        $customField = $this->getCustomField($key, null, true);

        if ($customField == null) {
            return $this->addCustomField($key, $newValue);
        }

        $now = Carbon::now()->format('Y-m-d H:m:s');

        return $customField->update([
            'value' => $newValue,
            'updated_at' => $now,
        ]);
    }

    /**
     * Добавляем кастомное поле.
     *
     * @param string $key
     * @param $value
     *
     * @return mixed
     */
    public function addCustomField(string $key, $value)
    {
        $existing = $this->custom_fields()
            ->where('key', $key)
            ->where('value', (string) $value)
            ->first();

        if ($existing) {
            return $existing;
        }

        $now = Carbon::now()->format('Y-m-d H:m:s');

        return $this->custom_fields()->create([
            'key' => $key,
            'value' => (string) $value,
            'created_at' => $now,
            'updated_at' => $now,
        ]);
    }

    /**
     * Удаляем кастомное поле.
     *
     * @param string $key
     *
     * @return mixed
     */
    public function deleteCustomField(string $key)
    {
        return $this->custom_fields()->where('key', $key)->delete();
    }

    /**
     * Удаляем все кастомные поля.
     *
     * @return mixed
     */
    public function deleteAllCustomFields()
    {
        return $this->custom_fields()->delete();
    }
}
