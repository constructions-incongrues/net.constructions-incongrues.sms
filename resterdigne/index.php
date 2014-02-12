<?php
$database = file('http://resterdigne.net/database.txt');
echo utf8_decode($database[array_rand($database)]);
