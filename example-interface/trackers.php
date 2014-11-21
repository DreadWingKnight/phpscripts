<?php
$trackers = array();

// Tracker List (must be valid announce URIs)

// Main Network
//$trackers[] = 'http://core-tracker.enlist-a-distro.net:81/announce';
/*$trackers[] = 'http://core-tracker.enlist-a-distro.net:9881/announce';
$trackers[] = 'http://core-tracker.enlist-a-distro.net:9820/announce';
$trackers[] = 'http://sors-tracker.enlist-a-distro.net:81/announce';
$trackers[] = 'http://sors-tracker.enlist-a-distro.net:9820/announce';
$trackers[] = 'http://sors-tracker.enlist-a-distro.net:9881/announce';
$trackers[] = 'http://jet-tracker.enlist-a-distro.net:81/announce';
$trackers[] = 'http://jet-tracker.enlist-a-distro.net:9820/announce';
$trackers[] = 'http://jet-tracker.enlist-a-distro.net:9881/announce';
$trackers[] = 'http://ayu-tracker.enlist-a-distro.net:81/announce';
$trackers[] = 'http://ayu-tracker.enlist-a-distro.net:9820/announce';
$trackers[] = 'http://ayu-tracker.enlist-a-distro.net:9881/announce';
$trackers[] = 'http://ayu2-tracker.enlist-a-distro.net:81/announce';
$trackers[] = 'http://ayu2-tracker.enlist-a-distro.net:9820/announce';
$trackers[] = 'http://ayu2-tracker.enlist-a-distro.net:9881/announce';
*/
$trackers[] = 'http://clients-tracker.enlist-a-distro.net:9800/announce';
$trackers[] = 'http://clients-tracker.depthstrike.com:9800/announce';

$parserips = array();

// List of known Parser IPs
$parserips[] = '62.112.157.58';
$parserips[] = '69.93.98.42';
$parserips[] = '216.194.70.3';
$parserips[] = '195.56.77.83';
$parserips[] = '207.44.185.69';
$parserips[] = '67.15.138.54';
$parserips[] = '83.149.119.115';

// Tracker Network Hub (must be a valid announce URI)
$trackerhub = 'http://clients-tracker.enlist-a-distro.net:9800/announce';

// Override the numtorrents= value in the torrent request - Uses a calculated number of torrents instead of the specified number.
$overridenumtorrents = true;
// Require the torrentcrc= value in the torrent request
$requireid = false;

// Enable Mainline DHT - $nodes array contains values of array('<host address>',<port number>)
$nodes[] = array('router.bitcomet.com', 554);
$nodes[] = array('router.bittorrent.com', 6881);
$nodes[] = array('dhtbootstrap.depthstrike.com', 5560);
$nodes[] = array('84.255.198.133', 11513);
$nodes[] = array('router.utorrent.com', 6881);

$httpseeds[] = 'http://depthstrike.com/ead/webseed/seed.php';
//$httpseeds[] = 'http://webseed.depthstrike.com/seed.php';
$httpseeds[] = 'http://opensource.depthstrike.com/seed.php';

// Enable Azureus DHT - true or false value
$enableDHT = true;

// Source IP torrent number overrides, Removes randomness for specific IPs
$overrideip = array();
$overrideip['192.168.20.25'] = '0';
$overrideip['192.168.20.31'] = '0';
$overrideio['192.168.20.10'] = '0';
$overrideip['192.168.20.1'] = '0';
$overrideip['63.226.184.128'] = '4';
?>
