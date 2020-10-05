<?php


namespace App\Lib;


class Position extends Point
{
    protected $radius;

    /**
     * Position constructor.
     * @param $latitude
     * @param $longitude
     * @param $radius
     */
    public function __construct($latitude, $longitude, $radius)
    {
        parent::__construct($latitude, $longitude);
        $this->latitude = $latitude;
        $this->longitude = $longitude;
        $this->radius = $radius;
    }

    /**
     * @return mixed
     */
    public function radius()
    {
        return $this->radius;
    }
}
