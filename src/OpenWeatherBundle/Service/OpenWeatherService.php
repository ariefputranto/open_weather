<?php

namespace OpenWeatherBundle\Service;

use Doctrine\ORM\EntityManagerInterface;
use OpenWeatherBundle\Service\ApiService;
use OpenWeatherBundle\Entity\City;
use Doctrine\ORM\Query;

class OpenWeatherService
{
    private $city_name, $location, $radius, $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
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
        $city = $this->entityManager->getRepository(City::class);
        $query = $city->createQueryBuilder('c')
            ->where('lower(c.placeName) like :city_name')
            ->setParameter('city_name','%'.strtolower($this->city_name).'%')
            ->orderBy('c.placeName')
            ->setMaxResults(10)
            ->getQuery();
        $result = $query->getResult(Query::HYDRATE_ARRAY);

        return json_encode(['data' => $result]);
    }

    public function searchLocation()
    {
    	$city = $this->entityManager->getRepository(City::class);
        $query = $city->createQueryBuilder('c')
            ->where('c.id = :id')
            ->setParameter('id', $this->location)
            ->orderBy('c.placeName')
            ->setMaxResults(1)
            ->getQuery();
        $result = $query->getOneOrNullResult(Query::HYDRATE_ARRAY);

        $weather = ApiService::open_weather($result['placeName']);

        return $weather;
    }

    public function generateMap()
    {
    	$city = $this->entityManager->getRepository(City::class);
        $query = $city->createQueryBuilder('c')
            ->where('c.id != :id')
            ->setParameter('id', $this->location)
            ->orderBy('c.placeName')
            ->getQuery();
        $result_all = $query->getResult(Query::HYDRATE_ARRAY);

        $query = $city->createQueryBuilder('c')
            ->where('c.id = :id')
            ->setParameter('id', $this->location)
            ->orderBy('c.placeName')
            ->setMaxResults(1)
            ->getQuery();
        $result_current_location = $query->getOneOrNullResult(Query::HYDRATE_ARRAY);

        $marker = [];
        foreach ($result_all as $key => $val) {
            $distance = self::haversineGreatCircleDistance($val['latitude'],$val['longitude'],$result_current_location['latitude'], $result_current_location['longitude']);
            if ($distance <= $this->radius) {
                $marker []= $val;
            }
        }

        $result = ['result' => $result_current_location, 'marker' => $marker];

        return json_encode($result);
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
