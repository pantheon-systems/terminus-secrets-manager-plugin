# Terminus Secrets Manager Plugin

[![CircleCI](https://circleci.com/gh/pantheon-systems/terminus-secrets-manager-plugin.svg?style=shield)](https://circleci.com/gh/pantheon-systems/terminus-secrets-manager-plugin)
[![Early Access](https://img.shields.io/badge/Pantheon-Early_Access-yellow?logo=pantheon&color=FFDC28)](https://pantheon.io/docs/oss-support-levels#early-access)

A plugin to handle Secrets via Terminus.

NOTE: This is still a WORK IN PROGRESS; it is not guaranteed to be error-free.

## Configuration

These commands require no configuration.

## Usage

* `terminus secret:list`
* `terminus secret:set`
* `terminus secret:delete`

### Listing secrets

Use `terminus secret:list` to list existing secrets for a given site:

```
terminus secret:list <site>

 ------------- ------------- ---------------------------
  Secret name   Secret type   Secret value
 ------------- ------------- ---------------------------
  file.json     file          contents of a secret file
  foo           env           bar
 ------------- ------------- ---------------------------
```

### Setting secrets

Use `terminus secret:set <site> <secret_name> <secret_value> [--type=TYPE] [--scope=SCOPE]` to set a secret for a given site:

```
terminus secret:set <site> foo bar

[notice] Success

```

```
terminus secret:set <site> file.json "{}" --type=file

[notice] Success

```

```
terminus secret:set <site> foo bar --scope=ic --scope=user

[notice] Success

```

Note: If you do not include a `type` or `scope` flag, their defaults will be `env` and `ic` respectively.

### Deleting secrets

Use `terminus secret:delete <site> <secret_name>` to delete a secret for a given site:

```
terminus secret:delete <site> foo

[notice] Success

```

## Installation

To install this plugin using Terminus 3:
```
terminus self:plugin:install terminus-secrets-manager-plugin
```

## Testing

This plugin includes three testing targets:

* `composer lint`: Syntax-check all php source files.
* `composer cs`: Code-style check.
* `composer functional`: Run functional test with phpunit

To run all tests together, use `composer test`.

Note that prior to running the tests, you should first run:
* `composer install`

## Help

Run `terminus help <command>` for help.

## Using secrets with Integrated Composer

### Prerequisites

***Terminus***

Have terminus [installed](https://pantheon.io/docs/terminus/install) and working

***Terminus Plugin (all platforms)***

1. `terminus plugin:search secret` will yield more than one secret manager. You want the one with the word MANAGER in the title. 

1. `terminus plugin:install pantheon-systems/terminus-secrets-manager-plugin` The other secret “manager” is legacy code and unsupported. [Jira ticket to update that listing with “deprecated”](https://getpantheon.atlassian.net/browse/CMS-962).

1. `terminus` without any arguments will list all the commands. You should now see “secret” commands in that list.

### Steps

***Before***

`terminus auth:login`

***To create a fresh site for testing***

1. Set the upstream to the ID of our Drupal 9 composer-based upstream: 

	 `export SITE_UPSTREAM_ID=drupal-composer-managed`

1. `export SITE_NAME=$USER-secrets-testing`

1. `terminus site:create ${SITE_NAME} “${USER} Secrets Testing”  ${SITE_UPSTREAM_ID}`

1.  `terminus local:clone ${SITE_NAME}`

1. Set secrets using one of the methods shown below. Changing a file and pushing the code to development will trigger a composer run.

***Github token authentication***

In order to get a github token, you must [generate a github token](https://docs.github.com/en/authentication/keeping-your-account-and-data-secure/creating-a-personal-access-token)

The Github token needs all of the "repo" permissions:

![image](https://user-images.githubusercontent.com/87093053/191616923-67732035-08aa-41c3-9a69-4d954ca02560.png) 

Once you have the token, you can set the secret value to the token like this:

`terminus secret:set ${SITE_NAME} github-oauth.github.com ${GITHUB_TOKEN} --type=composer --scope user --scope ic`

`github-oauth.github.com` is a magic tokenname for composer that authenticates all github url's with the credentials from the token you provide. There are several ["magic" variable names](https://getcomposer.org/doc/articles/authentication-for-private-packages.md#command-line-global-credential-editing), or you can choose "basic authentication" by providing a COMPOSER_AUTH variable.

***HTTP basic authentication***

For multiple private repositories on multiple private domains, you will need to create a COMPOSER_AUTH json and make it available via the COMPOSER_AUTH environment variable.

Composer has the ability to read private repository access information from the environment variable: COMPOSER_AUTH. The COMPOSER_AUTH variables has to be in a [specific JSON format](https://doc.codingdict.com/composer/doc/articles/http-basic-authentication.html). 

That format example is here:

```bash
#!/bin/bash

read -e COMPOSER_AUTH_JSON <<< {
    "http-basic": {
        "github.com": {
            "username": "my-username1",
            "password": "my-secret-password1"
        },
        "repo.example2.org": {
            "username": "my-username2",
            "password": "my-secret-password2"
        },
        "private.packagist.org": {
            "username": "my-username2",
            "password": "my-secret-password2"
        }
    }
}
EOF

`terminus secret:set ${SITE_NAME} COMPOSER_AUTH ${COMPOSER_AUTH_JSON} --type=env --scope user --scope ic`
```
