<?php


require "predis/autoload.php";
Predis\Autoloader::register();


// Connexion à Redis
try {

    $redis = new Predis\Client(array(
        "scheme" => "tcp",
        "host" => "localhost",//changer le nom de la base
        "port" => 6379
       // "password"=>"bsXiEYKjq3AfpKRjuuRheT5ig7nZyZuF"//changer le mot de passe de la base
    ));

}
catch (Exception $e) {
    die($e->getMessage());
}
 
// mise à jour de la valeur
$redis->set('message', 'Hello world');

// recuperation de la valeur
$value = $redis->get('message');

// affichage de la valeur'
//print($value);
//echo ($redis->exists('message')) ? "Oui" : "Non";

//suppression de la clé
$redis->del('message'); 



$redis->rpush('players', 'P1',"P2","P3","P4","P5","P6","P7","P8","P9","P10");
$players = $redis->lrange('players',0,-1);
$redis->del('players'); 



$redis->set('word', $_POST['htmlWord']);
$redis->expire('word','60');
$time= $redis->ttl('word');
$myWord = $redis->get('word');

$redis->lpush('testedLetters', $_POST['letter']);
$letters = $redis->lrange('testedLetters',0,-1);
$redis->del('letters');