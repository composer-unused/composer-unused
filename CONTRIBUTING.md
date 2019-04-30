# CONTRIBUTING

Thank you for considering contributing to this package!

## Steps to contribute

1. Fork the repository
2. `git clone yourname/composer-unsed`
3. Make your changes
4. Run `composer check`
5. Create your Pull-Request

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
