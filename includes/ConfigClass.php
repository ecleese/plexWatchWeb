<?php
ini_set('auto_detect_line_endings', true);
define('PWW_MAJOR_VERSION', 1);
define('PWW_MINOR_VERSION', 7);
define('PWW_RELEASE_VERSION', 0);

class ConfigClass {
	private $path;
	private $plexWatchDb;
	private $dateFormat;
	private $timeFormat;
	private $pmsIp;
	private $pmsPort;
	private $plexUser;
	private $plexPass;
	private $plexAuthToken;
	private $pmsUrl;
	private $globalGrouping;
	private $userGrouping;
	private $chartsGrouping;

	public function __construct($path = NULL) {
		if (!empty($path)) {
			$this->path = $path;
			$this->readFromFile();
		}
	}

	public function save() {
		$this->setFromPOST();
		$this->writeToFile();
	}

	public function getPlexWatchDb() {
		return $this->plexWatchDb;
	}

	public function getDateFormat() {
		return $this->dateFormat;
	}

	public function getTimeFormat() {
		return $this->timeFormat;
	}

	public function getPmsIp() {
		return $this->pmsIp;
	}

	public function getPmsPort() {
		return $this->pmsPort;
	}

	public function getPlexUser() {
		return $this->plexUser;
	}

	public function getPlexPass() {
		return $this->plexPass;
	}

	public function getPlexAuthToken() {
		return $this->plexAuthToken;
	}

	public function getPmsUrl() {
		return $this->pmsUrl;
	}

	public function getGlobalGrouping() {
		return $this->globalGrouping;
	}

	public function getUserGrouping() {
		return $this->userGrouping;
	}

	public function getChartsGrouping() {
		return $this->chartsGrouping;
	}

	// ************** Private Functions *****************
	private function sendError($error_msg) {
		// FIXME: Redirect properly if on settings page
		header('Location: settings.php?e=' . urlencode($error_msg));
		trigger_error($error_msg, E_USER_ERROR);
	}

	private function readFromFile() {
		if (!file_exists($this->path)) {
			$error_msg = 'Attempted to read non-existent settings!';
			$this->sendError($error_msg);
		}
		$config = file_get_contents($this->path);
		if ($config === false) {
			$error_msg = 'ConfigClass :: Error reading config file.';
			$this->sendError($error_msg);
		}
		// Attempt to read the settings into an associative array
		$data = json_decode($config, true);
		if ($data === NULL) {
			// Original setting file, or broken
			readOldSettings($config);
			return;
		}
		// FIXME: Verify attributes exist before access
		$fileVersion = $this->getVersionString($data['majorVersion'],
			$data['minorVersion'], $data['releaseVersion']);
		$currentVersion = $this->getVersionString();
		$versionCompare = version_compare($fileVersion, $currentVersion);
		if ($versionCompare > 0) {
			// FIXME: Attempt to read anyway?
			$error_msg = 'Settings file newer than we know how to handle.';
			$this->sendError($error_msg);
		} else if ($versionCompare < 0) {
			// Settings older than our current version
			$this->readOldSettings($config, $data['majorVersion'],
				$data['minorVersion'], $data['releaseVersion']);
			return;
		}
		$this->setPlexWatchDb($data['plexWatchDb']);
		$this->setDateFormat($data['dateFormat']);
		$this->setTimeFormat($data['timeFormat']);
		$this->setPmsIP($data['pmsIp']);
		$this->setPmsPort($data['pmsPort']);
		$this->setPlexUser($data['plexUser']);
		$this->setPlexPass(base64_decode($data['plexPass']));
		$this->setAuthToken($data['plexAuthToken']);
		$this->setPmsUrl();
		$this->setGlobalGrouping($data['globalGrouping']);
		$this->setUserGrouping($data['userGrouping']);
		$this->setChartsGrouping($data['chartsGrouping']);
	}

	private function readOldSettings($config, $maj = NULL, $min = NULL, $rel = NULL) {
		if (empty($maj)) {
			// Parsing an original settings file
			$this->readOrigSettings($config);
		}
		//FIXME: Delete the following, just here to shut the linter up
		$min = -1;
		$rel = -1;
	}

