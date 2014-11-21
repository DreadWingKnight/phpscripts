<?
$indexphp = true;
require_once("BEncode.php");
require_once("BDecode.php");
include('downloads.php');
include('sources.php');
include('trackers.php');
require_once("base32.php");
$clientstracker = BDecode(file_get_contents('/var/www/htdocs/ead/webseed/clients.bencode'));
// Prelim array crap
$basetorrent = array();
$current = 'odd';
	for( $i = 0 ; $i < count($downloads); $i++ )
	{
		$basetorrent[$i] = BDecode(file_get_contents($downloads[$i]['torrent']));
		$downloads[$i]['hash'] = sha1(BEncode($basetorrent[$i]['info']),true);
		$basetorrent[$i]['groptout'] = $downloads[$i]['groptout'];
	}

	foreach( $basetorrent as $file )
	{
		if( $file['groptout'] == false )
		{
			$statefile = "./".$file['info']['name'].".sources";
			if( !file_exists($statefile) || filemtime($statefile) + 1800 <= time() )
			{
				if( $torrentphp == false )
				echo "<!-- file ".$file['info']['name']." added to stats list at ".time()." - Previous Time: ".filemtime($statefile)." -->\n";
				file_put_contents('./'.$file['info']['name'].'.sources', file_get_contents('http://www.filemirrors.com/find.src?file='.urlencode($file['info']['name']).'&size='.$file['info']['length'].'&getright=1'));
			}

			$input = explode("\n",file_get_contents("./".$file['info']['name'].".sources") );
			foreach( $input as $newsource )
			{
				if($newsource != "")
				{
				$splitsource = explode( "\"", $newsource );
				if ( $splitsource[1] != "" )
				{
					if( !is_array($sources[$file['info']['name']]['sources']) || !in_array( $splitsource[1], $sources[$file['info']['name']]['sources'] ) )
						$sources[$file['info']['name']]['sources'][] = $splitsource[1];
					if( $torrentphp == false )
						echo "<!-- source $splitsource[1] for file ".$file['info']['name']." added -->\n";
				}
				}
			}
		}
	}

// Prelim Functions, move later

function buildmagnet ( $inputarray , $sourcesarray )
{
	$magnetprefix = "magnet:?";
	if( isset( $inputarray['tiger'] ) && isset( $inputarray['sha1'] ) )
		$magxt[] = "xt=urn:bitprint:".BASE32::base32encode($inputarray['sha1']).".".BASE32::base32encode($inputarray['tiger']);
	if( !isset( $inputarray['tiger'] ) && isset( $inputarray['sha1'] ) )	
		$magxt[] = "xt=urn:sha1:".BASE32::base32encode($inputarray['sha1']);
	if( isset( $inputarray['tiger'] ) && !isset( $inputarray['sha1'] ) )	
		$magxt[] = "xt=urn:tiger:".BASE32::base32encode($inputarray['tiger']);
	if( isset( $inputarray['ed2k'] ) )
		$magxt[] = "xt=urn:ed2k:".bin2hex($inputarray['ed2k']);
	$output = $magnetprefix;
	foreach( $magxt as $value )
		$output .= $value."&";
	$output .="xl=".$inputarray['length']."&dn=".$inputarray['name'];
	if( count($sourcesarray) > 0 )
		foreach( $sourcesarray as $source )
		$output .= "&xs=".$source;
	return $output;
}
?>
<HTML><HEAD>
<TITLE>Depthstrike.com Mirrors for Open-Source/Freeware Projects</TITLE>
<link rel=stylesheet href="bnbt.css">
</HEAD>
<BODY topmargin=0 leftmargin=0 rightmargin=0 bottommargin=0>
<table border=1 cellspacing=0 cellpadding=0 width=100%>
<tr valign=top><th align=left colspan=5>Current hosted files (newest first, not sorted by project):</th>

</tr>
<tr valign=bottom>
<Th>Project
<br />Discussion Forum</th>
<th>Build Version</th>
<th>Filesize</th>
<th class=hashes>Checksums:
<br>CRC32 (Hex) -  ED2K (Hex)
<br>MD5 (Hex) - SHA1 (Hex)
<br>Tiger Tree (Base32)
</th>
<th>Download Links<br>and Statistics:</th></tr>
<?
$per_page = 6;
if( isset($_GET['page']) )
	$page_no = $_GET['page'];
else
	$page_no = 0;
