<p align="center">
    <img src="https://raw.githubusercontent.com/composer-unused/composer-unused/main/art/logo.png" width="450" alt="composer-unused logo">
</p>

# composer-unused
A Composer tool to show unused Composer dependencies by scanning your code.

Created by [Andreas Frömer](https://twitter.com/icanhazstring) and [contributors](https://github.com/composer-unused/composer-unused/graphs/contributors), logo by [Caneco](https://twitter.com/caneco).

[![](https://img.shields.io/github/actions/workflow/status/composer-unused/composer-unused/validate-code.yml?branch=main&label=build&style=flat-square)](https://github.com/composer-unused/composer-unused)
[![](https://img.shields.io/github/actions/workflow/status/composer-unused/composer-unused/validate-phar.yml?branch=main&label=build-phar&style=flat-square)](https://github.com/composer-unused/composer-unused)
[![](https://img.shields.io/github/tag-date/composer-unused/composer-unused.svg?label=version&style=flat-square)](https://github.com/composer-unused/composer-unused/releases/latest)

> ⚠️ If you want to use this tool as composer-plugin head over to [composer-unused-plugin](https://github.com/composer-unused/composer-unused-plugin).

## Motivation

When working in a big repository, you sometimes lose track of your required Composer
packages. There may be so many packages you can't be sure if they are actually used or not.

Unfortunately, the `composer why` command only gives you the information about why
a package is installed in dependency to another package.

How do we check whether the provided *symbols* of a package are used in our code?

`composer unused` to the rescue!

![example](art/example.gif)

## Installation

⚠️ This tool heavily depends on certain versions of its dependencies. A **local installation of this tool is not recommended** as it might not work as intended or can't be installed correctly. We do recommened you download the `.phar` archive or use **PHIVE**  to install it locally.

### PHAR (PHP Archive) (recommended)
Install via `phive` or grab the latest `composer-unused.phar` from the latest release:

    phive install composer-unused
    curl -OL https://github.com/composer-unused/composer-unused/releases/latest/download/composer-unused.phar

### Local
You can also install `composer-unused` as a local __development__ dependency:

    composer require --dev icanhazstring/composer-unused

## Usage
Depending on the kind of your installation the command might differ.

*Note: Packages must be installed via `composer install` or `composer update` prior to running `composer-unused`.*

### PHAR
The `phar` archive can be run directly in you project:

    php composer-unused.phar

### Local
Having `composer-unused` as a local dependency you can run it using the shipped binary:

    vendor/bin/composer-unused


### Exclude folders and packages
Sometimes you don't want to scan a certain directory or ignore a Composer package while scanning.
In these cases, you can provide the `--excludeDir` or the `--excludePackage` option.
These options accept multiple values as shown next:

    php composer-unused.phar --excludeDir=config --excludePackage=symfony/console
    php composer-unused.phar \
        --excludeDir=bin \
        --excludeDir=config \
        --excludePackage=symfony/assets \
        --excludePackage=symfony/console

> Make sure the package is named exactly as in your `composer.json`

### Configuration
You can configure composer-unused by placing a `composer-unused.php` beside the projects `composer.json`
This configuration can look something like this: [composer-unused.php](composer-unused.php)

#### Ignore dependencies by name
To ignore dependencies by their name, add the following line to your configuration:

```
$config->addNamedFilter(NamedFilter::fromString('dependency/name'));
```

#### Ignore dependencies by pattern
To ignore dependencies by pattern, add the following line to your configuration

```
$config->addPatternFilter(PatternFilter::fromString('/dependency\/name/'));
```

> You can ignore multiple dependencies by a single organization using `PatternFilter` e.g. `/symfony\/.*/`

#### Additional files to be parsed
Per default, `composer-unused` is using the `composer.json` autoload directive to determine where to look for files to parse.
Sometimes dependencies don't have their composer.json correctly set up, or files get loaded in another way.
Using this, you can define additional files on a per-dependency basis.

```
$config->setAdditionalFilesFor('dependency/name', [<list-of-file-paths>]);
```

## Changelog

Please have a look at [`CHANGELOG.md`](CHANGELOG.md).

## Contributing

Please have a look at [`CONTRIBUTING.md`](CONTRIBUTING.md).

## Code of Conduct

Please have a look at [`CODE_OF_CONDUCT.md`](CODE_OF_CONDUCT.md).

## License

This package is licensed under the [MIT License](LICENSE).
