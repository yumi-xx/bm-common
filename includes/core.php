<?php

// Yes, this site uses sessions! Please enable cookies!
session_start();

// deployment-specific configuration
include("config.php");

//function definitions

// updates the user's current style and stashes that into a SQL table
function updateUserStyle($css = NULL, $id = NULL)
{

	if ($id) {
	$conn = new mysqli(CONFIG_DB_SERVER, CONFIG_DB_USERNAME, CONFIG_DB_PASSWORD, CONFIG_DB_DATABASE);
	if ($conn->connect_error) {
		return False;
	}
	$cmd = "UPDATE `users` SET `pref_css`='$css' WHERE `id`='$id'";
	$conn->query($cmd);
	}
	$_SESSION['pref_css'] = $css;
	reloadUserStyle();
	return True;
}
function reloadUserStyle() {
	switch($_SESSION['pref_css']) {
	case "classic":
		$_SESSION['stylesheet']=CONFIG_COMMON_WEBPATH."css/style_suckless.css";
		$_SESSION['mascot']=CONFIG_COMMON_WEBPATH."img/rei.png";
		$_SESSION['motd']="お帰りなさい";
		break;
	case "gold":
		$_SESSION['stylesheet']=CONFIG_COMMON_WEBPATH."css/style_suckless_gold.css";
		$_SESSION['mascot']=CONFIG_COMMON_WEBPATH."img/yui.png";
		$_SESSION['motd']="おかえりなさい";
		break;
	case "wu_tang":
		$_SESSION['stylesheet']=CONFIG_COMMON_WEBPATH."css/style_suckless_wutang.css";
		$_SESSION['mascot']=CONFIG_COMMON_WEBPATH."img/ghost.png";
		$_SESSION['motd']="Protect ya neck";
		break;
	case "red":
		$_SESSION['stylesheet']=CONFIG_COMMON_WEBPATH."css/style_suckless_red.css";
		$_SESSION['mascot']=CONFIG_COMMON_WEBPATH."img/mao.png";
		$_SESSION['motd']="为人民服务";
		break;
	case "nier":
		$_SESSION['stylesheet']=CONFIG_COMMON_WEBPATH."css/style_suckless_nier.css";
		$_SESSION['mascot']=CONFIG_COMMON_WEBPATH."img/2B.png";
		$_SESSION['motd']="おかえりなさい";
		break;
	default:
		$_SESSION['pref_css']='bigmike';
	case "bigmike":
		$_SESSION['stylesheet']=CONFIG_COMMON_WEBPATH."css/style_suckless_bigmike.css";
		$_SESSION['mascot']=CONFIG_COMMON_WEBPATH."img/mike.png";
		$_SESSION['motd']="おかえりなさい";
		break;
	case "yys":
		$_SESSION['mascot']=CONFIG_COMMON_WEBPATH."img/yuzuko.png";
		$_SESSION['stylesheet']=CONFIG_COMMON_WEBPATH."css/style_suckless_yys.css";
		$_SESSION['motd']="よぉ";
		break;
	case "worlds":
		$_SESSION['mascot']=CONFIG_COMMON_WEBPATH."img/hand.png";
		$_SESSION['stylesheet']=CONFIG_COMMON_WEBPATH."css/style_suckless_worlds.css";
		$_SESSION['motd']="we'll see creation come undone";
		break;
	}
}
// returns the size of a directory in bytes
function dirSize($dir)
{
	$totalSize = 0;
	foreach (new DirectoryIterator($dir) as $file) {
		if ($file->isFile()) {
			$totalSize += $file->getSize();
		}
	}
	return $totalSize;
}
// returns a human-readable file-size
function human_filesize($bytes, $decimals = 2)
{
    $size = array('B','kB','MB','GB','TB','PB','EB','ZB','YB');
    $factor = floor((strlen($bytes) - 1) / 3);
    return sprintf("%.{$decimals}f", $bytes / pow(1024, $factor)) . @$size[$factor];
}
// really silly function for sending files in a stream-compatible mode
// this will be integrated with a download.php when you request to download
// a file
function sendAnime($filename)
{
	header("Content-Type:".mime_content_type("$filename"));
	header("Content-Disposition: attachment; filename=\"" . basename("$filename") . "\"");
	header('X-Sendfile:'.realpath($filename));
}
// used for determining elligibility to be downloaded as a zip file
function isAnAudioFile($filename)
{
	$extension = pathinfo($filename)["extension"];
	if ($extension == "mp3") return true;
	if ($extension == "m4a") return true;
	if ($extension == "flac") return true;
	if ($extension == "aiff") return true;
	if ($extension == "m3u") return true;
	if ($extension == "mpg") return true;
	if ($extension == "wav") return true;
	if ($extension == "ogg") return true;
	if ($extension == "wma") return true;
	return false;
}
// was used for determining elligibility to be viewed as a gallery
// I should clean this code up, since I'm not sure this is called anymore
function isAPictureFile($filename)
{
	$extension = pathinfo($filename)["extension"];
	if ($extension == "png") return true;
	if ($extension == "jpg") return true;
	if ($extension == "jpeg") return true;
	if ($extension == "jpe") return true;
	if ($extension == "gif") return true;
	return false;
}
// Passed an array of filenames, tests if all files are music files
// Returns true/false if this folder looks like a folder of music
function testForMusicFiles($files)
{
	$isAMusicFolder = false;
	if (empty($files))
		return false;
	foreach ($files as $item) {
		if (isAnAudioFile($item)) {
			$isAMusicFolder = true;
			break;
		}
	}
	return $isAMusicFolder;
}
// tests if all passed files are pictures
// was used to determine gallery elligibility, probably deprecated
function onlyPictures($files)
{
	if (empty($files))
		return false;
	foreach ($files as $item) {
		if (!isAPictureFile($item)) {
			return false;
		}
	}
	return true;
}
// return an array of all files in a directory
function getFiles($dir)
{
	$array = array();
	$files = scandir("share".$dir);
	foreach($files as $file) {
		if (is_file("share".$dir.$file)) {
			$array[] = $file;
		}
	}
	return $array;
}
// Generates a random hex string, mostly for generating a salt
function randomHex($len) {
	$chars = 'abcdef01234567890';
	for ($i = 0; $i < $len; $i++)
		$hex .= $chars[rand(0, strlen($chars) - 1)];
	return $hex;
}
function randomBase64($len) {
	return base64_encode(random_bytes($len));
}
function print_login()
{
	if (isset($_SESSION['userid'])) {
		print('<a href="'.CONFIG_WEBHOMEPAGE.'">home</a></br>');
		print('<a href="'.CONFIG_COMMON_WEBPATH.'logout.php">logout</a>');
	}
	else {
		print('<a href="'.CONFIG_COMMON_WEBPATH.'login.php?ref='.$_SERVER['REQUEST_URI'].'">login</a>');
	}
}
function lwrite($file, $msg) {
	$fd = fopen($file, "a");
	fwrite($fd, @date('[d/M/Y:H:i:s]').' '.$msg.PHP_EOL);
	fclose($fd);
	return;
}
?>
