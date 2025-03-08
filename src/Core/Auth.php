<?php

/**
 * @method static base_url($path = "") {}
 * @method static static($path = "") {}
 */

namespace Minic\Core;
use Minic\Core\Bootstrap;

class Auth {
    private const TOKEN_NAME = "access_token";
    private const TOKEN_EXPIRY = 3600; 
    private array $options;
    public function __construct (Bootstrap $app, array $options = []) {
        $this->options = array_merge(
            [
                "auth_code" => $app->getConfig('app.auth_code')
            ],
            $options
        );
        
    }
    public function authorize ($key) {
        if ($key === $this->options["auth_code"]) {
            return true;
        }   
        else return false;
    }
    

    public function login() {
        $token = bin2hex(random_bytes(32)); 
        setcookie(self::TOKEN_NAME, $token, time() + self::TOKEN_EXPIRY, "/", "", true, true);
        return ["message" => "Login successful", "token" => $token];
    }


    public function getToken() {
        $headers = getallheaders();
        if (isset($headers['Authorization'])) {
            if (preg_match('/Bearer\s(\S+)/', $headers['Authorization'], $matches)) {
                return $matches[1];
            }
        }

        if (isset($_COOKIE[self::TOKEN_NAME])) {
            return $_COOKIE[self::TOKEN_NAME];
        }

        return null;
    }


    public function isAuthenticated() {
        $token = self::getToken();
        return !empty($token); 
    }


    public function logout() {
        setcookie(self::TOKEN_NAME, "", time() - 3600, "/");
        return ["message" => "Logged out successfully"];
    }
}

?>
