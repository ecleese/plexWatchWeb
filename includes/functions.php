<?php

  // this is wrong
  //include_once dirname(dirname(__FILE__)) . '/config/config.php';

/* either load or return the plexWatch config
 * we might want to time this at some point.
 * If a user doesn't close the browser, this will never update
 */
function loadPwConfig() {
  session_start();
  //unset($_SESSION['pwc']);
  if (!isset($_SESSION['pwc'])) {
    global $plexWatch;
    $db = dbconnect();
    $json = $db->querySingle("SELECT json_pretty from config");
    $_SESSION['pwc'] =  keysToLower(json_decode($json));
  }
  return $_SESSION['pwc'];
}

/* return friends name based on user/platform */
function FriendlyName($user,$platform = NULL) {
  $user = strtolower($user);
  $platform = strtolower($platform);
  
  $config = loadPwConfig();
  $fn = $config->{'user_display'};
  if (is_object($fn)) {
    if ($fn->{$user.'+'.$platform}) {
      //print "user+platform match";
      return $fn->{$user.'+'.$platform};
    } else if ($fn->{$user}) {
      //print "user match";
      return $fn->{$user};
    }
  }
  return $user;
}

/* db connector */
function dbconnect() {
  global $plexWatch;
  $db = new SQLite3($plexWatch['plexWatchDb']);
  $db->busyTimeout(10*1000);
  return $db;
}

/* dbtable -- processed of grouped */
function dbTable() {
  global $plexWatch;
  if ($plexWatch['globalHistoryGrouping'] == "yes") {
    return "grouped";
  } 
  return "processed";
}



/* function to lowercase all object keys. easier for matching */
function &keysToLower(&$obj){
  $type = (int) is_object($obj) - (int) is_array($obj);
  if ($type === 0) return $obj;
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


?>