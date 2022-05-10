#!/usr/bin/env bats

@test "run auth:hello command without authentication" {
  run terminus auth:logout
  run terminus auth:hello
  [ "$status" -ne 0 ]
}

@test "run auth:hello command after authentication" {
  run terminus auth:login --machine-token="$TERMINUS_TOKEN"
  [[ $output == *"[notice] Logged in via machine token"* ]]
  run terminus auth:hello
  [[ $output == *"Hello, "* ]]
  [ "$status" -eq 0 ]
}
