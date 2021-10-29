# CRUD-php-panel
1 file to render an entire CRUD panel

#### requirements
- minimum php 5.4 (tested)
- mysql

### functionalities
- create new records
- read records
- update records
- delete records
- filter records by bar
- paginate records

## Usage
```php
//include the file
include_once "./CRUD.php";

//initialize the CRUD panel 
$CRUDS = new CRUD(
//all the tables you want to show in array
      array(
        "users",
        "comments",
        "posts"
      ),
//your database connection link, mysqli
        $database_connection_link,
//the location at which the CRUD panel is rendered, in future this will be removed
        "localhost:8080",
//the database scheme 
        "db"
    );
```
