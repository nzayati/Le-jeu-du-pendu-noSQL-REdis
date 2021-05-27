<!doctype html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Le PeNdU</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/css/bootstrap.min.css" integrity="sha384-Vkoo8x4CGsO3+Hhxv8T/Q5PaXtkKtu6ug5TOeNV6gBiFeWPGFN9MuhOf23Q9Ifjh" crossorigin="anonymous">
</head>
<body>
<?php include("index.php"); ?>

<nav class="navbar navbar-light bg-light">
    <span class="navbar-brand mb-0 h1">Le PeNdU</span>
    <span class="navbar-text">
    <?php 
    
    $players=getPlayers($redis);
    $playerName=getPlayerName($redis);
    if(isset($playerName)){
        echo "Bonjour"+$playerName+", ton score est "+$playerScore+" points !";}
    else{
        echo'<form action="page.php" method="post"><span><select>';
        $in=0;
        foreach ($players as $p){
            echo "<option name='setPlayer' value='s+$in+'>$p</option>";
            $in++;}
        echo '</select><button type="submit">Se connecter</button></span></form>';
    }
    
    ?>
    </span>
</nav>


<div class="container">

    <div class="row">
        <div class="col-sm-3">
            <h2>Liste des joueurs</h2>
            <ul>
                <?php
                foreach ($players as $p){
                    echo "<li>$p</li>";
                }
                ?>
            </ul>
        </div>
        <div class="col-sm-6">
            <h2>Mot à trouver</h2>
            <span>
            <? 
            $myWord=getWord($redis);
            if(isset($myWord)){
                $length = strlen($myWord);
                $myArray = array();
                for ($i=0; $i<$length; $i++) {
                     $myArray[$i] = $myWord[$i]; 
                }
                for ($j=0; $j<$length; $j++) {
                    if(in_array ( $myArray[$j], $letters)){
                        echo strtoupper($myArray[$j]);
                    }
                    else{
                        echo "_";
                    }
                    echo " &nbsp";
                }
            }
            else{
                $myWord=getWord($redis);
                echo " Proposez un mot !";
            }
            if(isWinning($redis)){
                echo " Bravo !";
            }
     ?></span>
        </div>



        <div class="col-sm-3">
            <h2>Propositions</h2>
            <ul><?php
                $letters=getLetters($redis);
                foreach ($letters as $l){

                    echo "<li>$l</li>";
                }
                ?>
            </ul>
        </div>
    </div>
    <div class="row">
        <div class="col-sm-6">
            <h2>Temps restant</h2>
            <span><?php echo getTtlWord($redis,getWord($redis)); ?> secondes</span>
        </div>
        <div class="col-sm-6">
            <h2>Nombre d'essais restant</h2>
            <span><?php echo $currentTry; ?> essais</span>
        </div>
    </div>
    <div class="row">
        <div class="col-sm-6">
            <h2>Proposer une lettre</h2>
            <form method="post">
            <span><input type="text" name="letter" size="3"/><button>Valider</button></span>
            </form>
        </div>
        <div value="my_word" class="col-sm-6">
            <h2>Proposer un mot</h2>
            <form action="page.php" method="post">
            <span><input type="text" name="htmlWord" size="20"/><button type="submit">Valider</button></span>
            </form>
        </div>
    </div>
</div>
</body>
</html>