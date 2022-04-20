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
			
			.container {
				width:90%;
			}
        </style>
    </head>
    <body>
	
        <div class="container">
            
			<?php require($_SERVER['DOCUMENT_ROOT'] . "/static/header.php"); ?>
            <div class="padding">
			<h1>Admin Panel</h1>
			<?php
			session_start();
			ini_set("display_errors", 1);

			ini_set("display_startup_errors", 1);
			error_reporting(E_ALL);
			if (!isset($_SESSION["siteusername"])) {
				header("Location: ../index.php");
			}
			if (isAdmin($_SESSION["siteusername"], $conn) == false) {
				die("Ur not admin");
			} else {
				if (@$_POST["purge"]) {
					archiveAllUserInfo($_POST["subject"], $conn);
					logDB(
						$_SESSION["siteusername"] .
							" purged posts from a user named " .
							$_POST["subject"],
						$conn
					);
					echo "Suces<br>";
				} elseif (@$_POST["ban"]) {
					delAccount($_POST["subject"], $conn);
					logDB(
						$_SESSION["siteusername"] .
							" has deleted a user named " .
							$_POST["subject"],
						$conn
					);
					echo "succes<br>";
				} elseif (@$_POST["del"]) {
					delPostsFromUser($_POST["subject"], $conn);
					logDB(
						$_SESSION["siteusername"] .
							" has deleted posts from a user named " .
							$_POST["subject"],
						$conn
					);
					echo "succes<br>";
				}
				echo "Ready.";
			}
			function _getServerLoadLinuxData()
			{
				if (is_readable("/proc/stat")) {
					$stats = @file_get_contents("/proc/stat");
					if ($stats !== false) {
						// Remove double spaces to make it easier to extract values with explode()
						$stats = preg_replace("/[[:blank:]]+/", " ", $stats); // Separate lines
						$stats = str_replace(["\r\n", "\n\r", "\r"], "\n", $stats);
						$stats = explode("\n", $stats);
						// Separate values and find line for main CPU load
						foreach ($stats as $statLine) {
							$statLineData = explode(" ", trim($statLine));
							// Found!
							if (count($statLineData) >= 5 && $statLineData[0] == "cpu") {
								return [
									$statLineData[1],
									$statLineData[2],
									$statLineData[3],
									$statLineData[4],
								];
							}
						}
					}
				}
				return null;
			}
			// Returns server load in percent (just number, without percent sign)
			function getServerLoad()
			{
				$load = null;
				if (stristr(PHP_OS, "win")) {
					$cmd = "wmic cpu get loadpercentage /all";
					@exec($cmd, $output);
					if ($output) {
						foreach ($output as $line) {
							if ($line && preg_match("/^[0-9]+\$/", $line)) {
								$load = $line;
								break;
							}
						}
					}
				} else {
					if (is_readable("/proc/stat")) {
						// Collect 2 samples - each with 1 second period
						// See: https://de.wikipedia.org/wiki/Load#Der_Load_Average_auf_Unix-Systemen
						$statData1 = _getServerLoadLinuxData();
						sleep(1);
						$statData2 = _getServerLoadLinuxData();
						if (!is_null($statData1) && !is_null($statData2)) {
							// Get difference
							$statData2[0] -= $statData1[0];
							$statData2[1] -= $statData1[1];
							$statData2[2] -= $statData1[2];
							$statData2[3] -= $statData1[3]; // Sum up the 4 values for User, Nice, System and Idle and calculate // the percentage of idle time (which is part of the 4 values!)
							$cpuTime =
								$statData2[0] +
								$statData2[1] +
								$statData2[2] +
								$statData2[3];
							// Invert percentage to get CPU time, not idle time
							$load = 100 - ($statData2[3] * 100) / $cpuTime;
						}
					}
				}
				return $load;
			}
			?><br><b>PHP Memory Usage:</b>
			<?php
			function convert($size)
			{
				$unit = ["bytes", "KB", "MB", "GB", "TB", "PB"];
				return @round($size / pow(1024, $i = floor(log($size, 1024))), 2) .
					" " .
					$unit[$i];
			}
			echo convert(memory_get_usage(true));

			// 123 kb
			?>
			<br>
			<b>Current CPU Usage:</b>
			<?php
			$cpuLoad = getServerLoad();
			if (is_null($cpuLoad)) {
				echo "Unable to determine CPU load. (maybe too old Windows or missing rights at Linux or Windows)";
			} else {
				echo $cpuLoad . "%";
			}
			?>
			<br><br>
			<form method="post" enctype="multipart/form-data" id="submitform">
				<b>Purge Posts</b><br>
				<br><input placeholder="Subject" type="text" name="subject" required="required" size="63"></b><br>
				<input type="submit" name="purge" value="Post">
			</form>

			<form method="post" enctype="multipart/form-data" id="submitform">
				<b>Ban</b><br>
				<br><input placeholder="Subject" type="text" name="subject" required="required" size="63"></b><br>
				<input type="submit" name="ban" value="Post">
			</form>

			<form method="post" enctype="multipart/form-data" id="submitform">
				<b>Delete Posts</b><br>
				<br><input placeholder="Subject" type="text" name="subject" required="required" size="63"></b><br>
				<input type="submit" name="del" value="Post">
			</form><br><br>
			<?php
			$stmt = $conn->prepare("SELECT * FROM logs ORDER BY `logs`.`id` DESC LIMIT 10");
			$stmt->execute();
			$result = $stmt->get_result();
			while ($row = $result->fetch_assoc()) {
				echo $row["event"] . " @ " . $row["date"] . "<br>";
			}
			$stmt->close();
			 
			?>

        </div>             </div>
        <br>
        <?php require($_SERVER['DOCUMENT_ROOT'] . "/static/footer.php"); ?>
    </body>
</html>
