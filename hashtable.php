<?php
define("HASHTABLESIZE",100);

$hashtable = array_fill(0,HASHTABLESIZE,null);
$keys = array_fill(0,HASHTABLESIZE,array());

function hashfunc($key) {
	$hash = 5381;

	for ($i = 0; $i < count($key); $i++) {
		$hash = (($hash << 5) + $hash) + ord($key[$i]);
	}

	return $hash % HASHTABLESIZE;
}

function set($key, $value) {
	global $hashtable;
	global $keys;
	$bucket = hashfunc($key);
	
	if ($hashtable[$bucket] === null) {
		$hashtable[$bucket] = $value;
		$keys[$bucket][] = $key;
	} else {
		// collision

		if (is_array($hashtable[$bucket])) {

			$found = false;
			foreach ($keys[$bucket] as $k => $v) {
				if ($v == $key) {
					$hashtable[$bucket][$k] = $value;
					$found = true;
					break;
				}
			}   

			if (!$found) {
				$hashtable[$bucket][] = $value;
			}
		} else {
			if ($keys[$bucket][0] == $key) {
				$hashtable[$bucket] = $value;
			} else {
				$hashtable[$bucket] = array($hashtable[$bucket]);
				$hashtable[$bucket][] = $value;
			}
		}	
		if (!in_array($key, $keys[$bucket])) {
			$keys[$bucket][] = $key;
		}

	}
}

function get($key) {
	global $hashtable;
	global $keys;
	$rv = false;
	$bucket = hashfunc($key);
	if ($value = $hashtable[$bucket]) {
		if (is_array($value)) {
			foreach ($keys[$bucket] as $k => $v) {
				if ($v == $key) {
					$rv = $value[$k];
					break;
				}
			}	
		} else {
			$rv = $value;
		}
	}
	return $rv;
}

function delete($key) {

	global $hashtable;
	global $keys;
	$rv = false;
	$bucket = hashfunc($key);

	if ($hashtable[$bucket] !== null) {
		if (is_array($hashtable[$bucket])) {
			foreach ($keys[$bucket] as $k => $v) {
				if ($v == $key) {
					unset($hashtable[$bucket][$k]);
					unset($keys[$bucket][$k]);
					break;
				}
			}	
			if (count($hashtable[$bucket]) < 1) {
				$hashtable[$bucket] = null;
				$keys[$bucket] = array();
			}
		} else {
			$hashtable[$bucket] = null;
			$keys[$bucket] = array();
		}
	}
	return $rv;
}

?>
