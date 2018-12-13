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

    	$response = new Response();
		$response->setContent(json_encode($result));
		$response->headers->set('Content-Type', 'application/json');

		return $response;
    }

    /**
     * Matches /weather exactly
     *
     * @Route("/weather", name="default_weather")
     */
    public function weather()
    {
    	$request = Request::createFromGlobals();
    	$location = $request->request->get('id');

    	$city = $this->getDoctrine()->getRepository(City::class);
    	$query = $city->createQueryBuilder('c')
    		->where('c.id = :id')
    		->setParameter('id', $location)
    		->orderBy('c.placeName')
    		->setMaxResults(1)
    		->getQuery();
    	$result = $query->getOneOrNullResult(\Doctrine\ORM\Query::HYDRATE_ARRAY);

    	$weather = ApiController::open_weather($result['placeName']);
    	$weather = json_decode($weather);

    	return $this->render('weather.html.twig', array(
    		'weather' => $weather
    	));
    }

    /**
     * Matches /map exactly
     *
     * @Route("/map", name="default_map")
     */
    public function map()
    {
    	$request = Request::createFromGlobals();
    	$location = $request->query->get('location');
    	$radius = $request->query->get('radius');

    	$city = $this->getDoctrine()->getRepository(City::class);
    	$query = $city->createQueryBuilder('c')
    		->where('c.id != :id')
    		->setParameter('id', $location)
    		->orderBy('c.placeName')
    		->getQuery();
    	$result_all = $query->getResult(\Doctrine\ORM\Query::HYDRATE_ARRAY);

    	$query = $city->createQueryBuilder('c')
    		->where('c.id = :id')
    		->setParameter('id', $location)
    		->orderBy('c.placeName')
    		->setMaxResults(1)
    		->getQuery();
    	$result_current_location = $query->getOneOrNullResult(\Doctrine\ORM\Query::HYDRATE_ARRAY);

    	$marker = [];
    	foreach ($result_all as $key => $val) {
    		$distance = self::haversineGreatCircleDistance($val['latitude'],$val['longitude'],$result_current_location['latitude'], $result_current_location['longitude']);
    		if ($distance <= $radius) {
    			$marker []= $val;
    		}
    	}

    	return $this->render('map.html.twig', array(
    		'marker' => $marker,
   			'current_loc' => $result_current_location
    	));
    }

    private function haversineGreatCircleDistance($latitude_from, $longitude_from, $latitude_to, $longitude_to, $earth_radius = 6371)
	{
	  $lat_from = deg2rad($latitude_from);
	  $long_from = deg2rad($longitude_from);
	  $lat_to = deg2rad($latitude_to);
	  $long_to = deg2rad($longitude_to);

	  $lat_delta = $lat_to - $lat_from;
	  $long_delta = $long_to - $long_from;

	  $angle = 2 * asin(sqrt(pow(sin($lat_delta / 2), 2) +
	    cos($lat_from) * cos($lat_to) * pow(sin($long_delta / 2), 2)));
	  return $angle * $earth_radius;
	}
}