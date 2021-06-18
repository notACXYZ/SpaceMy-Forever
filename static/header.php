<?php
function getUserFromUsername($username, $connection) {
	$stmt = $connection->prepare("SELECT * FROM `users` WHERE `username` = ?");
	$stmt->bind_param("s", $username);
	$stmt->execute();
	$result = $stmt->get_result();
	$user = $result->fetch_assoc();
	if ($result->num_rows === 0) return('That user does not exist.');
	$stmt->close();

	return $user;
}

if(isset($_SESSION['siteusername'])) {
    $stmt = $conn->prepare("SELECT * FROM `users` WHERE `username` = ?");
    $stmt->bind_param("s", $_SESSION['siteusername']);
    $stmt->execute();
    $result = $stmt->get_result();
    while($row = $result->fetch_assoc()) {
        if($row['banstatus'] != "A") {
            header("Location: ../ban.php");
        }
    }
    $stmt->close();
}
?>
<div class="headerTop">
    <a href="/index.php"><img src="/static/spacemy2.png"></a>
    <small id="floatRight">
        <?php if(isset($_SESSION['siteusername'])) {?>
        <a href="/logout.php">Logout</a>
        <?php } else {?>
        <a href="/login.php">Login</a> &bull;
        <a href="/register.php">Register</a>
        <?php }?>&nbsp;&nbsp;
    </small><br>
    <span id="floatRight">
        <form method="get" action="/browse.php" style="margin:0">
        <select name="searchmethod">
            <option value="users">User</option>
            <option value="blog">Blog</option>
            <option value="groups">Group</option>
        </select>
        <input type="text" size="30" name="search"> <input type="submit" value="Search">
        </form> 
    </span>
</div>
<div class="headerBottom">
    <small>
        <a href="/groups">Groups</a> &bull;
        <a href="/blogs">Blogs</a> &bull;
        <?php if (isset($_SESSION['siteusername'])) {
            $stmt = $conn->prepare("SELECT * FROM `pms` WHERE sto = ? AND isRead = 0");
            $stmt->bind_param("s", $_SESSION['siteusername']);
            $stmt->execute();
            $stmt->store_result();
            $unread_pm_count = $stmt->num_rows;
            $stmt->close();

            $stmt = $conn->prepare("SELECT * FROM `friends` WHERE reciever = ? AND status = 'u'");
            $stmt->bind_param("s", $_SESSION['siteusername']);
            $stmt->execute();
            $stmt->store_result();
            $unread_friend_count = $stmt->num_rows;
            $stmt->close();
        ?>
        <a href="/pms.php">PMs<?php echo ($unread_pm_count === 0 ? "" : " (" . $unread_pm_count . ")")?></a> &bull;
        <a href="/friends/">Friends<?php echo ($unread_friend_count === 0 ? "" : " (" . $unread_friend_count . ")" )?></a> &bull;
        <?php }?>
        <a href="/jukebox.php">Jukebox</a> &bull;
        <?php // if (isset($_SESSION['siteusername'])) {? ><a href="/videos/">Videos</a>  &bull;< ?php } ?>
        <a href="/users.php">All Users</a>
        <?php if (isset($_SESSION['siteusername'])) {?>
        <span id="floatRight">
            <span id="custompadding">
                <?php if(isAdmin($_SESSION['siteusername'], $conn) == true) { echo '<a href="/admin">Admin</a> &bull;'; } ?>
                <a href="/files">Files</a> &bull;
                <a href="/edit">Edit Items</a> &bull;
                <a href="/manage.php">Manage User</a> &bull;
				<a href="/profile.php?id=<?php echo(htmlspecialchars(getUserFromUsername($_SESSION['siteusername'], $conn)["id"]));?>"><?php echo($_SESSION['siteusername'])?></a>
            </span>
        </span>
        <?php }?>
    </small>
</div>
<?php
if(isset($_SESSION['siteusername'])) {
    $stmt = $conn->prepare("SELECT * FROM users WHERE username = ?");
    $stmt->bind_param("s", $_SESSION['siteusername']);
    $stmt->execute();
    $result = $stmt->get_result();
    if($result->num_rows === 0) die("U have been ban");
    $stmt->close();
};
?>
