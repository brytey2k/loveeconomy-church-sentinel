<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class GivingType extends Model
{
    use SoftDeletes;

    protected $table = 'church.giving_types';

    protected $fillable = [
        'key',
        'name',
        'description',
        'contribution_type',
    ];

    /**
     * Members that have this giving type.
     */
    public function members()
    {
        return $this->belongsToMany(Member::class, 'church.member_giving_types', 'giving_type_id', 'member_id');
    }
}
