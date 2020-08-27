# This is my package Estimator

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
$est->set_averages($averages);

// and then

$estimated = $est->estimate_from_partials($partials);

// or 

$estimated = $est->estimate_from_total(100);
```

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