$page_offset = $per_page * $page_no;
$count = 0;
$pages = count($downloads) / $per_page;
for($i = $page_offset ;$i <count($downloads);$i++)
{
	if ($i - $page_offset + 1 > $per_page)
		break;
	if ($current == 'even') {
		echo "<tr valign=\"top\" class=\"even\">";
		$current = 'odd';
	}
	else {
		echo "<tr valign=\"top\" class=\"odd\">";
		$current = 'even';
	}
	echo "<td>";
	if ( isset($downloads[$i]['address']) )
		echo "<a href=\"".$downloads[$i]['address']."\">";
	if ( isset($downloads[$i]['logo']) )
		echo "<img src=".$downloads[$i]['logo']." align=left border=0>";
	echo $downloads[$i]['name'];
	if ( isset($downloads[$i]['address']) )
		echo "</a>";
	if( isset($downloads[$i]['forum']) )
		echo "<br /><a href=\"".$downloads[$i]['forum']."\">Discussion Forum</a>";
	if( isset($downloads[$i]['thread']) )
		echo "<br /><a href=\"".$downloads[$i]['thread']."\">Discussion Thread</a>";
	if( isset($downloads[$i]['links']) )
	{
		foreach($downloads[$i]['links'] as $name => $address)
		{
			echo "<br /><a href=\"".$address."\">".$name."</a>";
		}
	}

	echo "</td>\n";
	echo "<td>";
	if ( $downloads[$i]['showtorrent'] == 1 )
	{
		echo "<a href=\"";
		if( $downloads[$i]['usetphp'] == 1 )
			echo "torrent.php/".$downloads[$i]['torrent']."\">";
		else
			echo $downloads[$i]['torrent']."\">";
	}
	echo $downloads[$i]['version'];
	if( $downloads[$i]['showtorrent'] == 1 )
		echo "</a>";
	echo "</td>";
	echo "<td><nobr>".number_format($basetorrent[$i]['info']['length'])." bytes</nobr></td>\n";
	echo "<td class=hashes>";
	if ( isset($basetorrent[$i]['info']['crc32']) )
		echo "CRC32: ".$basetorrent[$i]['info']['crc32']."<br />";
	if ( isset( $basetorrent[$i]['info']['ed2k'] ) )
		echo "ED2K: ".bin2hex($basetorrent[$i]['info']['ed2k'])."<br />";
	if ( isset( $basetorrent[$i]['info']['md5sum'] ) )
		echo "MD5: ". $basetorrent[$i]['info']['md5sum'] . "<br />";
	if ( isset( $basetorrent[$i]['info']['sha1'] ) )
		echo "SHA1: ". bin2hex($basetorrent[$i]['info']['sha1'])."<br />";
	if ( isset( $basetorrent[$i]['info']['tiger'] ) )
		echo "<nobr>Tiger Tree: ". BASE32::base32encode($basetorrent[$i]['info']['tiger']) . "</nobr>";
	echo "</td>\n";
	echo "<td class=hashes>";
	if ( $downloads[$i]['showtorrent'] == 1 )
	{
		echo "<a href=\"";
		if( $downloads[$i]['usetphp'] == 1 )
			echo "torrent.php/".$downloads[$i]['torrent']."\">Torrent</a><br />";
		else
			echo $downloads[$i]['torrent']."\">Torrent</a><br />";
		if( !isset($clientstracker['files'][addslashes($downloads[$i]['hash'])]) )
		{
			echo "SD: ".$clientstracker['files'][$downloads[$i]['hash']]['complete'];
			echo " DL: ".$clientstracker['files'][$downloads[$i]['hash']]['incomplete'];
			if( !isset($clientstracker['files'][$downloads[$i]['hash']]['downloaded']) )
				echo "<br />Completed: ".$clientstracker['files'][$downloads[$i]['hash']]['downloaded']." Times";
			echo "<br />\n";
		}
		else
		{
			echo "SD: ".$clientstracker['files'][addslashes($downloads[$i]['hash'])]['complete'];
			echo " DL: ".$clientstracker['files'][addslashes($downloads[$i]['hash'])]['incomplete'];
			if( isset($clientstracker['files'][addslashes($downloads[$i]['hash'])]['downloaded']) )
				echo "<br />Completed: ".$clientstracker['files'][addslashes($downloads[$i]['hash'])]['downloaded']." Times";
			echo "<br />\n";
		}
		if( (isset($downloads[$i]['webseed']) && $downloads[$i]['webseed'] == 1) || isset($basetorrent[$i]['httpseeds']) || (isset($downloads[$i]['usetphp']) && $downloads[$i]['usetphp'] == 1 ) )
		{
			echo "<nobr><a href=\"http://wiki.depthstrike.com/index.php/Opensource:WebseedEnabled\" target='_blank'>Webseed Enabled</a> - ";
			if(isset($downloads[$i]['usetphp']) && $downloads[$i]['usetphp'] == 1 )
			{
			if ( count($httpseeds) != 0 )
			{
				if( !isset($basetorrent[$i]['httpseeds'] ) )
					$basetorrent[$i]['httpseeds'] = $httpseeds;
				else
				foreach( $httpseeds as $seed )
					if( !in_array($seed, $basetorrent[$i]['httpseeds'] ))
						$basetorrent[$i]['httpseeds'][] = $seed;
			}
			}
			if (isset($basetorrent[$i]['httpseeds']) )
				echo count($basetorrent[$i]['httpseeds'])." webseeds</nobr><br/>\n";
		}
		if( ( isset($downloads[$i]['sourcefilename']) && isset($sources[$downloads[$i]['sourcefilename']]['sources']) )|| (isset($basetorrent[$i]['sources']) ) )
			echo "<nobr><a href=\"http://wiki.depthstrike.com/index.php/Opensource:ExternalSourced\" target='_blank'>External Source Enabled</a> - ".count($sources[$downloads[$i]['sourcefilename']]['sources'])." sources</nobr><br/>\n";
	}
	if ( isset( $basetorrent[$i]['info']['ed2k'] ) || isset( $basetorrent[$i]['info']['sha1'] ) || isset( $basetorrent[$i]['info']['tiger'] ) )
		echo "<a href=\"".buildmagnet($basetorrent[$i]['info'], $downloads[$i]['magnetsources'])."\"><img src=\"magnet-tile2-16w-16h.gif\" border=0 align=left> Magnet</a><br clear=left />\n";
	if( isset( $downloads[$i]['mirrors'] ) )
		foreach( $downloads[$i]['mirrors'] as $mirrorname => $mirroraddress )
			echo "<a href=\"".$mirroraddress."\">".$mirrorname."</a><br />\n";
	echo "</td></tr>\n";
	$count++;
}
	echo "<tr class=odd align=center><td align=center colspan=5>Torrent seed/peer data last updated: ";

