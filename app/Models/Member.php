<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * @property int $id
 * @property string $first_name
 * @property string|null $last_name
 * @property string $phone
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 *
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Member newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Member newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Member query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Member whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Member whereFirstName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Member whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Member whereLastName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Member wherePhone($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Member whereUpdatedAt($value)
 *
 * @property int $branch_id
 * @property int $position_id
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property-read Branch $branch
 * @property-read \Illuminate\Database\Eloquent\Collection<int, GivingType> $givingTypes
 * @property-read int|null $giving_types_count
 * @property-read Position $position
 *
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Member onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Member whereBranchId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Member whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Member wherePositionId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Member withTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Member withoutTrashed()
 *
 * @mixin \Eloquent
 */
class Member extends Model
{
    use SoftDeletes;

    protected $table = 'church.members';

    protected $fillable = [
        'first_name',
        'last_name',
        'phone',
        'branch_id',
        'position_id',
    ];

    /**
     * Get the branch that the member belongs to.
     */
    public function branch()
    {
        return $this->belongsTo(Branch::class);
    }

    /**
     * Get the position that the member holds.
     */
    public function position()
    {
        return $this->belongsTo(Position::class);
    }

    /**
     * Giving types associated with this member.
     */
    public function givingTypes()
    {
        return $this->belongsToMany(GivingType::class, 'church.member_giving_types', 'member_id', 'giving_type_id');
    }

    /**
     * Deprecated alias for givingTypes(). Will be removed after full migration away from tags.
     */
    public function tags()
    {
        return $this->givingTypes();
    }
}
