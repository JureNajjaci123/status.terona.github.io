<?php
// Nastavitve
$minecraft_ip = 'terona.org'; // IP oziroma domena za Minecraft strežnik
$minecraft_port = 25565; // Privzeti Minecraft port

$website_url = 'https://terona.org'; // URL vaše spletne strani

$log_file = 'status_log.txt'; // Datoteka za shranjevanje zgodovine izpadov/delovanja

// Nastavi lokalni časovni pas
date_default_timezone_set('Europe/Ljubljana');

function logStatusChange($service, $status) {
    global $log_file;
    $timestamp = date('Y-m-d H:i:s');
    $log_entry = "$timestamp - $service is now $status\n";
    file_put_contents($log_file, $log_entry, FILE_APPEND);
}

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

function checkWebsiteStatus($url) {
    $headers = @get_headers($url);
    if ($headers && strpos($headers[0], '200')) {
        return true; // Stran je dostopna
    } else {
        return false; // Stran ni dostopna
    }
}

function monitorStatus() {
    global $website_url;

    // Preveri status Minecraft strežnika
    $minecraft_status = checkMinecraftStatus();
    $last_minecraft_status = file_exists('last_minecraft_status.txt') ? file_get_contents('last_minecraft_status.txt') : null;
    $last_minecraft_status_time = file_exists('last_minecraft_status_time.txt') ? file_get_contents('last_minecraft_status_time.txt') : null;
    $current_time = time();

    // Log status change only if the status changes and if enough time has passed
    if ($minecraft_status !== $last_minecraft_status || !$last_minecraft_status_time || ($current_time - $last_minecraft_status_time) > 600) { // 10 minute interval
        $status_text = $minecraft_status ? 'Online' : 'Offline';
        logStatusChange('Minecraft Server', $status_text);
        file_put_contents('last_minecraft_status.txt', $minecraft_status);
        file_put_contents('last_minecraft_status_time.txt', $current_time);
    }

    // Preveri status spletne strani
    $website_status = checkWebsiteStatus($website_url);
    $last_website_status = file_exists('last_website_status.txt') ? file_get_contents('last_website_status.txt') : null;
    $last_website_status_time = file_exists('last_website_status_time.txt') ? file_get_contents('last_website_status_time.txt') : null;

    if ($website_status !== $last_website_status || !$last_website_status_time || ($current_time - $last_website_status_time) > 600) { // 10 minute interval
        $status_text = $website_status ? 'Online' : 'Offline';
        logStatusChange('Website', $status_text);
        file_put_contents('last_website_status.txt', $website_status);
        file_put_contents('last_website_status_time.txt', $current_time);
    }
}

function generateMinecraftBars($uptimeDays = 90) {
    for ($i = 0; $i < $uptimeDays; $i++) {
        $isOnline = checkMinecraftStatus();
        echo $isOnline ? "<div class='status-line online'></div>" : "<div class='status-line offline'></div>";
    }
}

function generateWebsiteBars($uptimeDays = 90) {
    global $website_url;
    $isOnline = checkWebsiteStatus($website_url);
    for ($i = 0; $i < $uptimeDays; $i++) {
        echo $isOnline ? "<div class='status-line online'></div>" : "<div class='status-line offline'></div>";
    }
}

