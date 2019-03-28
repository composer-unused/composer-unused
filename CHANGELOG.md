# Changelog

## 0.4.0
Fixed:
- Fixed an issue where class constant `Namespace\Foobar::class` was not recognized as usage [#29](https://github.com/icanhazstring/composer-unused/issues/29)
- Fixed an issue where an empty namespace caused an exception [#27](https://github.com/icanhazstring/composer-unused/issues/27)

Added:
- Ignore packages that are not of type `library` [#25](https://github.com/icanhazstring/composer-unused/issues/25) [composer-schema#type](https://getcomposer.org/doc/04-schema.md#type)
- Ignore packages that define not namespace [#27](https://github.com/icanhazstring/composer-unused/issues/27)
- Add ability to ignore packages by config [#26](https://github.com/icanhazstring/composer-unused/issues/26) [README.md](https://github.com/icanhazstring/composer-unused#ignore-by-config)
- Packages are no longer shown as `unused` if: 
  - They are suggested by other packages (will show information about `suggested by: package/a`) [#23](https://github.com/icanhazstring/composer-unused/issues/23)
  - They are required by other packages (will show information about `required by: package/a`) [#22](https://github.com/icanhazstring/composer-unused/issues/22)

## 0.3.0
Fixed:
- Fixed an issue where only `autoload-dev` provided a needed namespace

Changed:
- Change the way how the plugin searched for usages
  Previously it used only `autoload` and `autoload-dev` directives of the projects own `composer.json`.
  
  This was changed so that now every file matching `*.php` beside the `composer.json` will be scanned.
  With the exception that `vendor` is always excluded
  
Added:
- Added new cli parameters
  - `--excludeDir|-xd` to add additional folders to exclude from scan
  - `--excludePackage|-xp` to add a package to ignore during scan
- Added Di Container for easier development and testing

## 0.2.0
Fixed:
- Fixed issue where static calls raised an exception

Added:
- Added error handler for debug usage
  - Use `-vvv` to enable debug mode and create a dump file

Improved:
- Added full integration test with composer test project

## 0.1.2
Fixed:
- Fixed issue with static calls on variable types
- Fixed issue with problems on identifying on group uses

Improved:
- Added more stable tests for validate parsing strategies

## 0.1.1
Fixed:
- Fixed issue with different composer directives (classmap, files)

## 0.1.0
Initial Release
