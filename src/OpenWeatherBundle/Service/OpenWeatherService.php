<?php

namespace OpenWeatherBundle\Service;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use OpenWeatherBundle\Service\ApiService;
use OpenWeatherBundle\Entity\City;

class OpenWeatherService extends Controller
{

    // public function indexAction()
    // {
    //     return $this->render('OpenWeatherBundle:Default:index.html.twig');
    // }
    private $city_name, $location, $radius;

    public function __construct()
    {
        $city_name = '';
        $location = '';
        $radius = 0;
    }

    public function setCity($city_name)
    {
        $this->city_name = $city_name;
    }

    public function setLocation($location)
    {
        $this->location = $location;
    }

    public function setRadius($radius)
    {
        $this->radius = $radius;
    }

    public function getCity()
    {
        return $this->city_name;
    }

    public function getLocation()
    {
        return $this->location;
    }

    public function getRadius()
    {
        return $this->radius;
    }

    public function searchCity()
    {
        $city = $this->getDoctrine()->getRepository(City::class);
        $query = $city->createQueryBuilder('c')
            ->where('lower(c.placeName) like :city_name')
            ->setParameter('city_name','%'.strtolower($this->city_name).'%')
            ->orderBy('c.placeName')
            ->setMaxResults(10)
            ->getQuery();
        $result = $query->getResult(\Doctrine\ORM\Query::HYDRATE_ARRAY);

        return new JsonResponse(['data' => $result]);
    }

    public function searchLocation()
    {
    	$city = $this->getDoctrine()->getRepository(City::class);
        $query = $city->createQueryBuilder('c')
            ->where('c.id = :id')
            ->setParameter('id', $this->location)
            ->orderBy('c.placeName')
            ->setMaxResults(1)
            ->getQuery();
        $result = $query->getOneOrNullResult(\Doctrine\ORM\Query::HYDRATE_ARRAY);

        $weather = ApiService::open_weather($result['placeName']);

        $response = new JsonResponse();

        return JsonResponse::fromJsonString($weather);
    }

    public function generateMap()
    {
    	$city = $this->getDoctrine()->getRepository(City::class);
        $query = $city->createQueryBuilder('c')
            ->where('c.id != :id')
            ->setParameter('id', $this->location)
            ->orderBy('c.placeName')
            ->getQuery();
        $result_all = $query->getResult(\Doctrine\ORM\Query::HYDRATE_ARRAY);

        $query = $city->createQueryBuilder('c')
            ->where('c.id = :id')
            ->setParameter('id', $this->location)
            ->orderBy('c.placeName')
            ->setMaxResults(1)
            ->getQuery();
        $result_current_location = $query->getOneOrNullResult(\Doctrine\ORM\Query::HYDRATE_ARRAY);

        $marker = [];
        foreach ($result_all as $key => $val) {
            $distance = self::haversineGreatCircleDistance($val['latitude'],$val['longitude'],$result_current_location['latitude'], $result_current_location['longitude']);
            if ($distance <= $this->radius) {
                $marker []= $val;
            }
        }

        $result = ['result' => $result_current_location, 'marker' => $marker];

        return new JsonResponse($result);
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
