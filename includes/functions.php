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
	/* if (isset($_SESSION['pwc'])) { unset($_SESSION['pwc']); } // for testing */
	if (!isset($_SESSION['pwc'])) {
		global $plexWatch;
		$db = dbconnect();
		if ($result = $db->querySingle("SELECT json_pretty from config")) {
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
function FriendlyName($user,$platform = NULL) {
	$user = strtolower($user);
	$platform = strtolower($platform);

	$config = loadPwConfig();
	if (is_object($config)) {
		$fn = $config->{'user_display'};
		if (is_object($fn)) {
			if (isset($fn->{$user.'+'.$platform})) {
				//print "user+platform match";
				return $fn->{$user.'+'.$platform};
			} else if (isset($fn->{$user})) {
				//print "user match";
				return $fn->{$user};
			}
		}
	}
	return $user;
}

/* DB connector */
function dbconnect() {
	global $plexWatch;

	if (!class_exists('SQLite3')) {
		die("<div class=\"alert alert-warning \">php5-sqlite is not installed. Please install this requirement and restart your webserver before continuing.</div>");
	}

	$db = new SQLite3($plexWatch['plexWatchDb']);
	$db->busyTimeout(10*1000);
	return $db;
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
?>
