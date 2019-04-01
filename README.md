# composer-unused
Show unused composer dependencies by scanning your code

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

![example](https://i.imgur.com/aTLwpgL.gif)

## Installation

### Global
If you manage a lot of repositories and don't want to install this package into all those repos, simply install it
as a global dependency (e.g. on your CI):

```bash
$ composer global require icanhazstring/composer-unused
```

### Local (dev requirement)
You can also install `composer-unused` as a local dev requirement:

```bash
$ composer require --dev icanhazstring/composer-unused
```

## Usage

Whether you installed it as local or global requirement, run:

```bash
$ composer unused
```

## Exclude folders and packages
Sometimes you don't want to scan a certain dir or ignore a composer package while scanning.
For this you can provide the `--excludeDir|-xd` or the `--excludePackage|-xp` parameter.

```bash
$ composer unused --excludeDir=config --excludePackage=symfony/console
```

> Make sure the package is named exactly as in your `composer.json`

> You can provide multiple folders and package by repeating the argument.

### Ignore by config
You also have the possibility to exclude packages by configuration. For this you need to provide the `extra`
directive in your `composer.json`.

```json
{
    "extra": {
        "unused": [
            "package/a",
            "package/b"
        ]
    }
}
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
