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
$userdata=$upa->getUser($newusername);
echo "<p>E-Mail of User '$newusername' before editing: '".$userdata['email']."'</p>";
$upa->editUser($newusername, "email", "testmail@example.org");
$userdata=$upa->getUser($newusername);
echo "<p>E-Mail of User '$newusername' after editing: '".$userdata['email']."'</p>";
print_r($userdata);


echo "<h2>Deleting a user</h2>";
echo "<p>Existence of user '$newusername' before deleting:".$upa->checkIfUserExists($newusername)."</p>";
$upa->killUser($newusername);
echo "<p>Existence of user '$newusername' after deleting:".$upa->checkIfUserExists($newusername)."</p>";
?>