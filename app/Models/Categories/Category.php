<?php

namespace App\Models\Categories;

use App\Models\Passwords\Password;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use OpenApi\Annotations as OA;

/**
 * @OA\Schema(
 *     title="Category",
 *     description="Category model",
 *     @OA\Xml(
 *          name="Category"
 *     ),
 *     @OA\Property(property="id", type="integer", example="1"),
 *     @OA\Property(property="name", type="string", example="Category 1"),
 *     @OA\Property(property="icon", type="string", example="<svg icon>"),
 * )
 *
 */
class Category extends Model
{
    use HasFactory;

    protected $hidden = [
        "created_at",
        "updated_at"
    ];

    public function password()
    {
        return $this->hasOne(Password::class);
    }
}
