<?php

// Send response to client to close connection
header('Connection: Close');

set_time_limit(0);

$path = dirname(__FILE__);
$prefix = realpath($path . '/../');
require_once $prefix . '/lib/init.php';
require_once $prefix . '/modules/localplay/upnp.controller.php';
require_once $prefix . '/modules/upnp/upnpplayer.class.php';

if (!AmpConfig::get('upnp_backend')) {
    die("UPnP backend disabled..");
}

// get current UPnP player instance
$controller = new AmpacheUPnP();
$instance = $controller->get_instance();
echo "UPnP instance = " . $instance['name'] . "\n";

$deviceDescr = $instance['url'];
//!!echo "UPnP device = " . $deviceDescr . "\n";
$player = new UPnPPlayer("background controller", $deviceDescr);

//!!echo "Current playlist: \n" . print_r($player->GetPlaylistItems(), true);
//!!echo "Current item: \n" . print_r($player->GetCurrentItem(), true);

// periodically (every second) checking state of renderer, until it is STOPPED
$played = false;
while (($state = $player->GetState()) == "PLAYING") {
    $played = true;
    echo ".";
    sleep(1);
}
echo "STATE = " . $state . "\n";

// If the song was played and then finished, start to play next song in list.
// Do not start anything if playback was stopped from beginning
if ($played)
{
    echo "UPnP play next" . "\n";
    if ($player->Next(false))
        echo "Next song started" . "\n";
    else
        echo "Next song FAILED!" . "\n";
}

?>
