<?php
    if (isset($_COOKIE['cookie_pseudo']) == false)
    {
        setcookie('cookie_pseudo', 'pseudo_issu_du_cookie', time() + 30*24*3600, '/', null, false, true); 
    }
    setcookie('cookie_pseudo', $_POST['pseudonyme'], time() + 30*24*3600, '/', null, false, true); 
    session_start();
?>

<!DOCTYPE HTML>

<html>
    <head>
        <title>Kindle notes parser</title>
        <link rel="stylesheet" type="text/css" href="css/stylesheet.css" />
        <!-- -->
    </head>

    <body>

        <!-- <div id="block_page"> -->

            <?php include("navigation.php"); ?>

            <div id="formulaire_central">

                <?php
                    echo ( 'Votre nouveau pseudo est '.$_POST['pseudonyme']);
                ?>

            </div>

        <!-- </div> -->
    
    </body>

</html>

