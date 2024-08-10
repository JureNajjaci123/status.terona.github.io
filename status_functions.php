<?php
// Log status changes to a file
function logStatusChange($statusType, $status) {
    $filename = 'status_log.txt'; // Log file name
    $currentTime = date('Y-m-d H:i:s'); // Current timestamp
    $statusText = $status ? 'Online' : 'Offline';

    $logEntry = "$currentTime - $statusType is now $statusText\n";
    file_put_contents($filename, $logEntry, FILE_APPEND);
}

// Check Minecraft server status
function checkMinecraftStatus() {
    global $minecraft_ip, $minecraft_port;
    return checkServerStatus($minecraft_ip, $minecraft_port);
}

// Check server status via socket
function checkServerStatus($ip, $port) {
    $socket = @fsockopen($ip, $port, $errno, $errstr, 2);
    if ($socket) {
        fclose($socket);
        return true;
    } else {
        return false;
    }
}

// Check website status
function checkWebsiteStatus($url) {
    $headers = @get_headers($url);
    return $headers && strpos($headers[0], '200');
}

// Update and log status changes
function updateStatus($statusType, $currentStatus) {
    static $lastStatus = array();
    if (!isset($lastStatus[$statusType])) {
        $lastStatus[$statusType] = !$currentStatus;
    }

    if ($lastStatus[$statusType] !== $currentStatus) {
        logStatusChange($statusType, $currentStatus);
        $lastStatus[$statusType] = $currentStatus;
    }
}

// Generate bars for Minecraft server status
function generateMinecraftBars($uptimeDays = 90) {
    for ($i = 0; $i < $uptimeDays; $i++) {
        $isOnline = checkMinecraftStatus();
        updateStatus('Minecraft server', $isOnline);
        echo $isOnline ? "<div class='status-line online'></div>" : "<div class='status-line offline'></div>";
    }
}

// Generate bars for website status
function generateWebsiteBars($uptimeDays = 90) {
    global $website_url;
    $isOnline = checkWebsiteStatus($website_url);
    updateStatus('Website', $isOnline);
    for ($i = 0; $i < $uptimeDays; $i++) {
        echo $isOnline ? "<div class='status-line online'></div>" : "<div class='status-line offline'></div>";
    }
}
?>
