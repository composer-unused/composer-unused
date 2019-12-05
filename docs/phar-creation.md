# Compile PHAR

1. Install humbug/box

    `$ curl -JOL https://github.com/humbug/box/releases/download/3.8.3/box.phar`
    
2. Compile box

    `$ php box.phar compile`
    
3. Sign box

    `$ gpg --default-key XXX --sign build/composer-unused.phar`
    
4. Detach signature

    `$ gpg --default-key XXX --output build/composer-unused.phar.asc --detach-sig build/composer-unused.phar`
 
5. Verify
    
    `$ gpg --verify build/composer-unused.phar.asc build/composer-unused.phar`
