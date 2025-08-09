<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Tag extends Model
{
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
