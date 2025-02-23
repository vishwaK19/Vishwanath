News Api Documentation
To develop this task i have used laravel 11 with sanctum as per the documentation is given.
Note: Before to test this task make sure to add api keys for the news and email smtp details.
**User Registaration:**
http://localhost/api/register. - This route is developed to create the user in the database. To store the data to the database i have added the validation to revent cyber attacks. I have also added uuid to the table so that we can access the user by uuid only to prevent the other user access by changing the id. Also added confirm validation rule with 'The password must contain at least one uppercase letter, one lowercase letter, one number, and one special character (@, #, $, *).'. Here the mail id unique to prevent duplicate registration. Passwor will be stored in database using Hash method

http://localhost/api/login. - 
