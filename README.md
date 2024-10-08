<h1 align="center">
 Orchestra Framework
</h1>
<p align="center">
 Author: Owen Burns
</p> 
<p align="center">
 <img src="https://owenburns.co.za/Orchestra/content/ink&quil.svg"/>
</p>

<p align="center">
 <img src="https://img.shields.io/github/repo-size/creator-solutions/Orchestra-Framework" />
</p>
<p align="center">
 <img alt="GitHub repo file count (file type)" src="https://img.shields.io/github/directory-file-count/creator-solutions/Orchestra-Framework">
</p>

<p align="center">Originally a personal practice project, this framework has evolved into a robust and scalable codebase,<br>designed to accelerate and enhance the development of any project.</p>   
<br/><br/><br/>

# About this project
This framework started as a custom-built solution for an e-commerce store before I had experience with popular frameworks like Laravel and Symfony. Over time, I realized that the unique way the framework was structured solved many challenges I encountered with vanilla PHP. It was then that I recognized its potential as a standalone framework, offering robust capabilities for managing and creating various web-based projects. Whether used as a full-stack codebase or solely as a RESTful API, it has grown into a powerful tool for developers.

As of October 2023, the framework was primarily focused on handling REST API requests. However, by July 2024, its capabilities have expanded to support popular frontend frameworks such as Angular, React, and Vue, in addition to its native Pulse templates. This allows developers the flexibility to choose their preferred tech stacks or leverage server-side rendering for building full-scale web applications. The framework's versatility opens up endless possibilities for development.
<br/><br/>

# Using the framework
Since this project is designed to serve as a default project rather than a traditional Composer package, it can be cloned directly to start a new project. Follow the steps below to clone the framework, create a new repository, and avoid affecting the base repository of the framework:      
<br/>

**Step 1: Cloning the project**
```
git clone https://github.com/The-Founders-Studio/Orchestra-Framework.git 'project_name'
```
__Replace 'project_name' (quotes included), with the name of the project you wish to build.__  

<br/>

**Step 2: Adding the new repo**  
```
git remote add 'repo_name' 'new_repo_url'
```
__Replace 'repo_name' and 'repo_url' to the name of the new repo you had made as well as the url of that repo__  

<br/>

**Step 3: Confirm it has been added**
```
git remote -v
```  

<br/>

**Step 4: Fetch any changes made from the new repo**
```
git fetch new_repo
```  

<br/>

**Step 5: Push codebase to new repo**
```
git push new_repo branch_name (main/master)
```
*If you receive any errors when pushing, make use of the force command when completing the initial push*
```
git push -f new_repo branch_name (main/master)
```  

<br/>

**Step 6: Remove the Orchestra framework Repo**
```
git remote set-url origin new_repo_url
```  

<br/>

**Step 7: Confirmed new repo is now origin**
```
git remote -v
```  

<br/>

## Setting up Routes
Setting up routes should a quick and easy job in order to get the ball rolling in the least amount of time. In Orchestra, there are a few ways routes can be used, observe:

### **1. The first step is to actually register the middleware with the endpoint. This can be done in the *api.php* file:**
   ```php
   Route::middleware('auth')->get('/test'); # http://domain.com/auth/test
   ```
   - The middelware, in this case ```auth``` is how we can group the endpoints together, should your project make use of duplicates.
   - The ```get()``` function takes a string parameter. This is the actual api endpoint we will be calling.
  
   We can have different variants of this single line to have more control over the endpoints.
<br></br>
   1.1 We can specify protected endpoints as well:
   ```php
   Route::middleware('dashboard')->getProtected('/home:Token');
   ```
   - In this case we use the function ```getProtected()```. This function contains more functionality than the normal ```get()```, as we now how  to do pre-request calculations. The function takes a string parameter as well with a ```:Value``` afterwards. This value is the header key that is required when the request is sent. 
<br></br>

   1.2 We can also specify get request with url request parameters:
   ```php
   Route::middleware('auth')->get('/test/{id}');
   ```
   - This will create a get request where the endpoint now has an added part. This will require a URL to look like this ```http://domain.com/auth/test/1```, __But this is only for GET requests__

<br></br>
### **2. The second step is defining the Router functions:**
     
   *The Router functions are created in your specific controllers. In this case we use the ``auth`` middleware, so our AuthController.php will look like this:*

```php
Router::post('/test', function (Request $req) {
   $val = $req->get('test') ?? "";

   return new JsonResponse(
      [
         'message' => 'success',
         'status' => true
      ],
      Response::HTTP_OK
   );
});
```
The ``Router`` class contains multiple functions that are provided for developers. Each ``GET``, ``POST``, ``PUT`` request methods have matching ``get``, ``post``, and ``put`` functions, each reference the specific request method that is expected when the endpoint is called.

Another **important** aspect is the endpoint used in the functions. They are case-sensitive. If the endpoint used in the controller, does not match the one registered in the ``api.php`` file, a 404 would be returned by defualt.

A get request method might look like this:
```php
Router::get('/test/{id}', function (Request $req, $id) {
   $val = $req->get('test') ?? "";

   return new JsonResponse(
      [
         'message' => 'success',
         'status' => true
      ],
      Response::HTTP_OK
   );
});
```
The parameter in the callback function is automatically handled by Orchestra, we're just passing a reference ``variable`` if you want to call it that. 

The parameter name that you pass to the endpoint must match the variable reference, example: if you use ``name``, then your Router function will look like this:
```php
Router::get('/user/{name}', function (Request $req, $name){
   return new JsonResponse(
      [
         'message' => 'success',
         'status' => true,
      ],
      Response::HTTP_OK
   );
});
```
   
