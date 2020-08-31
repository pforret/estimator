# Historical Estimator / Extrapolator

Github: 
![GitHub tag](https://img.shields.io/github/v/tag/pforret/estimator)
![Tests](https://github.com/pforret/estimator/workflows/Run%20Tests/badge.svg)
![Psalm](https://github.com/pforret/estimator/workflows/Detect%20Psalm%20warnings/badge.svg)
![Styling](https://github.com/pforret/estimator/workflows/Check%20&%20fix%20styling/badge.svg)

Packagist: 
[![Packagist Version](https://img.shields.io/packagist/v/pforret/estimator.svg?style=flat-square)](https://packagist.org/packages/pforret/estimator)
[![Packagist Downloads](https://img.shields.io/packagist/dt/pforret/estimator.svg?style=flat-square)](https://packagist.org/packages/pforret/estimator)

Package to help estimate stats based on partial data and historic averages.

Example: 
* given the average rainfall in December in 20 cities for the last 5 years (e.g.86 mm for Brussels, ...)
* when I have the rainfall this year for 15 of those cities, 
* estimate the other 5 cities

## Installation

You can install the package via composer:

```bash
composer require pforret/estimator
```

## Usage

``` php
use Pforret\Estimator\Estimator;
$est = new Estimator();
$est->set_references($references);

// and then

$estimated = $est->estimate_from_partials($partials);

// or 

$estimated = $est->estimate_from_total(100);
```

    $evaluation = $est->evaluate_partials($partials);

    // references = values set with set_references
    [references_count] => 4
    [references_maximum] => 25
    [references_mean] => 25
    [references_median] => 25
    [references_minimum] => 25
    [references_sum] => 100
    // partials = values specified with estimate_from_partials
    [partials_maximum] => 28
    [partials_mean] => 26
    [partials_median] => 25
    [partials_minimum] => 25
    [partials_multiplier] => 1.04
    [partials_sum] => 78
    // found = subset of references, matching with partials keys
    [found_count] => 3
    [found_count_fraction] => 0.75
    [found_mean] => 25
    [found_sum] => 75
    [found_sum_fraction] => 0.75
    // stat = statistic evaulation of estimate/extrapolation
    [stat_confidence] => 74.913
    [stat_deviation] => 3

## Example

    $references=[
        "John"  =>  100,
        "Kevin" =>  120,
        "Sarah" =>  100,
        "Vince" =>  100,
        ];
    $est = new Estimator();
    $est->set_references($references);
    $partials=[
        "John"  => 120,
        "Kevin" => 150,
        // "Sarah" is to be estimated
        "Vince" =>  175,
        ];
    $estimation=$est->estimate_from_partials();
    /*
        [John] => 120
        [Kevin] => 150
        [Vince] => 175
        [Sarah] => 139 <<< estimation
    */
    
    
    
## Testing

``` bash
composer test
```

## Changelog

Please see [CHANGELOG](CHANGELOG.md) for more information on what has changed recently.

## Contributing

Please see [CONTRIBUTING](CONTRIBUTING.md) for details.

## Security

If you discover any security related issues, please email author_email instead of using the issue tracker.

## Credits

- [Peter Forret](https://github.com/pforret)
- [All Contributors](../../contributors)

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
