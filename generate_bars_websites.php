<?php
$website_url = 'https://kreten.si'; // URL vaše spletne strani

function checkWebsiteStatus($url) {
    $headers = @get_headers($url);
    if ($headers && strpos($headers[0], '200')) {
        return true; // Stran je dostopna
    } else {
        return false; // Stran ni dostopna
    }
}

function generateBars($uptimeDays = 90) {
    $isOnline = checkWebsiteStatus('https://terona.org');
    for ($i = 0; $i < $uptimeDays; $i++) {
        echo $isOnline ? "<div class='status-line online'></div>" : "<div class='status-line offline'></div>";
    }
}

generateBars();
?>
