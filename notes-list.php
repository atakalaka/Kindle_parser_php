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
        <title>Kindle notes parser</title>
        <link rel="stylesheet" type="text/css" href="css/stylesheet.css" />
    </head>

    <body>

        <!-- <div id="block_page"> -->

            <?php include("navigation.php"); ?>
            
            <div id="formulaire_central">

                <?php include("header.php"); ?>
                
                <div>

                    <?php

                    if ($_GET['bookId']) {

                        try {
                            $bdd = new PDO('mysql:host=localhost;dbname=kindleParser;charset=utf8', 'root', 'root', array(PDO :: ATTR_ERRMODE => PDO :: ERRMODE_EXCEPTION));
                            }
                        catch(Exception $e)
                            {
                            die('Une erreur est survenue lors du chargement de la base de donnees : '.$e->getMessage()); //si ca marche pas, on arrete tout, et on affiche un message d'erreur
                            }
                        
                        $rep_booktitle = $bdd->query('SELECT * FROM books WHERE book_id = '.$_GET['bookId']); //je cherche le livre avec son id

                        $title = $rep_booktitle->fetch()['book_title'];
                        
                        echo ('<h1>Liste de mots pour '.$title.' </h1>'); //j'affiche son nom.
                        
                        // LE VOCABULAIRE
                        
                        echo ('<br/><h2>Votre Vocabulaire</h2><br/>');

                        $reponse = $bdd->query('SELECT english_text, translation FROM notes WHERE is_vocab = 1 AND book_id = '.$_GET['bookId']); //beaucoup de conditions a ajouter ici 

                        echo ('<table>
                                <tr>
                                    <th>Anglais</th>
                                    <th>Traduction</th>
                                </tr>');

                        while ($donnees = $reponse->fetch()){
                            echo ('<tr><td>' . $donnees['english_text'] .'</td><td>'.$donnees['translation']. '</td></tr>'); 
                        }

                        echo '</table>';

                        // LES CITATIONS

                        echo ('<br/><h2>Vos citations</h2><br/>');

                        $reponse = $bdd->query('SELECT english_text FROM notes WHERE is_vocab = 0 AND book_id = '.$_GET['bookId']); //beaucoup de conditions a ajouter ici 

                        echo '<ol>';

                        while ($donnees = $reponse->fetch()){
                            echo ('<li>' . $donnees['english_text'] . '</li>');
                        }

                        echo '</ol>';

                    } else {
                        echo ('<br/><p>Pas de livre trouve dans l\'URL de la page, essayez de repasser par votre liste de livres, et arretez de traffiquer les URL !</p>');
                    }

                    ?>

                </div>
            
            </div>
            
        <!-- </div> -->

    </body>

</html>