	private function readOrigSettings($config) {
		$data = array();
		foreach (preg_split("/((\r?\n)|(\r\n?))/", $config) as $line) {
			if (substr($line, 0, 1) === '$') {
				$config_line = explode(" = ", $line);
				preg_match("/\[\'([^\]]*)\'\]/", $config_line[0], $matches);
				preg_match("/\'(.*)\'/", $config_line[1], $matches2);
				$data[$matches[1]] = $matches2[1];
			}
		}
		// FIXME: Verify attributes exist before access
		$this->setPlexWatchDb();
		$this->setDateFormat($data['dateFormat']);
		$this->setTimeFormat($data['timeFormat']);
		$this->setPmsIP($data['pmsIp']);
		$this->setPmsPort($data['pmsHttpPort']);
		$this->setPlexUser($data['myPlexUser']);
		$this->setPlexPass(base64_decode($data['myPlexPass']));
		$this->setAuthToken($data['myPlexAuthToken']);
		$this->setPmsUrl();
		$this->setGlobalGrouping($data['globalHistoryGrouping']);
		$this->setUserGrouping($data['userHistoryGrouping']);
		$this->setChartsGrouping($data['chartsGrouping']);
	}

	private function writeToFile() {
		$data = array(
			'plexWatchDb'=>$this->plexWatchDb,
			'dateFormat'=>$this->dateFormat,
			'timeFormat'=>$this->timeFormat,
			'pmsIp'=>$this->pmsIp,
			'pmsPort'=>$this->pmsPort,
			'plexUser'=>$this->plexUser,
			'plexPass'=>base64_encode($this->plexPass),
			'plexAuthToken'=>$this->plexAuthToken,
			'globalGrouping'=>$this->globalGrouping,
			'userGrouping'=>$this->userGrouping,
			'chartsGrouping'=>$this->chartsGrouping,
			'majorVersion'=>PWW_MAJOR_VERSION,
			'minorVersion'=>PWW_MINOR_VERSION,
			'releaseVersion'=>PWW_RELEASE_VERSION
		);
		$json_opts = JSON_NUMERIC_CHECK;
		if (defined(JSON_PRETTY_PRINT)) {
			// Pretty print the config file if we are operating under PHP > 5.4.0.
			$json_opts = $json_opts | JSON_PRETTY_PRINT;
		}
		$json_data = json_encode($data, $json_opts);
		if ($json_data === false) {
			$error_msg = 'Error converting settings to JSON: ' . json_last_error_msg();
			$this->sendError($error_msg);
		}
		// FIXME: Ensure the path is in the open_basedir()
		$file = file_put_contents($this->path, $json_data);
		if ($file === false) {
			$error_msg = 'Failed to write the configuration to disk.';
			$this->sendError($error_msg);
		}
	}

	private function setFromPOST() {
		/*
		$itemId = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT,
			array('options'=>array('min_range'=>1)));
		if (!!empty($itemId) || $itemId === false) {
			echo "<p>ID field is required.</p>";
			$error_msg = 'PlexWatchWeb :: POST parameter "id" not found or invalid.';
			trigger_error($error_msg, E_USER_ERROR);
		}
		*/
		// FIXME: Again, the entire thing
		$plexWatchDb = $_POST['plexWatchDb'];
		$dateFormat = $_POST['dateFormat'];
		$timeFormat = $_POST['timeFormat'];
		$pmsIp = $_POST['pmsIp'];
		$pmsPort = $_POST['pmsPort'];
		$plexUser = $_POST['plexUser'];
		$plexPass = $_POST['plexPass'];
		if (isset($_POST['globalGrouping']) && $_POST['globalGrouping'] == 'yes') {
			$globalGrouping = true;
		} else {
			$globalGrouping = false;
		}
		if (isset($_POST['userGrouping']) && $_POST['userGrouping'] == 'yes') {
			$userGrouping = true;
		} else {
			$userGrouping = false;
		}
		if (isset($_POST['chartsGrouping']) && $_POST['chartsGrouping'] == 'yes') {
			$chartsGrouping = true;
		} else {
			$chartsGrouping = false;
		}

		$this->setPlexWatchDb($plexWatchDb);
		$this->setDateFormat($dateFormat);
		$this->setTimeFormat($timeFormat);
		$this->setPmsIP($pmsIp);
		$this->setPmsPort($pmsPort);
		$this->setPlexUser($plexUser);
		$this->setPlexPass($plexPass);
		$this->setAuthToken();
		$this->setPmsUrl();
		$this->setGlobalGrouping($globalGrouping);
		$this->setUserGrouping($userGrouping);
		$this->setChartsGrouping($chartsGrouping);
		$this->path = '../config/config.php';
	}

