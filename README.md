# Terminus Secrets Manager Plugin

[![CircleCI](https://circleci.com/gh/pantheon-systems/terminus-customer-secrets-plugin.svg?style=shield)](https://circleci.com/gh/pantheon-systems/terminus-secrets-manager-plugin)
[![Early Access](https://img.shields.io/badge/Pantheon-Early_Access-yellow?logo=pantheon&color=FFDC28)](https://pantheon.io/docs/oss-support-levels#early-access)

A plugin to handle Secrets via Terminus.

NOTE: This is still a WORK IN PROGRESS, this plugin is NOT FUNCTIONAL yet.

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

### Deleting secrets

Use `terminus secret:delete <site> <secret_name>` to delete a secret for a given site:

```
terminus secret:delete <site> foo

[notice] Success

```

## Installation

To install this plugin using Terminus 3:
```
terminus self:plugin:install terminus-customer-secrets-plugin
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
