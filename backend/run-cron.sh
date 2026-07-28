#!/bin/bash
while true; do
  php artisan schedule:work --verbose --no-interaction
  sleep 60
done