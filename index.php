<?php

include("connection.php");

?>
<form method="POST" action="http://localhost/pigeon/traitement.php" enctype="multipart/form-data">
          
    <!-- limite de fichier 100Ko -->

    <input type="hidden" name="MAX_FILE_SIZE" value="100000000">

    <input type="text" id="expediteur" name="expediteur" placeholder="Expediteur"></input>

    <input type="text" id="destinataire" name="destinataire" placeholder="Destinataire"></input>

    <textarea type="text" id="message" name="message" placeholder="Ecrivez votre message ici..."></textarea>

    <input type="file" name="upload"><br/>

    <input type="submit" name="envoyer" value="Envoyer le fichier">
</form>
