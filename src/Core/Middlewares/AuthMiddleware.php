<?php

/**
 * @method static base_url($path = "") {}
 * @method static static($path = "") {}
 */

namespace Minic\Core\Middlewares;

use Minic\Core\Auth;
use Minic\Core\Bootstrap;

class AuthMiddleware extends BaseMiddleware {
    protected Auth $auth;
    public function handle (Bootstrap $app) {
        $this->auth = new Auth();
        if ($this->auth->isAuthenticated()) {

        } else {
            
        }
    }
}
