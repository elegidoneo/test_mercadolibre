<?php


namespace App\Lib;


use Illuminate\Http\JsonResponse;

class Triangulation
{

    const EARTH_RADIUS = 6378137;

    /**
     * @var Position[]
     */
    private $position = [];

    /**
     * Triangulation constructor.
     * @param Position ...$position
     */
    public function __construct(Position ...$position)
    {
        $this->position = $position;
    }

    /**
     * @return Point|false
     * @throws \Exception
     */
    public function position()
    {
        $point = $this->intersection();
        abort_unless($point,JsonResponse::HTTP_NOT_FOUND, "Position do not intersect.");
        return $point;
    }

    /**
     * @return Point|false
     * @throws \Exception
     */
    private function intersection()
    {
        /* http://en.wikipedia.org/wiki/Trilateration */
        $P1 = $this->position[0]->toEarthCenteredVector();
        $P2 = $this->position[1]->toEarthCenteredVector();
        $P3 = $this->position[2]->toEarthCenteredVector();

        //$ex es el vector unitario en la dirección de P1 a P2.
        $ex = $P2->subtract($P1)->normalize();
        // $i es la magnitud con signo de la componente x, en la figura 1
        $i = $ex->dotProduct($P3->subtract($P1));
        /*
         * $ey es el vector unitario en la dirección y. Tenga en cuenta que los puntos P1, P2
         * y P3 están todos en el plano z = 0 del sistema de coordenadas de la figura 1.
         */
        $temp = $ex->multiplyByScalar($i);
        $ey = $P3->subtract($P1)->subtract($temp)->normalize();
        // $ez es el tercer vector base.
        $ez = $ex->crossProduct($ey);
        //$d es la distancia entre los centros P1 y P2.
        $d = $P2->subtract($P1)->length();
        //$j es la magnitud con signo de la componente y
        // en el sistema de coordenadas de la figura 1, del vector de P1 a P3.
        $j = $ey->dotProduct($P3->subtract($P1));

        $x = (
            pow($this->position[0]->radius(), 2) -
            pow($this->position[1]->radius(), 2) +
            pow($d, 2)
        ) / (2 * $d);

        $y = ((
            pow($this->position[0]->radius(), 2) -
            pow($this->position[2]->radius(), 2) +
            pow($i, 2) + pow($j, 2)
        ) / (2 * $j)) - (($i / $j) * $x);
        /* Si $z = NaN si el círculo no toca la esfera. Sin solución. */
        /* Si $z = 0, el círculo toca la esfera exactamente en un punto. */
        /* Si $z <0> z el círculo toca la esfera en dos puntos. */
        $z = sqrt(pow($this->position[0]->radius(), 2) - pow($x, 2) - pow($y, 2));
        /* El uso de valor absoluto hace que la fórmula sea válida incluso cuando los círculos no lo hacen */
        /* superposición. */

        if (is_nan($z)) {
            return false;
        }
        // triPt es un vector con ECEF x, y, z del punto de trilateración
        $triPt = $P1
            ->add($ex->multiplyByScalar($x))
            ->add($ey->multiplyByScalar($y))
            ->add($ez->multiplyByScalar($z));

        $triPtX = $triPt->components()[0];
        $triPtY = $triPt->components()[1];
        $triPtZ = $triPt->components()[2];
        // Convierta de nuevo a lat / long desde ECEF. Convierta a grados.
        $latitude = rad2deg(asin($triPtZ / self::EARTH_RADIUS));
        $longitude = rad2deg(atan2($triPtY, $triPtX));

        return new Point($latitude, $longitude);
    }
}
