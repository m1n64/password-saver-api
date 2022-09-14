<?php

namespace App\Models;

use App\Repositories\KeyModel\KeyInterface;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Key extends Model implements KeyInterface
{
    use HasFactory;

    protected $connection = "sqlite";

    protected $fillable = [
        "user_id",
        "key"
    ];

    public function user()
    {
        return User::where("id", $this->id);
    }
}
