<?php
function get_users()
{
	$dbh = mysqli_connect(CONFIG_DB_SERVER,
		CONFIG_DB_USERNAME,
		CONFIG_DB_PASSWORD,
		CONFIG_DB_DATABASE);
	$query = "SELECT users.id AS id, username, MAX(last_activity) AS last_activity,"
	. " `picture` AS picture FROM identity, users WHERE"
	. " identity.id = users.id GROUP BY users.id ORDER BY last_activity DESC";
	$res = mysqli_query($dbh, $query);
	while ($row = mysqli_fetch_assoc($res)) {
		$ret[$row['id']] = [
			'username' => $row['username'],
			'last_activity' => $row['last_activity'],
			'picture' => $row['picture']
		];
	}
	return $ret;
}
function get_lastuseractivity($id)
{
	$dbh = mysqli_connect(CONFIG_DB_SERVER,
		CONFIG_DB_USERNAME,
		CONFIG_DB_PASSWORD,
		CONFIG_DB_DATABASE);
	$query = "SELECT id, MAX(last_activity) AS date FROM `identity` WHERE"
	. " `id` = $id";
	$res = mysqli_query($dbh, $query);
	$row = mysqli_fetch_assoc($res);
	return $row['date'];
}
function get_userpictures($ids)
{
	$dbh = mysqli_connect(CONFIG_DB_SERVER,
		CONFIG_DB_USERNAME,
		CONFIG_DB_PASSWORD,
		CONFIG_DB_DATABASE);
	$query = "SELECT `id`, `picture` FROM `users` WHERE";
	for ($i = 0; $i < count($ids); $i++) {
		if ($i) $query .= ' OR';
		$query .= ' `id` = ' . $ids[$i];
	}
	$res = mysqli_query($dbh, $query);
	while ($row = mysqli_fetch_assoc($res))
		$ret[$row['id']] = $row['picture'];
	return $ret;
}
function get_userbios($ids)
{
	$dbh = mysqli_connect(CONFIG_DB_SERVER,
		CONFIG_DB_USERNAME,
		CONFIG_DB_PASSWORD,
		CONFIG_DB_DATABASE);
	$query = "SELECT `id`, `about` FROM `users` WHERE";
	for ($i = 0; $i < count($ids); $i++) {
		if ($i) $query .= ' OR';
		$query .= ' `id` = ' . $ids[$i];
	}
	$res = mysqli_query($dbh, $query);
	while ($row = mysqli_fetch_assoc($res))
		$ret[$row['id']] = $row['about'];
	return $ret;
}
function get_joindate($id)
{
	$dbh = mysqli_connect(CONFIG_DB_SERVER,
		CONFIG_DB_USERNAME,
		CONFIG_DB_PASSWORD,
		CONFIG_DB_DATABASE);
	$query = "SELECT `signup_date` FROM `users` WHERE"
	. " `id` = $id";
	$res = mysqli_query($dbh, $query);
	$row = mysqli_fetch_assoc($res);
	return $row['signup_date'];
}
function update_userpicture($id, $key)
{
	$dbh = mysqli_connect(CONFIG_DB_SERVER,
		CONFIG_DB_USERNAME,
		CONFIG_DB_PASSWORD,
		CONFIG_DB_DATABASE);
	$key = mysqli_real_escape_string($dbh, $key);
	$query = "SELECT picture FROM `users` WHERE"
	. " `id` = $id";
	$res = mysqli_query($dbh, $query);
	$oldpicture = mysqli_fetch_assoc($res)['picture'];
	unlink($oldpicture);
	$query  = "UPDATE `users` SET `picture`='$key' WHERE `id`=$id";
	return mysqli_query($dbh, $query);
}
function update_userbio($id, $bio)
{
	$dbh = mysqli_connect(CONFIG_DB_SERVER,
		CONFIG_DB_USERNAME,
		CONFIG_DB_PASSWORD,
		CONFIG_DB_DATABASE);
	$bio = mysqli_real_escape_string($dbh, $bio);
	$query  = "UPDATE `users` SET `About`='$bio' WHERE `id`=$id";
	return mysqli_query($dbh, $query);
}
?>
