# CoToDo
[![GitHub issues](https://img.shields.io/github/issues/CoToDo/CoToDo.svg)](https://github.com/CoToDo/CoToDo/issues)
[![Build Status](https://travis-ci.org/CoToDo/CoToDo.svg?branch=master)](https://travis-ci.org/CoToDo/CoToDo)
[![SonarCloud QualityGate](https://sonarcloud.io/api/project_badges/measure?project=cotodo%3Acotodo&metric=alert_status)](https://sonarcloud.io/dashboard?id=cotodo%3Acotodo)
[![SonarCloud Dulpicated Lines](https://sonarcloud.io/api/project_badges/measure?project=cotodo%3Acotodo&metric=duplicated_lines_density)](https://sonarcloud.io/dashboard?id=cotodo%3Acotodo)


Project built using symfony.

DEMO version can be found here: http://cotodo.herokuapp.com

## Installation

Install dependencies: 
```
composer install
```

Create database: (SQLite driver must be installed ```sudo apt install php7.1-sqlite3```) 
```
php bin/console doctrine:database:create
```

Update database schema:
```bash
php bin/console doctrine:migrations:migrate
```
Run server:
```
php bin/console server:run
```

## Docker setup

```bash
docker build -t cotodo/cotodo .
docker run -p 80:80 cotodo/cotodo
```

## Test Data

To add testing data to CoToDo you need to create account. Then login and in right dropdown menu click on Test Data.
Accounts of Testing Data are family members of House Lannister and House Greyjoy. Mail ends with '@co.todo'. Passwords for all test users is 1234. Typical user jaime@co.todo or euron@co.todo</p>