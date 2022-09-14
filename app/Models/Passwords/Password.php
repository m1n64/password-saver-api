<?php

namespace App\Models\Passwords;

use App\Models\Categories\Category;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use OpenApi\Annotations as OA;

/**
 * @OA\Schema(
 *     title="Password",
 *     description="Password model",
 *     @OA\Xml(
 *          name="Password"
 *     ),
 *     @OA\Property(property="id", type="integer", example="1"),
 *     @OA\Property(property="name", type="string", example="github"),
 *     @OA\Property(property="login", type="string", nullable=true, example="johndoe"),
 *     @OA\Property(property="category_id", type="integer", example="1"),
 *     @OA\Property(property="category", type="object", ref="#/components/schemas/Category"),
 *)
 *
 * Class Password
 * @package App\Models\Passwords
 */
class Password extends Model
{
    use HasFactory;

    protected $fillable = [
        "user_id",
        "name",
        "login",
        "password",
        "category_id"
    ];

    protected $hidden = [
        "created_at",
        "updated_at",
        "user_id",
        "password"
    ];

    public function category()
    {
        return $this->belongsTo(Category::class);
    }
}
