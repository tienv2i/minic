<?php

/**
 * @method static base_url($path = "") {}
 * @method static static($path = "") {}
 */

namespace Minic\Core\Middlewares;

use Minic\Core\Bootstrap;

abstract class BaseMiddleware {

    abstract public function handle(Bootstrap $app);
}
