<?php


require "predis/autoload.php";
Predis\Autoloader::register();


// Connexion à Redis
try {

    $redis = new Predis\Client(array(
        "scheme" => "tcp",
        "host" => "localhost",//changer le nom de la base
        "port" => 6379,
        //"password"=>"bsXiEYKjq3AfpKRjuuRheT5ig7nZyZuF",//changer le mot de passe de la base
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

function getPlayerName($redis) {
    $redis->del('currentPlayer');
    if(isset($_POST['currentPlayer'])){
        $redis->set('currentPlayer', $_POST['setPlayer']);
        
    }else{
        $_POST["setPlayer"]='';
    }
    $playerName = $redis->get('currentPlayer');
    return $playerName;
}

function getPlayers($redis) {
    $redis->rpush('players', 'P1',"P2","P3","P4","P5","P6","P7","P8","P9","P10");
    $players = $redis->lrange('players',0,-1);
    $redis->del('players'); 
    return $players;
}

function getWord($redis){
    if(isset($_POST['htmlWord'])){
        $redis->set('word', $_POST['htmlWord']);
        $redis->expire('word','60');
        $myWord = $redis->get('word');
        
         return $myWord;
    }else{
        
    }
}
function getTtlWord($redis,$word){

    $ttl=$redis->ttl($word);
    return $ttl;
}
function getLetters($redis){

    $lettersArray=$redis->lrange('testedLetters',0,-1);
    return $lettersArray;
}
function isWinning($redis){
    $letters=getLetters($redis);
    $myWord=getWord($redis);
    $length = strlen($myWord);
    $myArray = array();
    for ($i=0; $i<$length; $i++) {
         $myArray[$i] = $myWord[$i]; 
    }
    for ($j=0; $j<$length; $j++) {
        if(in_array ( $myArray[$j], $letters)){
            
        }
        else{
            return false;;
        }
    }return true;

}

$redis->set('maxTry','10');
$MAX_TRY=$redis->get('maxTry');

$redis->set('currentTryr','0');


$currentTry=$redis->get('currentTryr');

function setLetters($redis){
    if(isset($_POST['letter']) && isset($myWord)){
        $let=$_POST['letter'];
        if(isset($letters[$let])) {
            echo 'hello';
        }
        else{
            $redis->lpush('testedLetters', $let);
            //incrementer currenttry
            $redis->incr('currentTryr');
            }
    }
    
    $letters = $redis->lrange('testedLetters',0,-1);
}
setLetters($redis);
$redis->del('letter');
$redis->del('currentTryr');