	// Setter functions
	private function setPlexWatchDb($path) {
		$this->plexWatchDb = $path;
		// FIXME: Ensure the file exists, is a SQLite3 database, and has PW data
		// 		and is in open_basedir()
	}

	private function setDateFormat($format) {
		$this->dateFormat = $format;
		// FIXME: Validate format?
	}

	private function setTimeFormat($format) {
		/*
		// Check if the date format is still using the old PHP formats
		if (strpos($settings->getTimeFormat(),"g") !== false ||
			strpos($settings->getTimeFormat(),"G") !== false) {
			header("Location: settings.php?error=datetime");
		}
		*/
		$this->timeFormat = $format;
		// FIXME: Validate format?
	}

	private function setPmsIP($ipAddr) {
		$this->pmsIp = $ipAddr;
		// FIXME: Validate as valid hostname/IP.
	}

	private function setPmsPort($port) {
		$this->pmsPort = $port;
		// FIXME: Validate int within port range
	}

	private function setPlexUser($user) {
		$this->plexUser = $user;
		// FIXME: Validate... length?
	}

	private function setPlexPass($pass) {
		$this->plexPass = $pass;
		// FIXME: Validate length?
	}

	private function setGlobalGrouping($enabled) {
		if ($enabled == 'yes') {
			$this->globalGrouping = true;
		} else if ($enabled == 'no') {
			$this->globalGrouping = false;
		} else {
			$this->globalGrouping = (bool) $enabled;
		}
	}

	private function setUserGrouping($enabled) {
		if ($enabled == 'yes') {
			$this->userGrouping = true;
		} else if ($enabled == 'no') {
			$this->userGrouping = false;
		} else {
			$this->userGrouping = (bool) $enabled;
		}
	}

	private function setChartsGrouping($enabled) {
		if ($enabled == 'yes') {
			$this->chartsGrouping = true;
		} else if ($enabled == 'no') {
			$this->chartsGrouping = false;
		} else {
			$this->chartsGrouping = (bool) $enabled;
		}
	}

	// Authenitcation Token Functions
	private function setAuthToken($token = NULL) {
		if (!empty($token) && $this->checkAuthToken($token)) {
			$this->plexAuthToken = $token;
			return;
		}
		if (empty($this->plexAuthToken) || !$this->checkAuthToken()) {
			$this->plexAuthToken = $this->getNewAuthToken();
			return;
		}
	}

	private function checkAuthToken($token = NULL) {
		$currentToken = $this->plexAuthToken;
		if (!empty($token)) {
			$this->plexAuthToken = $token;
		}
		$valid = $this->setPmsUrl(true);
		if (!$valid) {
			$this->plexAuthToken = $currentToken;
		}
		return $valid;
	}

