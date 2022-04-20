<?php require($_SERVER['DOCUMENT_ROOT'] . "/static/config.inc.php"); ?>
<?php require($_SERVER['DOCUMENT_ROOT'] . "/static/conn.php"); ?>
<?php require($_SERVER['DOCUMENT_ROOT'] . "/lib/profile.php"); ?>
<?php include($_SERVER['DOCUMENT_ROOT'] . "/lib/captcha.php"); ?>
<!DOCTYPE html>
<html>
    <head>
        <title><?php echo $config['pr_title']; ?></title>
        <link rel="stylesheet" href="/static/css/required.css"> 
        <script>function onLogin(token){ document.getElementById('submitform').submit(); }</script>
    </head>
    <body>
        <div class="container">
            <?php require($_SERVER['DOCUMENT_ROOT'] . "/static/header.php"); ?>
            <br>
            <div class="padding">
                <div class="padding">
                    <?php 
                    if($_SERVER['REQUEST_METHOD'] == 'POST' && $_POST['captchacode']) {
                        
                        if(!isset($_SESSION['siteusername'])){ $error = "What the... you're not logged in! O_O"; goto skip; }
                        
                        if (isAdmin($_SESSION['siteusername'], $conn) == true) {
                            $btw = "<b>Admin detected!</b>";
                        }
                        
                        $captchacode = htmlspecialchars(@$_POST['captchacode']);

                        $MD5code = isset($_SESSION['captcha_dcode']) ? $_SESSION['captcha_dcode'] : false;
		                if($MD5code===false || !isset($_POST['captchacode']) || md5(strtoupper($_POST['captchacode'])) !== $MD5code){ // case insensitive
			                unset($_SESSION['captcha_dcode']);
			                $error = "Invalid Captcha... T_T";
		                } else {
		                    $success = "Valid Captcha! ^_^";
		                }

                    }
                    skip:
                    ?>
                    <center>
                    <div id="login" style="width: 236px;">
                        <h2>Captcha Test</h2>
                        <?php if(isset($btw)) { echo "<small style='color:black'>".$btw."</small><br>"; } ?>
                        <?php if(isset($error)) { echo "<small style='color:red'>".$error."</small>"; } ?>
                        <?php if(isset($success)) { echo "<small style='color:green'>".$success."</small>"; } ?>
                        <form action="" method="post" id="submitform">
                            <table>
                                <tbody>
								<!-- CAPTCHA START -->
								
								
                                <tr class="username">
                                    <td class="label"><label for="captcha_input"><b>Captcha:</b></label></td>
                                    <!--<td class="input"><input name="captcha_input" type="text" id="captcha_input"></td>-->
                                    
                                    <td>
                                        <a href="#" onclick="(function(){var i=document.getElementById('chaimg'),s=i.src;i.src=s+'&amp;';})();">
                                          <img src="/lib/captcha.php?o=1" alt="CAPTCHA Image" id="chaimg" />
                                        </a> 
                                        <small>(<a href="#" onclick="(function(){var i=document.getElementById('chaimg'),s=i.src;i.src=s+'&amp;';})();">Refresh</a>)
                                        </small>
                                        <br />
                                        <input type="text" name="captchacode" autocomplete="off" style="Width: 200px;"/>
                                    </td>
                                    
                                </tr>

                                <tr class="buttons">
                                    <td colspan="2"><input type="submit" value="Submit" class="g-recaptcha" data-sitekey="<?php echo $config['recaptcha_sitekey']; ?>" data-callback="onLogin"></td>
                                </tr>
								
								<!-- CAPTCHA END -->
								
                            </tbody></table>
                        </form>
                        </center>
                    </div>
                </div><br>
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