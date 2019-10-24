# Changelog

## [0.6.0] - 2019-10-24
### Fixed
- Fixed replaced usage on `Zend\ServiceManager` in configuration files
### Removed
- Removed shorthand options `-d` and `-p` as they could be already in use 

## [0.6.0] - 2019-10-24
### Changed
- Added custom `psr/container-interface` implementation as a replacement for `zendframework/zend-servicemanager`
- Inverted the validation of valid Composer package types (no longer a whitelist, but rather a blacklist of invalid types)

### Fixed
- Fixed issue with short option for cli parameter (`--excludeDir|-d` and `--excludePackage|-p`)

## [0.5.6] - 2019-04-30
### Fixed
- Fixed support for typo3 packages thanks to [@tomasnorre](https://github.com/tomasnorre) 

## [0.5.5] - 2019-04-12
### Fixed
- Fixed support for yii2 packages thanks to [@moltam](https://github.com/moltam)
 
## [0.5.4] - 2019-04-11
### Fixed
- Fixed issue where composer-unused-dump was created even when not in debug mode [#41](https://github.com/icanhazstring/composer-unused/issues/41)
- Fixed issue where exit code was greater 0 on skipped packages, while it should be on unused packages [#42](https://github.com/icanhazstring/composer-unused/pull/42) Thanks to [@binarious](https://github.com/binarious)

## [0.5.3] - 2019-04-11
### Fixed
- Fixed issue where qualified namespaces where not recognized when called from global namespace
  - This caused some false-positives (e.g. for symfony-bundles)

## [0.5.2] - 2019-04-11
### Fixed
- Fixed unused scan for `symfony-bundle` types

## [0.5.1] - 2019-03-29
### Fixed
- Fixed an issue where a package could provide an empty as well as a valid namespace

  ```json
  {
  "autoload": {
    "psr-4": {
        "": "src/",
        "A\\": "src/"
      }
    }
  }
  ```

## [0.5.0] - 2019-03-29
### Changed
- The plugin will exit with a code > 0 if there are unused packages
- Temporary solution to "silent" ignore ext- packages (might be changed in the future [#33](https://github.com/icanhazstring/composer-unused/issues/33))

### Added
 - Cli parameter to exit clean (even with unused packages) --ignore-exit-code

## [0.4.0] - 2019-03-28
### Fixed
- Fixed an issue where class constant `Namespace\Foobar::class` was not recognized as usage [#29](https://github.com/icanhazstring/composer-unused/issues/29)
- Fixed an issue where an empty namespace caused an exception [#27](https://github.com/icanhazstring/composer-unused/issues/27)

### Added
- Ignore packages that are not of type `library` [#25](https://github.com/icanhazstring/composer-unused/issues/25) [composer-schema#type](https://getcomposer.org/doc/04-schema.md#type)
- Ignore packages that define not namespace [#27](https://github.com/icanhazstring/composer-unused/issues/27)
- Add ability to ignore packages by config [#26](https://github.com/icanhazstring/composer-unused/issues/26) [README.md](https://github.com/icanhazstring/composer-unused#ignore-by-config)
- Packages are no longer shown as `unused` if: 
  - They are suggested by other packages (will show information about `suggested by: package/a`) [#23](https://github.com/icanhazstring/composer-unused/issues/23)
  - They are required by other packages (will show information about `required by: package/a`) [#22](https://github.com/icanhazstring/composer-unused/issues/22)

### [0.3.1] - 2019-03-26
### Fixed
- Improve error handling

## [0.3.0] - 2019-03-26
### Fixed
- Fixed an issue where only `autoload-dev` provided a needed namespace

### Changed
- Change the way how the plugin searched for usages
  Previously it used only `autoload` and `autoload-dev` directives of the projects own `composer.json`.
  
  This was changed so that now every file matching `*.php` beside the `composer.json` will be scanned.
  With the exception that `vendor` is always excluded
  
### Added
- Added new cli parameters
  - `--excludeDir|-xd` to add additional folders to exclude from scan
  - `--excludePackage|-xp` to add a package to ignore during scan
- Added Di Container for easier development and testing

## [0.2.0] - 2019-03-25
### Fixed
- Fixed issue where static calls raised an exception

### Added
- Added error handler for debug usage
  - Use `-vvv` to enable debug mode and create a dump file

Improved:
- Added full integration test with composer test project

## [0.1.2] - 2019-03-25
### Fixed
- Fixed issue with static calls on variable types
- Fixed issue with problems on identifying on group uses

Improved:
- Added more stable tests for validate parsing strategies

## [0.1.1] - 2019-03-25
### Fixed
- Fixed issue with different composer directives (classmap, files)

## [0.1.0] - 2019-03-23
Initial Release
