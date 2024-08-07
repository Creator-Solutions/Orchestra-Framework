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

<p align="center">Self built practice project that transformed into an<br>ever-growing production used codebase that jumpstarts any project</p>   
<br/><br/><br/>

## About this project
This framework initially began as a practice project aimed at delving into the core concepts of renowned frameworks like Symfony and Laravel, which served as inspiration. Over time, as we refined and expanded the codebase, we recognized the opportunity to develop it into a standalone framework for enhanced management and creation of various Web API projects.

As of October 2023, the framework supports only POST requests and Web API requests. However, we're excited to announce that moving forward, we'll be extending our support beyond Web API development to encompass RESTful API development as well as server-side rendered templates. This expansion reflects our commitment to catering to a wider range of project needs and ensuring versatility in our framework's capabilities.
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

### Setting up your project to handle requests

<p>
 It's no new learning curve that all RESTful API's require some form of setup in order to handle requests sent from the frontend. During the improvements stage of the project, various improvements have been made on how the project is setup to handle these requests.
</p>

<br />

During the building phase of the project, the way endpoints were setup was long and stretched. The user had to define a middleware resource, something like
```auth``` depending on what that resource requires. and after defining the resource, the user would then have to link a specific controller, to a specific callback, which was a function in the controller. This could case some headaches, as the function's name was case sensitive, and you were very limited on what you could name your functions, as the url where the request was sent from, would need to match. In some cases your url would look like this ```https://domain.com/auth/registerUser```, it just doesn't look right. Therefore change were made


### Defining the middleware 
<br />
<p>
 Previously middleware was defined like this:
</p>

```php 
$this->router->add('/auth', ['_controller' => AuthController::class, '_callback' =>'login']);
```
<p>
 but now, we can define them like this :
</p>

```php 
Route::middleware('auth')->get('/login');
```

this new way of adding middlware resource can be done within our **api.php** file.
<p>
 Lets break some of the code down shall we:
</p> 
<br />
<p>
 This part of the code : 
</p> 

```php
Route::middleware('auth')
```

<p>
 Creates an element in a list, defined by a key based on the middleware resource you have provided. This allows us to create multiple resources, without having to worry about what endpoints are linked to them, as the endpoints will be added to a new list where the middleware resource would be the main key to retrieve them. You can even specify routes that have the same endpoint as long as they have different middleware resources pointing to them.
</p> 
<br />

<p>
 And then there is the second half of the code: 
</p> 

```php
get('/login');
```

The **get** method is what stores the endpoint under the middleware provided. This is all done under the hood for you by the framework without requiring more lines of code to be written.

### Defining the routes

Linking routes to the controller methods, required the user to create a controller class. The controller class would then have the method that the user defined when creating the middleware resources, i.e. ```php
some_code...'_callback' =>'login'])```. This feature has now gone under massive reconstruction to improve the quality of handling these resources. 

After finalizing these changes, routes can now be defined by implemented the various methods provided by the ```Router``` class. 
```php
Router::post('/login', function (Request $req) {
   $message = "This is a test request";

   return new JsonResponse(
      array(
         'message' => $message
      ),
      Response::HTTP_OK
   );
});
```

By passing ```php Request $req``` as a parameter, it also removes the requirement of having global class variables in order to retrieve request paremeters, headers and all sorts of values.
