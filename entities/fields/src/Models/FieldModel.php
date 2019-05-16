<?php

namespace InetStudio\CustomFieldsPackage\Fields\Models;

use OwenIt\Auditing\Auditable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use InetStudio\AdminPanel\Base\Models\Traits\Scopes\BuildQueryScopeTrait;
use InetStudio\CustomFieldsPackage\Fields\Contracts\Models\FieldModelContract;

/**
 * Class FieldModel.
 */
class FieldModel extends Model implements FieldModelContract
{
    use Auditable;
    use SoftDeletes;
    use BuildQueryScopeTrait;

    /**
     * Тип сущности.
     */
    const ENTITY_TYPE = 'custom_field';

    /**
     * Should the timestamps be audited?
     *
     * @var bool
     */
    protected $auditTimestamps = true;

    /**
     * Связанная с моделью таблица.
     *
     * @var string
     */
    protected $table = 'custom_fields';

    /**
     * Атрибуты, для которых разрешено массовое назначение.
     *
     * @var array
     */
    protected $fillable = [
        'customizable_type',
        'customizable_id',
        'key',
        'value',
    ];

    /**
     * Атрибуты, которые должны быть преобразованы в даты.
     *
     * @var array
     */
    protected $dates = [
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    /**
     * Загрузка модели.
     */
    protected static function boot()
    {
        parent::boot();

        self::$buildQueryScopeDefaults['columns'] = [
            'id',
            'customizable_type',
            'customizable_id',
            'key',
            'value',
        ];
    }

    /**
     * Сеттер атрибута metable_type.
     *
     * @param $value
     */
    public function setMetableTypeAttribute($value)
    {
        $this->attributes['customizable_type'] = trim(strip_tags($value));
    }

    /**
     * Сеттер атрибута metable_id.
     *
     * @param $value
     */
    public function setMetableIdAttribute($value)
    {
        $this->attributes['customizable_id'] = (int) trim(strip_tags($value));
    }

    /**
     * Сеттер атрибута key.
     *
     * @param $value
     */
    public function setKeyAttribute($value): void
    {
        $this->attributes['key'] = trim(strip_tags($value));
    }

    /**
     * Сеттер атрибута value.
     *
     * @param $value
     */
    public function setValueAttribute($value): void
    {
        $this->attributes['value'] = trim(strip_tags($value));
    }

    /**
     * Геттер атрибута type.
     *
     * @return string
     */
    public function getTypeAttribute(): string
    {
        return self::ENTITY_TYPE;
    }

    /**
     * Полиморфное отношение с остальными моделями.
     *
     * @return MorphTo
     */
    public function customizable(): MorphTo
    {
        return $this->morphTo();
    }
}
