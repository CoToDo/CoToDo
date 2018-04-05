# CoToDo
Projekt založený v Symfony pro webovou stránku.

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

