# heroes-share-bot
Bot for Heroes Share

[![](https://github.com/tattersoftware/heroes-share-bot/workflows/PHPUnit/badge.svg)](https://github.com/tattersoftware/heroes-share-bot/actions?query=workflow%3A%22PHPUnit)
[![](https://github.com/tattersoftware/heroes-share-bot/workflows/PHPStan/badge.svg)](https://github.com/tattersoftware/heroes-share-bot/actions?query=workflow%3A%22PHPStan)

## Description

**Heroes Share Bot** provides automation services for web and social applications related
to Blizzard's "Heroes of the Storm".

## Requirements

**Heroes Share Bot** is built on version 4 of the CodeIgniter PHP framework. You will need
to be sure your environment meets all the
[system requirements](https://codeigniter4.github.io/CodeIgniter4/intro/requirements.html).
Framework requirements may change but here is a good start:

* PHP 7.3 or newer
* PHP extensions (`php -m`): intl, json, mbstring, mysqlnd, xml, curl
* A database with one of the framework's supported drivers (default: SQLite3)

Framework requirements may depend on your choice of web host. See "Hosting with ..."
in the CodeIgniter [User Guide](https://codeigniter4.github.io/CodeIgniter4/installation/running.html).

These are additional requirements specific to this project:

* [Composer](https://getcomposer.org/download/)
* Access to credentials for Reddit

## Installation

1. Clone or download the repo
2. Rename **env** to **.env** and fill credentials (*see below*)
3. Install the framework, modules, and dependencies: `composer install`
4. Migrate the database: `php spark migrate -all`
5. Seed the database: `php spark db:seed InitialSeeder`

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

If you want to use any of these optional extensions you will need to supply their
configuration in **.env** as well:

1. Sentry.io (exception tracking): Fill `sentry.dsn` from the Sentry.io Project Settings (if you want to use exception reporting)
2. Pushover: Fill `pushover.user` and `pushover.token` from the Pushover Client page for your desired device

## Usage

### Reddit

Subreddit monitoring happens in three stages, each corresponding to its own command. Launch
the commands from the CLI using [CodeIgniter's "spark"](https://codeigniter4.github.io/CodeIgniter4/cli/cli_commands.html).

* Fetch: Scans **/r/heroesofthestorm** for new Submissions (Links/Posts and Comments). `php spark reddit:fetch`
* Process: Filters cached submissions by the configured regex pattern (see **app/Config/Reddit.php**) and loads them into the database. `php spark reddit:process`
* Notify: Sends notifications by email or Pushover for submissions identified during Process. `php spark reddit:notify`

> **Note**: The jobs are best run every minute by cron to get the latest results. They are intentionally split up to allow for job queuing.

## Testing

Copy **php.xml.dist** to **php.xml** and follow the same configuration steps above using the values from the various test environments.
Once the config file is complete you can run the tests:

	composer test

### GitHub Actions

There is a unit test action configured to run on GitHub for every pull request to the `develop`
branch. There are necessary environment variables equivalent to those mentioned in the
**Configuration** section above. These need to be supplied as **Secrets** on the GitHub
repo and then added to the **env** section in the [workflow definition YAML file](.github/workflows/test.yml).
