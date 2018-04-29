# REST API Test Task for job interviews

This is a very simple and basic CRUD API project serving as a test task for job interviews.

As such it is missing important features such as client authorization and refined installation process.

## Installation (Linux)

Clone or download the entire project, set up a virtual host (if necessary), change database username and password in /src/settings.php  
Download dependencies by using composer.json file in the root directory.  
You'll have to manually create an empty database with the specified name and a utf-8 charset.  
To add actual structure to the database, enter the root folder of the project and run  

    vendor/bin/doctrine orm:schema-tool:update --force

from the command line.

The API is now ready for use.

## Usage

API accepts both application/json as well as multipart/form-data  
All results are returned in plain JSON  
All calls are made to {sitename}/api/users  
Action depends on call method:

GET:  
Syntax: {sitename}/api/users?field1=value1[&field2=value2][...]  
Accepted fields: lastname, firstname, patronymic, phone, email  
Search by id is intentionally not supported  
Result: Returns a list of users for whom all listed fields have specified values  

POST:  
Syntax: requires all 5 fields: lastname, firstname, patronymic, phone, email  
Requirements: email should be unique for each user. phone and email fields are validated  
Result: Tries to add a new user and returns "Success" message or a pair "Error": "Error message"  
If there was an error, JSON array will also contain a list of missing and invalid fields under "Missing" and "Invalid" keys respectively  

PUT:   
Syntax: requires id and and 1 or more fields to update  
Requirements: email and phone fields are validated  
To get user's id use GET method  
Result: Tries to update user data and returns "Success" message or a pair "Error": "Error message"  
If any fields were invalid, JSON array will also contain a list of invalid fields under "Invalid" key  

DELETE: requires id  
To get user's id use GET method  
Result: Tries to delete a user and returns "Success" message or a pair "Error": "Error message"  