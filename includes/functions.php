<?php
// For debugging - we might want this set to 0 in production
ini_set('display_errors', 0);

// Include the config class before starting the session
require_once(dirname(__FILE__) . '/ConfigClass.php');

if (!isset($_SESSION)) {
	session_start();
}

// Load in the settings file
$config_file = dirname(__FILE__) . '/../config/config.php';
if (file_exists($config_file)) {
	if (array_key_exists('settingsMod', $_SESSION)) {
		// Refresh settings every 60s
		if ($_SESSION['settingsMod'] < (time() - 60)) {
			$_SESSION['settings'] = new ConfigClass($config_file);
			$_SESSION['settingsMod'] = time();
		} else {
		}
	} else {
		$_SESSION['settings'] = new ConfigClass($config_file);
		$_SESSION['settingsMod'] = time();
	}
	$settings = $_SESSION['settings'];
} else {
	if (strstr($_SERVER['REQUEST_URI'], 'settings.php') === false) {
		if (strstr($_SERVER['REQUEST_URI'], 'datafactory') !== false) {
			// If we are in one of the datafactory files, just print a message
			echo 'Config file not found';
			trigger_error('PlexWatchWeb :: Config file not found.', E_USER_ERROR);
		}
		header('Location: ' . getBase() . '/settings.php');
		exit; // FIXME: or return?
	}
}

// Attempt to set the timezone
date_default_timezone_set(@date_default_timezone_get());

/* Either load or return the plexWatch config
 * we might want to time this at some point.
 * If a user doesn't close the browser, this will never update
 */
