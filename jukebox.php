<?php require($_SERVER['DOCUMENT_ROOT'] . "/static/config.inc.php"); ?>
<?php require($_SERVER['DOCUMENT_ROOT'] . "/static/conn.php"); ?>
<?php require($_SERVER['DOCUMENT_ROOT'] . "/lib/profile.php"); ?>
<!DOCTYPE html>
<html>
    <head>
        <title><?php echo $config['pr_title']; ?></title>
        <link rel="stylesheet" href="/static/css/required.css"> 
        <style>
            ul {
                columns: 5;
                -webkit-columns: 5;
                -moz-columns: 5;
                padding: none;
            }
        </style>
    </head>
    <body>
        <div class="container">
            <?php require($_SERVER['DOCUMENT_ROOT'] . "/static/header.php"); ?>
            <br>
            <div class="padding">
                <?php 
                    ini_set('display_errors', 1);
                    ini_set('display_startup_errors', 1);
                    error_reporting(E_ALL);
                    if(isset($_GET['random'])) {
                        $stmt = $conn->prepare("SELECT `music` FROM users WHERE `music` != 'default.mp3' ORDER BY RAND() LIMIT 1");
                        $stmt->execute();
                        $result = $stmt->get_result();
                    } else if(!isset($_GET['random'])) {
                        if(isset($_GET['id'])) {
                            $stmt = $conn->prepare("SELECT `music` FROM users WHERE id = ?");
                            $stmt->bind_param("i", $_GET['id']);
                            $stmt->execute();
                            $result = $stmt->get_result();
                        } else {
                            $stmt = $conn->prepare("SELECT `music` FROM users WHERE `music` != 'default.mp3' ORDER BY RAND() LIMIT 1");
                            $stmt->execute();
                            $result = $stmt->get_result();
                        }
                    }
                    while($row = $result->fetch_assoc()) { 
                        echo '<center><audio style="width: 39.4em;" controls><source src="/dynamic/music/' . $row['music'] . '"></audio></center><br>';
                    }
                ?>
                <center><a href="?random=true"><button>Random</button></a></center>
                <div class="login">
                    <div class="loginTopbar">
                        <b>Ze Jukebox</b>
                    </div>
                    <div class="padding">
                        <ul>
                            <?php
                                $stmt = $conn->prepare("SELECT * FROM users ORDER BY id DESC");
                                $stmt->execute();
                                $result = $stmt->get_result();

                                while($row = $result->fetch_assoc()) { 
                            ?>
                                <li><a href="?id=<?php echo $row['id']; ?>"><?php echo htmlspecialchars($row['username']); ?></a></li>
                            <?php } ?>
                        </ul>
                    </div>
                </div>

                <br>
                <table class="cols">
                    <tbody>
                        <tr>
                            <td>
                                <b>Get Started!</b><br>
                                Join for free, and view profiles, connect with others, blog, customize your profile, and much more!<br><br><br>
                                <span id="splash">» <a href="register.php">Learn More</a></span>	
                            </td>
                            <td>
                                <b>Create Your Profile!</b><br>
                                Tell us about yourself, upload your pictures, and start adding friends to your network.<br><br><br><br>
                                <span id="splash">» <a href="register.php">Start Now</a></span>		
                            </td>
                            <td>
                                <b>Browse Profiles!</b><br>
                                Read through all of the profiles on SpaceMy! See pix, read blogs, and more!<br><br><br><br>
                                <span id="splash">» <a href="users.php">Browse Now</a></span>
                            </td>
                            <td>
                                <b>Invite Your Friends!</b><br>
                                Invite your friends, and as they invite their friends your network will grow even larger!<br><br><br><br>
                                <span id="splash">» <a href="register.php">Invite Friends Now</a></span>	
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
        <br>
        <?php require($_SERVER['DOCUMENT_ROOT'] . "/static/footer.php"); ?>
    </body>
</html>
