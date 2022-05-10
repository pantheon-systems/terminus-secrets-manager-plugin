#!/usr/bin/env bats

#
# confirm-install.bats
#
# Ensure that Terminus and the Composer plugin have been installed correctly
#

@test "confirm terminus version" {
  terminus --version
}

@test "get help on auth:hello command" {
  run terminus help auth:hello
  [[ $output == *"Say hello"* ]]
  [ "$status" -eq 0 ]
}
