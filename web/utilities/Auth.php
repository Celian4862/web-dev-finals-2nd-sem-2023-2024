<?php

namespace Utilities;

use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use Utilities\Helper;

class Auth
{
    private static $KEY = "secrete_dapat_ni";

    public static function generate(string $email, string $password): string
    {
        $payload = [
            "iss" => "localhost",
            "aud" => "localhost",
            "email" => $email,
            "password" => $password,
        ];

        return JWT::encode($payload, self::$KEY, "HS256");
    }

    public static function authenticate(string $token): bool
    {
        try {
            $decode = JWT::decode($token, new Key(self::$KEY, "HS256"));
        } catch (\Exception) {
            return false;
        }

        $employee = Helper::getDatabase()->query(<<<SQL
        SELECT
            id,
            password,
            (
                string::join(
                    " ",
                    details.fullName.first,
                    details.fullName.last
                )
            ) as name
        FROM ONLY employee
        WHERE email = "{$decode->email}"
        LIMIT 1;
        SQL);

        if (!$employee || !password_verify($decode->password, $employee["password"])) {
            return false;
        }

        // ! Temporary
        $_SESSION["employee"] = $employee["id"];

        return true;
    }
}
