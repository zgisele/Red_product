<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
 /**
 * @OA\Schema(
 *     schema="Hotel",
 *     type="object",
 *     title="Hotel",
 *     required={"name", "location", "rooms", "price"},
 *     @OA\Property(property="id", type="integer", example=1),
 *     @OA\Property(property="name", type="string", example="Hotel Paris"),
 *     @OA\Property(property="location", type="string", example="Paris"),
 *     @OA\Property(property="rooms", type="integer", example=10),
 *     @OA\Property(property="price", type="number", format="float", example=100.5),
 *     @OA\Property(property="image", type="string", nullable=true, example="hotels/hotel1.jpg"),
 *     @OA\Property(property="user_id", type="integer", example=1)
 * )
 */


class Hotel extends Model
{

   
    use HasFactory;
    protected $fillable = ['name', 'location', 'rooms', 'price','image', 'user_id'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
