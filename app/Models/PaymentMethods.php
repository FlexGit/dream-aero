<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\PaymentMethods
 *
 * @property int $id
 * @property string $name наименование способа оплаты
 * @property int $is_active признак активности
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|PaymentMethods newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|PaymentMethods newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|PaymentMethods query()
 * @method static \Illuminate\Database\Eloquent\Builder|PaymentMethods whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PaymentMethods whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PaymentMethods whereIsActive($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PaymentMethods whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PaymentMethods whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class PaymentMethods extends Model
{
    use HasFactory;

	/**
	 * The attributes that are mass assignable.
	 *
	 * @var string[]
	 */
	protected $fillable = [
		'name',
		'is_active',
	];
}
