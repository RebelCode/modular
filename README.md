# RebelCode - Modular

[![Build Status](https://travis-ci.org/RebelCode/modular.svg?branch=develop)](https://travis-ci.org/RebelCode/modular)
[![Code Climate](https://codeclimate.com/github/RebelCode/modular/badges/gpa.svg)](https://codeclimate.com/github/RebelCode/modular)
[![Test Coverage](https://codeclimate.com/github/RebelCode/modular/badges/coverage.svg)](https://codeclimate.com/github/RebelCode/modular/coverage)
[![Latest Stable Version](https://poser.pugx.org/rebelcode/modular/version)](https://packagist.org/packages/rebelcode/modular)
[![This package complies with Dhii standards](https://img.shields.io/badge/Dhii-Compliant-green.svg?style=flat-square)][Dhii]

## Details
A system for working with Dhii modules. Allows primarily implementation and dependency-based ordered running of modules
in the standard way. Will look for module file `module.php` in folders that are immediate children of a given directory.
This means that you can store all files of a module in one directory, and all modules in one place.

[Dhii]: https://github.com/Dhii/dhii
