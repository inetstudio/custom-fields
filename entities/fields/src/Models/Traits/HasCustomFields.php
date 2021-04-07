<?php

namespace InetStudio\CustomFieldsPackage\Fields\Models\Traits;

use ArrayAccess;
use Illuminate\Support\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Contracts\Container\BindingResolutionException;
use InetStudio\CustomFieldsPackage\Fields\Contracts\Models\FieldModelContract;

/**
 * Trait HasCustomFields.
 */
trait HasCustomFields
{
    use HasCustomFieldsCollection;

    /**
     * The queued custom fields.
     *
     * @var array
     */
    protected $queuedCustomFields = [];

    /**
     * Get CustomFields class name.
     *
     * @return string
     *
     * @throws BindingResolutionException
     */
    public function getCustomFieldClassName(): string
    {
        $model = app()->make(FieldModelContract::class);

        return get_class($model);
    }

    /**
     * Получаем все кастомное поля материала.
     *
     * @return MorphMany
     *
     * @throws BindingResolutionException
     */
    public function custom_fields(): MorphMany
    {
        $className = $this->getCustomFieldClassName();

        return $this->morphMany($className, 'customizable');
    }

    /**
     * Attach the given custom to the model.
     *
     * @param  int|string|array|ArrayAccess|FieldModelContract  $customFields
     *
     * @throws BindingResolutionException
     */
    public function setCustomFieldsAttribute($customFields): void
    {
        if (! $this->exists) {
            $this->queuedCustomFields = $customFields;

            return;
        }

        $this->attachCustomFields($customFields);
    }

    /**
     * Boot the HasCustomFields trait for a model.
     */
    public static function bootHasCustomFields()
    {
        static::created(
            function (Model $customizableModel) {
                if ($customizableModel->queuedCustomFields) {
                    $customizableModel->attachCustomFields($customizableModel->queuedCustomFields);
                    $customizableModel->queuedCustomFields = [];
                }
            }
        );

        static::deleted(
            function (Model $customizableModel) {
                $customizableModel->syncCustomFields(null);
            }
        );
    }

    /**
     * Get the custom list.
     *
     * @return array
     *
     * @throws BindingResolutionException
     */
    public function getCustomFieldsList(): array
    {
        return $this->custom_fields()
            ->pluck('value', 'key')
            ->toArray();
    }

    /**
     * Получаем кастомное поле.
     *
     * @param  string  $key
     * @param $default
     * @param  bool  $returnObject
     *
     * @return mixed|null
     *
     * @throws BindingResolutionException
     */
    public function getCustomField(string $key, $default = null, bool $returnObject = false)
    {
        $builder = $this->custom_fields()
            ->where('key', $key);

        if ($returnObject) {
            return $builder->withTrashed()->first();
        } else {
            $customFields = $builder->first();
        }

        return ($customFields) ? $customFields->value : $default;
    }

    /**
     * Scope query with all the given custom fields.
     *
     * @param  Builder  $query
     * @param  int|string|array|ArrayAccess|FieldModelContract  $customFields
     *
     * @return Builder
     *
     * @throws BindingResolutionException
     */
    public function scopeWithAllCustomFields(Builder $query, $customFields): Builder
    {
        $customFields = $this->isCustomFieldsStringBased($customFields)
            ? $customFields : $this->hydrateCustomFields($customFields)->pluck('key')->toArray();

        collect($customFields)->each(
            function ($customFieldsItem) use ($query) {
                $query->whereHas(
                    'custom_fields',
                    function (Builder $query) use ($customFieldsItem) {
                        return $query->where('key', $customFieldsItem);
                    }
                );
            }
        );

        return $query;
    }

    /**
     * Scope query with any of the given custom fields.
     *
     * @param  Builder  $query
     * @param  int|string|array|ArrayAccess|FieldModelContract  $customFields
     *
     * @return Builder
     *
     * @throws BindingResolutionException
     */
    public function scopeWithAnyCustomField(Builder $query, $customFields): Builder
    {
        $customFields = $this->isCustomFieldsStringBased($customFields)
            ? $customFields : $this->hydrateCustomFields($customFields)->pluck('key')->toArray();

        return $query->whereHas(
            'custom_fields',
            function (Builder $query) use ($customFields) {
                $query->whereIn('key', (array) $customFields);
            }
        );
    }

    /**
     * Scope query with any of the given custom fields.
     *
     * @param  Builder  $query
     * @param  int|string|array|ArrayAccess|FieldModelContract  $customFields
     *
     * @return Builder
     *
     * @throws BindingResolutionException
     */
    public function scopeWithCustomFields(Builder $query, $customFields): Builder
    {
        return $this->scopeWithAnyCustomField($query, $customFields);
    }

