<?php
$content = file_get_contents('resources/views/welcome.blade.php');
$decoded = mb_convert_encoding($content, 'ISO-8859-1', 'UTF-8');
file_put_contents('resources/views/welcome.blade.php', $decoded);
echo "Done";
