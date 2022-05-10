#!/usr/bin/env bats

@test "run hello command" {
  run terminus hello
  [[ $output == *"Hello, World!"* ]]
  [ "$status" -eq 0 ]
}
