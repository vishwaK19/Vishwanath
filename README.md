News Api Documentation
To develop this task i have used laravel 11 with sanctum as per the documentation is given.
**Note:** Before to test this task make sure to add api keys for the news and email smtp details.
**User Registaration:**
http://localhost/api/register(Post). - This route is developed to create the user in the database. To store the data to the database i have added the validation to revent cyber attacks. I have also added uuid to the table so that we can access the user by uuid only to prevent the other user access by changing the id. Also added confirm validation rule with 'The password must contain at least one uppercase letter, one lowercase letter, one number, and one special character (@, #, $, *).'. Here the mail id unique to prevent duplicate registration. Password will be stored in database using Hash method

http://localhost/api/login(Post). - Login route is created using the post method. while submitting the request users data will be santize. After validating the user access token will be created in database using laravel sanctum. After login in the response user unique id and auth key will be return. Which will be used while accessing any request.

http://localhost/api/logout(Post) - Logout route is created usinng the post method which will check that is athenticated user is there like auth key in the header then it will allow to logout and deletes all the tokens from the database related that user. Middleware auth:sanctum is added to filter that request which will check that the request have auth key or not if there is no auth key of bearer type then it will throw response as unathenticated access.

http://localhost/api/sendMail(Post) - To reset the password first user need to add email address. And that email id will be check in the database that the user email address is exist in database. if it exists then it will send mail to email address and in that there will be one link with token. 

http://localhost/api/reset(Post) - After clicking the reset link while submitting it data will be santized like token, email and password. Token and email id will be in the url or heade. After passing all the validation the data will be updated in the database of the password column.

**Article Management:**
**Note:** To fetch the artcle run command php artisan fetch:articles which is created by running command php artisan make:command FetchArticlesCommand. I have also added the code in console to fetch the artilce per hour. To fetch the article there is need to add api keys in env file.

For this i have created aritcleController and Article model and migration to store the data.

http://localhost/api/articles(get) - This route is ued to fetch the articles which is stored in the database with pagination by using laravel's default pagination method. I have also added middleware to this route so that only authenticated user can access this page for this there is need to pass auth key in the header and sanctum will do its work by veryifing its data. In the same api if the user pass search filter "keyword"-> this will fetch the title and content data, "date" -> this will search for the article by the date, "category" -> this will fetch data by category, "source" -> this will fetch the data by source with pagination.

http://localhost/api/showArticle/{articleId}(get) - in this route when the user click on the any list then article id will be passed to that route and perticullar data will be fetched from the database with all details. To perform this action user must be authenticated and pass the token in the haeder. Otherwise it will return message unauthenticated.

 **User Preferences:** 
 Note: User model, tabel and controller is created.
 http://localhost/api/user-preferences(post) - This route is protected by middleware sanctum which will first check the user authentication. If its true then it will allow the user to store the user preference and store in the database in array.

http://localhost/api/user-preferences/{userId}(get) - this route is also authenticated which will display prefernce of the user by uuid of the user. 

http://localhost/api/personalized-feed/{userId}(get) - This route is fetch the data as per the user preference which is store in the database. WHich is also auth user. I have added relationship between the user and userprefernece model. So that userid will be foreign id in the userepreference table. 

 **Data Aggregation**

I have created the cumtom command to fetch the article hourly in laravel there is need to add that code console.php. To fetch the command php artisan fetch:articles is used if incase any error is there it will be printed in the log file.

 





