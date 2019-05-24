# SMS-API
For running the project first clone it with
$)

# Files

StackEdit stores your files in your browser, which means all your files are automatically saved locally and are accessible **offline!**

## Create files and folders

The file explorer is accessible using the button in left corner of the navigation bar. You can create a new file by clicking the **New file** button in the file explorer. You can also create folders by clicking the **New folder** button.

## Switch to another file

All your files are listed in the file explorer. You can switch from one to another by clicking a file in the list.

## Rename a file

You can rename the current file by clicking the file name in the navigation bar or by clicking the **Rename** button in the file explorer.

## Delete a file

You can delete the current file by clicking the **Remove** button in the file explorer. The file will be moved into the **Trash** folder and automatically deleted after 7 days of inactivity.

## Export a file

You can export the current file by clicking **Export to disk** in the menu. You can choose to export the file as plain Markdown, as HTML using a Handlebars template or as a PDF.

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

