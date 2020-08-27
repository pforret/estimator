<?php

namespace Pforret\Estimator\Tests;

use Pforret\Estimator\Estimator;
use PHPUnit\Framework\TestCase;

class EstimatorTest extends TestCase
{
    /** @test */
    public function test_initiate()
    {
        $est = new Estimator();
        $this->assertTrue(isset($est));
    }

    public function test_evaluation()
    {
        $est = new Estimator();
        $averages = [
            "A" => 25,
            "B" => 25,
            "C" => 25,
            "D" => 25,
        ];
        $est->set_averages($averages);
        $partials=$averages;
        unset($partials["D"]);
        $evaluation=$est->evaluate_partials($partials,true);
        $this->assertTrue($evaluation["count_fraction"]=== .75,"count_fraction is correct");
        $this->assertTrue($evaluation["total_fraction"]=== .75,"total_fraction is correct");

        $partials["C"]=28;
        $evaluation=$est->evaluate_partials($partials,true);
        $this->assertEquals(3,$evaluation["deviation"],"standard deviation");
    }

    public function test_from_partials()
    {
        $est = new Estimator();
        $averages = [
            "A" => 25,
            "B" => 25,
            "C" => 25,
            "D" => 25,
        ];
        $est->set_averages($averages);
        unset($averages["D"]);
        $estimated = $est->estimate_from_partials($averages);
        $this->assertEquals(25, $estimated["D"], "Precision 1");
    }

    public function test_from_partials2()
    {
        $est = new Estimator();
        $averages = [
            "A" => 25,
            "B" => 25,
            "C" => 25,
            "D" => 25,
        ];
        $est->set_averages($averages);
        $averages = [
            "A" => 250,
            "B" => 250,
            "C" => 250,
        ];
        $estimated = $est->estimate_from_partials($averages, 1);
        $this->assertEquals(250, $estimated["D"], "estimate_from_partials - precision 1");

        $estimated = $est->estimate_from_partials($averages, 4);
        $this->assertEquals(252, $estimated["D"], "estimate_from_partials - precision 4");
    }

    public function test_from_total()
    {
        $est = new Estimator();
        $averages = [
            "A" => 1,
            "B" => 2,
            "C" => 3,
            "D" => 4,
        ];
        $est->set_averages($averages);
        $estimated = $est->estimate_from_total(100, 1);
        $this->assertEquals(40, $estimated["D"], "estimate_from_total - precision 1");

        $estimated = $est->estimate_from_total(100, 3);
        $this->assertEquals(39, $estimated["D"], "estimate_from_total - precision 3");
    }
}
