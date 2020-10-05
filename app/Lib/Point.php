<?php

namespace App\Lib;

use Nubs\Vectorix\Vector;

/**
 * Class Point
 * @package App\Lib
 */
class Point
{
    const EARTH_RADIUS = 6378137;

    protected $latitude;

    protected $longitude;

    /**
     * Point constructor.
     * @param $latitude
     * @param $longitude
     */
    public function __construct($latitude, $longitude)
    {
        $this->latitude = $latitude;
        $this->longitude = $longitude;
    }

    /**
     * @return mixed
     */
    public function latitude()
    {
        return $this->latitude;
    }

    /**
     * @return mixed
     */
    public function longitude()
    {
        return $this->longitude;
    }

    /**
     * @return Vector
     */
    public function toEarthCenteredVector()
    {
        $vx = self::EARTH_RADIUS * (cos(deg2rad($this->latitude()))
                * cos(deg2rad($this->longitude())));
        $vy = self::EARTH_RADIUS * (cos(deg2rad($this->latitude()))
                * sin(deg2rad($this->longitude())));
        $vz = self::EARTH_RADIUS * (sin(deg2rad($this->latitude())));

        return new Vector([$vx, $vy, $vz]);
    }
}
