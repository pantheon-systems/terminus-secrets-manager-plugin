# Terminus Secrets Manager Plugin

Pantheon’s Secrets Manager Terminus plugin is key to maintaining industry best practices for secure builds and application implementation. Secrets Manager provides a convenient mechanism for you to manage your secrets and API keys directly on the Pantheon platform.

## Key Features

- Securely host and maintain Secrets on Pantheon
- Use private repositories in Integrated Composer builds
- Create and update secrets via Terminus
- Ability to set a `COMPOSER_AUTH` environment variable and/or a `Composer auth.json` authentication file with Terminus commands
- Ability to define site-specific Secrets (organization-specific Secrets are not yet supported)
- Ability to define the level of secrecy for each managed item (this determines which users can view the value of a Secret after entering it)
- Secrets are encrypted at rest

## Secrets Manager Plugin Requirements

Secrets Manager requires the following:

- A Pantheon account
- Integrated Composer
- Terminus

## Early Access

The Secrets Manager plugin is available for Early Access participants. Features for Secrets Manager are in active development. Pantheon's development team is rolling out new functionality often while this product is in Early Access. Visit the [Pantheon Slack channel](https://slackin.pantheon.io/) (or sign up for the channel if you don't already have an account) to learn how you can enroll in our Early Access program. Please review [Pantheon's Software Evaluation Licensing Terms](https://legal.pantheon.io/#contract-hkqlbwpxo) for more information about access to our software.

## Installation

Terminus 3.x has built in plugin management.

Run the command below to install Terminus Secrets Manager.

```
terminus self:plugin:install terminus-secrets-manager-plugin
```

## Terminus Secrets Manager Commands

### Set a Secret

The Secrets `set` command takes the following format:

- `Name`
- `Value`
- `One or more scopes`

The scope determines access to the Secret’s value. For example, if the scope is set to `users`, it will allow the user to view the Secret in Terminus. If the scope is set to `ic`, it makes the Secret available to the Integrated Composer build. 


Run the command below to set a Secret in Terminus:

```
terminus secret:set <site> <secret-name> <secret-value>

[notice] Success

```

```
terminus secret:set <site> file.json "{}" --type=file

[notice] Success

```

```
terminus secret:set <site> <secret-name> --scope=user,ic

[notice] Success

```

Note: If you do not include a `type` or `scope` flag, their defaults will be `env` and `ic` respectively.

#### Multiple Key Versions 

If you need multiple versions of a key for different environments or one key for development and one key for production:

Add all keys with a naming structure that helps when listing the keys:

```
keyName_production: value
keyName_development: value
```

or

```
keyName_multiDevName: value
```

NOTE: There are no key arrays in the json file; all keys are at the root. 

### List Secrets

The Secrets `list` command provides a list of all Secrets available for a site. The following fields are available:

- `Name`
- `Scope`
- `Type`
- `Value`

Note that the `value` field will be empty or contain a placeholder value unless the `user` scope was specified when the secret was set.

Run the command below to list a site’s Secrets:

`terminus secret:list` 

```
terminus secret:list <site>

 ------------- ------------- ---------------------------
  Secret name   Secret type   Secret value
 ------------- ------------- ---------------------------
  file.json     file          contents of a secret file
  secret-name   env           secrets-content
 ------------- ------------- ---------------------------
```

### Delete a Secret

The Secrets `delete` command will remove a Secret from all of its scopes.

Run the command below to delete a Secret:

```
terminus secret:delete <site> <secret-name>

[notice] Success

```

### Help

Run `terminus secret list` for a complete list of available commands. Use terminus help <command> to get help with a specific command.

## Use Secrets with Integrated Composer

You must configure your private repository and provide an authentication token before you can use the Secrets Manager Terminus plugin with Integrated Composer.

### GitHub Repository

1. [Generate a Github token](https://docs.github.com/en/authentication/keeping-your-account-and-data-secure/creating-a-personal-access-token). The Github token must have all "repo" permissions selected.

    NOTE: Check the repo box that selects all child boxes. **Do not** check all child boxes individually as this does not set the correct permissions.

    ![image](https://user-images.githubusercontent.com/87093053/191616923-67732035-08aa-41c3-9a69-4d954ca02560.png) 

1. Set the secret value to the token via terminus: `terminus secret:set <site> github-oauth.github.com <github_token> --type=composer --scope=user,ic`

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

### GitLab Repository

1. [Generate a GitLab token](https://docs.gitlab.com/ee/user/profile/personal_access_tokens.html). Ensure that `read_repository` scope is selected for the token.

1. Set the secret value to the token via Terminus: `terminus secret:set <site> gitlab-oauth.gitlab.com <gitlab_token> --type=composer --scope=user,ic`

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

### Bitbucket Repository

1. [Generate a Bitbucket oauth consumer](https://support.atlassian.com/bitbucket-cloud/docs/use-oauth-on-bitbucket-cloud/). Ensure that Read repositories permission is selected for the consumer. Also, set the consumer as private and put a (dummy) callback URL.

1. Set the secret value to the consumer info via Terminus: `terminus secret:set <site> bitbucket-oauth.bitbucket.org "<consumer_key> <consumer_secret>"--type=composer --scope=user,ic`

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

## Authentication

### GitHub

`github-oauth.github.com` is a magic token name for composer that authenticates all Github URLs with the credentials from the token you provide. There are several ["magic" variable names](https://getcomposer.org/doc/articles/authentication-for-private-packages.md#command-line-global-credential-editing), or you can choose "basic authentication" by providing a `COMPOSER_AUTH` variable.

### GitLab

`gitlab-oauth.gitlab.com` is a magic token name for Composer that authenticates all GitLab URLs with the credentials from the token you provide. There are several ["magic" variable names](https://getcomposer.org/doc/articles/authentication-for-private-packages.md#command-line-global-credential-editing), or you can choose "basic authentication" by providing a `COMPOSER_AUTH` variable.

### Bitbucket

`bitbucket-oauth.bitbucket.com` is a magic token name for Composer that authenticates all Bitbucket URLs with the credentials from the token you provide. There are several ["magic" variable names](https://getcomposer.org/doc/articles/authentication-for-private-packages.md#command-line-global-credential-editing), or you can choose "basic authentication" by providing a `COMPOSER_AUTH` variable.

### HTTP Basic Authentication

You must create a `COMPOSER_AUTH json` and make it available via the `COMPOSER_AUTH` environment variable if you have multiple private repositories on multiple private domains.

Composer has the ability to read private repository access information from the environment variable: `COMPOSER_AUTH`. The `COMPOSER_AUTH` variables must be in a [specific JSON format](https://doc.codingdict.com/composer/doc/articles/http-basic-authentication.html). 

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

`terminus secret:set ${SITE_NAME} COMPOSER_AUTH ${COMPOSER_AUTH_JSON} --type=env --scope=user,ic`
```
