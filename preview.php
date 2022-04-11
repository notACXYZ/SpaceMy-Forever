<?php require($_SERVER['DOCUMENT_ROOT'] . "/static/config.inc.php"); ?>
<?php require($_SERVER['DOCUMENT_ROOT'] . "/static/conn.php"); ?>
<?php require($_SERVER['DOCUMENT_ROOT'] . "/lib/profile.php"); ?>
<!DOCTYPE html>
<html>
<head>
    <title><?php echo $config['pr_title']; ?></title>
    <?php $user = getUserFromId((int)$_GET['id'], $conn); ?>

    <link rel="stylesheet" href="/static/css/required.css">
    <link rel="stylesheet" href="/static/css/profile.css">
    <link rel="stylesheet" href="/static/css/table3.css">
    <style>
        <?php echo $user['css']; ?>
    </style>
</head>
<body>
<div class="container">
    <?php require($_SERVER['DOCUMENT_ROOT'] . "/static/header.php");

    if($_SERVER['REQUEST_METHOD'] == 'POST') {
        if(!isset($_SESSION['siteusername'])){ $error = "you are not logged in"; goto skipcomment; }
        if(!$_POST['comment']){ $error = "your comment cannot be blank"; goto skipcomment; }
        if(strlen($_POST['comment']) > 1000){ $error = "your comment must be shorter than 1000 characters"; goto skipcomment; }
        if(!isset($_POST['g-recaptcha-response'])){ $error = "captcha validation failed"; goto skipcomment; }
        if(!validateCaptcha($config['recaptcha_secret'], $_POST['g-recaptcha-response'])) { $error = "captcha validation failed"; goto skipcomment; }

        $stmt = $conn->prepare("INSERT INTO `comments` (toid, author, comment) VALUES (?, ?, ?)");
        $stmt->bind_param("sss", $_GET['id'], $_SESSION['siteusername'], $text);
        $text = htmlspecialchars($_POST['comment']);
        $stmt->execute();
        $stmt->close();
        logDB($_SESSION['siteusername'] . " has commented a user named " . $user['username'] . " with the content: " . $text, $conn);
        skipcomment:
    }

    if(isset($_SESSION['siteusername']) && $user['username'] == $_SESSION['siteusername']) {
        $user['ownAccount'] = true;
    }

    if($user['badges'] != "") {
        $user['badgesArray'] = explode(";", $user['badges']);
        $lastElement = array_pop($user['badgesArray']);
        $user['badgesBuffer'] = "";
        foreach ($user['badgesArray'] as $explodedVal) {
            $user['badgesBuffer'] = $user['badgesBuffer'] . " <img src='/static/badges/" . $explodedVal . ".png'>";
        }
    }

    $user['privacyArray'] = explode("|", $user['privacy']);
    $group = getGroupFromId($user['currentgroup'], $conn);

    $lastLoginReal = (int)strtotime($user['lastlogin']);
    if(time() - $lastLoginReal < 15 * 60) {
        $lastLogin = true;
    }
    ?>
    <br>
    <div class="padding">
                <span id="padding10">
                    <small><a href="/">SpaceMy</a> / <a href="/users.php">Profiles</a> / <a href="/profile.php?id=<?php echo $_GET['id']?>"><?php echo $user['username']; ?></a></small>
                </span><br>
        <div class="topLeft">
            <center class="userInfo">
                <table id="Table2" width="300" cellspacing="0" cellpadding="0" align="center">
                    <tbody>
                    <tr>
                        <td class="text" width="75" height="75" bgcolor="#ffffff">
                            <img id="pfp" src="/dynamic/pfp/<?php echo $user['pfp']; ?>" border="0">
                        </td>
                        <td class="text" width="15" height="75" bgcolor="#ffffff">&nbsp;</td>
                        <td class="text" width="193" height="75" bgcolor="#ffffff" align="left">
                            <p>
                                <?php echo htmlspecialchars($user['gender']); ?><br>
                                <?php echo htmlspecialchars($user['age']); ?> year(s) old<br>
                                <?php echo htmlspecialchars($user['location']); ?><br>
                                <?php if(isset($lastLogin)) { echo "<img id='online' src='/static/online.gif'>"; } ?><br>
                                <br>
                            </p>
                            <p>
                                Last Login:<br>
                                <?php echo $user['lastlogin'];?><br>
                                <br>
                            </p>
                        </td>
                    </tr>
                    <tr>
                        <td colspan="3" style="padding-left:3px;">
                        </td>
                    </tr>
                    <tr valign="middle">
                        <td colspan="3" style="padding-left:3px;">
                        </td>
                    </tr>
                    </tbody>
                </table>
                <?php echo $user['badgesBuffer']; ?><br><br>
                <div class="song">
                    <small>
                        <audio controls><source src="/dynamic/music/<?php echo $user['music']; ?>"></audio><br><span id="songText"><?php echo htmlspecialchars($user['song']); ?></span>
                    </small>
                </div>
            </center>
            <br>
            <center>
                <table class="contactTable" width="300" cellspacing="0" cellpadding="0" bordercolor="#426BBA" border="1">
                    <tbody>
                    <tr>
                        <td class="text tdborder" style="WORD-WRAP:break-word" width="300" height="15" bgcolor="#426BBA" align="left">&nbsp;&nbsp;&nbsp;<span class="whitetext12">Contacting <?php echo $user['username']; ?></span>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <table width="300" cellspacing="0" cellpadding="0" bordercolor="#000000" border="0">
                                <tbody><tr>
                                    <td></td>
                                </tr>
                                <tr>
                                    <td class="text" width="120" nowrap="" height="5" bgcolor="#ffffff" align="center">
                                        <a href="pm.php?id=<?php echo $user['id']; ?>">
                                            <img src="static/sendMailIcon.gif" border="0" align="middle">
                                        </a>
                                    </td>
                                    <td width="15" height="5" bgcolor="#ffffff">

                                    </td>
                                    <td class="text" width="150" valign="top" nowrap="" height="5" bgcolor="#ffffff" align="center">
                                        <a href="">
                                            <img src="static/forwardMailIcon.gif" border="0" align="middle"></a>
                                    </td>
                                </tr>
                                <tr>
                                    <td colspan="3"></td>
                                </tr>
                                <tr>
                                    <td class="text" width="130" valign="top" nowrap="" height="5" bgcolor="#ffffff" align="center">
                                        <a href="/friends/add.php?id=<?php echo $user['id']; ?>">
                                            <img src="static/addFriendIcon.gif" border="0" align="middle"></a>
                                    </td>
                                    <td width="15" height="5" bgcolor="#ffffff">

                                    </td>
                                    <td class="text" width="150" valign="middle" nowrap="" height="2" bgcolor="#ffffff" align="center">
                                        <a href="">
                                            <img src="static/addFavoritesIcon.gif" border="0" align="middle"></a>
                                    </td>
                                </tr>
                                <tr>
                                    <td colspan="3"></td>
                                </tr>
                                <tr>
                                    <td class="text" width="130" valign="top" nowrap="" height="5" bgcolor="#ffffff" align="center">
                                        <a href="">
                                            <img src="static/icon_add_to_group.gif" border="0" align="middle"></a>
                                    </td>
                                    <td width="15" height="5" bgcolor="#ffffff"></td>
                                    <td class="text" width="150" valign="top" nowrap="" height="5" bgcolor="#ffffff" align="center">
                                        <a href="block_user.php?id=<?php echo $user['id']; ?>">
                                            <img src="static/blockuser.gif" border="0" align="middle"></a>
                                    </td>
                                </tr>
                                <tr>
                                    <td></td>
                                </tr>
                                </tbody></table>
                        </td>
                    </tr>
                    </tbody>
                </table>

                <br>
                <table id="Table1" class="interestsAndDetails" width="293" cellspacing="0" cellpadding="0" bordercolor="#426BBA" border="1" bgcolor="#6699cc">
                    <tbody>
                    <tr>
                        <td class="text tdborder" wrap="" width="293" valign="middle" bgcolor="#6699cc" align="left">&nbsp;
                            <span class="whitetext12">
                                            <big><?php echo $user['username']; ?>'s Interests</big>
                                        </span>
                        </td>
                    </tr>
                    <tr valign="top">
                        <td class="tdborder">
                            <table id="Table2" width="293" cellspacing="3" cellpadding="3" bordercolor="#000000" border="0" bgcolor="#ffffff" align="center">
                                <tbody>
                                <tr id="GeneralRow"><td width="100" valign="top" nowrap="" bgcolor="#97BEEC" align="left"><span class="lightbluetext8">Interests</span></td><td id="ProfileGeneral" width="175" bgcolor="#C9E0FA"><?php echo htmlspecialchars($user['interests']); ?></td></tr>
                                <tr id="MusicRow"><td width="100" valign="top" nowrap="" bgcolor="#97BEEC" align="left"><span class="lightbluetext8">Music</span></td><td id="ProfileMusic" width="175" bgcolor="#C9E0FA"><?php echo htmlspecialchars($user['interestsmusic']); ?></td></tr>
                                <tr id="Groups"><td width="100" valign="top" nowrap="" bgcolor="#97BEEC" align="left"><span class="lightbluetext8">Groups:</span></td><td id="ProfileHeroes" width="175" bgcolor="#C9E0FA"><?php echo "<a href='/groups/view.php?id=" . $group['id'] . "'>" . $group['name'] . "</a>"; ?>
                                    </td>
                                </tr>
                                </tbody>
                            </table>
                        </td>
                    </tr>
                    </tbody>
                </table>
                <br>
            </center>
            <div class="url">
                <b>SpaceMy URL:</b><br>
                &nbsp;&nbsp;https://spacemy.acxyz.ca/profile.php?id=<?php echo $user['id']; ?>
            </div>
        </div>
        <div class="topRight">
            <?php if($user['privacyArray'][0] == "hide") { } else { ?>
                <div class="blogSection">
                    <b><?php echo htmlspecialchars($user['username']); ?>'s Latest Blog Entry</b><br><br>
                    <?php
                    $stmt = $conn->prepare("SELECT * FROM blogs WHERE author = ? ORDER BY id DESC");
                    $stmt->bind_param("s", $user['username']);
                    $stmt->execute();
                    $result = $stmt->get_result();
                    while($row = $result->fetch_assoc()) {
                        if($row['visiblity'] != "Link Only") {
                            ?>
                            <span id="blogPost"><?php echo htmlspecialchars($row['subject']); ?> [<a href="blogs/view.php?id=<?php echo $row['id']; ?>">view more</a>]</span><br>
                        <?php } } ?>
                </div>
            <?php } ?>
            <div class="about">
                <b>About <?php echo htmlspecialchars($user['username']); ?></b>
            </div>
            <div class="bio">
                <?php echo parseText($user['bio']); ?>
            </div><br>
            <?php if($user['privacyArray'][1] == "hide") { } else { ?>
                <div class="about">
                    <b><?php echo htmlspecialchars($user['username']); ?>'s Friends</b>
                </div><br>
                <table id="friends">
                    <tbody>
                    <tr>
                        <th style="width: 60%">User</th>
                        <th style="width: 40%;text-align:right">Last Login</th>
                    </tr>
                    <?php
                    $user['friendsUserArray'] = array();
                    $stmt = $conn->prepare("SELECT * FROM friends WHERE reciever = ? AND status = 'a'");
                    $stmt->bind_param("s", $user['username']);
                    $stmt->execute();
                    $result = $stmt->get_result();
                    while($row = $result->fetch_assoc()) {
                        array_push($user['friendsUserArray'], $row['sender']);
                        ?>
                        <tr>
                            <td>
                                <a href="/profile.php?id=<?php echo getUserFromName($row['sender'], $conn)['id']?>" style="text-decoration: none">
                                    <img style="vertical-align: middle" width="24" height="24" src="/dynamic/pfp/<?php echo getPFPFromUser($row['sender'], $conn); ?>">
                                    <b style="vertical-align: middle"><?php echo $row['sender']; ?></b>
                                </a>
                            </td>
                            <td><span style="text-align: right;float: right"><?php echo getUserFromName($row['sender'], $conn)['lastlogin']?></span></td>
                        </tr>
                    <?php } ?>
                    <?php
                    $stmt = $conn->prepare("SELECT * FROM friends WHERE sender = ? AND status = 'a'");
                    $stmt->bind_param("s", $user['username']);
                    $stmt->execute();
                    $result = $stmt->get_result();
                    while($row = $result->fetch_assoc()) {
                        array_push($user['friendsUserArray'], $row['reciever']);
                        ?>
                        <tr>
                            <td>
                                <a href="/profile.php?id=<?php echo getUserFromName($row['reciever'], $conn)['id']?>" style="text-decoration: none">
                                    <img style="vertical-align: middle" width="24" height="24" src="/dynamic/pfp/<?php echo getPFPFromUser($row['reciever'], $conn); ?>">
                                    <b style="vertical-align: middle"><?php echo $row['reciever']; ?></b>
                                </a>
                            </td>
                            <td><span style="text-align: right;float: right"><?php echo getUserFromName($row['reciever'], $conn)['lastlogin']?></span></td>
                        </tr>
                    <?php }?>
                    </tbody>
                </table><br>
            <?php } ?>
            <div class="about">
                <b><?php echo htmlspecialchars($user['username']); ?>'s comments</b>
            </div><br>
            <div class="comment">
                <form method="post" enctype="multipart/form-data" id="submitform">
                    <?php if(isset($error)) { echo $error . "<br>"; } ?>
                    <b>Comment</b> <br>
                    <?php if($user['privacyArray'][2] != "friend") { ?>
                        <textarea cols="32" id="com" placeholder="Comment" name="comment"></textarea><br><small><a href="https://www.markdownguide.org/basic-syntax">Markdown</a> & Emoticons are allowed.</small><br>
                        <script src="/js/commd.js"></script>
                        <input type="submit" value="Post" class="g-recaptcha" data-sitekey="<?php echo $config['recaptcha_sitekey']; ?>" data-callback="onLogin">
                    <?php } else { ?> This user only allows friends to comment.<?php } ?>
                </form>
            </div><br>
            <table id="userWall">
                <tbody>
                <?php
                $stmt = $conn->prepare("SELECT * FROM comments WHERE toid = ? AND status = 'p' ORDER BY id DESC");
                $stmt->bind_param("i", $_GET['id']);
                $stmt->execute();
                $result = $stmt->get_result();

                while($row = $result->fetch_assoc()) {
                    ?>
                    <tr>
                        <td class="tableLeft">
                            <a href="profile.php?id=<?php echo getIDFromUser($row['author'], $conn); ?>"><div><b><center><?php echo $row['author']; ?></center></b></div><img src="/dynamic/pfp/<?php echo getPFPFromUser($row['author'], $conn); ?>"></a>
                        </td>
                        <td class="tableRight">
                            <div><b class="date"><?php echo $row['date']; ?></b> <img src="/static/silk/award-star-gold-2-icon.png"> <?php if(isset($user['ownAccount'])) { echo "<a href='/lib/unpin.php?id=" . $row['id'] . "'>[unpin]</a> <a href='/lib/deletecomment.php?id=" . $row['id'] . "'>[x]</a>"; } ?></div><div><p><?php echo parseText($row['comment']); ?></p></div>
                        </td>
                    </tr>
                <?php } ?>
                <?php
                $stmt = $conn->prepare("SELECT * FROM comments WHERE toid = ? AND status = 'n' ORDER BY id DESC LIMIT 100");
                $stmt->bind_param("i", $_GET['id']);
                $stmt->execute();
                $result = $stmt->get_result();
                $rows = mysqli_num_rows($result);

                echo "<small>[" . $rows . "/100]</small>";
                while($row = $result->fetch_assoc()) {
                    ?>
                    <tr>
                        <td class="tableLeft">
                            <a href="profile.php?id=<?php echo getIDFromUser($row['author'], $conn); ?>"><div><b><center><?php echo $row['author']; ?></center></b></div><img src="/dynamic/pfp/<?php echo getPFPFromUser($row['author'], $conn); ?>"></a>
                        </td>
                        <td class="tableRight">
                            <div><b class="date"><?php echo $row['date']; ?></b> <?php if($row['author'] == $_SESSION['siteusername'] && !isset($user['ownAccount'])) { echo " <a href='/lib/delcommentfroma.php?id=" . $row['id'] . "'>[x]</a>"; } ?><?php if(isset($user['ownAccount'])) { echo "<a href='/lib/pin.php?id=" . $row['id'] . "'>[pin]</a> <a href='/lib/deletecomment.php?id=" . $row['id'] . "'>[x]</a>"; } ?></div><div><p><?php echo parseText($row['comment']); ?></p></div>
                        </td>
                    </tr>
                <?php } ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
<br>
<?php require($_SERVER['DOCUMENT_ROOT'] . "/static/footer.php"); ?>
</body>
</html>
