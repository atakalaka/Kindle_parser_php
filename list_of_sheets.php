<?php
    if (isset($_COOKIE['cookie_pseudo']) == false)
    {
        setcookie('cookie_pseudo', 'pseudo_issu_du_cookie', time() + 30*24*3600, '/', null, false, true); 
    }
    session_start();
?>

<!DOCTYPE HTML>
<html>

    <head>
        <title>my Kindle notes</title>
        <link rel="stylesheet" type="text/css" href="css/stylesheet.css" />
        <!-- -->
    </head>
    
    <body>

        <!-- <div id="block_page"> -->

            <?php include("navigation.php"); ?>

            <div id="formulaire_central">

                <?php include("header.php"); ?>

                <div id="books_picture"> </div>

                <h1>Voici une liste de vos livres charges dans notre base de donnees : </h1>

                <br>

                <?php
                try {
                    $bdd = new PDO('mysql:host=localhost;dbname=kindleParser;charset=utf8', 'root', 'root', array(PDO :: ATTR_ERRMODE => PDO :: ERRMODE_EXCEPTION));
                    }
                catch(Exception $e)
                    {
                    die('Une erreur est survenue lors du chargement de la base de donnees : '.$e->getMessage()); //si ca marche pas, on arrete tout, et on affiche un message d'erreur
                    }

                $reponse = $bdd->query('SELECT * FROM books WHERE book_owner = '.'\''.$_COOKIE['cookie_pseudo'].'\'');  
                while ($donnees = $reponse->fetch()){
                    echo ('<a href="notes-list.php?bookId=' . $donnees['book_id'] . '" a>'.$donnees['book_title'].'</p>'); //je mets le nom du livre en parametre query string (dans l'URL).
                }
                ?>

            </div>

        <!-- </div> -->

    </body>

</html>