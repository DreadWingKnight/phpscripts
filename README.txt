See LICENSE.txt from the subversion repository, git repository or source package for license information.

Purpose

The purpose of this script (torrent.php and its related files) is to allow a BitTorrent Tracker that is capable of clustering (peer information sharing) to dynamically increase the size of its cluster without requiring major torrent editing every time the cluster's layout changes.
Torrents passed to users by way of this script do not need to have the announce url or announce list stored in the .torrent files themselves as they are dynamically generated when the user downloads.

---

Configuration

Individual configuration values are set in the following files and are documented in comments:
- configure.php - Handles MySQL Settings
- trackers.php - Handles Tracker Configuration

---

Configuration notes for various servers:

APACHE
- The torrent.php script is capable of being hidden from public view in the URLs by using mod_rewrite.

Example code for .htaccess:

<IfModule mod_rewrite.c>
RewriteEngine On
RewriteBase /(path of the site)/
RewriteRule ^(.*\.torrent)$ /(path of the site)/torrent.php [L]
</IfModule>

--

NGINX
- The torrent.php script is capable of being hidden from public view in the URLS by using the rewrite engine in the server.
NOTE: This is untested as of September 14, 2014. If you use these settings and it doesn't work, please report to me and, if possible, submit a pull request with the correct format.

rewrite ^/(.*.torrent)$ /(path of the site}/torrent.php$1? break;

---

Usage

If you've set things up on your server so that files can be sub-paths of a php script, you use the following address:
http://<server address with port>/<path>/torrent.php/<torrent filename>

If you haven't, you probably should as some browsers and downloader applications will throw a fit.
Otherwise you can use:

http://<server address with port>/<path>/torrent.php?torrentname=<torrent filename>&torrentcrc=<crc/folder>

Note that depending on your configuration, torrentcrc might not be needed.