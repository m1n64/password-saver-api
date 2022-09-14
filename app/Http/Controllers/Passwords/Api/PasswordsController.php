<?php

namespace App\Http\Controllers\Passwords\Api;

use App\Classes\Constants\Messages;
use App\Classes\Constants\StatusCodes;
use App\Classes\Helpers\EncryptionHelper;
use App\Http\Controllers\Controller;
use App\Models\Key;
use App\Models\Passwords\Password;
use App\Repositories\KeyModel\KeyInterface;
use App\Traits\JsonResponseTrait;
use Defuse\Crypto\Crypto;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Validator;
use Mockery\Generator\StringManipulation\Pass\Pass;
use OpenApi\Annotations as OA;

class PasswordsController extends Controller
{
    use JsonResponseTrait;

    /**
     * @var Password
     */
    protected Password $password;

    /**
     * @var Key
     */
    protected KeyInterface $key;

    /**
     * PasswordsController constructor.
     * @param Password $password
     * @param Key $key
     */
    public function __construct(
        Password $password,
        KeyInterface $key
    )
    {
        $this->password = $password;
        $this->key = $key;
    }

    /**
     * @OA\Get(
     *     path="/passwords/get",
     *     description="Get passwords by user",
     *     tags={"Passwords"},
     *     operationId="Get password",
     *     security={{"apiAuth": {}}},
     *     @OA\Response(
     *          response=200,
     *          description="Success Answer",
     *          @OA\JsonContent(
     *              type="object",
     *              @OA\Property(property="success", type="bool", example="true"),
     *              @OA\Property(property="message", type="string", example=""),
     *              @OA\Property(property="data", type="array", @OA\Items(
     *                  ref="#/components/schemas/Password"
     *              ))
     *          )
     *     ),
     *     @OA\Response(
     *          response=401,
     *          description="Unathorized",
     *          @OA\JsonContent(
     *              type="object",
     *              @OA\Property(property="message", type="string", example="Unathorized."),
     *          )
     *     )
     * )
     *
     * @param Request $request
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\Routing\ResponseFactory|\Illuminate\Http\Response
     */
    public function getByUser(Request $request)
    {
        $user = $request->user();

        $passwords = $this->password::with("category")
            ->where("user_id", $user->id)
            ->get();

        return $this->success("", $passwords);
    }

    /**
     * @OA\Post(
     *     path="/passwords/set",
     *     description="Set password by user",
     *     tags={"Passwords"},
     *     operationId="Add password",
     *     security={{"apiAuth": {}}},
     *     @OA\RequestBody(
     *          description="",
     *          required=true,
     *          @OA\MediaType(
     *              mediaType="application/x-www-form-urlencoded",
     *              @OA\Schema(
     *                  title="Password data",
     *                  @OA\Property(property="name", type="string", example="githib"),
     *                  @OA\Property(property="login", type="string", example="johndoe1"),
     *                  @OA\Property(property="password", type="string", example="admin12311"),
     *                  @OA\Property(property="category_id", type="integer", example="1"),
     *              )
     *          )
     *     ),
     *     @OA\Response(
     *          response=200,
     *          description="Success Answer",
     *          @OA\JsonContent(
     *              type="object",
     *              @OA\Property(property="success", type="bool", example="true"),
     *              @OA\Property(property="message", type="string", example=""),
     *              @OA\Property(property="data", type="object",
     *                  ref="#/components/schemas/Password"
     *              )
     *          )
     *     ),
     *     @OA\Response(
     *          response=401,
     *          description="Unathorized",
     *          @OA\JsonContent(
     *              type="object",
     *              @OA\Property(property="message", type="string", example="Unathorized."),
     *          )
     *     )
     * )
     *
     * @param Request $request
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\Routing\ResponseFactory|\Illuminate\Http\Response
     * @throws \Defuse\Crypto\Exception\EnvironmentIsBrokenException
     */
    public function setForUser(Request $request)
    {
        $user = $request->user();

        $validator = Validator::make($request->only(["password", "name", "login", "category_id"]), [
           "password" => ["required", "string"],
           "name" => ["required", "string"],
           "login" => ["required", "string"],
           "category_id" => ["required", "integer"],
        ]);

        if ($validator->fails()) return $this->error(Messages::VALIDATION_FAILS, StatusCodes::VALIDATION_ERROR, $validator->messages());

        $cryptoKey = $this->getCryptoKey($user->id);

        if (is_null($cryptoKey)) return $this->error(Messages::ALL_THE_SERVERS_ERRORS, StatusCodes::SERVER_ERROR);

        $encryptKey = $this->decryptKey($cryptoKey->key);

        $data = $validator->validated();
        $encrypt = EncryptionHelper::encrypt($data["password"], $encryptKey);

        $data["user_id"] = $user->id;
        $data["password"] = $encrypt;

        $newPassword = $this->password::create($data)
            ->load("category");

        return $this->success("", $newPassword);
    }

