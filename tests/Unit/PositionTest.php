<?php

namespace Tests\Unit;

use App\Lib\Position;
use PHPUnit\Framework\TestCase;

class PositionTest extends TestCase
{
    /**
     * @test
     * @testdox
     */
    public function caseOne()
    {
        $position = new Position(1, 1, 1);
        $this->assertEquals(1, $position->radius());
    }
}
