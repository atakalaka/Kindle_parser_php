<header>

        <form action="pseudo_change.php" method="post" enctype="multipart/form-data">
                <p>
                        Bonjour <?php echo $_COOKIE['cookie_pseudo']; ?>  ! Ca n'est pas vous ? Modifier votre pseudo ici :   
                        <input type="text" name="pseudonyme" />   
                        <input type="submit" value="Valider votre pseudo" /> 
                </p>
        </form>

</header>