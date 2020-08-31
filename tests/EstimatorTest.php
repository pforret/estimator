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
        $references = [
            "A" => 25,
            "B" => 25,
            "C" => 25,
            "D" => 25,
        ];
        $est->set_references($references);
        $partials = $references;
        unset($partials["D"]);
        $evaluation = $est->evaluate_partials($partials, true);
        $this->assertTrue($evaluation["found_count_fraction"] === .75, "count_fraction is correct");
        $this->assertTrue($evaluation["found_sum_fraction"] === .75, "total_fraction is correct");

        $partials["C"] = 28;
        $evaluation = $est->evaluate_partials($partials, true);
        print_r($evaluation);
        $this->assertEquals(3, $evaluation["stat_variance"], "variance");
        $this->assertEquals(1.732, $evaluation["stat_deviation"], "deviation");
    }

    public function test_from_partials()
    {
        $est = new Estimator();
        $references = [
            "A" => 25,
            "B" => 25,
            "C" => 25,
            "D" => 25,
        ];
        $est->set_references($references);
        unset($references["D"]);
        $estimated = $est->estimate_from_partials($references);
        $this->assertEquals(25, $estimated["D"], "Precision 1");
    }

    public function test_from_partials2()
    {
        $est = new Estimator();
        $references = [
            "A" => 25,
            "B" => 25,
            "C" => 25,
            "D" => 25,
        ];
        $est->set_references($references);
        $references = [
            "A" => 250,
            "B" => 250,
            "C" => 250,
        ];
        $estimated = $est->estimate_from_partials($references, 1);
        $this->assertEquals(250, $estimated["D"], "estimate_from_partials - precision 1");

        $estimated = $est->estimate_from_partials($references, 3);
        $this->assertEquals(249, $estimated["D"], "estimate_from_partials - precision 4");
        $references = [
            "A" => 100,
            "B" => 200,
            "C" => 300,
        ];
        $estimated = $est->estimate_from_partials($references, 1);
        $this->assertEquals(200, $estimated["D"], "estimate_from_partials - precision 1");

        $estimated = $est->estimate_from_partials($references, 3);
        $this->assertEquals(201, $estimated["D"], "estimate_from_partials - precision 4");
    }

    public function test_from_total()
    {
        $est = new Estimator();
        $references = [
            "A" => 1,
            "B" => 2,
            "C" => 3,
            "D" => 4,
        ];
        $est->set_references($references);

        $estimated = $est->estimate_from_total(100, 1);
        $this->assertEquals(40, $estimated["D"], "estimate_from_total - precision 1");

        $estimated = $est->estimate_from_total(100, 3);
        $this->assertEquals(39, $estimated["D"], "estimate_from_total - precision 3");
    }

    public function test_salaries(){
        $est = new Estimator();
        $references=[
            "John"  =>  100,
            "Kevin" =>  120,
            "Sarah" =>  100,
            "Vince"  =>  100,
        ];
        $est->set_references($references);
        $partials=[
            "John"  => 120,
            "Kevin" => 150,
            "Vince" =>  175,
            ];
        $estimation=$est->estimate_from_partials($partials);
        print_r($est->evaluate_partials($partials));
        print_r($estimation);
        $this->assertEquals(139, $estimation["Sarah"], "test_salaries");
    }
}
