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
            <div class="padding">
                <h1 id="noMargin">About</h1><br>
				<h3 id="noMargin">Original </h3>
                <ul>
                    <li><a href="https://github.com/the-real-sumsome">chief bazinga</a></li>
                    <li><a href="https://github.com/typicalname0">typicalname0</a></li>
                    <li><a href="https://github.com/ezist">ezist</a></li>
                </ul><br>
				
				<h3 id="noMargin">Rehost <small>(SpaceMy Forever)</small></h3>
                <ul>
                    <li><a href="https://github.com/notACXYZ/">ACXYZ <small>(aka bobman99)</small></a></li>
                </ul>

                Made with MySQL, PHP 7.3 and Composer.<br><br>
                <b>Composer Packages</b><br>
                <ul>
                    <li><a href="https://github.com/SmItH197/SteamAuthentication">Steam PHP OAuth</a></li>
                    <li><a href="https://github.com/erusev/parsedown">Parsedown</a></li>
                    <li><a href="https://github.com/matthiasmullie/minify">Minify</a></li>
					<li><a href="https://github.com/snipe/banbuilder">BanBuilder</a></li>
                </ul>
            </div>
        </div>
        <br>
        <?php require($_SERVER['DOCUMENT_ROOT'] . "/static/footer.php"); ?>
    </body>
</html>
