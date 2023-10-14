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




