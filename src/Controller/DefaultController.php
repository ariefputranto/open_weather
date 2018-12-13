<?php
namespace App\Controller;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use App\Controller\ApiController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class DefaultController extends Controller
{
	/**
     * Matches / exactly
     *
     * @Route("/", name="default_index")
     */
    public function index()
    {
        return $this->render('index.html.twig');
    }


	/**
     * Matches /city exactly
     *
     * @Route("/city", name="default_searchCity")
     */
    public function searchCity(Request $request)
    {
    	var_dump ($request);
    	die();
        $number = random_int(0, 100);
        $result = ApiController::open_weather('kediri');
        var_dump (json_decode($result));
        die();
    }

    /**
     * Matches /map exactly
     *
     * @Route("/map", name="default_map")
     */
    public function map(Request $request)
    {
    	var_dump ($request);
    	die();
        $number = random_int(0, 100);
        $result = ApiController::open_weather('kediri');
        var_dump (json_decode($result));
        die();
    }
}