<p align="center">
    <img src="https://user-images.githubusercontent.com/24455386/75344693-6ff93680-589b-11ea-93bd-25ffebf62d74.png"/>
</p>
<p align="center">
    <img src="https://sonarcloud.io/api/project_badges/measure?project=adrien-chinour_ellias&metric=alert_status"/>
</p>

# Ellias : Email Alias Manager

## Introduction

Ellias is an open source project based on Symfony for email aliases. Add your email provider and
manage alias, import/export aliases easily.

## Installation

Build project and start docker environment
```bash
docker-compose build
docker-compose up -d
docker exec ellias_php composer setup
yarn install
```

Create a new admin user
```shell script
docker exec -it ellias_php bin/console app:user:create
```
