# Terminus Customer Secrets Plugin

[![CircleCI](https://circleci.com/gh/pantheon-systems/terminus-customer-secrets-plugin.svg?style=shield)](https://circleci.com/gh/pantheon-systems/terminus-customer-secrets-plugin)
[![Terminus v2.x - v3.x Compatible](https://img.shields.io/badge/terminus-2.x%20--%203.x-green.svg)](https://github.com/pantheon-systems/terminus-plugin-example/tree/2.x)
[![Early Access](https://img.shields.io/badge/Pantheon-Early_Access-yellow?logo=pantheon&color=FFDC28)](https://pantheon.io/docs/oss-support-levels#early-access)

A plugin to handle Customer Secrets via Terminus.

NOTE: This is still a WORK IN PROGRESS, this plugin is NOT FUNCTIONAL yet.

## Configuration

These commands require no configuration.

## Usage
* `terminus customer-secrets:list`
* `terminus customer-secrets:set`
* `terminus customer-secrets:delete`

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
