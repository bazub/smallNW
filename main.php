<?php
//This part gets the User that you want the github info from
$name=$_GET["name"];
$url="https://github.com/";
$url.=$name;

//This part starts cURL on https://github.com/user and stores the retrieved file in "tmpusr.txt"
$ch = curl_init($url);
$fout = fopen("tmpusr.txt", "w") or die("can't open output file");
curl_setopt ($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_HEADER, 0);
curl_setopt($ch, CURLOPT_FILE, $fout);
$result=curl_exec($ch);
curl_close($ch);
fclose($fout);

//This part opens 'tmpusr.txt' so it can start scraping the page.
$fin= fopen("tmpusr.txt","r") or die("can't open input file");
$noimg=0;
while(!feof($fin)){
	$line=fgets($fin); //Read the file, one line at a time.

	//Get avatar
	//The avatar url is on the line following the line which has "avatared" in it. There is only one such line.	
	if($noimg==0){
		$i=strpos($line,"avatared");
		if($i!==false){
			//The keyword "avatared" was found
			$line=fgets($fin);
			$n=strlen($line);
			$i=strpos($line,'"');
			$line=substr($line,$i+1,$n-1);
			$i=strpos($line,'"');
			$line=substr($line,0,$i);
			// $line now contains the url for the avatar
			echo '
				<img src="'.$line.'" height="100" width="100"></img><br>
			';
			//We found the image, so there is no need to search again for it.
			$noimg=1;
		}
	}

	//Get projects	
	//The project links/names can be found after the line which has 'class="forks"' in it. There is only one such line for every project.
	//We only search for projects only after finding the image. 
	if($noimg==1){	
		$i=strpos($line,'class="forks"');
		if($i!==false){
			//The keyword was found.
			$line=fgets($fin);
			$n=strlen($line);
			$i=strpos($line,'"');
			$line=substr($line,$i+1,$n-i);
			$i=strpos($line,'"');
			$line=substr($line,1,$i+1);
			//$line now has the following format: $username/$projectname/Network. We only need the first 2 things, so we are splitting $line, and ignoring the last word			
			list($user, $projname, $aux) = split('[/]', $line);
			//To show all projects, comment the next line
			if(strtoupper(substr($projname,0,3))=="TT_")
				echo 	$user," - ",
					'<a href="https://github.com/'.$user.'/'.$projname.'">',
					$projname,
					"</a>",			
					"<br>";
		}
	}
	
}

?>

