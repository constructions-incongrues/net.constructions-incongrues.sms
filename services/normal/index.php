<?php
$database = file('http://norme.resterdigne.net/normal.txt');
echo htmlspecialchars_decode(utf8_decode($database[array_rand($database)]), ENT_QUOTES);
