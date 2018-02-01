<?php
require_once("../class_nextcloud_user_provisioning_api.php");
$upa = new nextcloud_user_provisioning_api();

echo '<h1>Examples Group Functions</h1>';

echo "<h2>All Groups</h2>";
$groups=$upa->getAllGroups();
echo "<ol>";
foreach ($groups as $g) {
	echo "<li>$g</li>\n";
}
echo "</ol>";


$g=$groups[0];
echo "<h2>$g</h2>";
$ext=$upa->checkIfGroupExists($g);
echo "<h3>Existence of group '$g': ".$ext."</h3>";
echo "<h3>Group members of group '$g':</h3>";
echo "<ol>";
$members=$upa->getGroupMembers($g);
foreach ($members as $m) {
	echo "<li>$m</li>\n";
}
echo "</ol>";

$newgroupname='neuetestgruppe123';
$newgroupmember='Daniel';
echo "<h3>Adding a group</h3>";
$ext=$upa->checkIfGroupExists($newgroupname);
echo "<hp>Existence of '$newgroupname' before adding: ".$ext."</hp>";
echo "<p>Adding group '$newgroupname'</p>";
$upa->addGroup($newgroupname);
$ext=$upa->checkIfGroupExists($newgroupname);
echo "<p>Existence of '$newgroupname' after adding: ".$ext."</p>";
$ext=$upa->checkIfUserIsMemberOfGroup($newgroupmember, $newgroupname);
echo "<p>Is user '$newgroupmember' a member of group '$newgroupname' before adding? ".$ext."</p>";
if ($ext===false) {
	echo "<p>'$newgroupmember' is added as a member to group '$newgroupname'</p>";
	$upa->addUserToGroup($newgroupmember, $newgroupname);
	$ext=$upa->checkIfUserIsMemberOfGroup($newgroupmember, $newgroupname);
	echo "<p>Is user '$newgroupmember' a member of group '$newgroupname' after adding? ".$ext."</p>";
}
echo "<h3>Deleting a group</h3>";
echo "<p>Group '$newgroupname' is being deleted</p>";
$upa->killGroup($newgroupname);
$ext=$upa->checkIfGroupExists($newgroupname);
echo "<hp>Existence of '$newgroupname' after deleting: ".$ext."</hp>";

?>