echo date ("F d Y H:i:s", filemtime('/var/www/htdocs/ead/webseed/clients.bencode'));
echo " GMT".date(" O", filemtime('/var/www/htdocs/ead/webseed/clients.bencode') ).".<br />\n";
echo "Current time: ";
echo date ("F d Y H:i:s", time() );
echo " GMT".date(" O", time() ).".</th></tr>";
//$trackerstats = BDecode(file_get_contents('http://core-tracker.depthstrike.com:9800/info.bencode'));
echo "<tr>";
	echo "<td align=center colspan=5>";

echo "Tracker Files: ".$clientstracker['filecount']."<br />";
echo "Tracker Peers: ".$clientstracker['peers']." - Unique: ".$clientstracker['unique']."<br />";
echo "Tracker Software Version: ".$clientstracker['version'];
echo "</td></tr>\n";
$page_no = $page_no+1;
	echo "<tr><th colspan=5>Page ";
if( $pages == 1 && $page_no == 1 )
{
	echo "1 ";
}
if( $pages > 1 )
{
	for( $j = 0; $j < $pages; $j++ )
	{
		$k = $j + 1;
		if( $k != $page_no )
		{
			if( isset($_GET['category']) )
				echo "<a href=?category=".$_GET['category']."&page=".$j.">".$k."</a> ";
			else
				echo "<a href=?page=".$j.">".$k."</a> ";
		}
		else
		echo $k." ";
	}
}
else
	echo ("1");
echo "</th></tr>";
?>
</table>
<table border=1 cellspacing=0 cellpadding=0 width="100%"><tr><th align=left colspan=2>Notes:</td></tr>
<tr class=odd><td>1&gt; Magnet links are compatible with GNUTella clients, DirectConnect clients that support Thex or SHA1 hashing, and ED2K clients configured to support magnets.</td><th rowspan=3><a href="http://depthstrike.com/ead/donate.php"><img src="http://images.paypal.com/images/x-click-but04.gif" border=0></a>
<br>Help Keep This
<br>Site Running</th>
<tr class=even><td>2&gt; All the peer to peer networks work better if you share. With torrents, seed to at LEAST 100% on files. With the other networks, keep the file in a shared folder.</td></tr>

</table>
</body>
</html>