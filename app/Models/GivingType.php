<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * @property int $id
 * @property string $key
 * @property string $name
 * @property string|null $description
 * @property string $contribution_type
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 *
 * @method static \Illuminate\Database\Eloquent\Builder<static>|GivingType newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|GivingType newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|GivingType onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|GivingType query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|GivingType whereContributionType($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|GivingType whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|GivingType whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|GivingType whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|GivingType whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|GivingType whereKey($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|GivingType whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|GivingType whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|GivingType withTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|GivingType withoutTrashed()
 *
 * @property bool $auto_assignable
 * @property-read \Illuminate\Database\Eloquent\Collection<int, Member> $members
 * @property-read int|null $members_count
 * @property-read \Umbrellio\LTree\Collections\LTreeCollection<int, Branch> $branches
 * @property-read int|null $branches_count
 *
 * @method static \Illuminate\Database\Eloquent\Builder<static>|GivingType whereAutoAssignable($value)
 *
 * @mixin \Eloquent
 */
class GivingType extends Model
{
    use SoftDeletes;

    protected $table = 'church.giving_types';

    protected $casts = [
        'auto_assignable' => 'bool',
    ];

    protected $fillable = [
        'key',
        'name',
        'description',
        'contribution_type',
        'auto_assignable',
    ];

    /**
     * Members that have this giving type.
     */
    public function members()
    {
        return $this->belongsToMany(Member::class, 'church.member_giving_types', 'giving_type_id', 'member_id')
            ->withTimestamps();
    }

    /**
     * Branches that have this giving type.
     */
    public function branches()
    {
        return $this->belongsToMany(Branch::class, 'church.branch_giving_types', 'giving_type_id', 'branch_id');
    }
}
