# ToDo-List

ToDo list is a simple web application 

### Features
* Pick a date from callendar
* Add new work
* Update work
* Edit work
* Delete work

### Technologies used:
* HTML5
* SCSS
* VueJs, Vuetify
* PHP 7.1

### Require Server

* Only support with domain or sub domain (not support alias or directory)
* PHP 7.1 or later
* For developer: NodeJs >= 12.8 

### Demo 
website: http://todo-txd.tk

### Development
- Setup 
```
composer install

yarn 
or
npm install
```
- Config server information: `configs/server.php` 
```php
<?php
define("BASE_URL", "http://todo.local");
define("PDO_DNS", "mysql:host=localhost;dbname=todo");
define("USERNAME", "root");
define("PASSWORD", "");
```
- Database sample:  `configs/todo.sql`