    /**
     * @OA\Post(
     *     path="/passwords/update/{passwordId}",
     *     description="Update password by id for user",
     *     tags={"Passwords"},
     *     operationId="Update password",
     *     @OA\Parameter(
     *         parameter="passwordId",
     *         name="passwordId",
     *         description="Password ID",
     *         @OA\Schema(
     *             type="integer"
     *         ),
     *         in="path",
     *         required=true
     *     ),
     *     security={{"apiAuth": {}}},
     *     @OA\RequestBody(
     *          description="",
     *          required=false,
     *          @OA\MediaType(
     *              mediaType="application/x-www-form-urlencoded",
     *              @OA\Schema(
     *                  title="Password data",
     *                  @OA\Property(property="name", type="string", example="githib"),
     *                  @OA\Property(property="login", type="string", example="johndoe1"),
     *                  @OA\Property(property="password", type="string", example="admin12311"),
     *                  @OA\Property(property="category_id", type="integer", example="1"),
     *              )
     *          )
     *     ),
     *     @OA\Response(
     *          response=200,
     *          description="Success Answer",
     *          @OA\JsonContent(
     *              type="object",
     *              @OA\Property(property="success", type="bool", example="true"),
     *              @OA\Property(property="message", type="string", example=""),
     *              @OA\Property(property="data", type="object",
     *                  ref="#/components/schemas/Password"
     *              )
     *          )
     *     ),
     *     @OA\Response(
     *          response=401,
     *          description="Unathorized",
     *          @OA\JsonContent(
     *              type="object",
     *              @OA\Property(property="message", type="string", example="Unathorized."),
     *          )
     *     )
     * )
     *
     * @param Request $request
     * @param $passwordId
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\Routing\ResponseFactory|\Illuminate\Http\Response
     * @throws \Defuse\Crypto\Exception\EnvironmentIsBrokenException
     */
    public function updateForUser(Request $request, $passwordId)
    {
        $user = $request->user();

        $cryptoKey = $this->getCryptoKey($user->id);

        if (is_null($cryptoKey)) return $this->error(Messages::ALL_THE_SERVERS_ERRORS, StatusCodes::SERVER_ERROR);

        $encryptKey = $this->decryptKey($cryptoKey->key);

        $password = $this->password::where("user_id", $user->id)
            ->where("id", $passwordId);

        if (is_null($password)) return $this->error("Password ".Messages::NOT_FOUND);

        $data = $request->only(["name", "login", "category_id", "password"]);

        if ($data["password"]) {
            $data["password"] = EncryptionHelper::encrypt($data["password"], $encryptKey);
        }

        $password->update($data);
        $updated = $password->with("category")
            ->first();

        return $this->success("", $updated);
    }

