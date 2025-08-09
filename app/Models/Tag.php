<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Tag extends Model
{
    use SoftDeletes;

    protected $table = 'church.tags';

    protected $fillable = [
        'key',
        'name',
        'description',
    ];

    /**
     * Members that have this tag.
     */
    public function members()
    {
        return $this->belongsToMany(Member::class, 'church.member_tags', 'tag_id', 'member_id');
    }
}
