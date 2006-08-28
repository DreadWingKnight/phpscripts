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

$torrentphp = true;
include("trackers.php");
require_once("BEncode.php");
require_once("BDecode.php");

$parser = (in_array($_SERVER['REMOTE_ADDR'], $parserips)) ? TRUE : FALSE;

function make_seed() {
	list($usec, $sec) = explode(' ', microtime());
	return (float) $sec + ((float) $usec * 100000);
}

srand(make_seed());
$splitpath = (!empty($_SERVER['PATH_INFO'])) ? array_reverse(split("/",$_SERVER['PATH_INFO'])) : '';
if (empty($splitpath)) {
	$torrentcrc = (!empty($_GET['torrentcrc'])) ? $_GET['torrentcrc'] : '';
	$numtorrents = (!empty($_GET['numtorrents'])) ? $_GET['numtorrents'] : '';
	$torrentfilename = (!empty($_GET['torrentname'])) ? $_GET['torrentname'] : '';
}
else {
	if (count($splitpath) > 3){
		$torrentcrc = (!empty($splitpath[2])) ? $splitpath[2] : '';
		$numtorrents = (!empty($splitpath[1])) ? $splitpath[1] : '';
		$torrentfilename = (!empty($splitpath[0])) ? $splitpath[0] : '';
	}
	else
	{
		$torrentcrc = (!empty($splitpath[1])) ? $splitpath[1] : '';
		$torrentfilename = (!empty($splitpath[0])) ? $splitpath[0] : '';
	}
}

if ( empty($_GET) && empty($_SERVER['PATH_INFO']) ) {
	echo 'Error 0000 - No torrent information passed';
	exit;
}

if (empty($torrentcrc) && $requireid == true ) {
	echo 'Error 0001 - Torrent CRC/identifier code not specified.';
	exit;
}

if (empty($numtorrents) && $overridenumtorrents != true ){
	echo 'Error 0002 - Number of torrents not specified. This may be caused by server misconfiguration.';
	exit;
}

if (empty($torrentfilename)) {
	echo 'Error 0003 - Torrent Filename not specified';
	exit;
}

if ($overridenumtorrents == true) {
	$numtorrents = count($trackers);
}

if (empty($torrentcrc) && $requireid != true) {
	$torrentfull = $torrentfilename;
	}
else {
	$torrentfull = $torrentcrc.'/'.$torrentfilename;
}

if (!file_exists($torrentfull)) {
	header('HTTP/1.0 404 Not Found');
	header('Content-Type: text/plain');
	echo("The torrent file specified in the torrentname= paramater does not exist on this server\n");
	echo("Please verify the link or contact site administration\n");
	echo("Debug information: \n");
	echo("Torrent Full Path: $torrentfull \n");
	echo("Torrent CRC/ID: $torrentcrc \n");
	echo("Torrent Filename: $torrentfilename \n");
	print_r($splitpath);
	exit;
}

$fd = fopen($torrentfull, "rb");
$stream = fread($fd, filesize($torrentfull));
fclose($fd);
// Convert BEncoded torrent data to PHP-readable arrays
$torrentcontent = BDecode($stream);

// Remove excess torrent internal data.
// Removes Announce-List
unset($torrentcontent['announce-list']);

// Removes Azureus-inserted Resume Data
unset($torrentcontent['resume']);
unset($torrentcontent['tracker_cache']);
unset($torrentcontent['torrent filename']);

$announcearray[] = $trackers;
if ($parser != true) {
	if ( $numtorrents > 1 ) {
		if ( array_key_exists($_SERVER['REMOTE_ADDR'],$overrideip) )
			$torrentid = $overrideip[$_SERVER['REMOTE_ADDR']];
		else
			$torrentid = rand(1,$numtorrents)-1;
		$torrentcontent['announce-list'] = $announcearray;
	}
	else {
		$torrentid = 0;
	}
	$torrentcontent['announce'] = $trackers[$torrentid];
}
else {
	$torrentcontent['announce'] = $trackerhub;
}

// Enable Azureus DHT Backup in the config file
if ($enableDHT == true)
	$torrentcontent['azureus_properties']['dht_backup_enable'] = 1;

// Enable Mainline DHT Backup in the config file
if (count($nodes) > 0){
	if (isset($torrentcontent['nodes']))
		$torrentcontent['nodes'] += $nodes;
	else
		$torrentcontent['nodes'] = $nodes;
}

if ( count($httpseeds) != 0 )
{	
	if( !isset($torrentcontent['httpseeds'] ) )
		$torrentcontent['httpseeds'] = $httpseeds;
	else
	foreach( $httpseeds as $seed )
		if( !in_array($seed, $torrentcontent['httpseeds'] ))
			$torrentcontent['httpseeds'][] = $seed;
}
include("sources.php");

if( !isset($torrentcontent['info']['files']) )
{
	if( count($sources[$torrentcontent['info']['name']]['sources'] ) != 0 )
	{
		if( count($sources[$torrentcontent['info']['name']]['sources'] ) < 5 )
			$torrentcontent['sources'] = $sources[$torrentcontent['info']['name']]['sources'];
		else
		{
			$outputindex = array_rand($sources[$torrentcontent['info']['name']]['sources'],5);
			foreach( $outputindex as $key )
				$torrentcontent['sources'][] = $sources[$torrentcontent['info']['name']]['sources'][$key];
		}

	}
}

$torrentreturn = BEncode($torrentcontent);
header('Content-Type: application/x-bittorrent');
header("Content-Disposition: attachment ; filename=\"".$torrentfilename."\"");
print_r($torrentreturn);
?>