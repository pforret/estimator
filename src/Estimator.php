<?php
// Author: Peter Forret (pforret, peter@forret.com)
namespace Pforret\Estimator;

class Estimator
{
    private $averages=Array();
    private $labels=Array();
    private $count_averages=0;
    private $total_averages=0;

    public function set_averages(array $averages)
    {
        $this->averages=$averages;
        $this->labels=array_keys($this->averages);
        $this->count_averages=count($this->labels);
        $this->total_averages=array_sum(array_values($this->averages));
    }

    public function evaluate_partials(array $partials)
    {
        $total_found=0;
        $total_partials=0;
        $count_found=0;
        foreach($partials as $label => $value){
            if(in_array($label,$this->labels)){
                $average=$this->averages[$label];
                $total_found+=$average;
                $total_partials+=$value;
                $count_found++;
            }
        }
        return [
            "count_found"       =>  $count_found,
            "count_averages"    =>  $this->count_averages,
            "count_fraction"    =>  round($count_found/$this->count_averages,3),

            "total_found"       =>  $total_found,
            "total_averages"    =>  $this->total_averages,
            "total_fraction"    =>  round($total_found/$this->total_averages,3),

            "total_new"         => $total_partials,
            "multiplier"        => round($total_partials/$total_found,3),
        ];
    }

    public function estimate_from_partials(array $partials, int $precision=1): array
    {
        $evaluation=$this->evaluate_partials($partials);
        $total_found=$evaluation["total_found"];
        $total_partials=$evaluation["total_new"];
        $given=array_keys($partials);
        foreach($this->averages as $label => $average){
            if(!in_array($label,$given)){
                if($precision>1){
                    $partials[$label]=round($average/$total_found*$total_partials/$precision)*$precision;
                    } else {
                    $partials[$label]=round($average/$total_found*$total_partials);
                }
            }
        }
        return $partials;
    }

    public function estimate_from_total(int $total, int $precision=1): array
    {
        $partials=Array();
        foreach($this->averages as $label => $average){
            $partials[$label]=round($average/$this->total_averages*$total/$precision)*$precision;
        }
        return $partials;
    }
}
