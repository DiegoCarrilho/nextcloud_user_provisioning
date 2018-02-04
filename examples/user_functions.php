<?php
require_once("../class_nextcloud_user_provisioning_api.php");
$upa = new nextcloud_user_provisioning_api();

echo '<h1>Examples User Functions</h1>';

echo "<h2>All Users</h2>";
$users=$upa->getAllUsers();
echo "<ol>";
foreach ($users as $u) {
	echo "<li>$u</li>\n";
}
echo "</ol>";

$u=$users[0];
echo "<h2>User $u</h2>";
echo "<h3>Does user '$u' exist? ".$upa->checkIfUserExists($u)."</h3>";
echo "<h3>Information about user '$u':</h3>";
echo "<pre>";
print_r($upa->getUser($u));
echo "</pre>";
echo "<h3>Group Memberships of user '$u':</h3>";
$groups=$upa->getUserGroups($u);
echo "<ol>";
foreach ($groups as $g) {
	echo "<li>$g</li>\n";
}
echo "</ol>";

$newusername='idontexist';
echo "<h2>Adding a user</h2>";
echo "<p>Existence of user '$newusername' before adding:".$upa->checkIfUserExists($newusername)."</p>";
$upa->addUser($newusername,"testpassword");
echo "<p>Existence of user '$newusername' after adding:".$upa->checkIfUserExists($newusername)."</p>";

echo "<h2>Adding multiple users</h2>";
$newusers=array("newuser1"=>"newuuser1spassword","newuser2"=>"newuuser2spassword","newuser1"=>"newuuser1spassword");
$erg=$upa->addMultipleUsers($newusers);
print_r($erg);

echo "<h2>Editing a user's data</h2>";
$userdata=$upa->getUser($newusername);
echo "<p>E-Mail of User '$newusername' before editing: '".current($userdata['email'])."'</p>";
$upa->editUser($newusername, "email", "noone@nowhere.com");
$userdata=$upa->getUser($newusername);
echo "<p>E-Mail of User '$newusername' after editing: '".current($userdata['email'])."'</p>";

echo "<h2>Disabling/Enabling a user</h2>";
$userinfo=$upa->getUser($newusername);
echo "<p>Enable-Status user '$newusername' before disabling:".$userinfo['enabled']."</p>";
$upa->disableUser($newusername);
$userinfo=$upa->getUser($newusername);
echo "<p>Enable-Status user '$newusername' after disabling:".$userinfo['enabled']."</p>";
$upa->enableUser($newusername);
$userinfo=$upa->getUser($newusername);
echo "<p>Enable-Status user '$newusername' after enabling:".$userinfo['enabled']."</p>";


echo "<h2>Deleting a user</h2>";
echo "<p>Existence of user '$newusername' before deleting:".$upa->checkIfUserExists($newusername)."</p>";
$upa->killUser($newusername);
echo "<p>Existence of user '$newusername' after deleting:".$upa->checkIfUserExists($newusername)."</p>";
?>