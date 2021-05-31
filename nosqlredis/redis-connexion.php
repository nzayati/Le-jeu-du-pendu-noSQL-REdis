<?php

//On lance la session
session_start();

require "predis/autoload.php";
Predis\Autoloader::register();

// Connexion à Redis
try {

    $redis = new Predis\Client(array(
        "scheme" => "tcp",
        "host" => "localhost", //changer le nom de la base
        "port" => 6379,
        //"password" => "THpCYGub1Hlz1mjSF34scGgWWaoyBYr5" //changer le mot de passe de la base
    ));
} catch (Exception $e) {
    die($e->getMessage());
}
