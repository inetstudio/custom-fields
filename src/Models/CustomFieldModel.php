<?php

namespace InetStudio\CustomFields\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Venturecraft\Revisionable\RevisionableTrait;
use InetStudio\CustomFields\Contracts\Models\CustomFieldModelContract;

/**
 * Class CustomFieldModel.
 */
class CustomFieldModel extends Model implements CustomFieldModelContract
{
    use SoftDeletes;

    use RevisionableTrait;

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

    protected $revisionCreationsEnabled = true;

    /**
     * Полиморфное отношение с остальными моделями.
     *
     * @return \Illuminate\Database\Eloquent\Relations\MorphTo
     */
    public function customizable()
    {
        return $this->morphTo();
    }
}
