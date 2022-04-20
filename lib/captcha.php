<?php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
/*
mod_captcha2.php

The original concept came from various technical discussion forums, and it was a mixture of various ideas.

The main basis is the content of this page:
http://jmhoule314.blogspot.com/2006/05/easy-php-captcha-tutorial-today-im.html
Plus the result of Kris Knigga's modification in response.
*/

	/* Append the CAPTCHA image and function to the page */
	 function autoHookPostForm(&$form){ 
		$form .= '<tr><td class="postblock"><b>Captcha</b></td><td><a href="#" onclick="(function(){var i=document.getElementById(\'chaimg\'),s=i.src;i.src=s+\'&amp;\';})();"><img src="'.$mypage.'" alt="CAPTCHA Image" id="chaimg" /></a> <small>(<a href="#" onclick="(function(){var i=document.getElementById(\'chaimg\'),s=i.src;i.src=s+\'&amp;\';})();">Refresh</a>)</small><br /><input type="text" name="captchacode" autocomplete="off"/></td></tr>'."\n";
	}

	/* Check whether the password matches the password immediately after receiving the send request */
	 function autoHookRegistBegin(&$name, &$email, &$sub, &$com, $upfileInfo, $accessInfo){
		if (valid() > LEV_MODERATOR ) return; //no captcha for admin mode
		@session_start();
		$MD5code = isset($_SESSION['captcha_dcode']) ? $_SESSION['captcha_dcode'] : false;
		if($MD5code===false || !isset($_POST['captchacode']) || md5(strtoupper($_POST['captchacode'])) !== $MD5code){ // case insensitive
			unset($_SESSION['captcha_dcode']);
			error("Invalid Captcha");
		}
	}

	 function ModulePage(){
		OutputCAPTCHA(); //Generate password, CAPTCHA image
	}
	
	$DisplayCaptcha = (isset($_GET["o"]));
	if ($DisplayCaptcha == "1"){
	    ModulePage();
	}

	/* Generate CAPTCHA image, clear code, secret code and embedded Script */
	 function OutputCAPTCHA(){
		@session_start();

        $CAPTCHA_WIDTH = 150; // Width
    	$CAPTCHA_HEIGHT = 25; // Height
    	$CAPTCHA_LENGTH = 6; // Number of plain characters
    	$CAPTCHA_GAP = 20; // Clear character spacing
    	$CAPTCHA_TEXTY = 20; // character vertical position
    	$CAPTCHA_FONTMETHOD = 0; // Type of font used (0: GDF (*.gdf) 1: TrueType Font (*.ttf))
    	$CAPTCHA_FONTFACE = array('font1.gdf'); // Use zigzag (can be randomly selected, but the type of font needs to be the same and cannot be mixed)
    	$CAPTCHA_ECOUNT = 2;

		// Randomly generate clear code and secret code
		$byteTable = Array('A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J', 'K', 'L', 'M', 'N', 'O', 'P', 'Q', 'R', 'S', 'T', 'U', 'V', 'W', 'X', 'Y', 'Z'); // 明碼定義陣列
		$LCode = ''; // clear code
		for($i = 0; $i < $CAPTCHA_LENGTH; $i++) $LCode .= $byteTable[rand(0, count($byteTable) - 1)]; // random sampling
		$DCode = md5($LCode); // Cipher (clear MD5)
		$_SESSION['captcha_dcode'] = $DCode; // Password stored in Session

		// generate a temporary image
		$captcha = ImageCreateTrueColor($CAPTCHA_WIDTH, $CAPTCHA_HEIGHT);
		$randcolR = rand(100, 230); $randcolG = rand(100, 230); $randcolB = rand(100, 230); // random color code value
		$backColor = ImageColorAllocate($captcha, $randcolR, $randcolG, $randcolB); // Background color
		ImageFill($captcha, 0, 0, $backColor); // 填入背景色
		$txtColor = ImageColorAllocate($captcha, $randcolR - 40, $randcolG - 40, $randcolB - 40); // Letter color
		$rndFontCount = count($CAPTCHA_FONTFACE); // number of random fonts

		// Type in text
		for($p = 0; $p < $CAPTCHA_LENGTH; $p++){
			if($CAPTCHA_FONTMETHOD){ // TrueType font
				// Set the rotation angle (left or right)
		    	if(rand(1, 2)==1) $degree = rand(0, 25);
		    	else $degree = rand(335, 360);
				// Layer, Font Size, Rotation Angle, X-axis, Y-axis (from the bottom left of the character), Font Color, Font Type, Printed Text
				ImageTTFText($captcha, rand(14, 16), $degree, ($p + 1) * $CAPTCHA_GAP, $CAPTCHA_TEXTY, $txtColor, $CAPTCHA_FONTFACE[rand(0, $rndFontCount - 1)], substr($LCode, $p, 1));
			}else{ // GDF font
				$font = ImageLoadFont($CAPTCHA_FONTFACE[rand(0, $rndFontCount - 1)]);
				// layer, font, X axis, Y axis (from the top left of the word), print text, font color
				ImageString($captcha, $font, ($p + 1) * $CAPTCHA_GAP, $CAPTCHA_TEXTY - 18, substr($LCode, $p, 1), $txtColor);
			}
		}

		// for confusion (draw ellipse)
		for($n = 0; $n < $CAPTCHA_ECOUNT; $n++){
	    	ImageEllipse($captcha, rand(1, $CAPTCHA_WIDTH), rand(1, $CAPTCHA_HEIGHT), rand(50, 100), rand(12, 25), $txtColor);
	    	ImageEllipse($captcha, rand(1, $CAPTCHA_WIDTH), rand(1, $CAPTCHA_HEIGHT), rand(50, 100), rand(12, 25), $backColor);
		}

		// output image
		header('Content-Type: image/png');
		header('Cache-Control: no-cache');
		ImagePNG($captcha);
		ImageDestroy($captcha);
	}
