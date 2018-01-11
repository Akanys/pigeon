<?php

// Connection

$BASE_URL = "https://benjaming.promo-4.codeur.online/pigeontest/"; // Base de l'url

include("connection.php"); // Connection à la base de donnée

// Récupération du fichier 

if(isset($_FILES['upload']))
{
    $dossier = 'fichier/transit/'; // Le dossier qui contient le fichier upload
    $fichier = basename($_FILES['upload']['name']); // Le nom du fichier
    
    /* Vérification du type de fichier
 
    $extensions = array('.zip');
    $extension = strrchr($_FILES['upload']['name'], '.');

    if(!in_array($extensions, $extensions)) // Si l'extension ($extension) n'est pas dans le tableau des extensions ($extensions), on affiche une erreur
    {
        $erreur = 'Fichier non autorisé';
    } */

    // Vérification de la taille du fichier

    $taille_maxi = 100000000; // Définition de la taille maximum en octet

    $taille = $_FILES['upload']['tmp_name']; // Taille du fichier

    if($taille>$taille_maxi) // Si la taille du fichier ($taille) est plus grande que la taille maximum acceptée ($taille_maxi) on affiche une erreur
    {
        $erreur = 'Fichier trop volumineux';
    }

    // Nom du fichier formaté

    $fichier = strtr($fichier,
        'ÀÁÂÃÄÅÇÈÉÊËÌÍÎÏÒÓÔÕÖÙÚÛÜÝàáâãäåçèéêëìíîïðòóôõöùúûüýÿ',
        'AAAAAACEEEEIIIIOOOOOUUUUYaaaaaaceeeeiiiioooooouuuuyy'); // Remplace les caractères dans une chaîne, ici les lettres avec accents par les même sans accents
    $fichier = preg_replace('/([^.a-z0-9]+)/i', '-', $fichier); // Rechercher et remplacer par une expression rationelle standard

    if(move_uploaded_file($_FILES['upload']['tmp_name'], $dossier . $fichier)) // Si le fichier est bien placer dans le dossier alors...
    { 

    // On instancie la classe.
    $zip = new ZipArchive();
     
    $aleaname = uniqid(); //création d'une suite aléatoire
    $foldername = 'fichier/'; //Variable pour donner plus facilement le chemin du dossier pour le zip
    $dossier = 'fichier/transit/'; //Variable pour donner le chemin du fichier à zipper
    $name = "upload" . $aleaname . ".zip"; // Création du nom unique pour le fichier dans une variable
    $namesave = $foldername . $name; // Variable de nom pour créer le dossier zip avec le chemin et 

    if(is_dir($dossier)) // On teste si le dossier existe, car sans ça le script risque de provoquer des erreurs
    {
   
    if($zip->open($namesave, ZipArchive::CREATE) == TRUE) // Ouverture de l’archive réussie
    {
    
    $fichiers = scandir($dossier); // Récupération des fichiers dans le dossier transit
    unset($fichiers[0], $fichiers[1]); // On enlève . et .. qui représentent le dossier courant et le dossier parent
    
    foreach($fichiers as $f) // On ajoute chaque fichier du dossier transit à l’archive en spécifiant l’argument optionnel
    {
    if(!$zip->addFile($dossier.$f, $f)) // Pour ne pas créer de dossier dans l’archive
    {
        echo 'Impossible d&#039;ajouter &quot;'.$f.'&quot;.<br/>';
    }
    }

    $zip->close(); // On ferme l’archive

    unlink($dossier . $f); //On supprime le fichier téléchargé précedement et copier dans l'archive

    }
    else
    {
    // Erreur lors de l’ouverture
    // On peut ajouter du code ici pour gérer les différentes erreurs
    echo 'Erreur, impossible de créer l&#039;archive.';
    }
    }
    else
    {
    // Possibilité de créer le dossier avec mkdir()
    echo 'Le dossier &quot;upload/&quot; n&#039;existe pas.';
    }

    // Récupération des données du formulaire dans des variables + Création d'un nom et renommage du fichier upload

        $expediteur = $_POST['expediteur']; // Récupération de l'expediteur dans une variable
        $destinataire = $_POST['destinataire']; // Récupération du destinataire dans une variable
        $message = $_POST['message']; // Récupération du message dans une variable

    // Remplacement des caractères spéciaux

        $message = str_replace("&#039;","'",$message);
        $message = str_replace("&#8217;","'",$message);
        $message = str_replace("&quot;",'"',$message);
        $message = str_replace('&lt;br&gt;','',$message);
        $message = str_replace('&lt;br /&gt;','',$message);
        $message = str_replace("&lt;","&lt;",$message);
        $message = str_replace("&gt;","&gt;",$message);
        $message = str_replace("&amp;","&",$message);
    
    // Envoi en base de donnée

        $envoi = $db->prepare("INSERT INTO upload(expediteur, destinataire, message, fichier) VALUE (:expediteur, :destinataire, :message, :name)");

        // On lie la variable $ définie au-dessus au paramètre : de la requête préparée
        $envoi->bindValue('expediteur', $expediteur);
        $envoi->bindValue('destinataire', $destinataire);
        $envoi->bindValue('message', $message);
        $envoi->bindValue('name', $name);

        //On exécute la requête

        $envoi->execute();

    // Envoi du message

        // Configuration

            // Fonction de vérification

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

            // Définition des variables

            $nom         = "Pigeon"; // Nom de l'expéditeur
            $email       = (isset($destinataire))   ? (Rec($destinataire))   : ''; // Récupération du mail de destination
            $email       = (IsEmail($email)) ? $email : ''; // Soit l'email est vide si erroné, soit il vaut l'email entré
            $expe        = (isset($expediteur))   ? (Rec($expediteur))   : ''; // Au-dessus
            $expe        = (IsEmail($expe)) ? $expe : ''; // pareil
            $objet       = "Une personne vous a envoyé un pigeon."; // Objet du message
            $messagemail = "Un pigeon voyageur vient de se cogner à votre fenêtre, cliquez sur le lien suivant pour aller récupérer votre message : https://benjaming.promo-4.codeur.online/pigeontest/upload.php?file=" . $name; // Message du mail contenant la variable pour le get de upload.php

            $form_action = '';

            $messagemail_envoye = "Votre pigeon s'est bien envolé  !";
            $messagemail_non_envoye = "Le départ du pigeon a échoué, veuillez réessayer ultérieurement.";
            $messagemail_formulaire_invalide = "Vérifiez que tous les champs soient bien remplis et que l'email soit sans erreur.";

        // fin de la configuration

            $err_formulaire = false; // sert pour remplir le formulaire en cas d'erreur si besoin

            if (isset($_POST['envoi']))
            {
                if (($nom != '') && ($email != '') && ($expe != '') && ($objet != '') && ($messagemail != '')) // Si les 4 variables sont remplies, on génère puis envoie le mail
                {        
                    $headers  = 'From:'.$nom.' <'.$expe.'>' . "\r\n"; // On génère la barre de l'expediteur ($headers .= 'Reply-To: '.$email. "\r\n" ; / $headers .= 'X-Mailer:PHP/'.phpversion();)

                    // Envoi du mail

                    if (mail($email, $objet, $messagemail, $headers)) // Si le mail est envoyé alors $envoi_email devient true
                    {
                        echo '<p>'.$messagemail_envoye.'</p>';
                        echo "https://benjaming.promo-4.codeur.online/pigeontest/upload.php?file=" . $name;
                    }
                    else // Sinon on affiche $messagemail_non_envoye
                    {
                    echo '<p>'.$messagemail_non_envoye.'</p>';
                    };

                }
                else // Si l'une des 3 variables (ou plus) est vide alors on affiche $messagemail_formulaire_invalide
                {
                echo '<p>'.$messagemail_formulaire_invalide.'</p>';
                $err_formulaire = true;
                };

            }; // Fin du if (!isset($_POST['envoi']))
        
    }

    else // Si le fichier n'est pas upload on affiche le message d'erreur suivant
    {
        echo 'Echec lors de l\'upload !';
    }
}
?>