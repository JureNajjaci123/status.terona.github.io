<?php
$minecraft_ip = 'terona.org'; // IP oziroma domena za Minecraft strežnik
$minecraft_port = 25565; // Privzeti Minecraft port

function checkMinecraftStatus() {
    global $minecraft_ip, $minecraft_port;
    return checkServerStatus($minecraft_ip, $minecraft_port);
}

function checkServerStatus($ip, $port) {
    $socket = @fsockopen($ip, $port, $errno, $errstr, 2);
    if ($socket) {
        fclose($socket);
        return true;
    } else {
        return false;
    }
}

function generateBars($uptimeDays = 90) {
    for ($i = 0; $i < $uptimeDays; $i++) {
        $isOnline = checkMinecraftStatus();
        echo $isOnline ? "<div class='online'></div>" : "<div class='offline'></div>";
    }
}

generateBars();
?>
