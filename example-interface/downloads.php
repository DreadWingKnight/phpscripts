<?
$database = mysql_connect( '192.168.20.53', 'opensource', 'osdata');
if( !$database )
echo "MySQL server did not connect - ".mysql_error()."<br>";
if( !mysql_select_db('opensource') )
echo "MySQL database did not select- ".mysql_error()."<br>";

$querystring = '';
if( !isset($_GET['category']) )
	$querystring = 'SELECT * FROM entries ORDER BY releaseorder DESC, pageorder DESC, entryno DESC';
else
	$querystring = "SELECT * FROM entries WHERE category = '".$_GET['category']."' ORDER BY releaseorder DESC, pageorder DESC, entryno DESC";

$query = mysql_query( $querystring ,$database);
if( $query )
{
while( $display = mysql_fetch_assoc( $query ) )
	{
	if( file_exists('./filedefs/'.$display['entryfilename']) )
	include('./filedefs/'.$display['entryfilename']);
	}
}

?>