# spacemy.xyz-rebooted (ACXYZ's Fork)
A fork of a fork/faithful recreation of typicalname0/spacemy.xyz

## Notes
Please be advised that this is the NOT official spacemy.xyz-rebooted repository.
If you're looking for the original, go [here](https://github.com/the-real-sumsome/spacemy.xyz-rebooted).

## Dependencies
- Requires composer
- [SteamAuthentication](https://github.com/SmItH197/SteamAuthentication).
- PHP >= 7.3, you could use a lower php version but I don't know if it'll work.
- mysqli/mysql server

## How to Setup
### 
```
git clone https://github.com/notACXYZ/spacemy.xyz-rebooted-1.git
mv (your git dir) (your webserver dir)
cd (your webserver dir)
php composer.phar install

sudo nano static/config.inc.php
```
Get a Steam API key from http://steamcommunity.com/dev/apikey

Get a recaptcha priv/pub key from https://www.google.com/recaptcha/admin

```
import the sql file into phpmyadmin/whatever
sudo service apache2 start
```

## Credits
### Original Contribs
- [Original](https://github.com/the-real-sumsome/spacemy.xyz-rebooted).

You're done.
