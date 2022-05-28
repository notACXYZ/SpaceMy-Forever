<?php require($_SERVER['DOCUMENT_ROOT'] . "/static/config.inc.php"); ?>
<?php require($_SERVER['DOCUMENT_ROOT'] . "/static/conn.php"); ?>
<?php require($_SERVER['DOCUMENT_ROOT'] . "/lib/profile.php"); ?>
<!DOCTYPE html>
<html>
    <head>
        <title><?php echo $config['pr_title']; ?></title>
        <link rel="stylesheet" href="/static/css/required.css"> 
        <script src='https://www.google.com/recaptcha/api.js' async defer></script>
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/simplemde/latest/simplemde.min.css">
        <script src="https://cdn.jsdelivr.net/simplemde/latest/simplemde.min.js"></script>
        <script src="/onLogin.js"></script>
        
        <?php $group = getGroupFromId((int)$_GET['id'], $conn); 
        if($_SESSION['siteusername'] == $group['owner']) { $ownsGroup = true; } else { $ownsGroup = false; }
        
        if($_SERVER['REQUEST_METHOD'] == 'POST' && $_POST['comment']) {
            if(!isset($_SESSION['siteusername'])){ $error = "you are not logged in"; goto skipcomment; }
            if(!$_POST['comment']){ $error = "your comment cannot be blank"; goto skipcomment; }
            if(strlen($_POST['comment']) > 500){ $error = "your comment must be shorter than 500 characters"; goto skipcomment; }
            if(!isset($_POST['g-recaptcha-response'])){ $error = "captcha validation failed"; goto skipcomment; }
            if(!validateCaptcha($config['recaptcha_secret'], $_POST['g-recaptcha-response'])) { $error = "captcha validation failed"; goto skipcomment; }

            $stmt = $conn->prepare("INSERT INTO `groupcomments` (toid, comment, author) VALUES (?, ?, ?)");
            $stmt->bind_param("iss", $_GET['id'], $text, $_SESSION['siteusername']);
            $text = htmlspecialchars($_POST['comment']);
            $stmt->execute();
            $stmt->close();
            skipcomment: 
        } else if($_SERVER['REQUEST_METHOD'] == 'POST' && $_POST['announce']) {
            echo "q";
        }
        ?>
        <?php $user = getUserFromName($group['owner'], $conn); ?>
        <meta property="og:title" content="<?php echo $group['name']; ?>" />
        <meta property="og:description" content="<?php echo preg_replace("/\"/", "&quot;", $group['description']); ?>" />
        <meta property="og:image" content="https://spacemy.acxyz.ca/dynamic/groups/<?php echo $group['pic']; ?>" />
        <style>
            .customtopLeft {
                float: left;
                width: calc( 22% - 20px );
                padding: 10px;
            }

            .customtopRight {
                float: right;
                width: calc( 78% - 20px );
                padding: 10px;
            }
        </style>
    </head>
    <body>
        <div class="container">
            <?php require($_SERVER['DOCUMENT_ROOT'] . "/static/header.php"); ?>
            <br>
            <div class="padding">
                <span id="padding10">
                    <small><a href="/">SpaceMy</a> / <a href="/groups/">Groups</a> / <a href="/groups/view.php?id=<?php echo $_GET['id']?>"><?php echo htmlspecialchars($group['name']); ?></a></small>
                </span><br>
                <div class="customtopLeft">  
                    <b><small>
                        <div class="groupssidelinks">
                            <ul>
                                <li><a href="index.php">Groups Home</a></li>
                                <li><a href="/edit/">My Groups</a></li>
                                <li><a href="new.php">Create Group</a></li>
                                <li class="last"><a href="">Search Groups</a></li>
                            </ul>
                        </div>
                    </small></b><br>
                    <div class="splashBlue">
                        Remember to make sure that your reply is not innapropriate! Have fun.
                    </div><br>
                    <center><b><?php echo htmlspecialchars($group['name']); ?></b><br><a href="/profile.php?id=<?php echo getIDFromUser($group['owner'], $conn); ?>"><div><b><?php echo $group['owner']; ?></b></div></a><br><br><img style="height: 4em; width: 4em;" src="/dynamic/groups/<?php echo $group['pic']; ?>"><br>
                    <?php if($group['private'] != "p") { ?><a href="join.php?id=<?php echo $group['id']; ?>"><button>Join</button></a></center><?php } else { ?> <b>This group is private.</b> <?php } ?><br>
                    <div class="userInfoBlog">
                        <?php echo parseText($user['bio']); ?><br>
                        <b>Gender: </b><?php echo $user['gender']; ?><br>
                        <b>Age: </b><?php echo $user['age']; ?><br>
                        <b>Location: </b><?php echo $user['location']; ?><br>
                        <b>Last Login: </b><?php echo $user['lastlogin']; ?><br><br>
                        <div class="contacting">
                            <div class="contactingTopbar">
                                Contacting
                            </div>
                            <div class="padding">
                                <ul>
                                    <li><a href="pm.php?id=<?php echo $user['id']; ?>">Message</a></li>
                                    <li><a href="/friends/add.php?id=<?php echo $user['id']; ?>">Friend</a></li>
                                    <li><a href="block.php?id=<?php echo $user['id']; ?>">Block </a></li>
                                    <li><a href="report.php?id=<?php echo $user['id']; ?>">Report</a></li>
                                </ul>
                            </div>
                        </div><br>
                        <center>
                            <small>[<a href="like.php?id=<?php echo $blog['id']; ?>">like</a>] [<a href="dislike.php?id=<?php echo $blog['id']; ?>">dislike</a>]
                            <br>[<?php echo getLikesFromBlog($blog['id'], $conn); ?> likes] [<?php echo getDislikesFromBlog($blog['id'], $conn); ?> dislikes] </small><br>
                        </center>
                    </div>
                </div>
                <div class="customtopRight">
                    <div class="splashBlue">
                        <h2 id="noMargin">Group Description</h2>
                        <?php echo htmlspecialchars($group['description']); ?>
                    </div><br>
                    <div class="splashBlue">
                        <b>Members</b><br>
                        <?php 
                            $stmt = $conn->prepare("SELECT * FROM users WHERE currentgroup = ? ORDER BY id DESC");
                            $stmt->bind_param("i", $_GET['id']);
                            $stmt->execute();
                            $result = $stmt->get_result();

                            while($row = $result->fetch_assoc()) { 
                        ?>
                            <a href="/profile.php?id=<?php echo getIDFromUser($row['username'], $conn); ?>"><?php echo $row['username']; ?></a> <?php if($ownsGroup == true) { echo " <a href='exile.php?id=" .  getIDFromUser($row['username'], $conn) . "'>[exile]</a>"; } ?><br>
                        <?php } ?>
                    </div><br>
                    <?php
                        if($ownsGroup == true) {
                    ?>
                        <div class="splashBlue">
                            <a href="https://spacemy.acxyz.ca/groups/join.php?id=<?php echo $_GET['id']?>">Copy this to invite your friends to your group.</a>
                        </div><br>
                    <?php } ?>
                    <div class="comment">
                        <form method="post" enctype="multipart/form-data" id="submitform">
                            <?php if(isset($error)) { echo $error . "<br>"; } ?>
                            <b>Reply</b><br>
                            <textarea cols="39" placeholder="Comment" id="com" name="comment"></textarea><br><small><a href="https://www.markdownguide.org/basic-syntax">Markdown</a> & Emoticons are allowed.</small><br>
                            <script>
                            var simplemde = new SimpleMDE({ element: document.getElementById("com") });
                            </script>
                            <input type="submit" value="Post" class="g-recaptcha" data-sitekey="<?php echo $config['recaptcha_sitekey']; ?>" data-callback="onLogin">
                        </form>
                    </div><br>
                    <table id="userWall">
                        <tbody>
                            <?php
                                $stmt = $conn->prepare("SELECT * FROM groupcomments WHERE toid = ? ORDER BY id DESC");
                                $stmt->bind_param("i", $_GET['id']);
                                $stmt->execute();
                                $result = $stmt->get_result();

                                while($row = $result->fetch_assoc()) { 
                            ?>
                            <tr>
                                <td class="tableLeft">
                                    <a href="/profile.php?id=<?php echo getIDFromUser($row['author'], $conn); ?>"><div><b><center><?php echo $row['author']; ?></center></b></div><img src="/dynamic/pfp/<?php echo getPFPFromUser($row['author'], $conn); ?>"></a>
                                </td>
                                <td class="tableRight">
                                    <div><b class="date"><?php echo $row['date']; ?></b></div><div><p><?php echo parseText($row['comment']); ?></p></div>
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
