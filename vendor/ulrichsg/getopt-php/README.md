# GetOpt.PHP

[![Build Status](https://travis-ci.org/getopt-php/getopt-php.svg?branch=master)](https://travis-ci.org/getopt-php/getopt-php)
[![Coverage Status](https://coveralls.io/repos/github/getopt-php/getopt-php/badge.svg?branch=master)](https://coveralls.io/github/getopt-php/getopt-php?branch=master)
[![Latest Stable Version](https://poser.pugx.org/ulrichsg/getopt-php/v/stable.svg)](https://packagist.org/packages/ulrichsg/getopt-php) 
[![Total Downloads](https://poser.pugx.org/ulrichsg/getopt-php/downloads.svg)](https://packagist.org/packages/ulrichsg/getopt-php) 
[![License](https://poser.pugx.org/ulrichsg/getopt-php/license.svg)](https://packagist.org/packages/ulrichsg/getopt-php)

GetOpt.PHP is a library for command-line argument processing. It supports PHP version 5.4 and above.

## Features

* Supports both short (e.g. `-v`) and long (e.g. `--version`) options
* Option aliasing, ie. an option can have both a long and a short version
* Cumulative short options (e.g. `-vvv`)
* Two alternative notations for long options with arguments: `--option value` and `--option=value`
* Collapsed short options (e.g. `-abc` instead of `-a -b -c`), also with an argument for the last option 
    (e.g. `-ab 1` instead of `-a -b 1`)
* Two alternative notations for short options with arguments: `-o value` and `-ovalue`
* Quoted arguments (e.g. `--path "/some path/with spcaces"`) for string processing
* Options with multiple arguments (e.g. `--domain example.org --domain example.com`)
* Operand (positional arguments) specification, validation and limitation
* Command routing with specified options and operands
* Help text generation
* Default argument values
* Argument validation

## Upgrading

If you are still using a legacy version of GetOpt.PHP, please consider upgrading to version 3.

Only a few adjustments to your code are required to benefit from a lot of improvements.
Refer to the [upgrade guide](https://getopt-php.github.io/getopt-php/upgrade.html) for details.

## Documentation

* [Documentation for the current version (3.0+)](http://getopt-php.github.io/getopt-php/)
* [Legacy documentation (1.4)](https://github.com/getopt-php/getopt-php/blob/1.4.1/README.markdown)

## License

GetOpt.PHP is published under the [MIT License](http://www.opensource.org/licenses/mit-license.php).
