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

$multitier = true;
$trackers = array();

// Tracker List (must be valid announce URIs)

// Main Network
// example announce entry:
// Create a new copy of the example for each announce url you add.
// $trackers[] = '<announce url>';

$parserips = array();

// List of known Parser IPs
// Example Parser Entry
// Create a new copy of the example for each known parser site.
// $parserips[] = '<Parser's IP Address>';

// Tracker Network Hub (must be a valid announce URI)
$trackerhub = '<Announce url of your tracker hub>';

// Override the numtorrents= value in the torrent request - Uses a calculated number of torrents instead of the specified number.
$overridenumtorrents = true;
// Require the torrentcrc= value in the torrent request
$requireid = false;

// Enable Mainline DHT - $nodes array contains values of array('<host address>',<port number>)
// Use the IP and port or hostname and port of known stable mainline DHT entry points
// example nodes:
// $nodes[] = array('router.bitcomet.com', 554);
// $nodes[] = array('router.bittorrent.com', 6881);
// $nodes[] = array('router.utorrent.com', 6881);


// HTTP Seeding support.
// Specification: 
// http://www.bittornado.com/docs/webseed-spec.txt
// Example:
// Create a new copy of the example for each http seed in use.
// $httpseeds[] = '<address of http seed>';

// Enable Azureus DHT - true or false value
$enableDHT = true;

// Source IP torrent number overrides, Removes randomness for specific IPs
$overrideip = array();
?>
