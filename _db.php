<?php
//db info
define('dbhost', 'yourdbhost');
define('dbname', 'yourdbname');
define('dbuser', 'yourdbuser');
define('dbpasswd', 'yourdbpasswd');

$link = mysql_connect(dbhost, dbuser, dbpasswd) or die("could not connect to database");
mysql_select_db(dbname) or die("could not select database");


// QUERY abstraction
function doQuery($qryStr, $doDebug = false, $pretend=false) {
	global $link;
	/*
		$qryStr: [string] actual SQL to run
		$doDebug: [binary] to show query after its run
		$pretend: [binary] during debug it is NICE to work on parsing feed without actually running 600+ queries
	*/

	//doLog('sql',$qryStr); // log all queries during BETA
//	$qryType = strtoupper(trim(array_shift(explode(' ', $qryStr)))); // pull the first word off the query to determine the TYPE: INSERT, SELECT, UPDATE, DELETE
	if(!$pretend) {
		if(!$qryResult = mysql_query($qryStr)) {
			die("Query Failed: ".mysql_error()."<br /><pre>".$qryStr."</pre>");
		}
	}
	if ($doDebug)	{
		echo "<hr /><code>".$qryStr."</code>";
	}
	return $qryResult;
}

function spill_results($r) {// helpfup debug output for mysql recordsets
	print $r.'<pre>';
	while($row = mysql_fetch_assoc($r)) {
		print_r($row);
	}
	print '</pre>';
}


function doSelect($table, $fields='*', $match='') {
	/* 	$table[string] name of table to use in query
		$field [string or array] = fields/columns to return from $table
		$match [string] = well-formed SQL conditional eg. "WHERE description like '%cars%'"
		returns mysql result set
	*/
	if(is_array($fields)) {
		$fields = implode(',', $fields);
	}
	$select = 'SELECT '.$fields." FROM {$table} {$match}";
	return doQuery($select);
}

function doInsert($table, $fields, $params=array()) {
	/* 	$table[string] name of table to use in query
		$field [array] = associative array where index is column name and value = new value to insert into that column
		$params [array] = unused so far
		returns mysql result set (does NOT return recently INSERTed data.. this is desirable at some point)
		pay attention to doEscape() if things start acting funny... all SQL runs thru it.
	*/
	$insert = "INSERT INTO ".$table;
	$columns = array();
	$values = array(); 
	foreach($fields as $k=>$v) {
			$columns[] = $k;
			$values[] = doEscape($v);
	}
	$insert = 'INSERT INTO '.$table .'('.join(',', $columns).') VALUES ('.join(',', $values).')';
	return doQuery($insert);
}

function doUpdate($table, $fields, $match) {
	/* 	$table[string] name of table to use in query
		$field [array] = associative array where index is column name and value = new value to insert into that column
		$match [string] = well-formed SQL conditional eg. "WHERE description like '%cars%'"
		returns mysql result set (does NOT return recently UDPATEd data.. this is desirable at some point)
		pay attention to doEscape() if things start acting funny... all SQL runs thru it.
	*/
	$update = "UPDATE ".$table." SET ";
	$pairs = array();
	foreach($fields as $k=>$v) {
		$pairs[] = "`$k` = ".doEscape($v);
	}
	$update .= implode(',', $pairs) ." WHERE ".$match;
    return doQuery($update);
}

function doDelete($table, $match) {
	/* 	$table[string] name of table to use in query
		$match [string] = well-formed SQL conditional eg. "WHERE description like '%cars%'"
	*/
    doQuery("DELETE FROM ".$table." WHERE ".$match);
}


function doEscape($values, $quotes = true) { 
	/* 	$values [string or array] name of table to use in query
		$quotes = wrap everything that ain't numeric in quotes.
		!!there must be a really great MYSQL escape wrapper function out there somewhere. when it turns up I'll swap this for it.
	*/
	global $link;
    if (is_array($values)) { 
        foreach ($values as $key => $value) { 
            $values[$key] = doEscape($value, $quotes); 
        } 
    } 
    else if ($values === null) { 
        $values = 'NULL'; 
    } 
    else if (is_bool($values)) { 
        $values = $values ? 1 : 0; 
    } 
    else if (!is_numeric($values)) { 
        $values = mysql_real_escape_string($values); 
        if ($quotes) { 
            $values = '"' . $values . '"'; 
        } 
    } 
    return $values; 
}
