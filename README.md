
## About 

This is a test API project and specifically created as an assignment for a job. Please follow the instructions below to setup the project

## Sanctum 
The token are being created and validated by Sanctum

## Installation  

After cloning the repo please goto project directory and run 
    
    composer update
    
### Create Database

Please create the database in mysql and change following in .env 

    DB_HOST=127.0.0.1
    DB_PORT=3306
    DB_DATABASE=future
    DB_USERNAME=root
    DB_PASSWORD=

### Run Migrations

    php artisan migrate

### Configure SMTP for Emails
Currently I have used MailTrap for testing you can also use the by creating free account 

[Mail Trap Free Account ](https://mailtrap.io/public-api)

### Update Mail SMTP settings in .env
	MAIL_MAILER=smtp
	MAIL_HOST=smtp.mailtrap.io
	MAIL_PORT=2525
	MAIL_USERNAME=4c5a34d7469009
	MAIL_PASSWORD=d3566cc596d1db
	MAIL_ENCRYPTION=tls

### Run for the Serach indexing on command prompt

    php artisan scout:import "App\Chats"

### Scchedule Cron for Status Change
    
    "* * * * * cd /path-to-your-project && php artisan schedule:run >> /dev/null 2>&1"

### Run the following command to start server
    
    php artisan serve
    
   Will create and run server instance can be accessed on following url
    
    http://localhost:8000
    
### Please download the postman project to test the API
    
    https://github.com/rizwanaabbas/futuresupport/blob/76f5678a4cc2bfdc85b14df52a69e0091f7a8c82/Future.postman_collection.json
    

## Tools and Extensions Used

* Laravel 
* Sanctum for token
* Mailtrap for test email
* algolia for search indexing
* Postman for API endpoint testing (Postman project is attached)
