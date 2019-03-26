# Changelog

## 0.3.0
Fixed:
- Fixed an issue where only `autoload-dev` provided a needed namespace

Changed:
- Change the way how the plugin searched for usages
  Previously it used only `autoload` and `autoload-dev` directives of the projects own `composer.json`.
  
  This was changed so that now every file matching `*.php` beside the `composer.json` will be scanned.
  With the exception that `vendor` is always excluded
  
Added:
- Added new cli parameter `--exclude` to add additional folders to exclude from scan
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
