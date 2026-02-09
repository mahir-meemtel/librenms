<?php
namespace App\Console\Commands;

use Illuminate\Foundation\Testing\Concerns\InteractsWithAuthentication;
use Illuminate\Foundation\Testing\Concerns\MakesHttpRequests;

class InternalHttpRequest
{
    use MakesHttpRequests;
    use InteractsWithAuthentication;

    /**
     * @var \Illuminate\Contracts\Foundation\Application|mixed
     */
    private $app;

    public function __construct()
    {
        $this->app = app();
    }
}
