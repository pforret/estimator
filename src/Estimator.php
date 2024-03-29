<?php

namespace Pforret\Estimator;

use Exception;

class Estimator
{
    private $references = [];
    private $labels = [];
    private $metadata = [];

    public function set_references(array $references): void
    {
        if (! $references) {
            throw new Exception('References cannot be set to empty array');
        }
        $this->references = $references;
        $this->labels = array_keys($this->references);
        $values = array_values($this->references);
        $this->metadata["references_count"] = count($references);
        $this->metadata["references_sum"] = array_sum($values);
        $this->metadata["references_mean"] = $this->calc_mean($values);
        $this->metadata["references_median"] = $this->calc_median($values);
        $this->metadata["references_minimum"] = min($values);
        $this->metadata["references_maximum"] = max($values);
    }

    public function evaluate_partials(array $partials, bool $with_stats = true): array
    {
        if (! $this->references) {
            throw new Exception('References not set, use `set_references` before using `evaluate_partials`');
        }
        if ($this->metadata["references_count"] === 0) {
            throw new Exception('References not set, use `set_references` before using `evaluate_partials`');
        }
        $references_found_sum = 0;
        $partials_sum = 0;
        $found_count = 0;
        $results = $this->metadata;
        foreach ($partials as $label => $value) {
            if (in_array($label, $this->labels)) {
                $references_found_sum += $this->references[$label];
                $partials_sum += $value;
                $found_count++;
            }
        }
        if (! $found_count) {
            $error_message = sprintf("No overlap between\n partials (%s) and\n references (%s):\n cannot extrapolate data", $this->array_keys_to_string($partials), $this->array_keys_to_string($this->references));

            throw new Exception($error_message);
        }
        $results["found_count"] = $found_count;
        $results["found_count_fraction"] = round($found_count / $this->metadata["references_count"], 3);

        $results["found_sum"] = $references_found_sum;
        $results["found_sum_fraction"] = round($references_found_sum / $this->metadata["references_sum"], 3);
        $results["found_mean"] = round($references_found_sum / $found_count, 3);

        $results["partials_sum"] = $partials_sum;
        $results["partials_multiplier"] = round($partials_sum / $references_found_sum, 5);
        $results["partials_mean"] = $this->calc_mean($partials);
        $results["partials_median"] = $this->calc_median($partials);
        $results["partials_minimum"] = min($partials);
        $results["partials_maximum"] = max($partials);

        if ($with_stats) {
            $deviation = 0;
            foreach ($partials as $label => $value) {
                if (in_array($label, $this->labels)) {
                    $expected = $this->references[$label] / $references_found_sum * $partials_sum;
                    $difference = abs($expected - $value);
                    $deviation += $difference * $difference;
                }
            }
            if ($found_count > 1) {
                $results["stat_variance"] = $deviation / ($found_count - 1);
            } else {
                $results["stat_variance"] = 9999;
            }
            $results["stat_deviation"] = round(sqrt($results["stat_variance"]), 3); // square root
            // TODO: statistical correct calculation of confidence
            $results["stat_trustable"] = round((100 - ($results["stat_deviation"] / max($results["partials_mean"], $this->metadata["references_mean"]))) * ($results["found_sum"] / $this->metadata["references_sum"]), 3);
        }
        ksort($results);

        return $results;
    }

    public function estimate_from_partials(array $partials, int $precision = 1, bool $with_originals = true): array
    {
        if ($with_originals) {
            $response = $partials;
        } else {
            $response = [];
        }
        $evaluation = $this->evaluate_partials($partials);
        $references_found_sum = $evaluation["found_sum"];
        $partials_sum = $evaluation["partials_sum"];
        $given = array_keys($partials);
        foreach ($this->references as $label => $average) {
            if (! in_array($label, $given)) {
                if ($precision > 1) {
                    $response[$label] = round($average / $references_found_sum * $partials_sum / $precision) * $precision;
                } else {
                    $response[$label] = round($average / $references_found_sum * $partials_sum);
                }
            }
        }

        return $response;
    }

    public function estimate_from_total(int $total, int $precision = 1): array
    {
        $partials = [];
        foreach ($this->references as $label => $average) {
            $partials[$label] = round($average / $this->metadata["references_sum"] * $total / $precision) * $precision;
        }

        return $partials;
    }

    private function calc_mean(array $values): float
    {
        return array_sum($values) / count($values);
    }
    
    private function calc_median(array $values): float
    {
        sort($values);
        $nb = count($values);
        if ($nb % 2 === 0) {
            // even number of values --  6 values (index 0 - 5), take ([2]+[3])/2
            return ($values[$nb / 2 - 1] + $values[$nb / 2]) / 2;
        } else {
            // odd number of values --  5 values (index 0 - 5), take [2]
            return $values[($nb - 1) / 2];
        }
    }

    private function array_keys_to_string(array $array): string
    {
        $keys = array_keys($array);
        sort($keys);

        return implode(", ", $keys);
    }
}
