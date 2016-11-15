MongoDB Wrapper
===================

This class has been built with the goal of simplifying basic CRUD functions for use with [MongoDB](https://www.mongodb.com/).

----------
Setup
=======

###MongoDB
Installing MongoDB on your machine is as simple as doing `install mongodb` in your Package Manager.
> **i.e**
>	MacOSX - `brew install mongodb`
>	Centos - `yum install mongodb`
>  Ubuntu - `apt-get install mongodb`

If you are unsure, there are guides available to assist you in the installation.
>[Install on Linux][2]
>Install MongoDB Community Edition and required dependencies on Linux.

>[Install on OS X][3]
>Install MongoDB Community Edition on OS X systems from Homebrew packages or from MongoDB archives.

>[Install on Windows][4]
>Install MongoDB Community Edition on Windows systems and optionally start MongoDB as a Windows service.
> 

###PHP-MongoDB
In order to setup PHP to work with MongoDB, you will need to download and install the `php**-mongodb` [driver][1] for your version of PHP.

> **Note:**

> Based on which OS you are using, your installation may differ.
> Please refer to [this][2] guide to install the driver for your version of PHP on your OS
> 
> For OSX users, you may receive a `dylib` error when attempting to run a PHP script which utilises the php-mongodb driver.
> If this is the case with you, try the following to resolve the issue.
> 
> - Run `brew reinstall --build-from-source php**-mongodb`  (where ** is the version of PHP)
> - Run `brew update` and attempt to reinstall.
> - Run `brew doctor`, and fix as many issues as possible then attempt to reinstall.
> [Github Issue Page][5]

To confirm that you have the PHP MongoDB driver installed successfully, you can run the below command in your Terminal / Command Prompt to validate that your PHP Installation can see your PHP-MongoDB Driver

`php -i | grep mongodb`

You should receive output similar to the below;

> mongodb
> mongodb support => enabled
> mongodb version => 1.1.9
> mongodb stability => stable
> mongodb.debug => no value => no value


#Connecting

Include the class in your project:
`include('/path/to/class/db.php');`

Build your array of Connection Parameters:
```php
	$settings = array(
	    'host' => 'localhost',
	    'user' => 'user',
	    'pass' => 'password',
	    'port' => '27017',
	    'db' => 'test',
	    'connectionName' => 'default',
	    'connectionOptions' => array(),
	);
```

The Db class Constructor accepts 1 argument, which is simply an array of settings which are required to connect to your database.

All fields are mandatory, with the exception of `connectionName` & `connectionOptions` (which simply needs to be an empty array).

Connect to MongoDB;

```php

$Db = new Db($settings);

```

# Inserting

Inserting data is as simple as constructing an array, and calling the `interact()` function;

```php
$query = array(
    'dbname' => 'users.dfc_users',
    'type' => 'insert'
);


$users = array();

$users[] = array(
    'name' => 'Peter Griffin',
    'age' => intval(40),
    'phone' => '07711001001',
    'status' => 'Married',
    'mental_capacity' => 'Child'
);

$users[] = array(
    'name' => 'Glen Quagmire',
    'age' => intval(38),
    'phone' => '07711001002',
    'status' => 'Single',
    'dependants' => 1092
);

$users[] = array(
    'name' => 'Joe Swanson',
    'age' => intval(42),
    'phone' => '07711001003',
    'status' => 'Married',
    'occupation' => 'Police Officer'
);

$query['data'] = $users;

$Db->interact($query);

```
Breaking down the above example, you will see that the array is constructed with two compulsary fields, required to perform the insert;

```php
$query = array(
    'collection' => 'users.user_details',
    'type' => 'insert'
);
```

When the array is constructed, we pass in the collection we are inserting into, and the 'type' of our 'interaction'.

In this example, we have specified the Database `users` with the Table `user_details`, by defining the `collection` field as `users.user_details`.

The 'type' has also been set to `insert` specifying that we are going to be inserting whatever is in the `data` field, into the specified collection.

Next we construct the data which will be inserted into the database, and then set the `$query['data']` variable with it. (You will notice one slight problem with the data being set; but let's not worry about that for now.)

Then, to perform the 'interaction', we call the function with our `$query` array.

# Querying Data

Querying data can be done in two ways, `queryAll()` and `query()`.

## queryAll()

The easiest one to use is `queryAll()`

This accepts one parameter, which is simply the collection you want to query.

```php
$db->queryAll('users.user_details');
```

This will return an array of all of the items in the the specified collection.
If you run a `var_dump()` our previously created table, it should look similar to this;

```bash

array(3) {
  [0] =>
  class stdClass#7 (6) {
    public $_id =>
    class MongoDB\BSON\ObjectID#6 (1) {
      public $oid =>
      string(24) "5829cb2d6284f4bad0394071"
    }
    public $name =>
    string(13) "Peter Griffin"
    public $age =>
    int(40)
    public $phone =>
    string(11) "07711001001"
    public $status =>
    string(7) "Married"
    public $mental_capacity =>
    string(5) "Child"
  }
  [1] =>
  class stdClass#9 (6) {
    public $_id =>
    class MongoDB\BSON\ObjectID#8 (1) {
      public $oid =>
      string(24) "5829cb2d6284f4bad0394072"
    }
    public $name =>
    string(13) "Glen Quagmire"
    public $age =>
    int(38)
    public $phone =>
    string(11) "07711001002"
    public $status =>
    string(6) "Single"
    public $dependants =>
    int(1092)
  }
  [2] =>
  class stdClass#11 (6) {
    public $_id =>
    class MongoDB\BSON\ObjectID#10 (1) {
      public $oid =>
      string(24) "5829cb2d6284f4bad0394073"
    }
    public $name =>
    string(11) "Joe Swanson"
    public $age =>
    int(42)
    public $phone =>
    string(11) "07711001003"
    public $status =>
    string(7) "Married"
    public $occupation =>
    string(14) "Police Officer"
  }
}

```

## query()

The query function can be as simple or as complex as you would like it to be, dependant on what you require.

The query call requires 3 parameters; `$collection`, `$filter`, `$options`
and can be called like so;

`$Db->query($collection,$filter,$options)`

The first parameter passed in, is `$collection`. This is the same as in the previous examples, where it is a string representation of the `database.table`.

The second parameter passed in is `$filter`. This is where you will add all of what would be the `WHERE` clauses, only a little differently... In an array style Markup.

The third parameter passed in is `$options`. This is where you define what would be your `SELECT` statement, and sorting preferences, also in an array style Markup.

### `$filter` Operations

#### Fields

Filtering by fields will operate in the same way as an SQL query will through the use of the `WHERE` clause. 
For example, the following SQL;

```sql
...
WHERE
	status = 'active';
```

would be defined in MongoDB as;

```php
$filter = array(
	'status' => 'active'
);
```

#### Operators

Query operators, such as Greater Than (`$gt`), Less Than (`$lt`), Logical AND, & Logical OR work in the same way. However, they are embeded as arrays themselves.
Some examples would be;

```php

// Examples of Greater Than & Less Than Operators

$filter = array(
	'age' => array(
		'$gt' => 10
	),
	'age' => array(
		'$lt' => 50
	)
);

// Example of Logical AND

$filter = array(
	'status' => 'active',
	'town' => 'Aylesbury'
);

// Example of Logical OR

$filter = array(
	'$or' => array(
	'status' => 'active',
	'town' => 'Aylesbury'
	)
);
```

### `$options` parameters

#### Selecting Columns

In order to limit the columns which are returned from your query, you will need to set the `projection` option, with an array of column names you would like to show with a boolean (1 or 0) value.
e.g.
```php
$options = array(
	'projection' => array(
		'name' => 1,
		'age' => 1,
		'_id' => 0
	)
);
```
This will return us with only the `name` and `age` columns. By default, *if you do not explicitly remove the `_id` column, it will always be returned*.


#### Sorting

To specify an order for the result set, you simply need to add the `'sort'` option to your options array, and specify what you would like to sort results by.
You define this by specifying a field to sort by, and assigning either a `1` (ascending) or `-1` (descending) to the field;
e.g.
```php
	$options = array(
		'sort' => array(
			'age' => 1
		)
	);
```

For a full list of query options you can define in your applications. Please consult the PHP.net manual [here][6]


# License

This MongoDB Wrapper class is open-sourced software licensed under the [MIT license][7].


  [1]: http://php.net/manual/en/set.mongodb.php
  [2]: https://docs.mongodb.com/manual/administration/install-on-linux/ "Linux"
  [3]: https://docs.mongodb.com/manual/tutorial/install-mongodb-on-os-x/ "OSX"
  [4]: https://docs.mongodb.com/manual/tutorial/install-mongodb-on-windows/ "Windows"
  [5]: https://github.com/Homebrew/homebrew-core/issues/6236
  [6]: http://php.net/manual/en/mongodb-driver-query.construct.php
  [7]: https://opensource.org/licenses/MIT
