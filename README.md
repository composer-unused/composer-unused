<p align="center">
    <img src="https://raw.githubusercontent.com/icanhazstring/composer-unused/master/art/logo.png" width="450" alt="composer-unused logo">
</p>

# composer-unused
A Composer tool to show unused Composer dependencies by scanning your code. 

Created by [Andreas Frömer](https://twitter.com/icanhazstring) and [contributors](https://github.com/icanhazstring/composer-unused/graphs/contributors), logo by [Caneco](https://twitter.com/caneco).

:fire::fire: Now available as Github Action on the [Marketplace](https://github.com/marketplace/actions/composer-unused) :fire::fire:

[![](https://img.shields.io/travis/icanhazstring/composer-unused.svg?style=flat-square)](https://travis-ci.org/icanhazstring/composer-unused)
[![](https://img.shields.io/github/tag-date/icanhazstring/composer-unused.svg?label=version&style=flat-square)](https://github.com/icanhazstring/composer-unused/releases/latest)

## Motivation

When working in a big repository, you sometimes lose track of your required Composer
packages. There may be so many packages you can't be sure if they are actually used or not.

Unfortunately, the `composer why` command only gives you the information about why
a package is installed in dependency to another package.

How do we check whether the provided *symbols* of a package are used in our code?

`composer unused` to the rescue!

![example](https://i.imgur.com/aTLwpgL.gif)

## Installation

### PHAR (PHP Archive) (recommended)
Install via `phive` or grab the latest `composer-unused.phar` from the latest release:

    $ phive install composer-unused
    
    $ curl -JOL https://github.com/icanhazstring/composer-unused/releases/latest/download/composer-unused.phar

### Global
If you have a lot of projects and don't want to install this package per project, simply install it
as a global dependency (e.g. on your CI):

    $ composer global require icanhazstring/composer-unused

### Local
You can also install `composer-unused` as a local __development__ dependency:

    $ composer require --dev icanhazstring/composer-unused

> :exclamation: Beware: Local (or global) requirement might lead to issues related to outdated or `replaced` dependencies and
> `composer-unused` might not work as intended!

## Usage
Depending on the art of your installation the command might differ.

### PHAR
The `phar` archive can be run directly in you project:

    $ php composer-unused.phar

### Local/Global
Having `composer-unused` as a local or global dependency you can run it as an composer-plugin:

    $ composer unused


### Exclude folders and packages
Sometimes you don't want to scan a certain directory or ignore a Composer package while scanning.
In these cases, you can provide the `--excludeDir` or the `--excludePackage` option.
These options accept multiple values as shown next:

    $ php composer-unused.phar --excludeDir=config --excludePackage=symfony/console
    $ php composer-unused.phar \
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

### Suppress progress bar
If you run `composer-unused` in CI the console progressbar often messes up your logs files.
To suppress them use:

    `$ php composer-unused.phar --no-progress`

## Troubleshooting
If you encounter some errors, try running:

    $ php composer-unused.phar -vvv


This command will enable the debug mode and create an error report beside your `composer.json`.
> `composer-unused-dump-YmdHis`

## Changelog

Please have a look at [`CHANGELOG.md`](CHANGELOG.md).

## Contributing

Please have a look at [`CONTRIBUTING.md`](CONTRIBUTING.md).

## Code of Conduct

Please have a look at [`CODE_OF_CONDUCT.md`](CODE_OF_CONDUCT.md).

## License

This package is licensed under the [MIT License](LICENSE).
