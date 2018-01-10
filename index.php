<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Document</title>
</head>
<body>

<form method="POST" action="https://benjaming.promo-4.codeur.online/pigeontest/traitement.php" enctype="multipart/form-data"> <!-- Formulaire pour envoi vers page de traitement --> 

    <input type="hidden" name="MAX_FILE_SIZE" value="100000000"> <!-- Limite de fichier 100Mo --> 

    <input type="text" id="expediteur" name="expediteur" placeholder="Expediteur"></input> <!-- Input pour l'expediteur --> 

    <input type="text" id="destinataire" name="destinataire" placeholder="Destinataire"></input> <!-- Input pour le destinataire--> 

    <textarea type="text" id="message" name="message" placeholder="Ecrivez votre message ici..."></textarea> <!-- Textarea pour le message --> 

    <input type="file" name="upload"><br/> <!-- Input de récupération du fichier --> 

    <input type="submit" name="envoi" value="Envoyer le fichier"> <!-- Input d'envoi pigeon --> 
</form>
    
</body>
</html>