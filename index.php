<?php require($_SERVER['DOCUMENT_ROOT'] . "/static/config.inc.php"); ?>
<?php require($_SERVER['DOCUMENT_ROOT'] . "/static/conn.php"); ?>
<?php require($_SERVER['DOCUMENT_ROOT'] . "/lib/profile.php"); ?>
<!DOCTYPE html>
<html>
    <head>
        <title><?php echo $config['pr_title']; ?></title>
        <link rel="stylesheet" href="/static/css/required.css"> 
        <style>
            .customtopLeft {
                float: left;
                width: calc( 60% - 20px );
                padding: 10px;
            }

            .customtopRight {
                float: right;
                width: calc( 40% - 20px );
                padding: 10px;
            }

            #login {
                text-align: center;
                border: 1px solid #039;
                margin: 0 0 10px 0;
                padding: 5px;
            }

            .grid-container {
                display: grid;
                grid-template-columns: auto auto auto;
                grid-gap: 3px;
                padding: 3px;
            }
            
            .grid-container > div {
                text-align: center;
            }

            .grid-container2 > div img {
                width: 49px;
                height: 49px;
            }

            .grid-container2 {
                display: grid;
                grid-template-columns: auto auto auto auto;
                grid-gap: 3px;
                padding: 3px;
            }
            
            .grid-container2 > div {
                text-align: center;
            }

            .grid-container > div img {
                width: 49px;
                height: 49px;
            }

            ul {
                list-style-type: square;
                padding-left: 20px;
                margin: 0px;
            }
        </style>
    </head>
    <body>
        <div class="container">
            <?php require($_SERVER['DOCUMENT_ROOT'] . "/static/header.php"); ?>
            <br>
            <div class="customtopLeft">
                <div class="padding">
                    <div class="hero">
                        <h1 id="noMargin">SpaceMy Forever</h1>
                        An opensourced passion project to replicate/mimmic the feel and customizability of 2008 MySpace. It was forked from the former spacemy.xyz. This is currently heavily under development, so expect some bugs or security bugs once in a while.<br><?php if(!isset($_SESSION['siteusername'])): ?><br><a href="register.php"><button>Join</button></a><?php endif ?>
                    </div><br>
                    <div class="login">
                        <div class="loginTopbar">
                            <b>Online Users</b>
                        </div>
                        <div class="grid-container2">
                            <?php
                                $stmt = $conn->prepare("SELECT * FROM users");
                                $stmt->execute();
                                $result = $stmt->get_result();

                                while($row = $result->fetch_assoc()) { 
                                    $lastLoginReal = (int)strtotime($row['lastlogin']);
                                    $NetTimeOnline = time() - $lastLoginReal;
                                    //echo $lastLoginReal."-lastlogin<br>". time() . "-time<br>". $NetTimeOnline ."-net<br>";
                                    //if(time() - $lastLoginReal < 15 * 60)
                                    // 4.8 hours
                                    if($NetTimeOnline < 17280) { ?>
                                    <div class="item1"><a href="profile.php?id=<?php echo getIDFromUser($row['username'], $conn); ?>"><div><center><?php echo $row['username']; ?></center></div><img src="/dynamic/pfp/<?php echo getPFPFromUser($row['username'], $conn); ?>"></a></div>
                                    <?php }    
                                } ?>
                        </div>
                    </div><br>
                    <div class="splashBlue">
                        Always make sure you're visiting the real SpaceMy!
                        <ul>
                            <li>Check the URL in your browser.</li>
                            <li>Make sure it begins with https://spacemy.xyz/</li>
                            <li>If ANY OTHER PAGE asks for your info, DON'T LOG IN!</li>
                        </ul>
                    </div>
                    <br>
                    <div class="login">
                        <div class="loginTopbar">
                            <b>SpaceMy Member Blogs</b><span style="float: right; color: white;"><small><a style="color: white;" href="/blogs/">[view more]</a></small></span>
                        </div>
                        <ul>
                        <?php
                            ini_set('display_errors', 1);
                            ini_set('display_startup_errors', 1);
                            error_reporting(E_ALL);

                            $stmt = $conn->prepare("SELECT * FROM blogs WHERE visiblity = 'Visible' ORDER BY id DESC LIMIT 10");
                            $stmt->execute();
                            $result = $stmt->get_result();

                            while($row = $result->fetch_assoc()) {
                        ?>
                            <li><span id="blogPost"><?php echo $row['subject']; ?> by <b><a href="/profile.php?id=<?php echo getIDFromUser($row['author'], $conn); ?>"><?php echo htmlspecialchars($row['author']); ?></a></b> [<a href="/blogs/view.php?id=<?php echo $row['id']; ?>">+</a>]</span></span></li>
                        <?php } ?>
                        </ul>
                    </div><br>
                    <div class="section" id="splash_greybox">
                        <span id="ctl00_Main_SplashDisplay_Splash">
                            <script type="text/javascript">
                                MySpaceRes.Header = {"Cancel":"Cancel / Cancelación","Continue":"Continue / Continuar"};
                            </script>
                        <style>
                            #splash_greybox {height: auto;border:none;background-color:transparent;}
                            #splash_graybox {background-color:#F2F5F7; border:1px solid #D0E4FD; padding-bottom:1px;}
                            #splash_graybox {width:496px;}
                            #splash_graybox .grayboxtable a {height:16px;padding-left:2px;font-family: Arial;font-weight:normal; font-size:12px;cursor:hand;}
                            #splash_graybox .grayboxtable a span {height:18px;vertical-align:middle;}
                            #splash_graybox {padding-top:4px;padding-bottom:2px;}
                            #splash_graybox .grayboxtable col {width:110px;}
                            #splash_graybox .grayboxtable td {padding-bottom:2px; _padding-bottom:0px;}

                            .gbicon
                            {
                                background:transparent url(/static/graybox006.gif) no-repeat scroll 0%;
                                float:left;
                                height:16px;
                                margin:0;
                                padding:0;
                                vertical-align:middle;
                                width:24px;
                            }

                            #gbitem { position: relative; width: 98%; background:none !important; padding:2px !important; clear:both; }
                            #gbitem a { font-family: Arial, Verdana; color: #1f1f7a; font-size: 11px; }
                            .gbicon { position: relative; }

                            #imgbicon {background-position:0px -5px;}
                            #profileeditorgbicon {background-position:0pt -23px;}
                            #blogsgbicon {background-position:0pt -42px;}
                            #chatroomsgbicon {background-position:0pt -61px;}
                            #classifiedsgbicon {background-position:0pt -79px;}
                            #eventsgbicon {background-position:0pt -97px;}
                            #forumsgbicon {background-position:0pt -116px;}
                            #groupsgbicon {background-position:0pt -134px;}
                            #impactgbicon {background-position:0pt -152px;}
                            #jobsgbicon {background-position:0pt -170px;}
                            #newsgbicon {background-position:0pt -188px;}
                            #pollsgbicon {background-position:0pt -568px;}
                            #weathergbicon {background-position:0pt -205px;}
                            #booksgbicon {background-position:0pt -221px;}
                            #comedygbicon {background-position:0pt -240px;}
                            #downloadsgbicon {background-position:0pt -258px;}
                            #filmmakersgbicon {background-position:0pt -277px;}
                            #horoscopesgbicon {background-position:0pt -295px;}
                            #moviesgbicon {background-position:0pt -313px;}
                            #musicgbicon {background-position:0pt -334px;}
                            #musicvideosgbicon {background-position:0pt -353px;}
                            #myspacetvgbicon {background-position:0pt -371px;}
                            #sportsgbicon {background-position:0pt -387px;}
                            #tvondemandgbicon {background-position:0pt -404px;}
                            #mobilegbicon {background-position:0pt -424px;}
                            #ringtonesgbicon {background-position:0pt -442px;}
                            #textalertsgbicon {background-position:0pt -459px;}
                            #findclassmatesgbicon {background-position:0pt -479px;}
                            #grademyprofgbicon {background-position:0pt -497px;}
                            #latinogbicon {background-position:0pt -517px;}
                            #mobilegamegbicon {background-position:0pt -534px;}
                            #celebritygbicon {background-position:0pt -590px;}
                            </style>



                        <div id="splash_graybox">

                        <table class="grayboxtable" cellspacing="0" cellpadding="0" border="0">
                        <colgroup>
                        <col><col><col><col>
                        </colgroup>
                            <tbody><tr>
                            <td><a href="forum">
                                <div class="gbicon" id="forumsgbicon"></div>
                                <span>Forum</span></a></td>
                            <td><a href="jukebox.php">
                                <div class="gbicon" id="musicgbicon"></div>
                                <span>Jukebox</span></a></td>
                            <td><a href="groups">
                                <div class="gbicon" id="findclassmatesgbicon"></div>
                                <span>Groups</span></a></td>
                            <td><a href="blogs">
                                <div class="gbicon" id="chatroomsgbicon"></div>
                                <span>Blogs</span></a></td>
                            </tr>
                            <tr>

                            <td style="DISPLAY: none"><a href="files">
                                <div class="gbicon" id="groupsgbicon"></div>
                                <span>Files</span></a></td>
                            <td><a href="pms.php">
                                <div class="gbicon" id="comedygbicon"></div>
                                <span>PMs</span></a></td>
                            <td><a href="friends">
                                <div class="gbicon" id="impactgbicon"></div>
                                <span>Friends</span></a></td>
                            <td><a href="users.php">
                                <div class="gbicon" id="imgbicon"></div>
                                <span>Users</span></a></td>
                            </tr>
                        </tbody></table>
                        </div>
                        <div class="clear"></div>
                        
                        </span>
                    </div>
                </div>
            </div>
	    <div class="customtopRight">
            <?php if(!isset($_SESSION['siteusername'])): ?>
                <div id="login">
					<b>Member Login</b>
					<form>
						<table>
							<tbody><tr class="email">
								<td class="label"><label for="email">E-Mail:</label></td>
								<td class="input"><input type="text" name="email" id="email"></td>
							</tr>
							<tr class="password">
								<td class="label"><label for="password">Password:</label></td>
								<td class="input"><input name="password" type="password" id="password"></td>
							</tr>
							<tr class="remember">
								<td colspan="2"><input type="checkbox" name="Remember" value="Remember" id="checkbox">
								<label for="checkbox">Remember my E-mail</label></td>
							</tr>
							<tr class="buttons">
								<td colspan="2"><a href="login.php"><img src="/static/button_login_main.gif" alt="SIGN UP NOW!" name="singup" id="singup" border="0"></a>
								<a href="register.php"><img src="/static/button_signup_main.gif" alt="SIGN UP NOW!" name="singup" id="singup" border="0"></a></td>
							</tr>
							<tr class="forgot">
								
							</tr>
						</tbody></table>
					</form>
				</div><br>
                                <?php endif ?>
                <div class="login">
                    <div class="loginTopbar">
                        <b>Cool New People</b><span style="float: right; color: white;"><small><a style="color: white;" href="/users.php">[view more]</a></small></span>
                    </div>
                    <div class="grid-container">
                        <?php
                            $stmt = $conn->prepare("SELECT * FROM users ORDER BY id DESC LIMIT 3");
                            $stmt->execute();
                            $result = $stmt->get_result();

                            while($row = $result->fetch_assoc()) { 
                        ?>
                            <div class="item1"><a href="profile.php?id=<?php echo getIDFromUser($row['username'], $conn); ?>"><div><center><?php echo $row['username']; ?></center></div><img src="/dynamic/pfp/<?php echo getPFPFromUser($row['username'], $conn); ?>"></a></div>
                        <?php } ?>
                    </div>
                </div>
                <br>
                
                
         <div class="login">
               <div class="loginTopbar">
                  <b>Changelogs</b><span style="float: right; color: white;"></span>
               </div>
               <?php /* Remove this if you want a changelog or something lol
               <div class="grid-container">
                  <?php include 'static/changelog.php';  ?>
               </div> */ ?>
	       
            </div>
            <br>
         </div>
            
		<!-- Promotional -->
         <div class="padding10">
            <table class="cols" style="float: left;">
               <tbody>
                  <tr>
                     <td>
                        <b>Get Started!</b><br>
                        Join for free, and view profiles, connect with others, blog, customize your profile, and much more!<br><br><br><br>
                        <span id="splash">» <a href="register.php">Learn More</a></span>
                     </td>
                     <td>
                        <b>Create Your Profile!</b><br>
                        Tell us about yourself, upload your pictures, and start adding friends to your network.<br><br><br><br><br>
                        <span id="splash">» <a href="register.php">Start Now</a></span>
                     </td>
                     <td>
                        <b>Browse Profiles!</b><br>
                        Read through all of the profiles on SpaceMy! See pix, read blogs, and more!<br><br><br><br><br>
                        <span id="splash">» <a href="users.php">Browse Now</a></span>
                     </td>
                     <td>
                        <b>Invite Your Friends!</b><br>
                        Invite your friends, and as they invite their friends your network will grow even larger!<br><br><br><br><br>
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
