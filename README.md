# CRUD-php-panel
1 file to render an entire CRUD panel
Currently only in Dutch

#### requirements
- minimum php 5.4 (tested)
- mysql
- bootstrap version 4.6 minimum

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
$CRUD = new CRUD(
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
    $CRUD->renderPaneel();
```



### Current bugs:

At the moment if you have a header that you want to ouput before the CRUD panel, do it like this:

```php
    if($_SERVER['REQUEST_METHOD'] == "GET") {
        include($_SERVER['DOCUMENT_ROOT'] . '/includes/header.inc');
    }
    //then renderPaneel()
```
Footers should be rendered after the call to renderPaneel()

### Screenshots
![image](https://user-images.githubusercontent.com/10829524/139453827-16329a48-9906-461c-8d84-e2e4a5cc9c90.png)

![image](https://user-images.githubusercontent.com/10829524/139453862-55f42b24-1c36-4fc6-93eb-e8fc0f00d4d2.png)

