function surligne(champ, erreur) 
{
   if(erreur)
      champ.style.backgroundColor = "#e2acb7";
   else
      champ.style.backgroundColor ="";
}

function verifMail(champ)
{
   var regex = /^[a-zA-Z0-9._-]+@[a-z0-9._-]{2,}\.[a-z]{2,6}$/;
   if(!regex.test(champ.value))
   {
      surligne(champ, true);
      return false;
   }
    surligne(champ, false);
    return true;
}

function verifMessage(champ)
{
   if(champ.value.length < 2 || champ.value.length > 140)
   {
      surligne(champ, true);
      return false;
   }
    surligne(champ, false);
    return true;   
}
function verifForm(champ)
{
   var mailOk = verifMail(f.Mail);
   var messageOk = verifMessage(f.Mail);

  if(mailOk && messageOk)
   {    
      return true;
    }
   else
   {
      alert("Le pigeon à du mal à s'envoler, veuillez remplir correctement les champs ");
      return false;
   }
}