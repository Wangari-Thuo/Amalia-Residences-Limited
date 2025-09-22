<?php
$fp = fsockopen("localhost", 3307, $errno, $errstr, 5);
if (!$fp) {
    echo "Socket failed: $errstr ($errno)<br>";
} else {
    echo "Port 3307 is open and reachable.<br>";
    fclose($fp);
}
?>