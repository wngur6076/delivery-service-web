<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;

/**
 * @OA\Info(
 *      title="쿠팡이츠 클론코딩",
 *      version="1.0.0",
 *      @OA\Contact(
 *          email="wngur6076@naver.com"
 *      ),
 * )
 */

/**
 *  @OA\Server(
 *      url=L5_SWAGGER_CONST_TEST_HOST,
 *      description="테스트 서버"
 *  )
 *
 */
class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;
}
