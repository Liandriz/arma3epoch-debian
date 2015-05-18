#Arma 3 Epoch Server on Debian Wheezy (7.x) x64
This repo is a full package, to install an Arma 3 Epoch Dedicated Server on a Debian Wheezy x64 Server.

This package will install:

* Arma 3 Dedicated Linux Server, from Steam, with Steamcmd,
* Epoch Mod, client and server files (0.3.0.3 here),
* ASM, Arma Server Monitor, a tool to monitor ressources of your server,
* Some upstart/stop/restart scripts,
* Scripts for log rotator and netlog rotator, Redis dumps backups, rcon send message.


**A lot of files are from different already existings packages, read last part to see thanks and sources.**

#Install
* Get a Debian Linux fresh box.
* Log in as root.

**As root:**

* Update & upgrade the fresh system:
<pre># apt-get update && apt-get dist-upgrade</pre>
* For Steam and ASM, add i386 support:
<pre># dpkg --add-architecture i386</pre>
* Update system sources:
<pre># apt-get update</pre>
* Install packages we will need:
<pre># apt-get install redis-server libhiredis-dev unzip wget curl dos2unix git perl screen build-essential lib32gcc1 lib32stdc++6 libstdc++6 g++-multilib libglib2.0-0 libglib2.0-0:i386 php5-cli</pre>
* Add user who will run the server:
<pre># adduser arma3server</pre>
* To give this new user access to the Redis dump, add him to Redis group:
<pre># usermod -a -G redis arma3server</pre>
* Redis Database Server Setup:
<pre># nano /etc/redis/redis.conf</pre>
 > change "requirepass"

 > check bind 127.0.0.1 to only listen localhost, not the whole internet

 > check dbfilename

 > check dir

* Finally restart Redis server:
<pre># service redis-server restart</pre>
* Done, time to switch to the new user account:
<pre># su - arma3server</pre>

**As new user, arma3server:**

* Login, go in your new home folder, and clone me (do **not** forget last dot):
<pre># cd ~/ && git clone https://github.com/Liandriz/arma3epoch-debian tmp && mv tmp/* . && rm -rf tmp</pre>

**Install Arma 3 Dedicated Server fils from Steam with Steamcmd**

If you have Steam Guard linked to your account, you will need a key from mail to access your account. So just init Steamcmd to get your auth token for the first time:

* Launch Steamcmd with auth params:
<pre>#  ~/steamcmd/steamcmd.sh +login your_steam_account_name your_steam_password</pre>
* Just let Steamcmd update, login and ask you the code, enter it, and exit from Steamcmd by enter "quit":
<pre># Steam> quit</pre>
* Now, edit the file *'config/arma3.txt'*, and add your Steam login and password at line 3:
<pre># nano ~/config/arma3.txt</pre>
<pre>@ShutdownOnFailedCommand 1
@NoPromptForPassword 1
login your_steam_login your_steam_password
force_install_dir ../server
app_update 233780 validate
quit</pre>
* Finally, installing server files from Steam:
<pre># ~/arma3_update</pre>
* Wait it downalods all the files...

**Install Epoch client files**

* Go to the server folder, and download client zip:
<pre># cd ~/server && wget http://rr.whocaresabout.de/epoch/Epoch_Client_0.3.0.3.zip</pre>
* Unzip it:
<pre># unzip Epoch_Client_0.3.0.3.zip</pre>
* Delete zip:
<pre># rm Epoch_Client_0.3.0.3.zip</pre>
* Cleaning some folder names, to lowercase:
<pre># mv @Epoch @epoch
# mv @epoch/Addons @epoch/addons
# mv @epoch/Keys @epoch/keys</pre>

#All set, you can now modify your config files.

**Path to server classic config files:**

<pre># nano ~/server/@epochhive/epochserver.ini
# nano ~/server/@epochhive/epochah.hpp
# nano ~/server/@epochhive/epochconfig.hpp</pre>

<pre># nano ~/config/epoch/basic.cfg
# nano ~/config/epoch/config.cfg
# nano ~/config/epoch/users/epoch/epoch.arma3profile</pre>

#Scripts
***~/arma3_update***

Launch it to update Arma Dedicated Server files from Steam when needed, no arg, all in the config file ***~/config/arma3.txt***.

