# Stanley-Bot
Stanley is a custom SMS messaging platform to prototype early services, products, and content.
For more about this, read the article 
[Chatbots: Your Ultimate Prototyping Tool](https://medium.com/@ideo/chatbots-ultimate-prototyping-tool-e4e2831967f3)

![bots](https://cdn-images-1.medium.com/max/2000/1*B95neDsqeB7pJ9XXGhsRNg.jpeg)

##Step 1
Get this code. For an overview on how the repo is organized:
* Actions: this is where you can find all the bot actions - response.php (which will be triggered by Twilio SMS API), deleteMessage.php (which you can use to manage messages), conversation.php (which is the page that lists the history of a conversation for a specific number)
* Assets: where stylesheets, images, and scripts are saved
* Config: all configuration files - such as config.php (where you will need to edit your project variables), installDB.php (which is the script that will install the database), messages.php (is where all the templated answers are saved), meta.php (where you can change the page headers).
* Includes: where all scripts, functions, and dependances are stored


##Step 2
Create an account on Twilio, and get your own phone number. Note: at some point you might be asked to add credit to your number in order to keep sending / receiving messages.

##Step 3
Create an account on Heroku, and a new app (you don’t necessarily need Heroku to host your bot, this is just a simple way we used to get started) Give it a name and pick a URL for your app. If you need to password-protect your app, here is how you can do it.

##Step 4
In the Heroku dashboard under “Resources”, install the add-on ClearDB. To manage the database, you’ll need to install a client such as Sequel Pro.

##Step 5
In the Heroku Dashboard under “Deploy”, link you Github repository to your Heroku app. Set it up as “automatic deploys” - so any time you commit new code to Github you are automatically live with the changes.

##Step 6
In the Heroku Dashboard under “Resources”, click on ClearDB and retrieve the information regarding your database: host, user name, password, database name. Type these in your Sequel Pro, and once you are connected install your dabase launching this script http://YOUR_ HEROKU_INSTANCE/config/installDB.php. Once the database is installed, you can delete installDB.php from your repo.

##Step 7
Now, create a private slack channel for you and your team. Invite to the channel everyone that should be involved in the conversations. 

##Step 8
Open config.php file in your code, and edit the variables with all the details regarding your app (list here).

##Step 9
Edit Twilio programmable SMS and change the URL (action/response.php)

##Step 10
Commit your code to github and you are good to go.
