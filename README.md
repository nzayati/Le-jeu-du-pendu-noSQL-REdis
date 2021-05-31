# TP 6 NoSQL REDIS 

### _Le jeu du pendu_

##### Auteurs :
- Zayati Narjess
- Bricha Fatima Zahra
- Mekedem Sara

## Comment jouer ?

- La liste des joueurs s'affiche
- On peut ajouter ou supprimer un joueur
- Un joueur propose un mot
- Les autres joueurs peuvent proposer des lettres ou des mots tour à tour
- Les joueurs ont 60 secondes et 10 essais pour trouver le mot
- Si le mot a été trouvé les joueurs participants gagnent
- Si le mot n'a pas été trouvé, le joueur ayant proposé gagne 10 points
- Si aucun mot n'a été proposé, on peut ajouter des joueurs 

## Comment lancer l'application ? 

- Lancer redis server
- Lancer wamp

## Structure de l'application

- index.php : Affichage de la page html général, c'est la page qu'on doit afficher dans l'url du navigateur
- page.php : Interractions entre php et redis
- redis-connexion.php : Contient le code php nécessaire pour la connexion à redis
- redis-functions.php : Contient les fonctions redis utilisées dans le code, il est possible ici de changer, lesle host ou le password (qui est commenté pour nous)
- /images : Contient les 10 images du pendu qui seront affichées lors du jeu
