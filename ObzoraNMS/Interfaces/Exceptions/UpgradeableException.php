<?php
namespace ObzoraNMS\Interfaces\Exceptions;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Throwable;

interface UpgradeableException
{
    /**
     * Try to convert the given Exception to this exception
     * It should return null if the Exception cannot be upgraded.
     *
     * @param  Throwable  $exception
     * @return static|null
     */
    public static function upgrade(Throwable $exception): ?static;

    public function render(Request $request): Response|JsonResponse;
}
