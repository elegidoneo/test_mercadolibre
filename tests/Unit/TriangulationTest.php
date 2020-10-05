<?php

namespace Tests\Unit;

use App\Lib\Position;
use App\Lib\Triangulation;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Tests\TestCase;

class TriangulationTest extends TestCase
{
    /**
     * @test
     * @testdox Checks when wrong parameters of an exception are passed
     */
    public function caseOne()
    {
        $this->expectException(NotFoundHttpException::class);
        $this->expectExceptionMessage("Position do not intersect.");
        $config = Config("satelliteInformation");
        $kenobi = new Position($config['kenobi'][0], $config['kenobi'][1], 1);
        $skywalker = new Position($config['skywalker'][0], $config['skywalker'][1], 1);
        $sato = new Position($config['sato'][0], $config['sato'][1], 1);

        $trilateration = new Triangulation($kenobi, $skywalker, $sato);
        $trilateration->position();
    }

    /**
     * @test
     * @testdox Check that latitude and longitude return if they are within satellite range
     * @throws \Exception
     */
    public function caseTwo()
    {
        $config = Config("satelliteInformation");
        $kenobi = new Position($config['kenobi'][0], $config['kenobi'][1], 5806600);
        $skywalker = new Position($config['skywalker'][0], $config['skywalker'][1], 5806615.5);
        $sato = new Position($config['sato'][0], $config['sato'][1], 5806642.7);

        $trilateration = new Triangulation($kenobi, $skywalker, $sato);
        $response = $trilateration->position();
        $this->assertEquals(9.612371971752903, $response->latitude());
        $this->assertEquals(-2.8849892115312006, $response->longitude());
    }
}
