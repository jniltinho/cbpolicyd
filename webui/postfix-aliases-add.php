<?php
# Postfix aliases add
# Copyright (C) 2008, LinuxRulz
# 
# This program is free software; you can redistribute it and/or modify
# it under the terms of the GNU General Public License as published by
# the Free Software Foundation; either version 2 of the License, or
# (at your option) any later version.
# 
# This program is distributed in the hope that it will be useful,
# but WITHOUT ANY WARRANTY; without even the implied warranty of
# MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
# GNU General Public License for more details.
# 
# You should have received a copy of the GNU General Public License along
# with this program; if not, write to the Free Software Foundation, Inc.,
# 51 Franklin Street, Fifth Floor, Boston, MA 02110-1301 USA.


include_once("includes/header.php");
include_once("includes/footer.php");
include_once("includes/db.php");



$db = connect_postfix_db();



printHeader(array(
		"Tabs" => array(
			"Back to Aliases" => "postfix-aliases-main.php",
		),
));


if ($_POST['action'] == "add")  {
?>
	<h1>Add Postfix Alias</h1>
<?php
?>
		<form method="post" action="postfix-aliases-add.php">
			<div>
				<input type="hidden" name="action" value="add2" />
			</div>
			<table class="entry">
				<tr>
					<td class="entrytitle">Alias Address</td>
					<td>
						<input type="text" name="postfix_alias_address" /> @
						<select name="postfix_transport_id">
<?php
							$sql = 'SELECT ID, DomainName FROM transports WHERE Disabled = 0 ORDER BY DomainName';
							$res = $db->query($sql);

							while ($row = $res->fetchObject()) {
?>
								<option value="<?php echo $row->id ?>">
									<?php echo $row->domainname ?>
								</option>
<?php
							}
?>
						</select>
					</td>
				</tr>
				<tr>
					<td class="entrytitle">Goto</td>
					<td><input type="text" name="postfix_alias_goto" /></td>
				</tr>
				<tr>
					<td colspan="2">
						<input type="submit" />
					</td>
				</tr>
			</table>
		</form>
<?php
	
	
	
# Check we have all params
} elseif ($_POST['action'] == "add2") {
?>
	<h1>Postfix Alias Add Results</h1>

<?php
	# Prepare statement
	$stmt = $db->prepare('SELECT ID, DomainName, Type, Transport, Disabled FROM transports WHERE ID = ?');
	$res = $stmt->execute(array($_POST['postfix_transport_id']));
	$row = $stmt->fetchObject();

	$mailbox = $_POST['postfix_alias_address'] . '@' . $row->domainname;


	$stmt = $db->prepare("INSERT INTO aliases (TransportID,Address,MailAddress,Goto,Disabled) VALUES (?,?,?,?,1)");
	$res = $stmt->execute(array(
		$row->id,
		$_POST['postfix_alias_address'],
		$mailbox,
		$_POST['postfix_alias_goto']
	));
	
	if ($res) {
?>
		<div class="notice">Postfix alias created</div>
<?php
	} else {
?>
		<div class="warning">Failed to create Postfix alias</div>
		<div class="warning"><?php print_r($stmt->errorInfo()) ?></div>
<?php
	}



} else {
?>
	<div class="warning">Invalid invocation</div>
<?php
}


printFooter();


# vim: ts=4
?>
