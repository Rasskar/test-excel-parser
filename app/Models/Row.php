<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Row extends Model
{
    /**
     * @var string
     */
    protected $table = 'rows';

    /**
     * @var bool
     */
    public $incrementing = false;

    /**
     * @var bool
     */
    public $timestamps = false;

    /**
     * @var string[]
     */
    protected $fillable = [
        'id',
        'name',
        'date',
    ];

    /**
     * @return string[]
     */
    protected function casts(): array
    {
        return [
            'date' => 'date',
        ];
    }
}
