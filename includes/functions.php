<?php
// For debugging - we might want this set to 0 in production
ini_set('display_errors', 0);

if (!isset($_SESSION)) {
	session_start();
}

/* Either load or return the plexWatch config
 * we might want to time this at some point.
 * If a user doesn't close the browser, this will never update
 */
function loadPwConfig() {
	if (!isset($_SESSION['pwc'])) {
		$database = dbconnect();
		if ($result = $database->querySingle("SELECT json_pretty from config")) {
			if ($json = json_decode($result)) {
				$_SESSION['pwc'] = keysToLower($json);
			}
		}
	}
	if (isset($_SESSION['pwc'])) {
		return $_SESSION['pwc'];
	}
}

/* Return friends name based on user/platform */
function FriendlyName($user, $platform = NULL) {
	$user = strtolower($user);
	$platform = strtolower($platform);

	$config = loadPwConfig();
	if (is_object($config)) {
		$friendlyName = $config->{'user_display'};
		if (is_object($friendlyName)) {
			if (isset($friendlyName->{$user.'+'.$platform})) {
				//print "user+platform match";
				return $friendlyName->{$user.'+'.$platform};
			} else if (isset($friendlyName->{$user})) {
				//print "user match";
				return $friendlyName->{$user};
			}
		}
	}
	return $user;
}

/* DB connector */
function dbconnect() {
	global $plexWatch;

	if (!class_exists('SQLite3')) {
		trigger_error('<div class="alert alert-warning ">' .
				'php5-sqlite is not installed. Please install this requirement and ' .
				'restart your webserver before continuing.' .
			'</div>',
			E_USER_ERROR);
	}

	$database = new SQLite3($plexWatch['plexWatchDb']);
	$database->busyTimeout(10*1000);
	return $database;
}

/* DBtable -- processed or grouped */
function dbTable($groupType = 'global') {
	global $plexWatch;
	switch ($groupType) {
		case 'global':
		case 'user':
			if ($plexWatch[$groupType.'HistoryGrouping'] == "yes") {
				return "grouped";
			}
			break;
		case 'charts':
			if ($plexWatch['chartsGrouping'] == "yes") {
				return "grouped";
			}
			break;
		default:
			break;
	}
	return "processed";
}

/* Function to lowercase all object keys. easier for matching */
function &keysToLower(&$obj) {
	$type = (int) is_object($obj) - (int) is_array($obj);
	if ($type === 0) {
		return $obj;
	}
	foreach ($obj as $key => &$val) {
		$element = keysToLower($val);
		switch ($type) {
			case 1:
				if (!is_int($key) && $key !== ($keyLowercase = strtolower($key))) {
					unset($obj->{$key});
					$key = $keyLowercase;
				}
				$obj->{$key} = $element;
				break;
			case -1:
				if (!is_int($key) && $key !== ($keyLowercase = strtolower($key))) {
					unset($obj[$key]);
					$key = $keyLowercase;
				}
				$obj[$key] = $element;
				break;
		}
	}
	return $obj;
}

/* Takes in a parsed session xml and returns the platform image URL */
function getPlatformImage($xml) {
	if (strstr($xml->Player['platform'], 'Roku')) {
		return "images/platforms/roku.png";
	} else if (strstr($xml->Player['platform'], 'Apple TV')) {
		return "images/platforms/appletv.png";
	} else if (strstr($xml->Player['platform'], 'Firefox')) {
		return "images/platforms/firefox.png";
	} else if (strstr($xml->Player['platform'], 'Chromecast')) {
		return "images/platforms/chromecast.png";
	} else if (strstr($xml->Player['platform'], 'Chrome')) {
		return "images/platforms/chrome.png";
	} else if (strstr($xml->Player['platform'], 'Android')) {
		return "images/platforms/android.png";
	} else if (strstr($xml->Player['platform'], 'Nexus')) {
		return "images/platforms/android.png";
	} else if (strstr($xml->Player['platform'], 'iPad')) {
		return "images/platforms/ios.png";
	} else if (strstr($xml->Player['platform'], 'iPhone')) {
		return "images/platforms/ios.png";
	} else if (strstr($xml->Player['platform'], 'iOS')) {
		return "images/platforms/ios.png";
	} else if (strstr($xml->Player['platform'], 'Plex Home Theater')) {
		return "images/platforms/pht.png";
	} else if (strstr($xml->Player['platform'], 'Linux/RPi-XBMC')) {
		return "images/platforms/xbmc.png";
	} else if (strstr($xml->Player['platform'], 'Safari')) {
		return "images/platforms/safari.png";
	} else if (strstr($xml->Player['platform'], 'Internet Explorer')) {
		return "images/platforms/ie.png";
	} else if (strstr($xml->Player['platform'], 'Unknown Browser')) {
		return "images/platforms/default.png";
	} else if (strstr($xml->Player['platform'], 'Windows-XBMC')) {
		return "images/platforms/xbmc.png";
	} else if (strstr($xml->Player['platform'], 'Xbox')) {
		return "images/platforms/xbox.png";
	} else if (strstr($xml->Player['platform'], 'Samsung')) {
		return "images/platforms/samsung.png";
	} else if (empty($xml->Player['platform'])) {
		if (strstr($xml->Player['title'], 'Apple')) {
			return "images/platforms/atv.png";
		} else if (preg_match("/TV [a-z][a-z]\d\d[a-z]\d\d\d\d/i",
				$xml->Player['title'])) {
			/* Matches Samsung naming standard:
			 * [Display Technology: 2 Letters][Size: 2 digits]
			 *   [Generation: 1 letter][Model: 4 digits]
			 */
			return "images/platforms/samsung.png";
		} else {
			return "images/platforms/default.png";
		}
	}
}

