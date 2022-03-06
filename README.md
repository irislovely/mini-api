## About This Project

Using Laravel to build a mini-aspire API: allows authenticated users to go through a loan application and make weekly payment.

## Flow description

An authenticated user, after logging in, can submit a loan application with the "amount" and "term" that he want.

By default, that loan will have an "approval" status of false (0), and admin (in this project: user with email admin@test.com) will need to hit the approve endpoint to activate that loan. If the loan is not approved, that user can't hit the payment endpoint. This simulates the real-life events, where a loan always need an approving process.

Loan and LoanPayment is split into 2 tables instead of merging into one, is for easy and better management, maintenance and programming.

In the LoanPayment table, when a user made a payment, the remaining amount to be paid will be updated accordingly, and if that payment amount is equal or bigger than the remaining, that mean the loan is paid off, and we'll set all the amount to 0 to complete that loan.

## Docker and Laravel Sail

This project is using Laravel 9, and can quickly be started by running on Docker with the support of Laravel Sail package.

The step to start this project on your machine:

1. Install Composer packages: **composer install**
2. Run **./vendor/bin/sail up** to start the application
3. The first time the application is up, all table migrations will be called, and also the seeder for Users table.

## API Endpoints:
The POSTMAN collection has more details of what parameters are sent with each endpoint.
- POST **/api/login**: Log user in with email/password
- GET **/api/users**: Get user info
- POST **/api/loan**: Apply for a new loan with the logged in user
- GET **/api/loan/{loanID}**: Get details of a loan with loanID
- POST **/api/loan/{loanID}/approval**: Approve the loan by an admin
- POST **/api/loan/{loanID}/payment**: Make monthly payment

## Models:

- **Loans** to store all the loans data
    
    |       |  |
    | ----------- | ----------- |
    | **id**      | The id of loan       |
    | **amount**   | The amount of loan        |
    | **term**   | The term of loan (in months)        |
    | **approval**   | The approval status of loan        |
    | **user_id**   | BelongsTo relationship with a User        |        

- **LoanPayment** to store the payment details of loans

    |       |  |
    | ----------- | ----------- |
    | **id**      | The id of payment       |
    | **weekly_payment_date**   | The date of weekly payment        |
    | **weekly_payment_amount**   | The amount of weekly payment        |
    | **payment_amount_remaining**   | The remaining payment amount of loan        |
    | **payment_amount_paid**   | The paid amount of loan        |
    | **total_weeks**   | The term of loan in weeks        |
    | **loan_id**   | BelongsTo relationship with a Loan        |    
    

## PHPUnit tests

This project has 2 Feature test classes **UserTest** and **LoanTest**, with the total of 13 tests.

Loan related test:
- **create new loan**
- **create new loan with invalid amount**
- **create new loan with invalid term**
- **get loan details**
- **admin approval**
- **admin approval with invalid data**
- **admin approval with invalid email**
- **loan weekly payment**
- **loan weekly payment not approved**

User related tests:
- **get user**
- **login with email**
- **login with invalid email**
- **login with incorrect password**