    /**
     * @OA\Delete(
     *     path="/passwords/delete/{passwordId}",
     *     description="Remove password by id from user",
     *     tags={"Passwords"},
     *     operationId="Remove password",
     *     security={{"apiAuth": {}}},
     *     @OA\Parameter(
     *         parameter="passwordId",
     *         name="passwordId",
     *         description="Password ID",
     *         @OA\Schema(
     *             type="integer"
     *         ),
     *         in="path",
     *         required=true
     *     ),
     *     @OA\Response(
     *          response=200,
     *          description="Success Answer",
     *          @OA\JsonContent(
     *              type="object",
     *              @OA\Property(property="success", type="bool", example="true"),
     *              @OA\Property(property="message", type="string", example=""),
     *              @OA\Property(property="data", type="object",
     *                  @OA\Property(property="id", type="integer", example="1"),
     *                  @OA\Property(property="deleted", type="bool", example="true"),
     *              )
     *          )
     *     ),
     *     @OA\Response(
     *          response=401,
     *          description="Unathorized",
     *          @OA\JsonContent(
     *              type="object",
     *              @OA\Property(property="message", type="string", example="Unathorized."),
     *          )
     *     )
     * )
     *
     * @param Request $request
     * @param $passwordId
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\Routing\ResponseFactory|\Illuminate\Http\Response
     */
    public function deleteByUser(Request $request, $passwordId)
    {
        $user = $request->user();

        $isSuccess = $this->password::where("user_id", $user->id)
            ->where("id", $passwordId)
            ->delete();

        $answer = [
            "id" => $passwordId,
            "deleted" => (bool) $isSuccess
        ];

        if ($isSuccess) return $this->success("", $answer);

        return $this->error("", StatusCodes::SUCCESS, $answer);
    }

    /**
     * @OA\Post(
     *     path="/passwords/decrypt/{passwordId}",
     *     description="Decrypt password by ID for user",
     *     tags={"Passwords"},
     *     operationId="Decrypt password",
     *     security={{"apiAuth": {}}},
     *     @OA\Parameter(
     *         parameter="passwordId",
     *         name="passwordId",
     *         description="Password ID",
     *         @OA\Schema(
     *             type="integer"
     *         ),
     *         in="path",
     *         required=true
     *     ),
     *     @OA\Response(
     *          response=200,
     *          description="Success Answer",
     *          @OA\JsonContent(
     *              type="object",
     *              @OA\Property(property="success", type="bool", example="true"),
     *              @OA\Property(property="message", type="string", example=""),
     *              @OA\Property(property="data", type="object",
     *                  @OA\Property(property="id", type="integer", example="1"),
     *                  @OA\Property(property="password", type="string", example="admin1111111"),
     *              )
     *          )
     *     ),
     *     @OA\Response(
     *          response=401,
     *          description="Unathorized",
     *          @OA\JsonContent(
     *              type="object",
     *              @OA\Property(property="message", type="string", example="Unathorized."),
     *          )
     *     )
     * )
     *
     * @param Request $request
     * @param $passwordId
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\Routing\ResponseFactory|\Illuminate\Http\Response
     * @throws \Defuse\Crypto\Exception\EnvironmentIsBrokenException
     * @throws \Defuse\Crypto\Exception\WrongKeyOrModifiedCiphertextException
     */
    public function decryptByUser(Request $request, $passwordId)
    {
        $user = $request->user();

        $cryptoKey = $this->getCryptoKey($user->id);

        if (is_null($cryptoKey)) return $this->error(Messages::ALL_THE_SERVERS_ERRORS, StatusCodes::SERVER_ERROR);

        $encryptKey = $this->decryptKey($cryptoKey->key);

        $password = $this->password::find($passwordId);

        if (is_null($password)) return $this->error("Password ". Messages::NOT_FOUND);

        $answer["id"] = $password->id;
        $answer["password"] = EncryptionHelper::decrypt(
            $password->password,
            $encryptKey
        );

        return $this->success("", $answer);
    }

    /**
     * @param $userId
     * @return mixed
     */
    protected function getCryptoKey($userId)
    {
        return $this->key::where("user_id", $userId)
            ->first();
    }

    /**
     * @param string $key
     * @return string
     */
    protected function decryptKey(string $key) : string
    {
        return Crypt::decryptString(
            $key
        );
    }
}