Usage - better not use in CRON job:
<pre># ~/arma3_update</pre>

***~/redis_backup***

Launch it to backup the ***Redis dumpfile***, zip it and archive it, manual task or can be set with CRON.

Usage (backup name generated from date) - can be used in CRON job:
<pre># ~/redis_backup</pre>

***~/rcon***

Modified version of an existing script, this is a PHP script to send message to all players in game, or execute a RCON command. There is 4 usages of the script:

* ~/rcon text "message to send to server"
* ~/rcon preset preset_id
* ~/rcon random
* ~/rcon cmd "custom command"


The first one send the select text to all players, the second picks up a preset text in config file to send it, the third send a random text from a list in config file, and the last one can execute a custom command, like "#kick Liandri" (see Battleye RCON commands help for commands).

The config file is ***~/config/rcon_cfg.php***, just edit server IP, port and password (*Battleye password, set in **beserver.cfg***), and add/edit/delete items in the preset and random lists.

To log any rcon actions, add ***>> ~/config/INSTANCE NAME/rcon/rcon.log*** at the end of your commandline, to append log to rconlogfile.
Usage - can be used in CRON job:
<pre># ~/rcon text|preset|random some_text|id >> ~/config/epoch/rcon/rcon.log</pre>

***~/epoch***

The most important file, a modified one from the official Epoch Server repo. 
With it, you can start, stop, restart, view log and netlog, check your config.
<pre># nano ~/epoch</pre>
Read comments, check if you want to enable/disable some tweaks, like IP, netlog, ASM, Redis, rcon, backups.

Best to try a 
<pre># ~/epoch check</pre>
before launching server.

Usage - can be used in CRON job:
<pre># ~/epoch start|stop|restart|status|check|log|netlog</pre>

#Last step
When your server is correctly setup, if you want to start your Epoch server at boot, edit this as ***root*** :
<pre># crontab -e</pre>
and add the line:
<pre>@reboot ~/epoch restart</pre>

#Cron Tasks example
Example of `crontab -e` for:

* Starting the Arma server at server boot,
* Reboot every 4hours (0/4/8/12/16/20),
* Send text messages for reboot advice: 1h, 30min, 15min, 10min, 5min, 2min and 1min before reboot (see *~/config/rcon_cfg.php*),
* Send random text from list (like hosting AD, website AD... see *~/config/rcon_cfg.php*) every 10minutes,
* Send specific text every hour,
* Force to zip and archive Redis dump every hour.

<pre>@reboot ~/epoch restart
00 0,4,8,12,16,20 * * * ~/epoch restart
00 3,7,11,15,19,23 * * * ~/cron preset 1h >> ~/config/epoch/rcon/rcon.log
30 3,7,11,15,19,23 * * * ~/cron preset 30m >> ~/config/epoch/rcon/rcon.log
45 3,7,11,15,19,23 * * * ~/cron preset 15m >> ~/config/epoch/rcon/rcon.log
50 3,7,11,15,19,23 * * * ~/cron preset 10m >> ~/config/epoch/rcon/rcon.log
55 3,7,11,15,19,23 * * * ~/cron preset 5m >> ~/config/epoch/rcon/rcon.log
58 3,7,11,15,19,23 * * * ~/cron preset 2m >> ~/config/epoch/rcon/rcon.log
59 3,7,11,15,19,23 * * * ~/cron preset 1m >> ~/config/epoch/rcon/rcon.log
*/10 * * * * ~/cron random >> ~/config/epoch/rcon/rcon.log
00 * * * * ~/cron text "my text to send to server" >> ~/config/epoch/rcon/rcon.log
00 * * * * ~/redis_backup</pre>

#PS
If at any time, you edit some config file or script on a Windows computer, and ssh/ftp the file to the server, do not forget to run *dos2unix* your file, to avoid some errors:
<pre># dos2unix my_file</pre>

#Thanks and sources
* **Epoch Team** for the Epoch Mod (http://epochmod.com),
* **Killswitch** for the ASM port (http://forums.bistudio.com/showthread.php?182602-Arma-Server-Monitor-for-Linux),
* **Dwarfer** for some optimizations (http://epochmod.com/forum/index.php?/topic/34942-wiphowto-linux-centos-70-epoch-server/),
* The original author of the PHP-RCON script.