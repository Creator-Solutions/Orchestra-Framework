<h1 align="center">
 Orchestra Framework
</h1>
<p align="center">
 Author: Creator Solutions - Owen Burns 
</p> 
<p align="center">
 <img src="https://owenburns.co.za/Orchestra/content/ink&quil.svg"/>
</p>

<p align="center">Self built practice project that transformed into an<br>ever-growing production used codebase that jumpstarts any projects</p>   
<br/><br/><br/>

## About this project
This framework was built as a practice project to understand concepts of more famous frameworks such as Symfony, which inspired this project, and Laravel. After building and improving the codebase, it was decided to build this project as a standalone framework to improve the management and creation of other Web API projects. As of October 2023, the project only supports POST request, and Web API requests. The framework, however, will only be support web api's and not any web based projects.   
<br/><br/>

## Using the framework
Due to the nature of the project not being an actual package to install, the project can be cloned as a default project. The following git commands can be used to create a new project and create a new repo without affecting the current base repo of the framework itself:      
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

## How to begin building your API
Building the API is the easy part. Navigating the framework, that's another ball-game, especially if you don't know how it works. In this section I'll explain the only changes that you need to make.  

<br/>

### Creating the Routes file:
If you take a look at the ```index.php``` file, you'll notice there is an import for ```core/config/routes.php```, but when you look in the config folder, there is no ```routes.php``` file. This is where you come in. Now before going ham on the routes.php file, there is some format you have to follow:  

<br/>

**Step 1: Setting up the routes file**
First things first, we need to add a couple of imports:  

<br/>

1 **Controllers:**
We will get back to the controllers in a little bit, but for now, create a controller class for instance ```AuthController.php``` in the ```Controllers``` folder. For instance:
```php
<?php

class AuthController{

    //Logic goes here...

}
```

1 **routes.php**:
Now we have our controller class. Now it's time to create the routes file. This file is where we will be generating all of our routes that the application will process.   <br />
First things first, we import our controller class we just made:
```php

<?php

include_once(dirname(__DIR__).'/Controllers/AuthController.php');

```
<br />
After that we import the Router class from the framework.   

<br />

```php
use Orchestra\Routing\Router;
```
<br />

After we have all of our imports we create the class like follows:
```php
class RouteCollection {
    
    /**
     * @var Router
     */
    private Router $router; 

    /**
     * @var array
     */
    private array $routes;

    public function __construct()
    {
        $this->router = new Router();

        $this->router->add('/auth', ['_controller' => AuthController::class, '_callback' =>'login']);
    }

    public function getRouteCollection():array{
        $this->routes = $this->router->getAll();

        return $this->routes;
    }
}
```
<br />

Unfortunately as of now we do not automatically generate this class with our framework, but will defintely be in our next update. However, onward...  

<br />

Let's take a look at the constructor.  <br />

We have this line of code 
```php 
$this->router->add('/auth', ['_controller' => AuthController::class, '_callback' =>'login']);
```  
<br/>

So let's break it down.  <br/>
```php
$this->router->add()
```  
<br/>

this is the easy part, we add a new object to our router array. Kids stuff. Now let's look at the format of the actual array object.  <br />
So the 
<br />
```php
'/auth'
```
<br/>

part, that will be the API 'accessor' or 'name', which means, a certain part of the url for instance ```'https:domain.com/auth/'``` would have the accessor. Pretty straight forward, after that we call this part  
<br/>
```php 
['_controller' => AuthController::class, '_callback' =>'login']
```  
<br/>
the    
<br/>
```php
'_controller'
```




