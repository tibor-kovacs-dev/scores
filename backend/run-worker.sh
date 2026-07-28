#!/bin/bash
php artisan queue:work --queue=default --sleep=3 --tries=3 --timeout=90