# composer-unused
Show unused composer dependencies by scanning your code

[![](https://img.shields.io/travis/com/icanhazstring/composer-unused.svg?style=flat-square)](https://travis-ci.org/icanhazstring/composer-unused)
[![](https://img.shields.io/github/tag-date/icanhazstring/composer-unused.svg?label=version&style=flat-square)](https://github.com/icanhazstring/composer-unused/releases/latest)

## Motivation

When working in a big repository, you sometimes lose track of your required composer
packages. There may be so many packages you can't be sure if they are used or not.

Unfortunately, the `composer why` command only gives you the information about why
a package is installed in dependency to an another package.

How do we check whether the provided *namespaces* of a package are used in our code?

`composer unused` is created to the rescue!

![example](https://i.imgur.com/aTLwpgL.gif)

## Installation

### Global
If you have a lot of projects and don't want to install this package per project, simply install it
as a global dependency (e.g. on your CI):

    $ composer global require icanhazstring/composer-unused


### Local (dev requirement)
You can also install `composer-unused` as a local dev dependency:

    $ composer require --dev icanhazstring/composer-unused

## Usage

Whether installing it as local or global dependency, run the command below inside your project directory:

    $ composer unused


### Exclude folders and packages
Sometimes you don't want to scan a certain directory or ignore a composer package while scanning.
For this, you can provide the `--excludeDir|-xd` or the `--excludePackage|-xp` option.
These options accept multiple values as this example:

    $ composer unused --excludeDir=config --excludePackage=symfony/console
    $ composer unused \
        --excludeDir=bin \
        --excludeDir=config \
        --excludePackage=symfony/assets \
        --excludePackage=symfony/console

> Make sure the package is named exactly as in your `composer.json`

### Ignore by configuration
You are also able to exclude packages by configuration. For this, you need to provide the `extra`
directive in your `composer.json` file.

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

    $ composer unused -vvv


This command will enable debug mode and create an error report beside your `composer.json`.
> `composer-unused-dump-YmdHis`

## Changelog

Please have a look at [`CHANGELOG.md`](CHANGELOG.md).

## Contributing

Please have a look at [`CONTRIBUTING.md`](CONTRIBUTING.md).

## Code of Conduct

Please have a look at [`CODE_OF_CONDUCT.md`](CODE_OF_CONDUCT.md).

## License

This package is licensed under the [MIT License](LICENSE).
