#!/usr/bin/env bash
./vendor/doctrine/orm/bin/doctrine orm:convert-mapping annotation ./data --force --namespace="App\\Entity\\" --from-database
./vendor/doctrine/orm/bin/doctrine orm:generate-entities ./data --generate-annotations=true

# -- filter Entity