	private function getNewAuthToken() {
		$plexAuthToken = '';
		if (empty($this->plexUser) || empty($this->plexPass)) {
			return $plexAuthToken;
		}
		$host = 'https://plex.tv/users/sign_in.xml';
		$process = curl_init($host);
		curl_setopt($process, CURLOPT_HTTPHEADER, array(
			'Content-Type: application/xml; charset=utf-8',
			'Content-Length: 0',
			'X-Plex-Device-Name: plexWatch/Web',
			'X-Plex-Product: plexWatch/Web',
			'X-Plex-Version: v' . $this->getVersionString(),
			'X-Plex-Client-Identifier: ' . uniqid('plexWatchWeb', true)
		));
		curl_setopt($process, CURLOPT_HEADER, false);
		curl_setopt($process, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
		curl_setopt($process, CURLOPT_USERPWD, $this->plexUser . ':' . $this->plexPass);
		curl_setopt($process, CURLOPT_TIMEOUT, 30);
		curl_setopt($process, CURLOPT_HTTPGET, true);
		curl_setopt($process, CURLOPT_POST, true);
		curl_setopt($process, CURLOPT_RETURNTRANSFER, true);
		$data = curl_exec($process);
		$authCode = curl_getinfo($process, CURLINFO_HTTP_CODE);
		$curlError = curl_error($process);
		curl_close($process);
		if ($authCode == 401) {
			// Authentication failure
			$error_msg = 'Plex.tv authentication failed. Check your Plex.tv ' .
				'username and password.';
			$this->sendError($error_msg);
		} else if ($curlError != 0) {
			// cURL error
			$error_msg = 'cURL error while retrieving data from plex.tv: ' . $curlError;
			$this->sendError($error_msg);
		} else {
			$xml = simplexml_load_string($data);
			if ($xml === false) {
				$errorCode = 'Error: Could not parse Plex.tv XML to retrieve ' .
					'authentication code.';
				$this->sendError($error_msg);
			}
			$plexAuthToken = (string) $xml['authenticationToken'][0];
			if (empty($plexAuthToken)) {
				$errorCode = 'Error: Could not find authentication code in the Plex.tv ' .
					'response.';
				$this->sendError($error_msg);
			}
		}
		return $plexAuthToken;
	}

	// Utility Functions
	private function getVersionString($maj = NULL, $min = NULL, $rel = NULL) {
		if (empty($maj) && empty($min) && empty($rel)) {
			return PWW_MAJOR_VERSION . '.' . PWW_MINOR_VERSION . '.' . PWW_RELEASE_VERSION;
		} else {
			return $maj . '.' . $min . '.' . $rel;
		}
	}

	private function setPmsUrl($checking = false) {
		if (!empty($this->pmsUrl)) {
			return $this->pmsUrl;
		}
		$prefixList = array('https://', 'http://');
		foreach ($prefixList as $prefix) {
			$pmsUrl = $prefix . $this->pmsIp . ':' . $this->pmsPort;
			if ($this->verifyPmsUrl($pmsUrl)) {
				$this->pmsUrl = $pmsUrl;
				return true;
			} else {
				continue;
			}
		}
		if (empty($this->pmsUrl) && !$checking) {
			$error_msg = 'Error: Unable to determine a valid URL for the PMS server.';
			$this->sendError($error_msg);
		}
		return false;
	}

	private function verifyPmsUrl($pmsUrl) {
		if (empty($pmsUrl)) {
			return false;
		}
		if (!empty($this->plexAuthToken)) {
			$myPlexAuthToken = '?X-Plex-Token='.$this->plexAuthToken;
		} else {
			$myPlexAuthToken = '';
		}
		$curlHandle = curl_init($pmsUrl . '/' . $myPlexAuthToken);
		curl_setopt($curlHandle, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($curlHandle, CURLOPT_SSL_VERIFYPEER, false);
		curl_setopt($curlHandle, CURLOPT_SSL_VERIFYHOST, false);
		$data = curl_exec($curlHandle);
		if ($data === false || curl_getinfo($curlHandle, CURLINFO_HTTP_CODE) >= 400) {
			curl_close($curlHandle);
			return false;
		}
		curl_close($curlHandle);
		$xml = simplexml_load_string($data);
		if ($xml === false) {
			$error_msg = 'Error: Could not parse XML from PMS server.';
			$this->sendError($error_msg);
		}
		$machineId = $xml['machineIdentifier'];
		if (empty($machineId) || strlen($machineId) < 1) {
			$error_msg = 'Error: Could not parse Plex.tv XML to retrieve ' .
				'authentication code.';
			$this->sendError($error_msg);
		}
		return true;
	}
}
?>