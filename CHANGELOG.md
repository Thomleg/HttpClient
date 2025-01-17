# Change Log

All notable changes to this project will be documented in this file. This project adheres
to [Semantic Versioning] (http://semver.org/). For change log format,
use [Keep a Changelog] (http://keepachangelog.com/).

## [1.3.1] - 2022-07-19

### Fixed

- Try to decode content if empty
- Trimmed content before inflate failed

## [1.3.0] - 2021-11-26

### Added

- PHP 8.1 compatibility

## [1.2.0] - 2021-07-15

### Changed

- Visibility of `Cookies` class properties to protected

## [1.1.0] - 2021-06-08

### Changed

- Compatibility with `berlioz/http-message` version 2

## [1.0.3] - 2021-03-30

### Fixed

- Empty header value are now ignored

## [1.0.2] - 2021-02-15

### Changed

- Separate headers' parser into an independent trait to be reused

## [1.0.1] - 2021-01-28

### Fixed

- Fix cookie negative max-age

## [1.0.0] - 2020-11-06

Initial version