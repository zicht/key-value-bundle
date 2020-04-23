# Change Log
All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](http://keepachangelog.com/)
and this project adheres to [Semantic Versioning](http://semver.org/).

## [Unreleased]
### Added|Changed|Deprecated|Removed|Fixed|Security
Nothing so far

## 2.3.1 - 2020-04-23
### Fixed
- The `PredefinedJsonSchemaKey::createKey` now only takes the schema file.  All other information
  will be obtained from the schema file:
  + Key: the file name
  + Value (default): the default values from the schema file
  + FriendlyName: the `description` or `title` from the schema file with a fallback to the Key
- We now ensure that the schema file will only be loaded in the cms.

## 2.3.0 - 2020-04-22
### Added
- The bundle now supports keys based on a json schema.  The values will be configurable in the
  CMS by rendering a form using [json-editor](https://github.com/json-editor/json-editor).

  The json schema file follows the well documented syntax from
  [json-schema.org](https://json-schema.org/understanding-json-schema/reference/index.html).

  ```php
  PredefinedJsonSchemaKey::createKey(
      realpath(__DIR__ . '/../Resources/public/key-value-storage/this-is-the-key.schema.json'));
  ```

## 2.2.2 - 2019-09-10
### Fixed
- The defaults values in yaml now also support json data.
  This is essential for the locale dependent type.

  ```yaml
  zicht_key_value:
      json_defaults:
          homepage: '{"nl":"/nl/thuis","en":"/en/home"}'
  ```

## 2.2.1 - 2018-12-06
### Fixed
- Minor PR feedback.

## 2.2.0 - 2018-12-06
### Changed
- No longer checking that the `web/media/key_value_storage` directory exists,
  instead a command and composer script is provided to create the directory
  during the `composer install`.  See README.md for configuration instructions.

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
