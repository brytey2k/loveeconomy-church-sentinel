<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Umbrellio\LTree\Interfaces\LTreeModelInterface;
use Umbrellio\LTree\Traits\LTreeModelTrait;

/**
 * @property int $id
 * @property int $giving_type_id
 * @property int|null $parent_id
 * @property string $name
 * @property string $path
 * @property string $amount_low
 * @property string $amount_high
 * @property bool $assignable
 * @property-read GivingType $givingType
 * @property-read GivingTypeSystem|null $parent
 * @property-read \Illuminate\Database\Eloquent\Collection<int, GivingTypeSystem> $children
 */
class GivingTypeSystem extends Model implements LTreeModelInterface
{
    use LTreeModelTrait;

    protected $table = 'church.giving_type_systems';

    protected $fillable = [
        'giving_type_id',
        'parent_id',
        'name',
        'amount_low',
        'amount_high',
        'assignable',
        'auto_assignable',
        'path',
    ];

    protected $casts = [
        'assignable' => 'bool',
        'auto_assignable' => 'bool',
    ];

    public function givingType()
    {
        return $this->belongsTo(GivingType::class);
    }

    public function parent()
    {
        return $this->belongsTo(self::class, 'parent_id');
    }

    public function children()
    {
        return $this->hasMany(self::class, 'parent_id');
    }
}
