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
                
                <div id="face">
                </div>

                <h1>Transformez vos notes Kindle en fiches de revision de vocabulaire : </h1>

                <form action="cible.php" method="post" enctype="multipart/form-data">

                    <p>
                        Formulaire d'envoi de fichier :<br />
                            <input type="file" name="monfichier" /><br />
                            <input type="submit" value="Envoyer le fichier" /> <br/>    
                    </p>
                    
                </form>
            
            </div>
            
        <!-- </div> -->

    </body>

</html>

