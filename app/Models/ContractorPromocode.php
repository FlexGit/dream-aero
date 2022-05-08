<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\Pivot;

/**
 * App\Models\ContractorPromocode
 *
 * @property \datetime $created_at
 * @property \datetime $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|ContractorPromocode newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|ContractorPromocode newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|ContractorPromocode query()
 * @mixin \Eloquent
 */
class ContractorPromocode extends Pivot
{
	use HasFactory;
	
	/**
	 * The attributes that are mass assignable.
	 *
	 * @var string[]
	 */
	protected $fillable = [
		'promocode_id',
		'contractor_id',
	];
	
	/**
	 * The attributes that should be cast.
	 *
	 * @var array
	 */
	protected $casts = [
		'created_at' => 'datetime:Y-m-d H:i:s',
		'updated_at' => 'datetime:Y-m-d H:i:s',
		'was_used' => 'boolean',
	];
}
