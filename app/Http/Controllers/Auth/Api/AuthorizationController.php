<?php

namespace App\Http\Controllers\Auth\Api;

use App\Classes\Constants\Messages;
use App\Classes\Constants\StatusCodes;
use App\Classes\Helpers\UserApiHelper;
use App\Http\Controllers\Controller;
use App\Models\Key;
use App\Models\User;
use App\Repositories\KeyModel\KeyInterface;
use App\Traits\JsonResponseTrait;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use OpenApi\Annotations as OA;

class AuthorizationController extends Controller
{
    use JsonResponseTrait;

    /**
     * @var User
     */
    protected User $user;

    /**
     * @var KeyInterface
     */
    protected KeyInterface $key;

    /**
     * LoginController constructor.
     */
    public function __construct(
        User $user,
        KeyInterface $key
    )
    {
        $this->user = $user;
        $this->key = $key;
    }

    /**
     * @OA\Post(
     *     path="/auth/login",
     *     operationId="Login",
     *     description="Login user by email and password",
     *     tags={"Authentification"},
     *     @OA\RequestBody(
     *          description="",
     *          required=true,
     *          @OA\MediaType(
     *              mediaType="application/x-www-form-urlencoded",
     *              @OA\Schema(
     *                  title="Login data",
     *                  @OA\Property(property="email", type="string", example="johndoe@mail.net"),
     *                  @OA\Property(property="password", type="string", example="admin123")
     *              )
     *          )
     *     ),
     *     @OA\Response(
     *          response=200,
     *          description="Successfull login",
     *          @OA\JsonContent(
     *              type="object",
     *              @OA\Property(property="success", type="bool", example="true"),
     *              @OA\Property(property="message", type="string", example=""),
     *              @OA\Property(property="data", type="object",
     *                  ref="#/components/schemas/User"
     *              )
     *          )
     *     ),
     *     @OA\Response(
     *          response=401,
     *          description="Email & Password does not match with our record",
     *          @OA\JsonContent(
     *              type="object",
     *              @OA\Property(property="success", type="bool", example="false"),
     *              @OA\Property(property="message", type="string", example="Validation error"),
     *              @OA\Property(property="data", type="object",
     *              )
     *          )
     *      ),
     *     @OA\Response(
     *          response=422,
     *          description="Validation error",
     *          @OA\JsonContent(
     *              type="object",
     *              @OA\Property(property="success", type="bool", example="false"),
     *              @OA\Property(property="message", type="string", example="Validation error"),
     *              @OA\Property(property="data", type="object",
     *                  @OA\Property(property="name", type="array", nullable=true, @OA\Items()),
     *                  @OA\Property(property="email", type="array", nullable=true, @OA\Items()),
     *                  @OA\Property(property="password", type="array", nullable=true, @OA\Items())
     *              )
     *          )
     *      )
     * )
     *
     * @param Request $request
     * @return Response
     */
    public function login(Request $request) : Response
    {
        $validator = Validator::make($request->only(["email", "password"]), [
            "email" => ["required", "string"],
            "password" => ["required", "string"]
        ]);

        if ($validator->fails()) {
            return $this->error(Messages::VALIDATION_FAILS, StatusCodes::VALIDATION_ERROR, $validator->messages());
        }

        $data = $validator->validated();
        if (!Auth::attempt($data)) {
            return $this->error(Messages::ERROR_LOGIN, StatusCodes::UNAUTHORIZED);
        }

        $user = $this->user::where("email", $data["email"])
            ->first();

        $answer = UserApiHelper::getArrayAnswer($user);

        $key = $this->key::where("user_id", $answer["id"])
            ->first();

        return $this->success("", $answer);
    }

    /**
     * @OA\Post(
     *     path="/auth/register",
     *     description="Register user by name, email and password",
     *     operationId="Registration",
     *     tags={"Authentification"},
     *     @OA\RequestBody(
     *          description="",
     *          required=true,
     *          @OA\MediaType(
     *              mediaType="application/x-www-form-urlencoded",
     *              @OA\Schema(
     *                  title="Registration data",
     *                  @OA\Property(property="name", type="string", example="Jonh Doe"),
     *                  @OA\Property(property="email", type="string", example="johndoe@mail.net"),
     *                  @OA\Property(property="password", type="string", example="admin123")
     *              )
     *          )
     *     ),
     *     @OA\Response(
     *          response=200,
     *          description="Successfull registration",
     *          @OA\JsonContent(
     *              type="object",
     *              @OA\Property(property="success", type="bool", example="true"),
     *              @OA\Property(property="message", type="string", example=""),
     *              @OA\Property(property="data", type="object",
     *                  ref="#/components/schemas/User"
     *              )
     *          )
     *     ),
     *     @OA\Response(
     *          response=422,
     *          description="Validation error",
     *          @OA\JsonContent(
     *              type="object",
     *              @OA\Property(property="success", type="bool", example="false"),
     *              @OA\Property(property="message", type="string", example="Validation error"),
     *              @OA\Property(property="data", type="object",
     *                  @OA\Property(property="name", type="array", nullable=true, @OA\Items()),
     *                  @OA\Property(property="email", type="array", nullable=true, @OA\Items()),
     *                  @OA\Property(property="password", type="array", nullable=true, @OA\Items())
     *              )
     *          )
     *      )
     * )
     *
     * @param Request $request
     * @return Response
     */
    public function register(Request $request) : Response
    {
        $validator = Validator::make($request->only([
            "name",
            "email",
            "password"
        ]), $this->user->rules);

        if ($validator->fails()) {
            return $this->error(Messages::VALIDATION_FAILS, StatusCodes::VALIDATION_ERROR, $validator->messages());
        }

        $data = $validator->validated();
        $data["password"] = Hash::make($data["password"]);

        $user = $this->user::create($data);

        $answer = UserApiHelper::getArrayAnswer($user);

        $key = $this->key::create([
            "user_id" => $user->id,
            "key" => Crypt::encryptString(Str::random(64))
        ]);

        /*$answer["key"] = $key->key;*/

        return $this->success(Messages::SUCCESS_REGISTER, $answer);
    }
}
