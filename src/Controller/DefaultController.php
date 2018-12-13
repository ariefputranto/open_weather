<?php
namespace App\Controller;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use App\Controller\ApiController;
use App\Entity\City;

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
    public function searchCity()
    {
    	$request = Request::createFromGlobals();
    	$city_name = $request->query->get('search');

    	$city = $this->getDoctrine()->getRepository(City::class);
    	$query = $city->createQueryBuilder('c')
    		->where('lower(c.placeName) like :city_name')
    		->setParameter('city_name','%'.strtolower($city_name).'%')
    		->orderBy('c.placeName')
    		->setMaxResults(10)
    		->getQuery();
    	$result = $query->getResult(\Doctrine\ORM\Query::HYDRATE_ARRAY);

    	// $result = [];
    	// foreach ($tmp as $key => $val) {
    	// 	$result[$key] = json_encode([
    	// 		'id' => $val['postcode'],
    	// 		'text' => $val['placeName']
    	// 	]);
    	// }

    	$response = new Response();
		$response->setContent(json_encode($result));
		$response->headers->set('Content-Type', 'application/json');

		return $response;
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