function loadPwConfig() {
	// FIXME: Expire the cache after an appropriate amount of time
	if (!isset($_SESSION['pwc'])) {
		$database = dbconnect();
		$query = "SELECT json FROM config";
		$result = getResults($database, $query);
		if ($result = $result->fetchColumn()) {
			if ($json = json_decode($result)) {
				$_SESSION['pwc'] = keysToLower($json);
			}
		}
	} else {
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
	global $settings;

	if (!extension_loaded('PDO')) {
		$error_msg = 'PHP PDO is not enabled. Please enable this ' .
			'requirement and restart your webserver before continuing.';
		echo '<div class="alert alert-warning ">' . $error_msg . '</div>';
		trigger_error($error_msg, E_USER_ERROR);
	}
	if (!in_array('sqlite', PDO::getAvailableDrivers(), true)) {
		$error_msg = 'PDO SQlite driver is not installed. Please install this ' .
			'requirement and restart your webserver before continuing.';
		echo '<div class="alert alert-warning ">' . $error_msg . '</div>';
		trigger_error($error_msg, E_USER_ERROR);
	}

	try {
		$database = new PDO('sqlite:' . $settings->getPlexWatchDb());
		// Throw exceptions on errors
		$database->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
		// Wait up to 5 seconds before giving up on getting a lock
		$database->setAttribute(PDO::ATTR_TIMEOUT, 5);
		return $database;
	} catch (PDOException $e) {
		$error_msg = 'Connection to the database failed: ' . $e->getMessage() .
			"\nStack Trace:\n" . $e->getTraceAsString();
		echo '<p>' . $error_msg . '</p>';
		trigger_error($error_msg, E_USER_ERROR);
	}
}

/* DBtable -- processed or grouped */
function dbTable($groupType = 'global') {
	global $settings;
	switch ($groupType) {
		case 'global':
			if ($settings->getGlobalGrouping()) {
				return 'grouped';
			}
			break;
		case 'user':
			if ($settings->getUserGrouping()) {
				return 'grouped';
			}
			break;
		case 'charts':
			if ($settings->getChartsGrouping()) {
				return 'grouped';
			}
			break;
		default:
			break;
	}
	return 'processed';
}

function getResults($database, $query, $params = NULL) {
	if (!isset($params)) {
		// Simple query
		try {
			$results = $database->query($query);
		} catch (PDOException $e) {
			$error_msg = 'There was a problem running "' . $query . "\".\n" .
				'Error: ' . $e->getMessage() . "\n" .
				"Stack Trace:\n" . $e->getTraceAsString();
			echo '<p>' . $error_msg . '</p>';
			trigger_error($error_msg, E_USER_ERROR);
		}
	} else {
		// Parameterized query
		try {
			$statement = $database->prepare($query);
		} catch (PDOException $e) {
			$error_msg = 'There was a problem preparing "' . $query . "\".\n" .
				'Error: ' . $e->getMessage() . "\n" .
				"Stack Trace:\n" . $e->getTraceAsString();
			echo '<p>' . $error_msg . '</p>';
			trigger_error($error_msg, E_USER_ERROR);
		}
		try {
			$statement->execute($params);
			$results = $statement;
		} catch (PDOException $e) {
			$error_msg = 'There was a problem executing "' . $query . "\".\n" .
				'Error: ' . $e->getMessage() . "\n" .
				"Stack Trace:\n" . $e->getTraceAsString();
			echo '<p>' . $error_msg . '</p>';
			trigger_error($error_msg, E_USER_ERROR);
		}
	}
	if ($results === false) {
		$error_msg = 'There was a problem running "' . $query . '".';
		echo '<p>' . $error_msg . '</p>';
		trigger_error($error_msg, E_USER_ERROR);
	}
	return $results;
}

function getPMSData($path) {
	global $settings;
	if (strpos($path, '?')) {
		$tokenPrefix = '&';
	} else {
		$tokenPrefix = '?';
	}
	if (strlen($settings->getPlexAuthToken()) > 0) {
		$myPlexAuthToken = $tokenPrefix .
			'X-Plex-Token=' . $settings->getPlexAuthToken();
	} else {
		$myPlexAuthToken = '';
	}
	$pmsUrl = $settings->getPmsUrl();
// error_log('PMS URL: ' . $pmsUrl);
	$url = $pmsUrl . $path . $myPlexAuthToken;
	$curlHandle = curl_init($url);
	curl_setopt($curlHandle, CURLOPT_RETURNTRANSFER, true);
	curl_setopt($curlHandle, CURLOPT_SSL_VERIFYPEER, false);
	curl_setopt($curlHandle, CURLOPT_SSL_VERIFYHOST, false);
	$data = curl_exec($curlHandle);
	if ($data === false || curl_getinfo($curlHandle, CURLINFO_HTTP_CODE) >= 400) {
		curl_close($curlHandle);
		$msg = 'Failed to retrieve "' . $url . '"';
		echo $msg;
		trigger_error($msg, E_USER_ERROR);
		return false;
	}
	curl_close($curlHandle);
	return $data;
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

/**
	* Takes in a parsed session xml and returns the platform image URL
	*
	* Suppress PHPMD warnings about the complexity of this function
	* @SuppressWarnings(PHPMD.CyclomaticComplexity)
	*/
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
	} else if(strstr($xml->Player['platform'], 'Opera')) {
		return "images/platforms/opera.png";
	} else if(strstr($xml->Player['platform'], 'KODI')) {
		return "images/platforms/kodi.png";
	} else if(strstr($xml->Player['platform'], 'Mystery 3')) {
		return "images/platforms/playstation.png";
	} else if(strstr($xml->Player['platform'], 'Mystery 4')) {
		return "images/platforms/playstation.png";
	} else if (empty($xml->Player['platform'])) {
		if (strstr($xml->Player['title'], 'Apple')) {
			return "images/platforms/atv.png";
		} else if (stristr($xml->Player['title'], 'Plex for Sony')) {
			return "images/platforms/playstation.png";
		} else if (preg_match("/TV [a-z][a-z]\d\d[a-z]\d\d\d\d/i",
				$xml->Player['title'])) {
			/* Matches Samsung naming standard:
			 * [Display Technology: 2 Letters][Size: 2 digits]
			 *   [Generation: 1 letter][Model: 4 digits]
			 */
			return "images/platforms/samsung.png";
		}
	}
	return "images/platforms/default.png";
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