#!/usr/bin/php
<?php
$dbname = ;
$dbuser = ;
$dbpass = ;

$mysql_handle = mysql_connect('myzipdb.cech3cllvxv3.us-west-2.rds.amazonaws.com:3306', $dbuser, $dbpass)
    or die("Error connecting to database server");

mysql_select_db($dbname, $mysql_handle)
    or die("Error selecting database: $dbname");

echo "Successfully connected to database!\n";

$zip = '90010';
$d = 10;

$sql = "SELECT * FROM zipcode WHERE zip_code= '$zip'";

$retval = mysql_query( $sql, $mysql_handle );
if(! $retval )
{
  die('Could not create table: ' . mysql_error());
}
else {
//if found, set variables
$row = mysql_fetch_array($retval);
$lat1 = $row['lat'];
$lon1 = $row['lon'];
//earth's radius in miles
$r = 3959;
 
            //compute max and min latitudes / longitudes for search square
$latN = rad2deg(asin(sin(deg2rad($lat1)) * cos($d / $r) + cos(deg2rad($lat1)) * sin($d / $r) * cos(deg2rad(0))));
$latS = rad2deg(asin(sin(deg2rad($lat1)) * cos($d / $r) + cos(deg2rad($lat1)) * sin($d / $r) * cos(deg2rad(180))));
$lonE = rad2deg(deg2rad($lon1) + atan2(sin(deg2rad(90)) * sin($d / $r) * cos(deg2rad($lat1)), cos($d / $r) - sin(deg2rad($lat1)) * sin(deg2rad($latN))));
$lonW = rad2deg(deg2rad($lon1) + atan2(sin(deg2rad(270)) * sin($d / $r) * cos(deg2rad($lat1)), cos($d / $r) - sin(deg2rad($lat1)) * sin(deg2rad($latN))));
 

//find all coordinates within the search square's area
//exclude the starting point and any empty city values
$query = "SELECT * FROM zipcode WHERE (lat <= $latN AND lat >= $latS AND lon <= $lonE AND lon >= $lonW) AND (lat != $lat1 AND lon != $lon1) AND city != '' ORDER BY st, city, lat, lon";
if(!$rs = mysql_query($query)) {
   echo "<strong>There was an error selecting nearby ZIP Codes from the database.</strong>\n";
}
elseif(mysql_num_rows($rs) == 0) {
   echo "No nearby ZIP Codes located within the distance specified. Please try a different distance.\n";
}
else {
   //output all matches to screen
   while($row = mysql_fetch_array($rs)) {
	  echo "'$row[city]''$row[state]' '$row[zip_code]' '$row[lat]' '$row[lon]'\n";

   }
}
}
?>