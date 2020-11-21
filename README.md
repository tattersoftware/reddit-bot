# heroes-share-bot
Bot for Heroes Share

[![](https://github.com/tattersoftware/heroes-share-bot/workflows/PHPUnit/badge.svg)](https://github.com/tattersoftware/heroes-share-bot/actions?query=workflow%3A%22PHPUnit)
[![](https://github.com/tattersoftware/heroes-share-bot/workflows/PHPStan/badge.svg)](https://github.com/tattersoftware/heroes-share-bot/actions?query=workflow%3A%22PHPStan)

## Credentials

The bot requires valid Reddit application credentials.
For more details read the [Reddit OAuth2 wiki](https://github.com/reddit-archive/reddit/wiki/OAuth2).

1. Login to Reddit and visit the "authorized applications" page (https://www.reddit.com/prefs/apps/)
2. Under "developed applications" select "create an app..."
3. Select "script" as the application type
4. Provide a name, description, and URLs in the required text fields
5. Select "create app"

Once your application is created you will need to copy the "client ID" and "secret" (see the
wiki article above for help). Add these along with your username and password into your
project's **.env** file, for example:
```
#--------------------------------------------------------------------
# REDDIT API
#--------------------------------------------------------------------

reddit.clientId = as98-asdn3h93r
reddit.clientSecret = LKhsa-ASJDn9a8sdion_laskdn0
reddit.username = MyFiRsTrEdItTbOt
reddit.password = ReallySecurePassword321
```
