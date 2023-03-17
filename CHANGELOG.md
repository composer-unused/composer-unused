# Changelog

## [Unreleased] - TBA
### Added
* Pass NameResolver to SymbolNameParser by @eliashaeussler in https://github.com/composer-unused/composer-unused/pull/460
* Add debug commands to see consumed and defined symbols by @icanhazstring in https://github.com/composer-unused/composer-unused/pull/463
* Add composer-json option to debug by @icanhazstring in https://github.com/composer-unused/composer-unused/pull/464
* Add hyperlink to default formatter by @icanhazstring in https://github.com/composer-unused/composer-unused/pull/468
### Fixed
* Make composer-json an option, not argument by @icanhazstring in https://github.com/composer-unused/composer-unused/pull/466
* Remove root namespace filter to avoid subpackage filtering by @icanhazstring in https://github.com/composer-unused/composer-unused/pull/465
### Changed
* Pass NameResolver to SymbolNameParser by @eliashaeussler in https://github.com/composer-unused/composer-unused/pull/460
### Removed
### Security
### Deprecation

## [0.8.7] - TBA
### Added
### Fixed
* Use correct version in CLI by @icanhazstring in https://github.com/composer-unused/composer-unused/pull/453
* Avoid exception on missing directory by @icanhazstring in https://github.com/composer-unused/composer-unused/pull/454
* fix junit formatter by @reinfi in https://github.com/composer-unused/composer-unused/pull/456
* JUnit Formatter - fix issue #457 by @reinfi in https://github.com/composer-unused/composer-unused/pull/458
* Avoid github pr info on ignored packages by @icanhazstring in https://github.com/composer-unused/composer-unused/pull/459
### Changed
### Removed
### Security
### Deprecation

## [0.8.6] - TBA
### Added
* Junit formatter by @reinfi in https://github.com/composer-unused/composer-unused/pull/440
* Add "static analysis" tag by @icanhazstring in https://github.com/composer-unused/composer-unused/pull/447
### Fixed
* fix: reimplement excludeDir by @simPod in https://github.com/composer-unused/composer-unused/pull/431
* do not add empty test cases to junit xml by @reinfi in https://github.com/composer-unused/composer-unused/pull/446
### Changed
### Removed
### Security
### Deprecation

## [0.8.5] - TBA
### Added
* Readd progress bar by @MarcinGladkowski in https://github.com/composer-unused/composer-unused/pull/427
### Fixed
* Marking required package as used by "required-by" even if other package is unused by @MarcinGladkowski in https://github.com/composer-unused/composer-unused/pull/424
* Update PatternFilter example in README by @mvhirsch in https://github.com/composer-unused/composer-unused/pull/416
* Explicitly require symfony/property-access by @eliashaeussler in https://github.com/composer-unused/composer-unused/pull/428
* Resolve issue with suggest-by feature by @eliashaeussler in https://github.com/composer-unused/composer-unused/pull/428
### Changed
### Removed
### Security
### Deprecation

