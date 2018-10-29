<!DOCTYPE HTML>
<?php 
    if (isset($_COOKIE['cookie_pseudo']) == false)
    {
        setcookie('cookie_pseudo', 'pseudo_issu_du_cookie', time() + 30*24*3600, '/', null, false, true); 
    }
    session_start();?>
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
                
                <?php
                // Testons si le fichier a bien été envoyé et s'il n'y a pas d'erreur

                // $masque_ponctuation = array('/^[ .,”\?!()]+/','/[ .,”\'?!()]+$/');
                $masque_ponctuation = '/^[ .,”\?!()]+|[ .,”\'?!()]+$/';
                $texte = preg_replace($masque_ponctuation,'','. Va t il y avoir... de la ponctuation ? , . (',2);
                echo ($texte);    

                    if (isset($_FILES['monfichier']) AND $_FILES['monfichier']['error'] == 0)
                        {
                        echo 'Nous avons trouvé un fichier au nom de ' . $_FILES['monfichier']['name'] . '. <br>';
                        // Testons si le fichier n'est pas trop gros
                        if ($_FILES['monfichier']['size'] <= 2000000)
                            {
                                echo 'Le fichier fait moins de 2Mo. <br>';
                                $infosfichier = pathinfo($_FILES['monfichier']['name']);
                                $extension_upload = $infosfichier['extension'];
                                if ($extension_upload == 'html' | $extension_upload == 'txt')
                                    {
                                    echo "Le fichier est bien un HTML.<br><br>";
                                    
                                    // je mets le fichier recupere par post sur le serveur 
                                    move_uploaded_file( $_FILES['monfichier']['tmp_name'], 'uploaded_files/'.$_FILES['monfichier']['name'] );
                                    
                                    // une fois sur le serveur, j'ouvre le fichier
                                    $monfichier = fopen('uploaded_files/'.$_FILES['monfichier']['name'], 'r'); //On ouvre le fichier pour travailler dessus. 
                                                            
                                    $content = stream_get_contents($monfichier, -1, -1); //lit la ressource envoyée par fopen
                                    
                                    $pattern_notes = '/noteText\'>.*</';                //la regex qui correspond aux notes dans le fichier html
                                    $pattern_title = '/bookTitle\'>.*\R</';             //la regex qui correspond au titre dans le fichier html
                                    $masque_ponctuation = '/^[ .,”\?!();]+|[ .,”\'?!();]+$/';

                                    if (preg_match_all($pattern_title, $content, $matches_title) !== 0 )
                                    {

                                        $texte_a_tailler_title = array('bookTitle\'>', '<'); //le masque qu'il faut enlever avant d'envoyer le texte dans la variable definitive

                                        $book_title = str_replace($texte_a_tailler_title,'',$matches_title[0])[0]; //la fonction qui enleve le masque 'bookTitle
                                        
                                        echo('<h1>'.$book_title.'</h1><br/>'); 
                                    }
                                    
                                    if (preg_match_all($pattern_notes, $content, $matches_notes) !== 0)
                                    {
                                        $texte_a_tailler_notes = array('noteText\'>', '<');

                                        $notes_taillees = str_replace($texte_a_tailler_notes,'',$matches_notes[0]);
                                        $vocab = $notes_taillees; //array avec le vocabulaire
                                        $quotes = array();        //array destine a contenir les citations
                                        $i=0;
                                        while ($vocab[$i]){
                                            //Si je trouve plus de 5 espaces dans vocab[$i], je le mets dans quotes et je le vire de vocab
                                            if (substr_count($vocab[$i], ' ')>5){
                                                array_push($quotes, $vocab[$i]);
                                                array_splice($vocab,$i,1); //Je vire vocab[$i], donc je n'ai pas besoin d'incrementer.
                                            } else {
                                                $vocab[$i]=preg_replace($masque_ponctuation,'',$vocab[$i],2); //je vire la ponctuation au debut et a la fin de vocab[$i]
                                                $i++;
                                            }
                                        }

                                        echo ('<br/><br/>');

                                    // On ajoute le livre a la base de donnees

                                    try
                                    {
                                        $bdd = new PDO('mysql:host=localhost;dbname=kindleParser;charset=utf8', 'root', 'root', array(PDO :: ATTR_ERRMODE => PDO :: ERRMODE_EXCEPTION));
                                    }
                                    catch(Exception $e)
                                    {
                                        die('Erreur : '.$e->getMessage());
                                    }

                                    // On ajoute une entrée dans la table books.

                                    $fake_book_id = rand(0,32767);

                                    $req = $bdd->prepare('INSERT INTO books(book_id, book_title, book_owner) VALUES(:book_id, :book_title, :book_owner)');
                                    // $req = $bdd->prepare('INSERT INTO books(book_title, book_owner) VALUES(:book_title, :book_owner)');
                                    $req->execute(array(
                                        'book_id' => $fake_book_id,
                                        'book_title' => $book_title,
                                        'book_owner' => $_COOKIE['cookie_pseudo']
                                        )); //On designe le pseudo de l'app comme owner du livre.
                                    
                                    // on ajoute le vocabulaire a la base de donnees
                                    
                                    date_default_timezone_set('UTC+2');//on donne le fuseau horaire pour la fonction date().
                                    
                                    // On prepare l'envoi de donnees dans SQL
                                    $req = $bdd->prepare('INSERT INTO notes(book_id, english_text, translation, is_vocab, upload_date) VALUES(:book_id, :english_text, :translation, :is_vocab, :upload_date)'); 
                                    $i=0; //&i sert a parcourir vocab
                                    while ($vocab[$i]){
                                    //On envoie les donnees de chaque mot de vocabulaire dans la base SQL
                                        $req->execute(array(
                                            'book_id' => $fake_book_id,
                                            'english_text' => $vocab[$i],
                                            'translation' => 'no translation for now',
                                            'is_vocab' => 1,
                                            'upload_date' => date('Y').'-'.date('m').'-'.date('d') //format : 2018-10-09
                                            ));
                                        $i++;
                                    }

                                    //on fait la meme chose pour les citations.
                                    $i=0;
                                    while ($quotes[$i]){
                                        $req->execute(array(
                                            'book_id' => $fake_book_id,
                                            'english_text' => $quotes[$i],
                                            'translation' => 'no translation for now',
                                            'is_vocab' => 0,
                                            'upload_date' => date('Y').'-'.date('m').'-'.date('d') //format : 2018-10-09
                                            ));
                                        $i++;
                                    }

                                    } else {echo 'Pas de correspondance trouvee... Le fichier n\'est pas un document Kindle intact, ou vous n\'avez surligne aucun passage.';}
                                    
                                    fclose($monfichier); //On ferme le fichier
                                                            
                                } else { echo 'Le fichier n\' est pas html. ';}
                            } else { echo 'Le fichier fait plus de 2Mo. Il est trop volumineux pour notre humble serveur. ';}
                        } else { echo 'Pas de fichier trouvé'; }
                ?>

                <a href=list_of_sheets.php> Cliquez ici pour acceder a votre liste de livres <a>

            </div>

        <!-- </div> -->

    </body>

</html>