    /**
     * Scope query without the given custom fields.
     *
     * @param  Builder  $query
     * @param  int|string|array|ArrayAccess|FieldModelContract  $customFields
     *
     * @return Builder
     *
     * @throws BindingResolutionException
     */
    public function scopeWithoutCustomFields(Builder $query, $customFields): Builder
    {
        $customFields = $this->isCustomFieldsStringBased($customFields)
            ? $customFields : $this->hydrateCustomFields($customFields)->pluck('key')->toArray();

        return $query->whereDoesntHave(
            'custom_fields',
            function (Builder $query) use ($customFields) {
                $query->whereIn('key', (array) $customFields);
            }
        );
    }

    /**
     * Scope query without any custom fields.
     *
     * @param  Builder  $query
     *
     * @return Builder
     */
    public function scopeWithoutAnyCustomField(Builder $query): Builder
    {
        return $query->doesntHave('custom_fields');
    }

    /**
     * Attach the given custom to the model.
     *
     * @param  int|string|array|ArrayAccess|FieldModelContract  $customFields
     *
     * @return $this
     *
     * @throws BindingResolutionException
     */
    public function attachCustomFields($customFields): self
    {
        static::$dispatcher->dispatch('inetstudio.custom_fields.attaching', [$this, $customFields]);

        foreach ($customFields as $key => $value) {
            $this->updateCustomField($key, $value);
        }

        static::$dispatcher->dispatch('inetstudio.custom_fields.attached', [$this, $customFields]);

        return $this;
    }

    /**
     * Sync the given custom to the model.
     *
     * @param  int|string|array|ArrayAccess|FieldModelContract|null  $customFields
     *
     * @return $this
     *
     * @throws BindingResolutionException
     */
    public function syncCustomFields($customFields): self
    {
        static::$dispatcher->dispatch('inetstudio.custom_fields.syncing', [$this, $customFields]);

        foreach (array_diff($this->custom_fields->pluck('key')->toArray(), array_keys($customFields ?? [])) as $key) {
            $this->deleteCustomField($key);
        }

        foreach ($customFields ?? [] as $key => $value) {
            if (! $value) {
                $this->deleteCustomField($key);
            } else {
                $this->updateCustomField($key, $value);
            }
        }

        static::$dispatcher->dispatch('inetstudio.custom_fields.synced', [$this, $customFields]);

        return $this;
    }

    /**
     * Detach the given custom from the model.
     *
     * @param  int|string|array|ArrayAccess|FieldModelContract  $customFields
     *
     * @return $this
     *
     * @throws BindingResolutionException
     */
    public function detachCustomFields($customFields): self
    {
        static::$dispatcher->dispatch('inetstudio.custom_fields.detaching', [$this, $customFields]);

        $this->deleteAllCustomFields();

        static::$dispatcher->dispatch('inetstudio.custom_fields.detached', [$this, $customFields]);

        return $this;
    }

    /**
     * Hydrate custom fields.
     *
     * @param  int|string|array|ArrayAccess|FieldModelContract  $customFields
     *
     * @return Collection
     *
     * @throws BindingResolutionException
     */
    protected function hydrateCustomFields($customFields): Collection
    {
        $isCustomFieldsStringBased = $this->isCustomFieldsStringBased($customFields);
        $isCustomFieldsIntBased = $this->isCustomFieldsIntBased($customFields);
        $field = $isCustomFieldsStringBased ? 'key' : 'id';
        $className = $this->getCustomFieldClassName();

        return $isCustomFieldsStringBased || $isCustomFieldsIntBased
            ? $className::query()->whereIn($field, (array) $customFields)->get() : collect($customFields);
    }

    /**
     * Обновляем кастомное поле.
     *
     * @param  string  $key
     * @param $newValue
     *
     * @return mixed
     *
     * @throws BindingResolutionException
     */
    protected function updateCustomField($key, $newValue)
    {
        $customFields = $this->getCustomField($key, null, true);

        if ($customFields === null) {
            return $this->addCustomField($key, $newValue);
        }

        if ($customFields->trashed()) {
            $customFields->restore();
        }

        return $customFields->update(
            [
                'value' => $newValue,
            ]
        );
    }

    /**
     * Добавляем кастомное поле.
     *
     * @param  string  $key
     * @param $value
     *
     * @return mixed
     *
     * @throws BindingResolutionException
     */
    protected function addCustomField($key, $value)
    {
        if (! $value) {
            return false;
        }

        $existing = $this->custom_fields()
            ->where('key', $key)
            ->where('value', $value)
            ->exists();

        if ($existing) {
            return false;
        }

        return $this->custom_fields()->create(
            [
                'key' => $key,
                'value' => $value,
            ]
        );
    }

    /**
     * Удаляем кастомное поле.
     *
     * @param  string  $key
     *
     * @return mixed
     *
     * @throws BindingResolutionException
     */
    protected function deleteCustomField($key)
    {
        return $this->custom_fields()
            ->where('key', $key)
            ->delete();
    }

    /**
     * Удаляем все кастомные поля.
     *
     * @return mixed
     *
     * @throws BindingResolutionException
     */
    protected function deleteAllCustomFields()
    {
        return $this->custom_fields()->delete();
    }
}