Apart from REST APIs, Orchestra offers Server Side Rendering (SSR) with it's native Pulse templates. This allows developers to render HTML views directly from the controller.
```php
Router::get('/', function () {
   return (new Template())->view('welcome');
});
```

In this example the Route returns a home page. The view names, or the HTML file names, are case-sensitive, and must contain a ``.pulse.php`` extention, otherwise they will be ignored by the system.

One can also pass data to these views that can be dynamically rendered:
```php
Router::get('/', function () {
   $content = ['message' => 'This is a message'];
   return (new Template())->view('welcome', $content);
});
```
the key in the array can then rendered or access via interpolation
```html
 <p> {{ message }}<p>
```
<br></br>
## Database Management & Querying
Sonata is a custom-built ORM (Object-Relational Mapping) system integrated into the Orchestra PHP framework. It serves as a bridge between the application’s object-oriented models and relational database tables, simplifying database interactions by abstracting raw SQL queries.

### Step 1: Make a migration
The Migration feature in the Orchestra PHP framework enables developers to manage and version database schema changes programmatically. It simplifies the process of creating, modifying, and updating database tables over time, ensuring that the database structure stays in sync with application requirements as the project evolves.

**Simply run the CLI Command**
```BASH
php ./bin/serve make:migration create_user_table
```

this will create a new migration file in the ``app`` folder. 
```php
<?php

use Orchestra\Sonata\Schema\Schema;
use Orchestra\Sonata\Scheme\Scheme;
use Orchestra\interfaces\MigrationInterface;

return new class implements MigrationInterface
{

   public function build(): void
   {
      Schema::create('user', function (Scheme $table) {
         $table->id();
         $table->string('username');
         $table->integer('age');
         $table->timestamps();
      });
   }

   public function destroy()
   {
      Schema::destroyIfExists('');
   }
};
```
The ``build`` function is where you define the desired table layout. Using the migration interface, you can specify columns along with their respective data types or column types. The build function translates PHP class definitions into executable SQL queries, which are automatically handled and run by the framework, eliminating the need to manually write SQL statements for creating or altering tables. 
- The ``id`` field provides an auto incremented primary key.
- the ``timestamp()`` creates timestamp columns for creation and updates

The ``destroy`` function provides a way to safely remove tables from the database. It checks if the table exists before attempting to delete it, ensuring that schema rollbacks or cleanup processes are handled gracefully. This function is useful for reversing migrations or managing table lifecycles.

Once you have the desired table layout and columns: run the following CLI command
```BASH
php ./bin/serve migrate:migration up
```

if everything was setup correctly, you'll get the following logged to the console:
```BASH
Migrated: 20240921104715_create_user_table
```

### Step 2: Create the model
The model creation functionality allows you to easily define database models that interact with the corresponding database tables. A model in the framework acts as a bridge between the application’s business logic and the database, simplifying tasks like querying, inserting, updating, and deleting records.

To create a model run the command:
```bash
php ./bin/serve make:model User
```

This will create a User model that you can link to a table
```php
class User extends Queryable
{

   /**
    * @var string
    */
   protected static $table = '';

   protected $props = [];
}
```
The User model class inherits from the Queryable class, this allows you to access multiple methods that mimic raw SQL queries. Which can be used like this:

```php
User::create([]);
User::find(1);
User::delete(1);
```

we can even create complex queries such as 
```php
$user = User::where('age', '=', 12)->select('*');
```

## Caching Mechanisms
The caching mechanism in the Orchestra Framework is designed to enhance application performance by reducing the need for repetitive data retrieval operations. This system employs a file-based caching strategy, which allows for efficient storage and retrieval of frequently accessed data, minimizing database load and response times.

### The Env Properties
Within the ``.env`` file are 2 properties that can be set for caching, however, these are provided from the get-go.
```ENV
CACHE_TYPE=file
CACHE_FOLDER=/var/www/orchestra/cache
```
- The ``CACHE_TYPE`` for now mainly supports file caching. But in future release will be extended to Memory Caching and Database Caching
-  The ``CACHE_FOLDER`` is where the cached files are stored within the project. Currently it has been set as the same directory for the log files.

### Step 1: Implenting caching:
To create a new instance of the FileCache class
```php 
$cache = new FileCache();
```

then simply do the initial DB query using either the RecordBuilder class or Sonotra ORM:
```php
$user = User::where('age', '=', 12)->select('*');
```

and finally cache the result by calling the ``set()`` function
```php
$cache->set('user', $user) // returns bool
```

If the steps were followed, a file will be created that looks like this ``ee11cbb19052e40b07aac0ca060c23ee.cache``. And contains the content of the query that was done
```cache
a:2:{s:5:"value";a:1:{i:0;a:5:{s:2:"id";i:1;s:8:"username";s:13:"james cameron";s:3:"age";i:12;s:10:"created_at";s:19:"2024-09-21 18:23:23";s:10:"updated_at";s:19:"2024-09-21 18:23:23";}}s:7:"expires";i:1727384728;}
```

### Step 2: Retrieving the cached values:
Should we want to check if the specific key exists from the cached file, we simply use the ``get()`` function provided by the ``FileCache`` class
```php
 $userCache = $cache->get('user');
```
this will automatically locate the cache file and process the contents to retrieve the specific key. And if we do a return from an endpoint like this 
```php
return new JsonResponse(
      [
         'message' => 'success',
         'status' => true,
         'user' => $userCache,
      ],
      Response::HTTP_OK
   );
```
we get this JSON returned 
```JSON
{
    "message": "success",
    "status": true,
    "user": [
        {
            "id": 1,
            "username": "james cameron",
            "age": 12,
            "created_at": "2024-09-21 18:23:23",
            "updated_at": "2024-09-21 18:23:23"
        }
    ]
}
```
