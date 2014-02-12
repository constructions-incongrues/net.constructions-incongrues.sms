<?php
$database = file('http://resterdigne.net/database.txt');
echo $database[array_rand($database)];
