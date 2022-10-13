# Terminus Secrets Manager Plugin

[![CircleCI](https://circleci.com/gh/pantheon-systems/terminus-secrets-manager-plugin.svg?style=shield)](https://circleci.com/gh/pantheon-systems/terminus-secrets-manager-plugin)
[![Early Access](https://img.shields.io/badge/Pantheon-Early_Access-yellow?logo=pantheon&color=FFDC28)](https://pantheon.io/docs/oss-support-levels#early-access)

A plugin for managing your Pantheon secrets via Terminus.

NOTE: Secrets Manager is still in Early Access. Customer Support is unable to provide assistance with this feature. Please create an Issue in the GitHub repo to report any issues or bugs.

## Installation

To install this plugin using Terminus 3:
```
terminus self:plugin:install terminus-secrets-manager-plugin
```

## Usage

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
terminus secret:set <site> foo bar --scope=user,ic

[notice] Success

```

Note: If you do not include a `type` or `scope` flag, their defaults will be `env` and `ic` respectively.

### Deleting secrets

Use `terminus secret:delete <site> <secret_name>` to delete a secret for a given site:

```
terminus secret:delete <site> foo

[notice] Success

```


## Using secrets with Integrated Composer

### Steps

1. [Generate a github token](https://docs.github.com/en/authentication/keeping-your-account-and-data-secure/creating-a-personal-access-token). The Github token needs all of the "repo" permissions (check this box specifically - only checking all the child boxes does not set the proper permissions): ![image](https://user-images.githubusercontent.com/87093053/191616923-67732035-08aa-41c3-9a69-4d954ca02560.png) 

1. Set the secret value to the token via terminus: `terminus secret:set <site> github-oauth.github.com <github_token> --type=composer --scope=user,ic`

1. Commit a change to generate a new build 

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

`terminus secret:set ${SITE_NAME} COMPOSER_AUTH ${COMPOSER_AUTH_JSON} --type=env --scope=user,ic`
```
