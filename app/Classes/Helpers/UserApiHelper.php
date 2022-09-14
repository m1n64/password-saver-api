<?php


namespace App\Classes\Helpers;


use App\Classes\Constants\Messages;
use App\Classes\Constants\TokenAbilities;
use App\Models\User;

class UserApiHelper
{

    /**
     * @param $user
     * @param bool $showToken
     * @return array
     */
    public static function getArrayAnswer($user, bool $showToken = true) : array
    {
        $data = [
            "id" => $user->id,
            "name" => $user->name,
            "email" => $user->email
        ];

        if ($showToken) {
            $abilities = [TokenAbilities::BASIC_ACCESS];

            if ($user->is_admin) {
                $abilities[] = TokenAbilities::ADMIN_ACCESS;
            }

            $authToken = $user->createToken(Messages::TOKEN_NAME, $abilities)->plainTextToken;

            $data["token"] = $authToken;
        }

        return $data;
    }

    /**
     * @param User $user
     * @return \Illuminate\Database\Eloquent\Builder|\Illuminate\Database\Eloquent\Model|object|null
     */
    public static function getRefreshedUser(User $user)
    {
        return $user->refresh()
            ->first();
    }
}
