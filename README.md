## About This Project

Using Laravel to build a mini-aspire API: allows authenticated users to go through a loan application and make weekly payment.

## Docker and Laravel Sail

This project is using Laravel 9, and can quickly be started by running on Docker with the support of Laravel Sail package.

The step to start this project on your machine:

1. Insall Sail package: **composer require laravel/sail --dev**
2. Run **./vendor/bin/sail up** to start the application
3. The file **docker-compose.yml** and **docker/7.4/Dockerfile** can be customize to fit your need. After changing the file, run **sail build --no-cache** to rebuild the application with new settings

## API Endpoints:
The POSTMAN collection has more details of what parameters are sent with each endpoint.
- POST **/api/login**: Log user in with email/password
- GET **/api/users**: Get user info
- POST **/api/loan**: Apply for a new loan with the logged in user
- GET **/api/loan/{loanID}**: Get details of a loan with loanID
- POST **/api/loan/{loanID}/approval**: Approve the loan by an admin
- POST **/api/loan/{loanID}/payment**: Make monthly payment


## File parameters:

- **name**: The name for that file
- **provider_id**: The id of the provider
- **file**: The file object (image or video files only)


## Models:

- **providers** to store all the providers
    
    |       |  |
    | ----------- | ----------- |
    | **id**      | The id of provider       |
    | **name**   | The name of provider        |  

- files to store all the uploaded files

    |       |  |
    | ----------- | ----------- |
    | **id**      | The id of file       |
    | **name**   | The name of file        |
    | **filepath**   | The path to the file on hosting        |
    | **thumb**   | The path to the file on hosting (if file is video)        |
    | **type**   | The extension of the file        |
    | **provider_id**   | The id of provider - foreign_key        |  
    
- **providers** and **files** have One-To-Many relationship


## PHPUnit tests

Have PHPUnit test class **FilesInteractionTest** to do testing on 4 features:

- **test_can_get_providers_list**: Test to see if can retrieve providers list
- **test_can_get_files_list**: Test to see if can retrieve files list
- **test_required_fields_when_create_file**: Test to see if fields **required** is working
- **test_can_create_file_complete**: Test to see if a file can go through all processes and successfully created


## Factory and seeders

Prepared seeder file to run **php artisan db:seeder** after migration


## Files filter

Files list can be filter by 2 attributes: **type** and **date**

http://localhost:8080/api/files?type=jpg

http://localhost:8080/api/files?date=2021-02-26


