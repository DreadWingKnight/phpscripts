<?php
/*
Copyright (c) 2006, Depthstrike Entertainment.
Module Author - Harold Feit - dwknight@depthstrike.com
All rights reserved.

Redistribution and use in source and binary forms, with or without modification, are permitted provided that the following conditions are met:

* Redistributions of source code must retain the above copyright notice, this list of conditions and the following disclaimer.
* Redistributions in binary form must reproduce the above copyright notice, this list of conditions and the following disclaimer in the documentation and/or other materials provided with the distribution.
* Neither the name of the Depthstrike Entertainment nor the names of its contributors may be used to endorse or promote products derived from this software without specific prior written permission.
* Use of this package or module without this license agreement present is only permitted with the express permission of Depthstrike Entertainment administration or the author of the module, either in writing or electronically with the digital PGP/GPG signature of a Depthstrike Entertainment administrator or the author of the module.

THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS "AS IS" AND ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT LIMITED TO, THE IMPLIED WARRANTIES OF MERCHANTABILITY AND FITNESS FOR A PARTICULAR PURPOSE ARE DISCLAIMED. IN NO EVENT SHALL THE COPYRIGHT OWNER OR CONTRIBUTORS BE LIABLE FOR ANY DIRECT, INDIRECT, INCIDENTAL, SPECIAL, EXEMPLARY, OR CONSEQUENTIAL DAMAGES (INCLUDING, BUT NOT LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS OR SERVICES; LOSS OF USE, DATA, OR PROFITS; OR BUSINESS INTERRUPTION) HOWEVER CAUSED AND ON ANY THEORY OF LIABILITY, WHETHER IN CONTRACT, STRICT LIABILITY, OR TORT (INCLUDING NEGLIGENCE OR OTHERWISE) ARISING IN ANY WAY OUT OF THE USE OF THIS SOFTWARE, EVEN IF ADVISED OF THE POSSIBILITY OF SUCH DAMAGE.

*/

$sources = array();
$querystring = '';
require_once('configure.php');

/*
// Example entry for flatfile storage:
$sources['<filename>']['sources'][] = '<full url to file>';
*/

if( $indexphp == false && $sourcesupplyphp == false && $torrentphp == false )
{
	include('downloads.php');
	for( $i = 0 ; $i < count($downloads); $i++ )
	{
		$basetorrent[$i] = BDecode(file_get_contents($downloads[$i]['torrent']));
		$downloads[$i]['hash'] = sha1(BEncode($basetorrent[$i]['info']),true);
		$basetorrent[$i]['groptout'] = $downloads[$i]['groptout'];
	}

	$sourcedb = mysql_connect( $mysqlserver, $mysqluser, $mysqlpass);
	if( !$sourcedb )
		echo "MySQL server did not connect - ".mysql_error()."<br>";
	if( !mysql_select_db($mysql_database) )
		echo "MySQL database did not select- ".mysql_error()."<br>";
	foreach( $basetorrent as $file )
	{
		if( $file['groptout'] == false )
		{
			$statefile = "./".$file['info']['name'].".sources";
			if( !file_exists($statefile) || filemtime($statefile) + 1800 <= time() )
			{
				if( $torrentphp == false )
				//echo "<!-- file ".$file['info']['name']." added to stats list at ".time()." - Previous Time: ".filemtime($statefile)." -->\n";
				file_put_contents('./'.$file['info']['name'].'.sources', file_get_contents('http://www.filemirrors.com/find.src?file='.urlencode($file['info']['name']).'&size='.$file['info']['length'].'&getright=1'));
				$input = explode("\n",file_get_contents("./".$file['info']['name'].".sources") );
				$currvalues = array();
				foreach( $input as $newsource )
				{
					if($newsource != "")
					{
						$splitsource = explode( "\"", $newsource );
						if ( $splitsource[1] != "" )
						{
							$currvalues[] = sprintf("( '%s', '%s', '%s')",sha1(BEncode($file['info'])), $file['info']['name'], $splitsource[1]);
							if( $torrentphp == false )
								echo "<!-- source $splitsource[1] for file ".$file['info']['name']." added to queue -->\n";
						}
					}
				}
				if( count($currvalues) > 0 )
				{
					$outquery = sprintf("INSERT INTO sources (`uniqueid`, `sourcefilename`, `address`) VALUES %s", implode(", ",$currvalues));
					mysql_query($outquery,$sourcedb);
					if( $torrentphp == false ) 
						echo sprintf("<!-- MySQL Error: %s -->",mysql_error());
				}

			}

		}

		$querystring = "SELECT * FROM sources WHERE uniqueid = '".sha1(BEncode($file['info']))."' ORDER BY entryno DESC";

		$query = mysql_query( $querystring ,$sourcedb);
		if( $query )
		{
			while( $display = mysql_fetch_assoc( $query ) )
			{
				if( !is_array($sources[$file['info']['name']]['sources']) || !in_array( $display['address'], $sources[$file['info']['name']]['sources'] ) )
				$sources[$file['info']['name']]['sources'][] = $display['address'];
			}
		}

	}
}

