<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AttributesModel extends Model
{
    use HasFactory;
    protected $table = "attributes";

    protected $fillable = [
        'checklist_id',
        'object_domain',
        'object_id',
        'description',
        'is_completed',
        'completed_at',
        'created_at',
    ];

    protected $casts = [
	  'is_completed' => 'boolean',
	];

	public function items(){
		return $this->hasOne(ItemAttributesModel::class, 'attribute_id', 'id');
	}
}
