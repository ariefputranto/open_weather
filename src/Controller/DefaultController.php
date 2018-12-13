<?php
namespace App\Controller;

use Symfony\Component\HttpFoundation\Response;
use App\Controller\ApiController;

class DefaultController
{
    public function index()
    {
        $number = random_int(0, 100);
        $result = ApiController::open_weather('kediri');
        var_dump (json_decode($result));
        die();

        return new Response(
            '<html><body>Lucky number: '.$number.'</body></html>'
        );
    }
}