if( $sourcesupplyphp == true )
{
	$statefile = "./".$_GET['fileid'].".sources";
	if( !file_exists($statefile) || filemtime($statefile) + 1800 <= time() )
	{
		if( $torrentphp == false )
		//echo "<!-- file ".$torrentcontent['info']['name']." added to stats list at ".time()." - Previous Time: ".filemtime($statefile)." -->\n";
		file_put_contents('./'.$_GET['fileid'].'.sources', file_get_contents('http://www.filemirrors.com/find.src?file='.urlencode($_GET['fileid']).'&getright=1'));
	}

	$input = explode("\n",file_get_contents("./".$_GET['fileid'].".sources") );
	foreach( $input as $newsource )
	{
		if( $newsource != "" )
		{
			$splitsource = explode( "\"", $newsource );
			if ( $splitsource[1] != "" )
			{
				if( !is_array($sources[$_GET['fileid']]['sources']) || !in_array( $splitsource[1], $sources[$_GET['fileid']]['sources'] ) )
					$sources[$_GET['fileid']]['sources'][] = $splitsource[1];
				//if( $torrentphp == false )
					//echo "<!-- source $splitsource[1] for file ".$torrentcontent['info']['name']." added -->\n";
			}
		}
	}
	$sourcedb = mysql_connect( $mysqlserver, $mysqluser, $mysqlpass);
	if( !$sourcedb )
		echo "MySQL server did not connect - ".mysql_error()."<br>";
	if( !mysql_select_db($mysql_database) )
		echo "MySQL database did not select- ".mysql_error()."<br>";
	$querystring = "SELECT * FROM sources WHERE sourcefilename = '".$_GET['fileid']."' ORDER BY entryno DESC";
	$sourcequery = mysql_query($querystring, $sourcedb );
	while( $inputsource = mysql_fetch_assoc($sourcequery) )
	{
		$sources[$_GET['fileid']]['sources'][] = $inputsource['address'];
	}
}	

if( $torrentphp == true )
{
	$sourcedb = mysql_connect( $mysqlserver, $mysqluser, $mysqlpass);
	if( !$sourcedb )
		echo "MySQL server did not connect - ".mysql_error()."<br>";
	if( !mysql_select_db($mysql_database) )
		echo "MySQL database did not select- ".mysql_error()."<br>";
	$statefile = "./".$torrentcontent['info']['name'].".sources";
	if( !file_exists($statefile) || filemtime($statefile) + 1800 <= time() )
	{
		if( $torrentphp == false )
		//echo "<!-- file ".$torrentcontent['info']['name']." added to stats list at ".time()." - Previous Time: ".filemtime($statefile)." -->\n";
		file_put_contents('./'.$torrentcontent['info']['name'].'.sources', file_get_contents('http://www.filemirrors.com/find.src?file='.urlencode($torrentcontent['info']['name']).'&size='.$torrentcontent['info']['length'].'&getright=1'));
		$input = explode("\n",file_get_contents("./".$torrentcontent['info']['name'].".sources") );
		$currvalues = array();
		foreach( $input as $newsource )
		{
			if($newsource != "")
			{
				$splitsource = explode( "\"", $newsource );
				if ( $splitsource[1] != "" )
				{
					$currvalues[] = sprintf("( '%s', '%s', '%s')", sha1(BEncode($torrentcontent['info'])), $torrentcontent['info']['name'], $splitsource[1]);
					if( $torrentphp == false )
						echo "<!-- source $splitsource[1] for file ".$torrentcontent['info']['name']." added to queue -->\n";
				}
			}
		}
		if( count($currvalues) > 0 )
		{
			$outquery = sprintf("INSERT INTO sources (`uniqueid`, `sourcefilename`, `address`) VALUES %s", implode(", ",$currvalues));
			mysql_query($outquery,$sourcedb);
			echo sprintf("<!-- MySQL Error: %s -->",mysql_error());
		}

	}
	$querystring = "SELECT * FROM sources WHERE uniqueid = '".sha1(BEncode($torrentcontent['info']))."' ORDER BY entryno DESC";
	$sourcequery = mysql_query($querystring, $sourcedb );
	while( $inputsource = mysql_fetch_assoc($sourcequery) )
	{
		$sources[$torrentcontent['info']['name']]['sources'][] = $inputsource['address'];
	}
}

?>