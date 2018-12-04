# blog-api

Creating a blog api written in php. I'm using mysql and plan to use React for a front-end.


## Database set up

You can find an sql file in the project that will create the database tables for you.

Name the database whatever you would like as you will have to do some setup in /config/db_handler.php anyway.

## Endpoints

The api accepts json data and the different paths are:

#### Users

blog-api/users/create.php - This will be for the user signup page and requires username, email, and password.

blog-api/users/delete.php - Delete a user, requires id.

blog-api/users/read.php - List all users.

blog-api/users/read_one.php - Show one user, requires id.

#### Articles

blog-api/articles/create.php - Create an article, requires title and body.

blog-api/articles/delete.php - Delete an article, requires id.

blog-api/articles/update.php - Updates an article, requires an id, title and body.

blog-api/articles/read.php - list all articles.

blog-api/articles/read_one.php - Show one article, requires id.

#### Sessions

blog-api/sessions/login.php - This will log a user on giving them access to creating, deleting and updating articles. It creates a cookie authentications token which is then compared to a token stored in the sessions table. I set cookies to expire after a day and also set the sessions in the sessions table to delete after it's expired as well. 
