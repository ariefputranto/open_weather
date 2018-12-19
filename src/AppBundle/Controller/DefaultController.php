<?php

namespace AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

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
        $weather = json_decode($weather);

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
        $result = json_decode($result);

        return $this->render('default/map.html.twig', array(
            'marker' => $result->marker,
            'current_loc' => $result->result
        ));
    }
}
