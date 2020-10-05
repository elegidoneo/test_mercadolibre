<?php

namespace Tests\Unit;

use App\Lib\Point;
use PHPUnit\Framework\TestCase;

class PointTest extends TestCase
{
    /**
     * @test
     * @testdox
     */
    public function caseOne()
    {
        $point = new Point(1, 1);
        $this->assertEquals(1, $point->latitude());
    }

    /**
     * @test
     * @testdox
     */
    public function caseTwo()
    {
        $point = new Point(1, 1);
        $this->assertEquals(1, $point->longitude());
    }

    /**
     * @test
     * @testdox
     */
    public function caseThree()
    {
        $point = new Point(1, 1);
        $this->assertEquals([
            6376194.305635547,
            111296.88559979972,
            111313.83923667614,
        ], $point->toEarthCenteredVector()->components());
    }
}
