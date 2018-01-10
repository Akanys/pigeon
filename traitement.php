<?php

// CONNECTION
$BASE_URL = "http://localhost/pigeon/";

include("connection.php");

// RECUPERATION 

if(isset($_FILES['upload']))
{
    $dossier = 'C:/wamp64/www/pigeon/fichier/'; // Le dossier qui contient le fichier upload
    $fichier = basename($_FILES['upload']['name']); // Le nom du fichier
    
    // Vérification du type de fichier
 
    $extensions = array('.zip'); // 
    $extension = strrchr($_FILES['upload']['name'], '.');

    if(!in_array($extensions, $extensions))
    {
        $erreur = 'Fichier non autorisé';
    }

    // Vérification de la taille du fichier

    $taille_maxi = 100000000;

    $taille = $_FILES['upload']['tmp_name'];

    if($taille>$taille_maxi)

    {
        $erreur = 'Fichier trop volumineux';
    }

    // Nom de fichier formater

    $fichier = strtr($fichier,
        'ÀÁÂÃÄÅÇÈÉÊËÌÍÎÏÒÓÔÕÖÙÚÛÜÝàáâãäåçèéêëìíîïðòóôõöùúûüýÿ',
        'AAAAAACEEEEIIIIOOOOOUUUUYaaaaaaceeeeiiiioooooouuuuyy');
    $fichier = preg_replace('/([^.a-z0-9]+)/i', '-', $fichier);

    if(move_uploaded_file($_FILES['upload']['tmp_name'], $dossier . $fichier)) // TRUE
    { 

        $aleaname = uniqid();
        $expediteur = $_POST['expediteur'];
        $destinataire = $_POST['destinataire'];
        $message = $_POST['message'];

        $name = "upload$aleaname.zip";

        rename("C:/wamp64/www/pigeon/fichier/$fichier", "C:/wamp64/www/pigeon/fichier/$name" );

        $envoi = $db->query("INSERT INTO upload(expediteur, destinataire, message, fichier) VALUE ('$expediteur', '$destinataire', '$message', '$name')");

        // ENVOI

        $nom         = "&#960;-geon";
        $email       = (isset($_POST['destinataire']))   ? Rec($_POST['destinataire'])   : '';
        $email       = (IsEmail($email)) ? $email : '';
        $message     = $_POST['message'];
        $objet       = "Une personne vous a envoyé un pigeon.";

        $message = str_replace("&#039;","'",$message);
        $message = str_replace("&#8217;","'",$message);
        $message = str_replace("&quot;",'"',$message);
        $message = str_replace('&lt;br&gt;','',$message);
        $message = str_replace('&lt;br /&gt;','',$message);
        $message = str_replace("&lt;","&lt;",$message);
        $message = str_replace("&gt;","&gt;",$message);
        $message = str_replace("&amp;","&",$message);

        // ENVOI EN BASE DE DONNEE
    
        $messagemail = "Un pigeon voyageur vient de se cogner à votre fenêtre, cliquez sur le lien suivant pour aller récupérer votre message : https://benjaming.promo-4.codeur.online/pigeon/upload.php?file=" . $name;

        //      FONCTION

        function IsEmail($email)
        {
        $value = preg_match('/^(?:[\w\!\#\$\%\&\'\*\+\-\/\=\?\^\`\{\|\}\~]+\.)*[\w\!\#\$\%\&\'\*\+\-\/\=\?\^\`\{\|\}\~]+@(?:(?:(?:[a-zA-Z0-9_](?:[a-zA-Z0-9_\-](?!\.)){0,61}[a-zA-Z0-9_-]?\.)+[a-zA-Z0-9_](?:[a-zA-Z0-9_\-](?!$)){0,61}[a-zA-Z0-9_]?)|(?:\[(?:(?:[01]?\d{1,2}|2[0-4]\d|25[0-5])\.){3}(?:[01]?\d{1,2}|2[0-4]\d|25[0-5])\]))$/', $email);
        return (($value === 0) || ($value === false)) ? false : true;
        }

        function Rec($text)
        {
        $text = htmlspecialchars(trim($text), ENT_QUOTES);
        if (1 === get_magic_quotes_gpc())
        {
        $text = stripslashes($text);
        }

        $text = nl2br($text);
        return $text;
        };

        //      CONFIGURATION

        $destinataire = $email;

        $copie = 'non';

        $form_action = '';

        $messagemail_envoye = "Votre message est bien parvenu !";
        $messagemail_non_envoye = "L'envoi du mail a échoué, veuillez réessayer SVP.";

        $messagemail_formulaire_invalide = "Vérifiez que tous les champs soient bien remplis et que l'email soit sans erreur.";

        //      FIN DE LA CONFIGURATION

        $email = (IsEmail($email)) ? $email : ''; // soit l'email est vide si erroné, soit il vaut l'email entré
        $err_formulaire = false; // sert pour remplir le formulaire en cas d'erreur si besoin

        // On prépare la requête
        $requete = $db->prepare("INSERT INTO message (nom, email, objet, message) VALUES (:nom, :email, :objet, :message)");

        // On lie la variable $email définie au-dessus au paramètre :email de la requête préparée
        $requete->bindValue('nom', $nom);
        $requete->bindValue('email', $email);
        $requete->bindValue('objet', $objet);
        $requete->bindValue('message', $messagemail);

        //On exécute la requête

        $requete->execute();

        if (isset($_POST['envoi']))
        {
        if (($nom != '') && ($email != '') && ($objet != '') && ($messagemail != ''))
        {
        // les 4 variables sont remplies, on génère puis envoie le mail
        $headers  = 'From:'.$nom.' <'.$email.'>' . "\r\n";
        //$headers .= 'Reply-To: '.$email. "\r\n" ;
        //$headers .= 'X-Mailer:PHP/'.phpversion();

        // envoyer une copie au visiteur ?
        if ($copie == 'oui')
        {
        $cible = $destinataire.';'.$email;
        }
        else
        {
        $cible = $destinataire;
        };

        // Envoi du mail
        $num_emails = 0;
        $tmp = explode(';', $cible);
        foreach($tmp as $email_destinataire)
        {
        if (mail($email_destinataire, $objet, $messagemail, $headers))
        $num_emails++;
        }

        if ((($copie == 'oui') && ($num_emails == 2)) || (($copie == 'non') && ($num_emails == 1)))
        {
        echo '<p>'.$messagemail_envoye.'</p>';
        }
        else
        {
        echo '<p>'.$messagemail_non_envoye.'</p>';
        };
        }
        else
        {
        // une des 3 variables (ou plus) est vide ...
        echo '<p>'.$messagemail_formulaire_invalide.'</p>';
        $err_formulaire = true;
        };
        }; // fin du if (!isset($_POST['envoi'])) */

        echo 'Envoi réussi!';
        
    }
    else // FALSE
    {
        echo 'Echec de l\'envoi !';
    }
}
?>