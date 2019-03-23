# composer-unused
Show unused packages by scanning your code

## Motivation

Working in a big repository with multiple people, sometimes you might lose track 
of you required composer packages. You have so many packages you can't be sure if they are used
or not.

You can use `composer why package/A` but that only gives you information about why this package is installed
in dependency to another package.

What if you need to find out if the provided *namespaces* of the package are used in your code?

`composer unused` to the rescue!

![usage](https://media.giphy.com/media/LYrx0fhQ4qD8asscts/giphy.gif)

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

 <span style="color: yellow">! [NOTE] Skipped 2 requirements. No package found or invalid constraint.</span>                                               

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
