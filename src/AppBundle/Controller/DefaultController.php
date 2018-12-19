<?php

namespace AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use AppBundle\Controller\ApiController;
use AppBundle\Entity\City;

class DefaultController extends Controller
{
    /**
     * @Route("/", name="default_index")
     */
    public function indexAction(Request $request)
    {
        return $this->render('default/index.html.twig');
    }

    /**
     * @Route("/city", name="default_searchCity")
     */
    public function searchCity(Request $request)
    {
        $city_name = $request->query->get('search');

        $open_weather = $this->get('open_weather.example');
        $open_weather->setCity($city_name);
        $result = $open_weather->searchCity();
        $result = $result->getContent();

        $response = new Response($result);
        $response->headers->set('Content-Type', 'application/json');

        return $response;
    }

    /**
     * @Route("/weather", name="default_weather")
     */
    public function weather(Request $request)
    {
        $location = $request->request->get('id');

        $open_weather = $this->get('open_weather.example');
        $open_weather->setLocation($location);
        $weather = $open_weather->searchLocation();
        $weather = json_decode($weather->getContent());

        return $this->render('default/weather.html.twig', array(
            'weather' => $weather
        ));
    }

    /**
     * @Route("/map", name="default_map")
     */
    public function map(Request $request)
    {
        $location = $request->query->get('location');
        $radius = $request->query->get('radius');

        $open_weather = $this->get('open_weather.example');
        $open_weather->setLocation($location);
        $open_weather->setRadius($radius);        
        $result = $open_weather->generateMap();
        $result = json_decode($result->getContent());

        return $this->render('default/map.html.twig', array(
            'marker' => $result->marker,
            'current_loc' => $result->result
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