function displayHistory() {
    global $log_file;
    if (file_exists($log_file)) {
        $history = file($log_file, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
        $history = array_reverse($history); // Obrni vrstni red vrstic
        echo nl2br(implode("\n", $history)); // Pretvori nove vrstice v <br> za HTML prikaz
    } else {
        echo "Zgodovina ni na voljo.";
    }
}

// Monitor the status of Minecraft server and website
monitorStatus();
?>
<!DOCTYPE html>
<html lang="sl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Terona Network Status</title>
    <!-- Favicon -->
    <link rel="icon" type="image/png" href="teroncaponca.png">
    <style>
        body {
            background: url('teronaback.png') no-repeat center center fixed;
            background-size: cover;
            color: white;
            font-family: Arial, sans-serif;
            text-align: center;
            margin: 0;
            padding: 0;
            position: relative; /* Dodano za pozicioniranje ognjene animacije */
        }
        .container {
            max-width: 800px;
            margin: 50px auto;
            padding: 20px;
            background-color: rgba(0, 0, 0, 0.7); /* Dark overlay for better text readability */
            border-radius: 10px;
            position: relative; /* Da bodo animirane pikice za vsebino */
            z-index: 1; /* Da bo vsebina nad ognjeno animacijo */
        }
        .logo {
            margin-bottom: 20px;
        }
        .status-overview {
            background-color: #1b9b4f;
            padding: 20px;
            margin-bottom: 20px;
            border-radius: 5px;
            font-size: 24px;
            font-weight: bold;
        }
        .status-box {
            background-color: #333;
            padding: 20px;
            margin-bottom: 20px;
            border-radius: 10px;
            text-align: left;
            border: 1px solid #444;
        }
        .status-box h2 {
            margin: 0 0 10px 0;
            font-size: 20px;
        }
        .status-bar {
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .bar {
            display: flex;
            align-items: center;
            flex: 1;
            margin-right: 10px;
        }
        .bar div {
            width: 4px;
            height: 20px;
            margin: 0 1px;
        }
        .online {
            background-color: green;
        }
        .offline {
            background-color: red;
        }
        .status-text {
            font-size: 18px;
            font-weight: bold;
            min-width: 120px;
        }
        .uptime-info {
            font-size: 12px;
            margin-top: 10px;
        }
        .operational {
            color: #00cc66;
        }
        .down {
            color: #cc0000;
        }
        .info-text {
            margin: 30px 0;
            font-size: 14px;
            color: #b3b3b3;
        }
        .history-box {
            background-color: #222;
            padding: 20px;
            border-radius: 10px;
            text-align: left;
            border: 1px solid #444;
            margin-top: 20px;
        }
        .history-box h2 {
            font-size: 20px;
            margin-bottom: 10px;
        }
        .history-content {
            font-size: 14px;
            color: #b3b3b3;
        }
        .notification {
            background-color: #ffcc00;
            color: #333;
            padding: 15px;
            border-radius: 5px;
            margin: 20px 0;
            font-weight: bold;
        }
        /* Ogenjski učinek z animiranimi pikicami */
        .fire-effect {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            pointer-events: none; /* Da ne ovira klikov */
            z-index: 0;
            overflow: hidden;
            background: transparent;
        }
        .fire-effect .fire {
            position: absolute;
            width: 10px;
            height: 10px;
            background: rgba(255, 165, 0, 0.8); /* Ognjeni barvni ton */
            border-radius: 50%;
            animation: fire-animation 2s infinite ease-in-out;
        }
        @keyframes fire-animation {
            0% {
                transform: scale(1);
                opacity: 1;
            }
            50% {
                transform: scale(1.5);
                opacity: 0.5;
            }
            100% {
                transform: scale(1);
                opacity: 1;
            }
        }
    </style>
</head>
<body>
    <div class="fire-effect">
        <?php
        // Generirajte ognjene pikice po naključnih mestih
        for ($i = 0; $i < 100; $i++) {
            $left = rand(0, 100) . '%';
            $top = rand(0, 100) . '%';
            $size = rand(5, 15) . 'px';
            echo "<div class='fire' style='left: $left; top: $top; width: $size; height: $size;'></div>";
        }
        ?>
    </div>
    <div class="container">
        <img src="images/teronca.png" alt="Terona Network Logo" class="logo">
        
        <div class="status-overview">
            All Systems Operational
        </div>

        <div class="info-text">
            Terona Network status stran prikazuje trenutno stanje strežnika in spletnih strani ter omogoči igralcem vpogled v morebitne motnje v delovanju.
        </div>

        <?php
        // Display notifications if any service is down
        $minecraftStatus = checkMinecraftStatus();
        $websiteStatus = checkWebsiteStatus($website_url);

        if (!$minecraftStatus || !$websiteStatus) {
            echo '<div class="notification">';
            if (!$minecraftStatus) {
                echo 'Minecraft Server is currently Offline. ';
            }
            if (!$websiteStatus) {
                echo 'Website is currently Offline. ';
            }
            echo '</div>';
        }
        ?>

        <div class="status-box">
            <h2>Websites</h2>
            <div class="status-bar">
                <div class="bar">
                    <?php generateWebsiteBars(); ?>
                </div>
                <div class="status-text <?php echo checkWebsiteStatus($website_url) ? 'operational' : 'down'; ?>">
                    <?php echo checkWebsiteStatus($website_url) ? 'Operational' : 'Offline'; ?>
                </div>
            </div>
            <div class="uptime-info">
                90 days ago &nbsp;&nbsp;&nbsp;&nbsp; 100.0 % uptime &nbsp;&nbsp;&nbsp;&nbsp; Today
            </div>
        </div>

        <div class="status-box">
            <h2>Minecraft servers</h2>
            <div class="status-bar">
                <div class="bar">
                    <?php generateMinecraftBars(); ?>
                </div>
                <div class="status-text <?php echo checkMinecraftStatus() ? 'operational' : 'down'; ?>">
                    <?php echo checkMinecraftStatus() ? 'Operational' : 'Offline'; ?>
                </div>
            </div>
            <div class="uptime-info">
                90 days ago &nbsp;&nbsp;&nbsp;&nbsp; 100.0 % uptime &nbsp;&nbsp;&nbsp;&nbsp; Today
            </div>
        </div>

        <!-- History Section -->
        <div class="history-box">
            <h2>Service Downtime History</h2>
            <div class="history-content">
                <?php displayHistory(); ?>
            </div>
        </div>
    </div>
</body>
</html>