## [0.8.4] - TBA
### Added
* Add JsonFormatter with test by @TomasVotruba in https://github.com/composer-unused/composer-unused/pull/398
* Add testcase for readonly class (#369) by @pascalheidmann in https://github.com/composer-unused/composer-unused/pull/402
* Don't report packages unused in annotations by @LeoVie in https://github.com/composer-unused/composer-unused/pull/404
### Fixed
* override exit code with "0" if option `--ignore-exit-code` is used by @pascalheidmann in https://github.com/composer-unused/composer-unused/pull/396
* Empty PSR4 namespace by @yoanmLf in https://github.com/composer-unused/composer-unused/pull/405
### Changed
* Prepare php 8.2 support by @pascalheidmann in https://github.com/composer-unused/composer-unused/pull/403
### Removed
### Security
### Deprecation

## [0.8.3] - TBA
### Added
### Fixed
* Keep the PatternFilter in used state by @nicklog in https://github.com/composer-unused/composer-unused/pull/353
* Wire up custom configuration file location support by @WyriHaximus in https://github.com/composer-unused/composer-unused/pull/354
* Fix typo in CONTRIBUTING.md by @Jean85 in https://github.com/composer-unused/composer-unused/pull/359
* Fix Symfony 4 support by @Jean85 in https://github.com/composer-unused/composer-unused/pull/360
* Mark new "composer" platform requirement as globally excluded. (Fixes: #381) by @AndreasA in https://github.com/composer-unused/composer-unused/pull/389
* Lexer version detector patch by @georgeconstantinou in https://github.com/composer-unused/composer-unused/pull/392
### Changed
* Add missing use statement by @OskarStark in https://github.com/composer-unused/composer-unused/pull/374
### Removed
### Security
### Deprecation

## [0.8.2] - 2022-03-22
### Added
### Fixed
* Avoid strpos comparison for php package by @icanhazstring in https://github.com/composer-unused/composer-unused/pull/318
* Resolve #328: Fix issue where output format could not be forced by cli by @icanhazstring in https://github.com/composer-unused/composer-unused/pull/334
* Expose Symfony\Polyfill to be able to run phar with php7.4 by @icanhazstring in https://github.com/composer-unused/composer-unused/pull/339
### Changed
* Make Required Dependencies faster by @scyzoryck in https://github.com/composer-unused/composer-unused/pull/322
* Split independent loops during looking for used packages by @scyzoryck in https://github.com/composer-unused/composer-unused/pull/324
### Removed
### Security
### Deprecation

## [0.8.1] - 2022-02-15
### Added
- Add `--output-format` option
  - Current supported values: `default` and `github`
  - `github` can be used to annotate PR
- Add `CiDetector` to change output format according to the current ci environment
- Add `symfony/dependency-injection` to leverage autowiring
- Add dedicated configuration (`composer-unused.php`)
### Fixed
- Fix `Lexer\Emulative` to use the current php version instead of latest one
- Removed `is_dir()` check from `LocalRepository` to avoid crashing `file_get_contents`
- Fix phar build using the latest `box-project/box` version
- Fix error if `composer.json` does not exist in given path
- Fix issue where `Filter` was marked as unused again after being already used
- Fix ignore/invalid dependencies showing up in `Ignored` section again
### Changed
- Change the information about ignored dependencies
- Raised minimum requirement for `composer-unused/symbol-parser` to `0.1.7`
### Removed
- Removed custom implementation of DI
### Security
### Deprecation

## [0.8.0] - 2022-02-03
### Fixed
- Fixed version output when running `bin/composer-unused --version`
### Added
- Added CLI argument `composer-json` which can be used to parse external projects.
  This will default to the current working directory.
- Added error message when `composer.json` is not readable
- Added check for zombie exclusion. It will report if any excluded packages or pattern did match any package
### Changed
- Change `bin/composer-unused` to be used as standalone binary
- Package type is now `library` instead of `composer-plugin`
### Removed
- Removed ability to work as `composer-plugin` (will be supported using `composer-unused/composer-unused-plugin`)
- Dropped support for php `7.3`

## [0.7.7] - 2021-07-26
### Added
- Added support for `psr/log` v2 and 3 [#200](https://github.com/composer-unused/composer-unused/pull/200) (thanks to [@simPod](https://github.com/simPod))

## [0.7.6| - 2021-07-15
### Removed
- Dropped support for composer v1

## [0.7.5] - 2020-10-28
### Added
- Added an `InstanceofStrategy` which detects usages in `instanceof` expressions [#100](https://github.com/composer-unused/composer-unused/pull/100)

## [0.7.4] - 2020-09-15
### Fixed
- Fixed an issue where `ext-ds` classes where not recognized as used [#88](https://github.com/composer-unused/composer-unused/pull/88)
- Fixed an issue where `extends` and `implements` of FQN was not markes as used [#90](https://github.com/composer-unused/composer-unused/pull/90)

## [0.7.3] - 2020-05-19
### Added
- Added workflow to verify integrity of build `phar` file
- Added self unused dependency check using `bin/composer-unused`
### Changed
- Readded `composer/composer` into root requirements as its required to run `bin/composer-unused`
### Fixed
- Fixed an issue where log level received wrong type [#83](https://github.com/composer-unused/composer-unused/pull/83) (thanks to [@JoshuaBehrens](https://github.com/JoshuaBehrens))

## [0.7.2] - 2020-05-19
### Added
- Added `phpspec/prophecy-phpunit` to remove deprecations warnings of `prophecy()` with `phpunit/phpunit:^9.0`
- Added support for composer 2.0
### Changed
- Changed the exit code `0` if there are not packages to scan [#78](https://github.com/icanhazstring/composer-unused/issues/78)
### Fixed
- Fixed an issue where `Core` extension was checked as `ext-core` instead of `php` [#79](https://github.com/icanhazstring/composer-unused/issues/79)

## [0.7.1] - 2019-12-09
### Added
- Added `--no-progress` to suppress progress bar for CI
### Fixed
- Fixed `bin/composer-unused` was unable to detect `vendor/autoload.php` when run as direct depdendency

## [0.7.0] - 2019-12-01
### Added
- Added scoped `phar` support
- Added `bin/composer-unused` as another entry point

### Changed
- Moved `composer/composer` into root requirements as its needed to run `bin/composer-unused`

## [0.6.2] - 2019-10-31
### Added
- Added support to scan for unused php extensions [#33](https://github.com/icanhazstring/composer-unused/issues/33) thanks to [@marcelthole](https://github.com/marcelthole)

## [0.6.1] - 2019-10-24
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
