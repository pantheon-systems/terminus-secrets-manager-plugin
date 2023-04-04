# Terminus Secrets Manager Plugin

Pantheon’s Secrets Manager Terminus plugin is key to maintaining industry best practices for secure builds and application implementation. Secrets Manager provides a convenient mechanism for you to manage your secrets and API keys directly on the Pantheon platform.

## Overview

### Key Features

- Securely host and maintain secrets on Pantheon
- Use private repositories in Integrated Composer builds
- Create and update secrets via Terminus
- Ability to set a `COMPOSER_AUTH` environment variable and/or a `Composer auth.json` authentication file with Terminus commands
- Ability to define site and org specific secrets
- Ability to define the level of secrecy for each managed item
- Secrets are encrypted at rest

### Early Access

The Secrets Manager plugin is available for Early Access participants. Features for Secrets Manager are in active development. Pantheon's development team is rolling out new functionality often while this product is in Early Access. Visit the [Pantheon Slack channel](https://slackin.pantheon.io/) (or sign up for the channel if you don't already have an account) to learn how you can enroll in our Early Access program. Please review [Pantheon's Software Evaluation Licensing Terms](https://legal.pantheon.io/#contract-hkqlbwpxo) for more information about access to our software.

## Concepts

### Site level secret

This is a secret that is set for a specific site using the site id. Based on the type and scope, this secret will be loaded on the different scenarios that will be supported by Secrets in Pantheon.

### Organization level secret

This is a secret that is set not for a given site but for an organization. This secret will be inherited by ALL of the sites that are OWNED by this organization. Please note that a [Supporting Organization](https://docs.pantheon.io/agency-tips#become-a-supporting-organization) won't inherit its secrets to the sites, only the Owner organization.

### Secret type

This is a field on the secret record. It defines the usage for this secret. Current types are:

- `runtime`: this secret will be used to retrieve it in application runtime using API calls to the secret service. More info on this to come at a later stage of the Secrets project. This will be the recommended way to set stuff like API keys for third-party integrations in your application.
- `env`: this secret will be used to set environment variables in the application runtime. More info on this to come at a later stage of the Secrets project.
- `composer`: this secret type is used for composer authentication to private packages.
- `file`: this type allows you to store files in the secrets. More info on this to come at a later stage of the Secrets project.

Note that you can only set a type per secret and this cannot be changed later (unless you delete and recreate the secret).

### Secret scope

This is a field on the secret record. It defines the components that have access to the secret value. Current scopes are:

- `ic`: this secret will be readable by the Integrated Composer runtime. You should use this scope to get access to your private repositories.
- `web`: this secret will be readable by the application runtime. More info on this to come at a later stage of the Secrets project.
- `user`: this secret will be readable by the user. This scope should be set if you need to retrieve the secret value at a later stage.
- `ops`: behavior to be defined. More info on this to come at a later stage of the Secrets project.

Note that you can set multiple scopes per secret and they cannot be changed later (unless you delete and recreate the secret).

## Organization and Site level secrets

This section describes how the secrets set at the organization and site level interact within them.




## Plugin Usage

### Secrets Manager Plugin Requirements

Secrets Manager requires the following:

- A Pantheon account
- A site that uses [Integrated Composer](https://docs.pantheon.io/guides/integrated-composer) and runs PHP >= 8.0
- Terminus 3

### Installation

Terminus 3.x has built in plugin management.

Run the command below to install Terminus Secrets Manager.

```
terminus self:plugin:install terminus-secrets-manager-plugin
```

### Site secrets Commands

#### Set a secret

The secrets `set` command takes the following format:

- `Name`
- `Value`
- `Type`
- `One or more scopes`


Run the command below to set a secret in Terminus:

```
terminus secret:site:set <site> <secret-name> <secret-value>

[notice] Success

```

```
terminus secret:site:set <site> file.json "{}" --type=file

[notice] Success

```

```
terminus secret:site:set <site> <secret-name> --scope=user,ic

[notice] Success

```

Note: If you do not include a `type` or `scope` flag, their defaults will be `runtime` and `user` respectively.


#### List secrets

The secrets `list` command provides a list of all secrets available for a site. The following fields are available:

- `Name`
- `Scope`
- `Type`
- `Value`
- `Environment Override Values`
- `Org Values`

Note that the `value` field will contain a placeholder value unless the `user` scope was specified when the secret was set.

Run the command below to list a site’s secrets:

`terminus secret:site:list`

```
terminus secret:site:list <site>

 ------------- ------------- ---------------------------
  Secret name   Secret type   Secret value
 ------------- ------------- ---------------------------
  secret-name   env           secrets-content
 ------------- ------------- ---------------------------
```

`terminus secret:site:list`

```
terminus secret:site:list <site> --fields="*"

 ---------------- ------------- ------------------------------------------ --------------- ----------------------------- --------------------
  Secret name      Secret type   Secret value                               Secret scopes   Environment override values   Org values
 ---------------- ------------- ------------------------------------------ --------------- ----------------------------- --------------------
  foo              env           bar                                        web, user
  foo2             runtime       bar2                                       web, user                                     default=barorg
  foo3             env           dummykey                                   web, user       live=sendgrid-live
 ---------------- ------------- ------------------------------------------ --------------- ----------------------------- --------------------
 ```

#### Delete a secret

The secrets `delete` command will remove a secret and all of its overrides.

Run the command below to delete a secret:

```
terminus secret:site:delete <site> <secret-name>

[notice] Success

```

### Organization secrets Commands

#### Set a secret

The secrets `set` command takes the following format:

- `Name`
- `Value`
- `Type`
- `One or more scopes`

Run the command below to set a secret in Terminus:

```
terminus secret:org:set <org> <secret-name> <secret-value>

[notice] Success

```

```
terminus secret:org:set <org> file.json "{}" --type=file

[notice] Success

```

```
terminus secret:org:set <org> <secret-name> --scope=user,ic

[notice] Success

```

Note: If you do not include a `type` or `scope` flag, their defaults will be `runtime` and `user` respectively.


#### List secrets

The secrets `list` command provides a list of all secrets available for an organization. The following fields are available:

- `Name`
- `Scope`
- `Type`
- `Value`
- `Environment Override Values`

Note that the `value` field will contain a placeholder value unless the `user` scope was specified when the secret was set.

Run the command below to list a site’s secrets:

`terminus secret:org:list`

```
terminus secret:org:list <org>

 ------------- ------------- ---------------------------
  Secret name   Secret type   Secret value
 ------------- ------------- ---------------------------
  secret-name   env           secrets-content
 ------------- ------------- ---------------------------
```

`terminus secret:org:list`

```
terminus secret:org:list <org> --fields="*"

 ---------------- ------------- ------------------------------------------ --------------- -----------------------------
  Secret name      Secret type   Secret value                               Secret scopes   Environment override values
 ---------------- ------------- ------------------------------------------ --------------- -----------------------------
  foo              env           bar                                        web, user
  foo2             runtime       bar2                                       web, user
  foo3             env           dummykey                                   web, user       live=sendgrid-live
 ---------------- ------------- ------------------------------------------ --------------- -----------------------------
 ```

#### Delete a secret

The secrets `delete` command will remove a secret and all of its overrides.

Run the command below to delete a secret:

```
terminus secret:org:delete <org> <secret-name>

[notice] Success

```

### Help

Run `terminus list secret` for a complete list of available commands. Use terminus help <command> to get help with a specific command.

## Use Secrets with Integrated Composer

You must configure your private repository and provide an authentication token before you can use the Secrets Manager Terminus plugin with Integrated Composer.


### Mechanism 1: Oauth Composer authentication

#### GitHub Repository

1. [Generate a Github token](https://docs.github.com/en/authentication/keeping-your-account-and-data-secure/creating-a-personal-access-token). The Github token must have all "repo" permissions selected.

    NOTE: Check the repo box that selects all child boxes. **Do not** check all child boxes individually as this does not set the correct permissions.

    ![image](https://user-images.githubusercontent.com/87093053/191616923-67732035-08aa-41c3-9a69-4d954ca02560.png) 

1. Set the secret value to the token via terminus: `terminus secret:site:set <site> github-oauth.github.com <github_token> --type=composer --scope=user,ic`

1. Add your private repository to the `repositories` section of `composer.json`:

    ```json
    {
        "type": "vcs",
        "url": "https://github.com/your-organization/your-repository-name"
    }
    ```

    Your repository should contain a `composer.json` that declares a package name in its `name` field. If it is a WordPress plugin or a Drupal module, it should specify a `type` of `wordpress-plugin` or `drupal-module` respectively. For these instructions, we will assume your package name is `your-organization/your-package-name`.

1. Require the package defined by your private repository's `composer.json` by either adding a new record to the `require` section of the site's `composer.json` or with a `composer require` command:

    ```bash
    composer require your-organization/your-package-name
    ```

1. Commit your changes and push to Pantheon.

#### GitLab Repository

1. [Generate a GitLab token](https://docs.gitlab.com/ee/user/profile/personal_access_tokens.html). Ensure that `read_repository` scope is selected for the token.

1. Set the secret value to the token via Terminus: `terminus secret:site:set <site> gitlab-oauth.gitlab.com <gitlab_token> --type=composer --scope=user,ic`

1. Add your private repository to the `repositories` section of `composer.json`:

    ```json
    {
        "type": "vcs",
        "url": "https://gitlab.com/your-group/your-repository-name"
    }
    ```

1. Require the package defined by your private repository's `composer.json` by either adding a new record to the `require` section of the site's `composer.json` or with a `composer require` command:

    ```bash
    composer require your-group/your-package-name
    ```

1. Commit your changes and push to Pantheon.

#### Bitbucket Repository

1. [Generate a Bitbucket oauth consumer](https://support.atlassian.com/bitbucket-cloud/docs/use-oauth-on-bitbucket-cloud/). Ensure that Read repositories permission is selected for the consumer. Also, set the consumer as private and put a (dummy) callback URL.

1. Set the secret value to the consumer info via Terminus: `terminus secret:site:set <site> bitbucket-oauth.bitbucket.org "<consumer_key> <consumer_secret>" --type=composer --scope=user,ic`

1. Add your private repository to the `repositories` section of `composer.json`:

    ```json
    {
        "type": "vcs",
        "url": "https://bitbucket.org/your-organization/your-repository-name"
    }
    ```

1. Require the package defined by your private repository's `composer.json` by either adding a new record to the `require` section of the site's `composer.json` or with a `composer require` command:

    ```bash
    composer require your-organization/your-package-name
    ```

1. Commit your changes and push to Pantheon.

### Mechanism 2: HTTP Basic Authentication

You may create a `COMPOSER_AUTH json` and make it available via the `COMPOSER_AUTH` environment variable if you have multiple private repositories on multiple private domains.

Composer has the ability to read private repository access information from the environment variable: `COMPOSER_AUTH`. The `COMPOSER_AUTH` variables must be in a [specific JSON format](https://getcomposer.org/doc/articles/authentication-for-private-packages.md#http-basic). 

Format example:

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

`terminus secret:site:set ${SITE_NAME} COMPOSER_AUTH ${COMPOSER_AUTH_JSON} --type=env --scope=user,ic`
```
