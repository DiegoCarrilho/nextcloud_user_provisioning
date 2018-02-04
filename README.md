# nextcloud_user_provisioning
PHP for easy automation using the user provisioning api of nextcloud (and probably owncloud)
The API itself is described here: https://docs.nextcloud.com/server/12/admin_manual/configuration_user/user_provisioning_api.html
However it took me some thinking to see, how I could use the API with standard PHP as usually provided in shared hosting environments where the usual console line commands can not be used. To make it easier for others to use the API, I coded this class.

If this class can be useful for you - welcome. You may use it without any warranty. You may also change and improve the code for your own purposes as you like.
I would be happy to receive an email to gitlab@dewest.net if this class is helpful for you, but no hassle with that.

Please accept that I can only give very limited support for this class.

In the repository you find the following files

nextcloud_user_provisioning_config.inc (where you have to write down your credentials)
class_nextcloud_user_provisioning.php (The class)
example/user_functions.php (Shows the usage of functions for user)
example/group_functions.php (Shows the usage of functions for groups)

Greetings

Daniel de West
