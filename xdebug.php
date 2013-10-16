<?php
// time php -d xdebug.profiler_enable=1 -d xdebug.profiler_output_dir=. xdebug.php
/*
 * We want to stop as soon as possible after the end time.
 */
declare(ticks=1);
define('END_TIME', (microtime(true) + 10));

register_tick_function(function() {
	global $random_strings;
	
	if (microtime(true) > END_TIME) {
		$string_count = count($random_strings);
		die("We're taking too long. We did get $string_count strings though.\n");
	}
});

/**
 * Generate and return a random string with a given length.
 * 
 * @param number $length
 * @return string
 */
function GenerateRandomString($length = 10) {
	$possible_characters = array_merge(range('A', 'Z'), range('a', 'z'), range('0', '9'));
	
	$the_string = '';
	for ($i = 0; ($i < $length); $i++) {
		$the_string .= $possible_characters[array_rand($possible_characters)];
	}
	
	return $the_string;
}

/*
 * Prepare our database.
 */
$db_link = new PDO('mysql:host=localhost;dbname=atlanticphp-xdebug', 'root', '');
$db_link->query('TRUNCATE string_list');

/*
 * Generate the list of random strings that we need and insert them into the database.
 */
define('NUMBER_OF_STRINGS', 10000);
$random_strings = array();

while (count($random_strings) < NUMBER_OF_STRINGS) {
	$new_string = GenerateRandomString();

	if (! in_array($new_string, $random_strings)) {
		$random_strings[] = $new_string;
		$db_link->query("INSERT INTO string_list (id, the_string) VALUES (null, '$new_string')");
	} else {
		echo "We already have the string '$new_string'. Let's start again.\n";
		$db_result = $db_link->query('DELETE * FROM string_list');
		
		if (! $db_result) {
			$error_info = $db_link->errorInfo();
			echo "There was a problem trying to remove everything from the string_list table.\n";
			echo "Error code: " . $error_info[0] . "\n";	
			echo "Error message: " . $error_info[2] . "\n";
			die();
		}
		$random_strings = array();
	}
	
	if ((count($random_strings) % 1000) == 0) {
		echo '.';
	}
}

echo "\nDONE!\n";
