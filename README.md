![logo ellias](.github/assests/logo.png)

# Ellias : Email Alias Manager

## Introduction

Ellias is an open source projetc based on Symfony. Ellias is an email alias manager. Add your email provider and
manage alias with tags and import/export aliases easily.

## Installation

Clone this repo:
```bash
git clone https://github.com/adrien-chinour/ellias.git
cd ellias
```

Create `.env` file:
```bash
# for default settings
cp .env.dist .env
```

Install all dependencies:
```bash
composer install
yarn install
```

Create database, by default it will create a sqlite db on `var` folder:
```
bin/console doctrine:database:create
bin/console doctrine:migrations:migrate
```

Create a user with the `app:user:create` command:
```bash
bin/console app:user:create
# enter username and password for user
```

You can now open your browser and log in.

## Usage

## Add provider
