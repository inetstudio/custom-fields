<?php

namespace InetStudio\CustomFields\Models;

use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Contracts\Auditable;
use Illuminate\Database\Eloquent\SoftDeletes;
use InetStudio\CustomFields\Contracts\Models\CustomFieldModelContract;

/**
 * Class CustomFieldModel.
 */
class CustomFieldModel extends Model implements CustomFieldModelContract, Auditable
{
    use SoftDeletes;

    use \OwenIt\Auditing\Auditable;

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

    /**
     * Should the timestamps be audited?
     *
     * @var bool
     */
    protected $auditTimestamps = true;

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
