<?php
$feed = json_decode(json_encode(simplexml_load_file('http://leopard-raws.org/rss.php')), true);
print_r($feed['channel']['item']);
?>