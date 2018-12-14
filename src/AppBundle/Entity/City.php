<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * City
 *
 * @ORM\Table(name="city")
 * @ORM\Entity
 */
class City
{
    /**
     * @var int
     *
     * @ORM\Column(name="id", type="bigint", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="SEQUENCE")
     * @ORM\SequenceGenerator(sequenceName="city_id_seq", allocationSize=1, initialValue=1)
     */
    private $id;

    /**
     * @var string|null
     *
     * @ORM\Column(name="postcode", type="string", length=200, nullable=true)
     */
    private $postcode;

    /**
     * @var string|null
     *
     * @ORM\Column(name="place_name", type="string", length=200, nullable=true)
     */
    private $placeName;

    /**
     * @var string|null
     *
     * @ORM\Column(name="state_name", type="string", length=200, nullable=true)
     */
    private $stateName;

    /**
     * @var string|null
     *
     * @ORM\Column(name="state_code", type="string", length=10, nullable=true)
     */
    private $stateCode;

    /**
     * @var float|null
     *
     * @ORM\Column(name="latitude", type="float", precision=10, scale=0, nullable=true)
     */
    private $latitude;

    /**
     * @var float|null
     *
     * @ORM\Column(name="longitude", type="float", precision=10, scale=0, nullable=true)
     */
    private $longitude;


}
