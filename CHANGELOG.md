# Change Log
All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](http://keepachangelog.com/)
and this project adheres to [Semantic Versioning](http://semver.org/).

## [Unreleased]
- Nothing so far

## 1.1.0 - 2017/07/19
### Added
- Can now specify the form type and form options for every predefined key
- Form type can be 'file', in which case the file is stored in `web/media/key_value_storage`
### Changed
- The `web/media/key_value_storage` directory must exist and be writable,
  this is checked once during the configuration compile time

## 1.0.0 - 2017/02/03
- Initial release
