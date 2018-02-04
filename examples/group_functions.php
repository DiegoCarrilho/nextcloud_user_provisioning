<?php
require_once("../class_nextcloud_user_provisioning_api.php");
$upa = new nextcloud_user_provisioning_api();
$GLOBALS['upa']=$upa;

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

$newgroupname=generateNewGroupName('newtestgroup123');
function generateNewGroupName($newgroupname) {
	if ($GLOBALS['upa']->checkIfUserExists($newgroupname)===true) {
		$newgroupname.=random_int(0,9);
		return generateNewgroupname($newgroupname);
	} else {
		return $newgroupname;
	}
}

$newgroupmember=generateNewGroupMember('newtestuser123');

function generateNewGroupMember($newgroupmember) {
	if ($GLOBALS['upa']->checkIfUserExists($newgroupmember)===true) {
		$newgroupmember.=random_int(0,9);
		return generateNewGroupMember($newgroupmember);
	} else {
		return $newgroupmember;
	}
}

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

echo "<h2>Adding multiple users to groups</h2>";
$newtestuser1=generateNewGroupMember("newtestuser_1_");
$newtestuser2=generateNewGroupMember("newtestuser_2_");
$upa->addMultipleUsers(array($newtestuser1=>"testpassword",$newtestuser2=>"testpassword"));
$newusers_and_groups=array(array($newtestuser1=>$newgroupname),array($newtestuser2=>"nonexistentgroup"));
$erg=$upa->addMultipleUsersToGroups($newusers_and_groups);
print_r($erg);
$upa->killMultipleUsers(array($newtestuser1,$newtestuser2));

echo "<h3>Deleting a group</h3>";
echo "<p>Group '$newgroupname' is being deleted</p>";
$upa->killGroup($newgroupname);
$ext=$upa->checkIfGroupExists($newgroupname);
echo "<hp>Existence of '$newgroupname' after deleting: ".$ext."</hp>";

?>