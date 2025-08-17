<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Pivot model for church.member_giving_type_systems
 *
 * @property int $member_id
 * @property int $giving_type_system_id
 * @property-read Member $member
 * @property-read GivingTypeSystem $system
 */
class MemberGivingTypeSystem extends Model
{
    protected $table = 'church.member_giving_type_systems';

    public $timestamps = true;

    public $incrementing = false;

    protected $primaryKey = null; // composite key; Eloquent will use where clauses on both keys

    protected $fillable = [
        'member_id',
        'giving_type_system_id',
    ];

    protected $casts = [
        'member_id' => 'int',
        'giving_type_system_id' => 'int',
    ];

    public function member()
    {
        return $this->belongsTo(Member::class);
    }

    public function system()
    {
        return $this->belongsTo(GivingTypeSystem::class, 'giving_type_system_id');
    }
}
