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

1. The first step is to actually register the middleware with the endpoint. This can be done in the *api.php* file:
   ```php
   Route::middleware('auth')->get('/test');
   ```
    
   

