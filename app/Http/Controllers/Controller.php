<?php

namespace App\Http\Controllers;

use Laravel\Lumen\Routing\Controller as BaseController;

/**
 * Class Controller
 * @package App\Http\Controllers
 */
class Controller extends BaseController
{
    const DEFAULT_PAGINATION_SIZE = 15;

    /**
     * @OA\Info(
     *   title="BRAMON API",
     *   version="1.0.0",
     *   @OA\Contact(
     *     email="support@bramonmeteor.org",
     *     name="Support Team"
     *   )
     * )
     */
}
