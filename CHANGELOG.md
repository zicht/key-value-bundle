# Change Log
All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](http://keepachangelog.com/)
and this project adheres to [Semantic Versioning](http://semver.org/).

## [Unreleased]
### Added|Changed|Deprecated|Removed|Fixed|Security
Nothing so far

## 3.0.0 - 2018-06-21
### Added
- Support for Symfony 3.x and Twig 2.x
### Removed
- Support for Symfony 2.x and Twig 1.x

## 2.1.2 - 2018-10-16
### Fixed
- When the required directory `web/media/key_value_storage` is not available,
  the code will first try to create it.  If that does not work, it will fail
  as before.

## 2.1.1 - 2018-03-12
### Fixed
- Removed the dependency on `zicht/admin-bundle`.

## 2.1.0 - 2018-03-08
### Added
- A new form type `zicht_locale_dependent_type`, values stored in this type are
  automatically requested in multiple configurable locales.  Retrieving the value for
  one of these types results in the value for the current locale. 

## 2.0.2 - 2018-01-10
### Changed
- Remove the `final` keyword from the `KeyValueStorage` class, see #6

## 2.0.1 - 2017-08-24
### Changed
- Bugfix to make sure the defaults are handled properly.

## 2.0.0 - 2017-08-09
### Added
- Default values can now be configured in `zicht_key_value.defaults`, these default values
  are provided to every `KeyDefinerInterface` by calling its `setDefaultValues` method.
- Class `AbstractKeyDefiner` which can be extended to get methods `setDefaultValues` and `getDefaultValue`.

## 1.2.0 - 2017-07-24
### Added
- Save a key through the StorageManager with `saveKey` 

## 1.1.0 - 2017-07-19
### Added
- Can now specify the form type and form options for every predefined key
- Form type can be 'file', in which case the file is stored in `web/media/key_value_storage`
### Changed
- The `web/media/key_value_storage` directory must exist and be writable,
  this is checked once during the configuration compile time

## 1.0.0 - 2017-02-03
- Initial release
