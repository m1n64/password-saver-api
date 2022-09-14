<?php

namespace App\Http\Controllers\Access\Api;

use App\Http\Controllers\Controller;
use App\Models\AccessCode;
use App\Traits\JsonResponseTrait;
use Illuminate\Http\Request;

class AccessController extends Controller
{
    use JsonResponseTrait;

    /**
     * @var AccessCode
     */
    protected AccessCode $accessCode;

    public function __construct(
        AccessCode $accessCode
    )
    {
        $this->accessCode = $accessCode;
    }

    public function check(Request $request, $code)
    {
        $isValid = $this->accessCode::where("code", $code)
            ->first();

        if (is_null($isValid)) return $this->error("Code is not valid");

        return $this->success("Code is valid!");
    }
}
