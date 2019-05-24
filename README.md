# SMS-API
First clone the repo with this command.

	$ git clone git@github.com:TuRaMbARr/SMS-API.git
After that go to new directory

	$ cd SMS-API

After that you must install the project dependencies

	$ composer install

Open **.env** file and replace *ReplaceYourMysqlUser* and *ReplaceYourPassword* with your MySQL database credentials.

Create and setup MySQL database with

	$ php bin/console doctrine:database:create
	$ php bin/console doctrine:migrations:migrate
	
make sure that your *redis-server* is up.
Then you can run the server. for example for running server on port 8000 
		
	$ php bin/console server:start 8000 && php ResendTask.php


#### ResendTask.php 
This script is for re-sending requests for rejected messages and communicate with main server through *redis-server*

#### Log file
Log data with generated in `app.log`.
