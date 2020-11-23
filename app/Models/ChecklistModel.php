<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ChecklistModel extends Model
{
    use HasFactory;
    protected $table = "checklist";

    protected $fillable = [
        'type',
    ];

    public function attributes()
    {
    	return $this->hasOne(AttributesModel::class, 'checklist_id', 'id');
    }

    public static function boot() {
        parent::boot();

        static::deleting(function($attr) {
             $attr->attributes()->delete();
        });
    }
}