function printStreamDetails($xmlfield) {
	$transcoded = array_key_exists('TranscodeSession', $xmlfield);
	// Set $data based on the stream type
	if ($transcoded) {
		$data = &$xmlfield->TranscodeSession;
		// Convert source to a friendly name if needed as well
		if ($xmlfield->Media['audioCodec'] == 'dca') {
			$xmlfield->Media['audioCodec'] = 'dts';
		}
	} else {
		$data = &$xmlfield->Media;
		$data['audioDecision'] = 'Direct Play';
	}
	// Convert to a friendly name if needed
	if ($data['audioCodec'] == 'dca') {
		$data['audioCodec'] = 'dts';
	}

	echo '<div class="span4">';
		echo '<h4>Stream Details</h4>';
			echo '<ul>';
				echo '<h5>Video</h5>';
				if ($transcoded) {
					echo '<li>Stream Type: <strong>'.$data['videoDecision'].'</strong></li>';
					echo '<li>Video Resolution: <strong>'.$data['height'].'p</strong></li>';
				} else {
					echo '<li>Stream Type: <strong>Direct Play</strong></li>';
					echo '<li>Video Resolution: <strong>'.$data['videoResolution'].'p</strong></li>';
				}
				echo '<li>Video Codec: <strong>'.$data['videoCodec'].'</strong></li>';
				echo '<li>Video Width: <strong>'.$data['width'].'</strong></li>';
				echo '<li>Video Height: <strong>'.$data['height'].'</strong></li>';
			echo '</ul>';
			echo '<ul>';
				echo '<h5>Audio</h5>';
				echo '<li>Stream Type: <strong>'.$data['audioDecision'].'</strong></li>';
				echo '<li>Audio Codec: <strong>'.$data['audioCodec'].'</strong></li>';
				echo '<li>Audio Channels: <strong>'.$data['audioChannels'].'</strong></li>';
			echo '</ul>';
	echo '</div>';
	// Force $data to Media to always get the source information
	$data = &$xmlfield->Media;
	echo '<div class="span4">';
		echo '<h4>Media Source Details</h4>';
		echo '<li>Container: <strong>'.$data['container'].'</strong></li>';
		echo '<li>Resolution: <strong>'.$data['videoResolution'].'p</strong></li>';
		echo '<li>Bitrate: <strong>'.$data['bitrate'].' kbps</strong></li>';
	echo '</div>';
	echo '<div class="span4">';
		echo '<h4>Video Source Details</h4>';
		echo '<ul>';
			echo '<li>Width: <strong>'.$data['width'].'</strong></li>';
			echo '<li>Height: <strong>'.$data['height'].'</strong></li>';
			echo '<li>Aspect Ratio: <strong>'.$data['aspectRatio'].'</strong></li>';
			echo '<li>Video Frame Rate: <strong>'.$data['videoFrameRate'].'</strong></li>';
			echo '<li>Video Codec: <strong>'.$data['videoCodec'].'</strong></li>';
		echo '</ul>';
		echo '<ul></ul>';
		echo '<h4>Audio Source Details</h4>';
		echo '<ul>';
			echo '<li>Audio Codec: <strong>'.$data['audioCodec'].'</strong></li>';
			echo '<li>Audio Channels: <strong>'.$data['audioChannels'].'</strong></li>';
		echo '</ul>';
	echo '</div>';
}
?>