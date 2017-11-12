<!DOCTYPE html>
<?php
include "../includes/core.php";
include CONFIG_COMMON_PATH . "includes/home.php";

if (CONFIG_REQUIRE_AUTHENTICATION)
	include CONFIG_COMMON_PATH."includes/auth.php";
if (CONFIG_HOOYA_PATH) {
	include CONFIG_HOOYA_PATH."includes/config.php";
	include CONFIG_HOOYA_PATH."includes/users.php";
	include CONFIG_HOOYA_PATH."includes/render.php";
}

if (isset($_FILES['picture']))
if ($_GET['id'] == $_SESSION['userid']) {
	print 'aa';die;
	update_userpicture($_SESSION['userid'], $_GET['picture']);
}
?>
<HTML>
<head>
	<?php include CONFIG_COMMON_PATH."includes/head.php";?>
	<title>bigmike — home</title>
</head>
<body>
<div id="container">
<div id="leftframe">
	<nav><?php print_login();?></nav>
	<img id="mascot" src=<?php echo $_SESSION['mascot'];?>>
</div>
<div id="rightframe" class=userpage>
<?php
	if (isset($_GET['id'])) {
		$id = (int)$_GET['id'];
		$username = get_username($id);
		$tagcount = get_usertagcount($id);
		$userpicture = get_userpicture($id);
		if (!$userpicture)
			$userpicture = 'ed083cf55c0598e29e072feca85a7993';
		$lastactivity = get_lastuseractivity($id);
		print '<header style=float:left;><a href=.>all users</a>';
		print "<h1 style='text-align:center;word-wrap:break-word'>$username";
		if ($id == $_SESSION['userid'])
			print " (you)";
		print '</h1></header>'
		. '<main>'
		. '<div class=summary>'
		. '<div><img '
		. "src=" . CONFIG_HOOYA_WEBPATH . "download.php?key=$userpicture&thumb></img></div>";
		if ($lastactivity) {
			print "<span>Last Online $lastactivity</span>";
		}
		if ($id == $_SESSION['userid'])
			print "<form method=post enctype='multipart/form-data'><input class=fupload type=file"
			. " name=picture id=fupload>"
			. "<label id=flabel for=fupload>Change picture</label>"
			.  "<input type=hidden name=id value=$id></form>";
		if ($id == $_SESSION['userid'])
			print '<a href="../util/change_passwd.php">Change password</a>'
			. '<a href="../util/invite.php">Invite a friend</a>'
			. '<a href="../util/acc_delete.php">Delete your account</a>';
		print '</div>'
		. '<dl>';
		if (CONFIG_HOOYA_PATH) {
			if ($tagcount > 0) {
				print '<dt>Pictures Tagged</dt>'
				. "<dd>$tagcount</dd>";
				$favoritetags = get_userfavorites($id, 5);
				render_bargraph($favoritetags);
			} else print "No tags added yet!";
		}
		print '</dl>'
		. '</main>'
		. '</div>';
	} else {
		print'<h1>Friends</h1><main>';
		foreach (get_users() as $id => $info) {
			if (!$info['picture']) $info['picture'] = 'ed083cf55c0598e29e072feca85a7993';
			print "<div style='padding:10px;display:inline-flex;flex-direction:column;align-items:center;'>"
			. $info['username']
			. "<a href=?id=$id>"
			. "<img style='max-width: 150px' src=" . CONFIG_HOOYA_WEBPATH
			. "download.php?key=" . $info['picture'] . "&thumb></img></a>"
			. "</div>";
		}
		print '</main>'
		. '</div>';
	}
?>
</div>
</div>
<script>
document.getElementById("fupload").onchange = function() {
	this.parentNode.submit();
};
</script>
<?php if (isset($_GET['picture']))
	print '<script>window.history.back();</script>';
?>
</body>
</HTML>
