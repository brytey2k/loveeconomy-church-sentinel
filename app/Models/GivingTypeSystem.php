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
 * @property bool $auto_assignable
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read GivingType $givingType
 * @property-read GivingTypeSystem|null $parent
 * @property-read \Illuminate\Database\Eloquent\Collection<int, GivingTypeSystem> $children
 * @property-read int|null $children_count
 * @property-read \Umbrellio\LTree\Collections\LTreeCollection<int, GivingTypeSystem> $ltreeChildren
 * @property-read int|null $ltree_children_count
 * @property-read GivingTypeSystem|null $ltreeParent
 *
 * @method static \Umbrellio\LTree\Collections\LTreeCollection<int, static> all($columns = ['*'])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|GivingTypeSystem ancestorByLevel(int $level = 1, ?string $path = null)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|GivingTypeSystem ancestorsOf(\Umbrellio\LTree\Interfaces\LTreeModelInterface $model, bool $reverse = true)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|GivingTypeSystem descendantsOf(\Umbrellio\LTree\Interfaces\LTreeModelInterface $model, bool $reverse = true)
 * @method static \Umbrellio\LTree\Collections\LTreeCollection<int, static> get($columns = ['*'])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|GivingTypeSystem newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|GivingTypeSystem newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|GivingTypeSystem parentsOf(array $paths)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|GivingTypeSystem query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|GivingTypeSystem root()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|GivingTypeSystem whereAmountHigh($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|GivingTypeSystem whereAmountLow($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|GivingTypeSystem whereAssignable($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|GivingTypeSystem whereAutoAssignable($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|GivingTypeSystem whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|GivingTypeSystem whereGivingTypeId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|GivingTypeSystem whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|GivingTypeSystem whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|GivingTypeSystem whereParentId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|GivingTypeSystem wherePath($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|GivingTypeSystem whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|GivingTypeSystem withoutSelf(int $id)
 *
 * @mixin \Eloquent
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
