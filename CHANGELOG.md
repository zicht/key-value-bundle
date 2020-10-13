# Change Log
All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](http://keepachangelog.com/)
and this project adheres to [Semantic Versioning](http://semver.org/).

## [Unreleased]
## Next major version
### Removed
- CamelCased Twig functions. They are replaced with snake-cased versions.
Replace `getFromKeyValueStorage`, `getPredefinedKey`, `getMissingDBValues` with their equivalent.

### Added|Changed|Deprecated|Removed|Fixed|Security

## 4.5.0 - 2020-10-13
### Added
- Forward merge from 2.x and 3.x.

## 4.4.1 - 2020-10-01
### Fixed
- Updates in `LocaleDependentDataType` regarding Symfony 4.
### Added
- New Twig functions following snake-case conventions.

## 4.4.0 - 2020-06-22
### Changed
- Introduced PSR-4 autoloading

## 4.3.0 - 2020-02-14 ❤️
### Fixed
- Compatibility with Symfony 5 by fixing a few issues.
### Changed
- Default settings for storage-paths are changed from `/web` to `/public` to keep the Symfony defaults.
Revert to `/web` (if needed) by adding the following configuration:
```yaml
zicht_key_value:
    paths:
        web: /web
        storage: /web/media/key_value_storage
```

## 4.2.2 - 2020-02-20
### Fixed
- Hardcoded key value storage column name as Doctrine doesn't accept camelCase column names by default and tries the snake_case variant

## 4.2.1 - 2019-12-20
### Fixed
- Compatibility with Symfony 4 by fixing a few deprecations.

## 4.2.0 - 2019-10-25
### Fixed
- Compatibility with Symfony 4 by fixing a few minor bugs.

## 4.1.0 - 2019-09-12
### Added
- Forward merge from 2.2.2 and 3.2.0.

## 4.0.2
### Fixed
- Update codestyle and require at least php `7.1`

## 4.0.1
### Fixed
- Overlooked a cachepurge after deleting a stored key from the database.
### Changed
- Misc. UI-improvements.

## 4.0.0
### Added
- Interfaces `KeyValueStorageManagerInterface|KeyValueStorageManagerDebugInterface` to detect the StorageManager. You can no longer typehint the class itself in your DI.
- Optional caching for KeyValueStorageManager by injecting a `Symfony\Component\Cache\Adapter\AdapterInterface`-service
```yaml
framework:
    cache:
        pools:
            app.cache.general_apcu: # or specific: app.cache.zicht_key_value.
                adapter: cache.adapter.apcu # can also be: `cache.app` for default filesystem cache
                name: app.cache.general_apcu
                public: true
                default_lifetime: 604800 # 1 week

zicht_key_value:
    cache:
        type: service
        id: app.cache.general_apcu # or `cache.app` for default filesystem cache. apcu is normally also cleared on `cache:clear`
```
This example was used for inspiration: https://github.com/symfony/symfony/issues/24545#issuecomment-336419270
- Collect debuginformation through Symfony's `DataCollector` and display calls to keys and values in the toolbar.

## 3.4.2 - 2020-08-25
### Fixed
- `PredefinedJsonSchemaKey::migrate` can now handle `[]` input by replacing it
  with `(object)[]`.

## 3.4.1 - 2020-08-25
### Added
- The command `zicht:key-value:migrate-json-schema-keys` now supports the flag
  `--replace-invalid` that will replace an invalid stored value with the default
  value.
- Forward merge from 2.4.1.
### Fixed
- `PredefinedJsonSchemaKey::isValid` can now handle `[]` input by replacing it
  with `(object)[]`.

## 3.4.0 - 2020-05-06
### Added
- Forward merge from 2.4.0.

## 3.3.1 - 2020-04-29
### Fixed
- Fixed Symfony 3 issue where form types should be identified using their class name.

## 3.3.0 - 2020-04-28
### Added
- Forward merge from 2.3.0 and 2.3.1.

## 3.2.0 - 2019-09-12
### Added
- Forward merge from 2.2.2.

## 3.1.0 - 2018-12-06
### Changed
- Merge from 2.2.1.

## 3.0.0 - 2018-06-21
### Added
- Support for Symfony 3.x and Twig 2.x
### Removed
- Support for Symfony 2.x and Twig 1.x

## 2.4.1 - 2020-05-13
### Fixed
- The `zicht:key-value:migrate-json-schema-keys` command now uses the Schema library itself
  to migrate from values in the database to newer schema versions.

## 2.4.0 - 2020-05-06
### Added
- The `zicht:key-value:migrate-json-schema-keys` command is now available to check and migrate
  json schema key values when the associated schema changes.  We advise that this command is executed
  every time a deploy is performed.

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
