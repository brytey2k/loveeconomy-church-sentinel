<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Carbon;
use Umbrellio\LTree\Interfaces\LTreeModelInterface;
use Umbrellio\LTree\Traits\LTreeModelTrait;

/**
 * @property int $id
 * @property string $name
 * @property int $level_id
 * @property int $country_id
 * @property string $path
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 *
 * @method static Builder<static>|Branch newModelQuery()
 * @method static Builder<static>|Branch newQuery()
 * @method static Builder<static>|Branch query()
 * @method static Builder<static>|Branch whereCountryId($value)
 * @method static Builder<static>|Branch whereCreatedAt($value)
 * @method static Builder<static>|Branch whereId($value)
 * @method static Builder<static>|Branch whereLevelId($value)
 * @method static Builder<static>|Branch whereName($value)
 * @method static Builder<static>|Branch wherePath($value)
 * @method static Builder<static>|Branch whereUpdatedAt($value)
 *
 * @property int $parent_id
 *
 * @method static \Umbrellio\LTree\Collections\LTreeCollection<int, static> all($columns = ['*'])
 * @method static Builder<static>|Branch ancestorByLevel(int $level = 1, ?string $path = null)
 * @method static Builder<static>|Branch ancestorsOf(\Umbrellio\LTree\Interfaces\LTreeModelInterface $model, bool $reverse = true)
 * @method static Builder<static>|Branch descendantsOf(\Umbrellio\LTree\Interfaces\LTreeModelInterface $model, bool $reverse = true)
 * @method static \Umbrellio\LTree\Collections\LTreeCollection<int, static> get($columns = ['*'])
 * @method static Builder<static>|Branch parentsOf(array<string> $paths)
 * @method static Builder<static>|Branch root()
 * @method static Builder<static>|Branch whereParentId($value)
 * @method static Builder<static>|Branch withoutSelf(int $id)
 *
 * @property Carbon|null $deleted_at
 * @property-read \Umbrellio\LTree\Collections\LTreeCollection<int, Branch> $ltreeChildren
 * @property-read int|null $ltree_children_count
 * @property-read Branch $ltreeParent
 *
 * @method static Builder<static>|Branch onlyTrashed()
 * @method static Builder<static>|Branch whereDeletedAt($value)
 * @method static Builder<static>|Branch withTrashed()
 * @method static Builder<static>|Branch withoutTrashed()
 *
 * @property-read Country $country
 * @property-read Level $level
 * @property-read \Umbrellio\LTree\Collections\LTreeCollection<int, GivingTypeSystem> $givingTypeSystems
 * @property-read int|null $giving_type_systems_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, GivingType> $givingTypes
 * @property-read int|null $giving_types_count
 *
 * @mixin \Eloquent
 */
class Branch extends Model implements LTreeModelInterface
{
    use LTreeModelTrait;
    use SoftDeletes;

    protected $table = 'organization.branches';

    protected $fillable = ['name', 'level_id', 'country_id', 'currency', 'parent_id', 'path'];

    public function level(): BelongsTo
    {
        return $this->belongsTo(Level::class);
    }

    public function country(): BelongsTo
    {
        return $this->belongsTo(Country::class);
    }

    public function ltreeParent(): BelongsTo
    {
        return $this->belongsTo(self::class, 'parent_id');
    }

    /**
     * Church giving types assigned to this branch.
     */
    public function givingTypes()
    {
        return $this->belongsToMany(GivingType::class, 'church.branch_giving_types', 'branch_id', 'giving_type_id')->withTimestamps();
    }

    /**
     * Church giving type systems assigned to this branch.
     */
    public function givingTypeSystems()
    {
        return $this->belongsToMany(GivingTypeSystem::class, 'church.branch_giving_type_systems', 'branch_id', 'giving_type_system_id')->withTimestamps();
    }
}
