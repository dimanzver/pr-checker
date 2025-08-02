#!/usr/bin/env bash

title=$1
url=$2
notify_title="Конфликт в PR $title"
pressed_button="$(notify-send "$notify_title" --action "to_github=Перейти в Github")"

if [[ "$pressed_button" == "to_github" ]]; then
  google-chrome-stable "$url"
fi
