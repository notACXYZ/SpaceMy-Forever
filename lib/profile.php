<?php
require($_SERVER['DOCUMENT_ROOT'] . "/vendor/autoload.php");
use Snipe\BanBuilder\CensorWords;

function getUserFromId($id, $connection) {
        $stmt = $connection->prepare("SELECT * FROM users WHERE id = ?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
    $result = $stmt->get_result();
    $user = $result->fetch_assoc();
	if($result->num_rows === 0) return('That user does not exist.');
	$stmt->close();

	return $user;
}

function getGroupFromId($id, $connection) {
        $stmt = $connection->prepare("SELECT * FROM groups WHERE id = ?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
    $result = $stmt->get_result();
    $user = $result->fetch_assoc();
    if($result->num_rows === 0) { $group['name'] = "None"; $group['id'] = 0; };
    $stmt->close();

    return $user;
}

function getInfoFromBlog($id, $connection) {
        $stmt = $connection->prepare("SELECT * FROM blogs WHERE id = ?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
    $result = $stmt->get_result();
    $user = $result->fetch_assoc();
    if($result->num_rows === 0) return('That blog does not exist.');
    $stmt->close();

    return $user;
}

function logDB($text, $mysqli) {
    $stmt = $mysqli->prepare("INSERT INTO logs (event) VALUES (?)");
    $stmt->bind_param("s", $text);
    $stmt->execute();
    $stmt->close();
}

function archiveAllUserInfo($username, $connection) {
    $stmt = $connection->prepare("UPDATE comments SET comment = '[archived]' WHERE author = ?");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $stmt->close();

    $stmt = $connection->prepare("UPDATE blogs SET message = '[archived]' WHERE author = ?");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $stmt->close();

    $stmt = $connection->prepare("UPDATE blogcomments SET comment = '[archived]' WHERE author = ?");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $stmt->close();

    $stmt = $connection->prepare("UPDATE groupcomments SET comment = '[archived]' WHERE author = ?");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $stmt->close();

    $stmt = $connection->prepare("UPDATE groups SET description = '[archived]', name = '[archived]', pic = '51zLZbEVSTL._AC_SX679_.jpg' WHERE owner = ?");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $stmt->close();

    return true;
}

function getAllFileSize($username, $conn) {
    $stmt = $conn->prepare("SELECT * FROM files WHERE owner = ?");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();
    $filesize = 0;
    while($row = $result->fetch_assoc()) {
        $filesize = $filesize + filesize("../dynamic/files/" . $row['filename']);
    }
    $stmt->close();
    return $filesize;
}

function delPostsFromUser($username, $conn) {
    $stmt = $conn->prepare("DELETE FROM comments WHERE author = ?");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $stmt->close();

    $stmt = $conn->prepare("DELETE FROM blogs WHERE author = ?");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $stmt->close();

    $stmt = $conn->prepare("DELETE FROM blogcomments WHERE author = ?");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $stmt->close();

    $stmt = $conn->prepare("DELETE FROM groupcomments WHERE author = ?");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $stmt->close();

    $stmt = $conn->prepare("DELETE FROM groups WHERE owner = ?");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $stmt->close();

    return true;
}

function delAccount($username, $connection) {
        $stmt = $connection->prepare("DELETE FROM users WHERE username = ?");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $stmt->close();
}

function isAdmin($username, $conn) {
    $stmt = $conn->prepare("SELECT * FROM users WHERE username = ? AND status = 'admin'");
    $stmt->bind_param("s", $_SESSION['siteusername']);
    $stmt->execute();
    $result = $stmt->get_result();
    if($result->num_rows === 0) { return false; } else { return true; }
    $stmt->close();
}

function UpdateLoginTime($username, $connection) {
    $stmt = $connection->prepare("UPDATE users SET lastlogin = now() WHERE username = ?");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $stmt->close();
}

function deleteComment($id, $conn) {
    $stmt = $conn->prepare("DELETE FROM comments WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $stmt->close();
}

function pinComment($id, $conn) {
    $stmt = $conn->prepare("UPDATE comments SET status = 'p' WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $stmt->close();
}

function unpinComment($id, $conn) {
    $stmt = $conn->prepare("UPDATE comments SET status = 'n' WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $stmt->close();
}

function getUserFromName($name, $connection) {
        $stmt = $connection->prepare("SELECT * FROM users WHERE username = ?");
        $stmt->bind_param("s", $name);
        $stmt->execute();
    $result = $stmt->get_result();
    $user = $result->fetch_assoc();
    if($result->num_rows === 0) return('That user does not exist.');
    $stmt->close();

    return $user;
}

function getPosts($name, $connection) {
    $stmt = $connection->prepare("SELECT id FROM reply WHERE author = ?");
    $stmt->bind_param("s", $name);
    $stmt->execute();
    $result = $stmt->get_result();
    $number = 0;
    while($row = $result->fetch_assoc()) {
        $number++;
    }
    return $number;
    $stmt->close();
}

function getIDFromUser($name, $connection) {
    $stmt = $connection->prepare("SELECT id FROM users WHERE username = ?");
    $stmt->bind_param("s", $name);
    $stmt->execute();
    $result = $stmt->get_result();
    while($row = $result->fetch_assoc()) {
        $id = $row['id'];
    }
    return $id;
    $stmt->close();
}

function getLikesFromBlog($id, $connection) {
    $stmt = $connection->prepare("SELECT * FROM bloglikes WHERE toid = ? AND type = 'l'");
    $stmt->bind_param("s", $id);
    $stmt->execute();
    $result = $stmt->get_result();
    $rows = mysqli_num_rows($result); 
    $stmt->close();

    return $rows;
}

function getDislikesFromBlog($id, $connection) {
    $stmt = $connection->prepare("SELECT * FROM bloglikes WHERE toid = ? AND type = 'd'");
    $stmt->bind_param("s", $id);
    $stmt->execute();
    $result = $stmt->get_result();
    $rows = mysqli_num_rows($result); 
    $stmt->close();

    return $rows;
}

function getLikesFromVideos($id, $connection) {
    $stmt = $connection->prepare("SELECT * FROM videolikes WHERE toid = ? AND type = 'l'");
    $stmt->bind_param("s", $id);
    $stmt->execute();
    $result = $stmt->get_result();
    $rows = mysqli_num_rows($result);
    $stmt->close();

    return $rows;
}

function getDislikesFromVideos($id, $connection) {
    $stmt = $connection->prepare("SELECT * FROM videolikes WHERE toid = ? AND type = 'd'");
    $stmt->bind_param("s", $id);
    $stmt->execute();
    $result = $stmt->get_result();
    $rows = mysqli_num_rows($result);
    $stmt->close();

    return $rows;
}

function getNameFromUser($id, $connection) {
    $stmt = $connection->prepare("SELECT username FROM users WHERE id = ?");
    $stmt->bind_param("s", $id);
    $stmt->execute();
    $result = $stmt->get_result();
    while($row = $result->fetch_assoc()) {
        $id = $row['username'];
    }
    return $id;
    $stmt->close();
}

function getPFPFromUser($name, $connection) {
    $stmt = $connection->prepare("SELECT pfp FROM users WHERE username = ?");
    $stmt->bind_param("s", $name);
    $stmt->execute();
    $result = $stmt->get_result();
    while($row = $result->fetch_assoc()) {
        $pfp = $row['pfp'];
    }
    return $pfp;
    $stmt->close();
}

function updateCategoryTime($id, $conn) {
    $stmt = $conn->prepare("UPDATE categories SET lastmodified = now() WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $stmt->close();

    return true;
}

function updateThreadTime($id, $conn) {
    $stmt = $conn->prepare("UPDATE threads SET lastmodified = now() WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $stmt->close();

    return true;
}

function updateSteamURL($url, $username, $connection) {
    $stmt = $connection->prepare("UPDATE users SET steamurl = ? WHERE username = ?");
    $stmt->bind_param("ss", $url, $username);
    $stmt->execute();
    $stmt->close();
}

function convertYoutube($string) {
	return preg_replace(
		"/\s*[a-zA-Z\/\/:\.]*youtu(be.com\/watch\?v=|.be\/)([a-zA-Z0-9\-_]+)([a-zA-Z0-9\/\*\-\_\?\&\;\%\=\.]*)/i",
		"<iframe width='290' height='200' src='//www.youtube.com/embed/$2' allowfullscreen></iframe>",
		$string
	);
}


function parseEmoticons($input) {
    $find = array(":troll:", ":nes:", ":cookie:", ":cookiemonster:", ":dance:", ":mac:", ":jon:", ":chiyo:");
    $replace = array(" <img src='/static/troll.png'> ", " <img src='/static/nes.gif'> ", " <img src='/static/cookie.gif'> ", " <img src='/static/CookieMonster.gif'> ", " <img src='/static/dance.gif'> ", " <img src='/static/macemoji.png'> ", " <img src='/static/jonnose.png'> ", " <img src='/static/chiyo.png'> ");
    $input = str_replace($find, $replace, $input);
    return $input;
}

function parseText($text) {
    $text = htmlspecialchars($text);
    $Parsedown = new Parsedown();
    $Parsedown->setSafeMode(true);
    $text = $Parsedown->line($text);

    $censor = new CensorWords;
    $censortext = $censor->censorString($text);
    $text = $censortext['clean'];

    $text = preg_replace("/@([a-zA-Z0-9-]+|\\+\\])/", "<a href='/redirectname.php?name=$1'>@$1</a>", $text);
    $text = parseEmoticons($text);
    $text = convertYoutube($text);

    $text = str_replace(PHP_EOL, "<br>", $text);

    return $text;
}

function stripURLTHingies($url) {
    $replace = array("https://steamcommunity.com/id/", "/");
    return str_replace($replace, "", $url);
}

function redirectToLogin() {
    header("Location: ../login.php");
}

if(isset($_SESSION['siteusername'])) {
    UpdateLoginTime($_SESSION['siteusername'], $conn); 
}
?>
