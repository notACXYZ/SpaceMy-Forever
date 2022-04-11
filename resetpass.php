<?php require($_SERVER['DOCUMENT_ROOT'] . "/static/config.inc.php"); ?>
<?php require($_SERVER['DOCUMENT_ROOT'] . "/static/conn.php"); ?>
<?php require($_SERVER['DOCUMENT_ROOT'] . "/lib/register.php"); ?>
<!DOCTYPE html>
<html>
    <head>
        <title><?php echo $config['pr_title']; ?></title>
        <link rel="stylesheet" href="/static/css/required.css"> 
        <script src='https://www.google.com/recaptcha/api.js' async defer></script>
        <script>function onLogin(token){ document.getElementById('submitform').submit(); }</script>
    </head>
    <body>
        <div class="container">
            <?php require($_SERVER['DOCUMENT_ROOT'] . "/static/header.php"); ?>
            <br>
            <div class="padding">
                <div class="padding">
                    <?php 
                    if($_SERVER['REQUEST_METHOD'] == 'POST' && $_POST['password'] && $_POST['username']) {                        
                        if($_POST['password'] !== $_POST['confirm']){ $error = "password and confirmation password do not match"; goto skip; }
    
                        if(strlen($username) > 21) { $error = "your username must be shorter than 21 characters"; goto skip; }
                        if(strlen($password) < 8) { $error = "your password must be at least 8 characters long"; goto skip; }
                        if(!preg_match('/[A-Za-z].*[0-9]|[0-9].*[A-Za-z]/', $password)) { $error = "please include both letters and numbers in your password"; goto skip; }
                        if(!isset($_POST['g-recaptcha-response'])){ $error = "captcha validation failed"; goto skip; }
                        if(!validateCaptcha($config['recaptcha_secret'], $_POST['g-recaptcha-response'])) { $error = "captcha validation failed"; goto skip; }

                        $to = htmlspecialchars($_POST['email']);
                        $subject = "SpaceMy Forever - Password Reset";
                        $txt = "SpaceMy Forever - Forgot Password\nUser: ?\n<a href='https://spacemy.acxyz.ca/resetpassword.php'><button>Reset</button></a>";
                        $txt = wordwrap($txt, 70);
                        $headers = "From: noreply@spacemy.acxyz.ca";

                        mail($to, $subject, $txt, $headers);
                    }
                    skip:
                    ?>
                    <?php if(isset($error)) { echo "<small style='color:red'>".$error."</small>"; } ?>
                    <center>
                    <div id="login" style="width: 236px;">
                        <b>Forgot Password</b>
                        <form action="" method="post" id="submitform">
                            <table>
                                <tbody><tr class="email">
                                    <td class="label"><label for="email">E-Mail:</label></td>
                                    <td class="input"><input type="email" name="email" id="email"></td>
                                </tr>
                                <tr class="buttons">
                                    <td colspan="2"><input type="submit" value="Send" class="g-recaptcha" data-sitekey="<?php echo $config['recaptcha_sitekey']; ?>" data-callback="onLogin"></td>
                                </tr>
                            </tbody></table>
                        </form>
                        </center>
                    </div>
                </div><br>
            </div>
        </div>
        <br>
        <?php require($_SERVER['DOCUMENT_ROOT'] . "/static/footer.php"); ?>
    </body>
</html>
