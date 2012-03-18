<?php


    $GLOBALS['wowarmory']['db']['driver'] = 'mysql'; // Dont change. Only mysql supported so far.


    $GLOBALS['wowarmory']['db']['hostname'] = 'localhost'; // Hostname of server. 


    $GLOBALS['wowarmory']['db']['dbname'] = 'figmentw_wow'; //Name of your database  *** base de datos de ORCADIANO ***


    $GLOBALS['wowarmory']['db']['username'] = 'figmentw_asiste'; //Insert your database username


    $GLOBALS['wowarmory']['db']['password'] = 'XgS@Kw87V~eX'; //Insert your database password


    // Only use the two below if you have received API keys from Blizzard.


	include('BattlenetArmory.class.php');


    





	//$character = $armory->getCharacter('ayme');


	//$members = $guild->getMembers('name','asc');


	//print_r($members[1]['character']['name']);


	//$reputation = $character->getData();


	//$thumbnailurl = $character->getProfilePicURL();


    


    //print_r($reputation["guild"]);





function checkcharacter($nombre,$guild,$region,$realname,$minimo){


	$nombre = ucfirst($nombre);


	$realname = ucfirst($realname);


	$enguild = "no";


	$armory = new BattlenetArmory($region,$realname);


	$meguild = $armory->getGuild($guild);


	$members = $meguild->getMembers('name','asc');


	$character = $armory->getCharacter($nombre);


	$reputation = $character->getData();


	if($reputation["guild"]["name"] == $guild){


		//Checamos el rango


		for($i=1;$i<=count($members);$i++){


			if($members[$i]["character"]["name"]==$nombre){


				//Comprovamos que el rango sea mayor al rango inpuesto por el admin


				$enguild = "si";


				if($members[$i]["rank"]<$minimo or $minimo==0){


					return "si";


				}else{


					return "Rango insuficiente";


				}				


				break;


			}


		}


		


		if($enguild == "no"){


			//Tenemos un problema al buscar al usuario en la guild


			return "Intenta mas tarde";


		}


		


	}else{


		return "Este usuario no pertenece a la Guild ".$guild;


	}


	//return print_r($reputation["guild"]["name"]);


}


	//echo checkcharacter("Toolshed","Sanctuary Of The Fallens")


?>