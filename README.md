# composer-unused
Show unused packages by scanning your code

[![](https://img.shields.io/travis/com/icanhazstring/composer-unused.svg?style=flat-square)](https://travis-ci.org/icanhazstring/composer-unused)
[![](https://img.shields.io/github/tag-date/icanhazstring/composer-unused.svg?label=version&style=flat-square)](https://github.com/icanhazstring/composer-unused/releases/latest)

## Motivation

Working in a big repository with multiple people, sometimes you might lose track 
of you required composer packages. You have so many packages you can't be sure if they are used
or not.

You can use `composer why package/A` but that only gives you information about why this package is installed
in dependency to another package.

What if you need to find out if the provided *namespaces* of the package are used in your code?

`composer unused` to the rescue!

![usage](https://i.imgur.com/sHjjprU.gif)

## Installation

Run

```bash
$ composer global require icanhazstring/composer-unused
```

## Usage

Run

```bash
$ composer unused
```

## Example (doctrine/orm)

```html
vagrant@ubuntu-bionic:/vagrant/doctrine/orm$ composer unused

Loading packages
----------------

 Loading 15 requirements
 15/15 [-----------------------------] 100%

 ! [NOTE] Skipped 2 requirements. No package found or invalid constraint.                                               

 * php
 * ext-ctype

 ! [NOTE] Found 13 packages to be checked.                                                                              

Scanning files...
-----------------

 1174/1174 [-----------------------------] 100%

Found 13 usued and 0 unused packages

Results
-------

 Used packages
 * doctrine/annotations ✓
 * doctrine/cache ✓
 * doctrine/collections ✓
 * doctrine/dbal ✓
 * doctrine/event-manager ✓
 * doctrine/inflector ✓
 * doctrine/instantiator ✓
 * doctrine/persistence ✓
 * doctrine/reflection ✓
 * ocramius/package-versions ✓
 * ocramius/proxy-manager ✓
 * symfony/console ✓
 * symfony/var-dumper ✓

 Unused packages
```

## Troubleshooting
If you encounter some errors, try running

```bash
$ composer unused -vvv
```

This will enable debug mode and create an error report beside your `composer.json`.
> `composer-unused-dump-YmdHis`

## Changelog

Please have a look at [`CHANGELOG.md`](CHANGELOG.md).

## Contributing

Please have a look at [`CONTRIBUTING.md`](CONTRIBUTING.md).

## Code of Conduct

Please have a look at [`CODE_OF_CONDUCT.md`](CODE_OF_CONDUCT.md).

## License

This package is licensed using the [MIT License](LICENSE).
