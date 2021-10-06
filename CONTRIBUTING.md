# CONTRIBUTING

Thank you for considering contributing to this package!

## Steps to contribute

### Installation

1. Fork the repository
2. `git clone yourname/composer-unused`
3. Download the [ext-ds extension](https://pecl.php.net/package/ds/) and make sure it is enabled in php.ini using the [installation instructions](https://github.com/php-ds/ext-ds)
    
   - Windows users - Place the DLL file in the php/ext folder in your *ampp directory

4. Download the [ext-zend-opcache extension](https://pecl.php.net/package/ZendOpcache) tgz file and compile it and make sure it is enabled in php.ini using the [installation instructions](https://github.com/zendtech/ZendOptimizerPlus)
    
    - Windows users

        1. Download a [pre-compiled DLL file](https://windows.php.net/downloads/pecl/releases/opcache/) 

        2. Place the DLL file in the php/ext folder in your *ampp directory
        
        3. Run the following lines:
        ```
        zend_extension=php_opcache.dll
        opcache.enable=On
        opcache.enable_cli=On
        ```
5. Run `composer install`

### Setting up Docker

1. Run `docker-composer up -d`
2. Use the corresponding created containers
    - composer-unused-7.3
    - composer-unused-7.4
    - composer-unused-8.0

### Submitting Changes

6. Make your changes
7. Run `composer check`
8. Create your Pull-Request

## Check your changes against local projects

To validate your changes against a project of yours, you can require your current cloned `composer-unused`
as a global dependency using a local path. So your changes would have immediate effects.

To do so, go to your global composer installation, typically somewhere around `~/.composer/composer.json`, and add
`composer-unused` as repository using the `path` (see [docs](https://getcomposer.org/doc/05-repositories.md#path)) 
configuration of composer.

    {
        "repositories": [
            {
                "type": "path",
                "url": "<path to your clone>"
            }
        ]
    }
    
After you have done this you can require your local clone as a global dependency (e.g. branch `feature/awesome`)

    $ composer global require icanhazstring/composer-unused:dev-feature/awesome
    
This should setup a symlink and you are ready to go. 

## More information

You can get more information about the list of all defined scripts (See composer.json).

    $ composer run-script --list
