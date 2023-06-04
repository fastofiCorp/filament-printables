<?php

namespace FastofiCorp\FilamentPrintables\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class FilamentPrintable extends Model
{
    use SoftDeletes;


    public function __construct()
    {
        $this->setTable(config('filament-printables.table'));
    }

    protected $fillable = [
        'slug',
        'name',
        'type',
        'template_view',
        'linked_resources',
        'format',
    ];

    protected $casts = [
        'linked_resources' => 'array',
        'format' => 'array',
    ];

    public function getRouteKeyName()
    {
        return 'slug';
    }
}
