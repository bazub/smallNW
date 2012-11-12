<?php
	
	function GetProjectsFromUser($name,$image){
		//This part gets the User that you want the github info from
		$url="https://github.com/";
		$url.=$name;
		//This part starts cURL on https://github.com/user and stores the retrieved file in "tmpusr.txt"
		/*
		$ch = curl_init('"'.$url.'"');
		$fout = fopen("tmpusr.txt", "w") or die("can't open output file");
		curl_setopt ($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_HEADER, 0);
		curl_setopt($ch, CURLOPT_FILE, $fout);
		$result=curl_exec($ch);
		if(curl_errno($ch)){
			echo 'Curl error: ' . curl_error($ch); //outputs the curl error that prevents the script from running
		}
		curl_close($ch);
		fclose($fout);
		*/
	
		//This part opens 'tmpusr.txt' so it can start scraping the page.
		$fin= fopen("tmpusr.txt","r") or die("can't open input file");
		if($image==NULL)
			$noimg=0;	
		else $noimg=1;
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
					//We found the image, so there is no need to search again for it.
					$sql2="UPDATE USERS SET img_link='".$line."' WHERE user='".$name."'";
					mysql_query($sql2); //update image location in Database
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
					$line=substr($line,$i+1,$n-$i);
					$i=strpos($line,'"');
					$line=substr($line,1,$i+1);
					//$line now has the following format: $username/$projectname/Network. We only need the second word, so we are splitting $line, and ignoring the first&last word
					list($aux, $projname, $aux) = explode('/', $line);
					//To show all projects, comment the next line
					if(strtoupper(substr($projname,0,3))=="TT_"){
						$link='https://github.com/'.$name.'/'.$projname;
						$desc=0; //didn't find description
						while($desc==0){
							$line=fgets($fin);
							$i=strpos($line,'description');
							if($i!==false){ //we found the description tag
								$line=fgets($fin);
								$description=strpbrk($line,'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz'); //ignore the whitespaces in the beggining
								$desc=1;
								$done=0;
								if(strtoupper(substr($description,0,5))=="DONE!"){
									$done=1; //project has been marked as "done"
								}
							}
						}
						$modi=0; 	//will search for last modified (this will be used in order to optimize the search)
									//if the last modified matches the "last modified" stored in the db, we will not search for other projects
						while($modi==0){
							$line=fgets($fin);
							$i=strpos($line,'updated-at');
							if($i!==false){ //we are on the line containing the date
								$i=strpos($line,'title');
								$dt=substr($line,$i+7,19);
								$modi=1;
							
							}
						
						}
					

						$sql2="SELECT * FROM TTs WHERE project='".$projname."' AND user='".$name."'";
						$res2=mysql_query($sql2);
						if(!$res2){
							//project does not exist in the database
							echo "should insert</br>";
							$sql3="INSERT INTO TTs (user,project,description,lastupdate,project_link,done) VALUES('".$name."','".$projname."','".$description."','".$dt."','".$link."','".$done."')";
							$res=mysql_query($sql3);
							echo $res;
						}
						else{
							//project is in the database
							//the 2 options are:
							//1) this is a project that hasn't been updated -> there are no other updated projects so we can go to the next user
							//2) this project was updated, so we update the info in the database
							$line=mysql_fetch_array($res2);
							if($line["last_update"]==$dt)
								break;
							else{
								$sql3="UPDATE TTs SET description='".$description."',lastupdate='".$dt."',done='".$done."' WHERE user='".$name."' AND project='".$projname."' ";
								mysql_query($sql3);
							}

						}
					}
				}
			
			
			
			}
	
		}
		fclose($fin);
	}
	mysql_connect('localhost','root','test123') or die ("database error");
	mysql_select_db("TT");
	$sql='CREATE TABLE IF NOT EXISTS `USERS` ('
        . ' `user` VARCHAR(100) NOT NULL, '
        . ' `img_link` VARCHAR(200) NULL'
        . ' )'
        . ' ENGINE = innodb;';
	mysql_query($sql);
	$sql = 'CREATE TABLE `TTs` ('
        . ' `user` VARCHAR(100) NOT NULL, '
        . ' `project` VARCHAR(100) NOT NULL, '
        . ' `description` VARCHAR(200) NOT NULL, '
        . ' `lastupdate` VARCHAR(50) NOT NULL, '
        . ' `project_link` VARBINARY(200) NOT NULL, '
        . ' `done` INT NOT NULL DEFAULT \'0\', '
        . ' `rating` FLOAT NOT NULL DEFAULT \'0\', '
        . ' `ratenum` INT NOT NULL DEFAULT \'0\', '
        . ' `tested` INT NOT NULL DEFAULT \'0\', '
        . ' `test_by` VARCHAR(100) NULL, '
        . ' `test_date` DATETIME NULL'
        . ' )'
        . ' ENGINE = innodb;';
	mysql_query($sql);
	$sql="SELECT * FROM USERS WHERE 1";
	$res=mysql_query($sql);
	echo '<h1><a href="add.php">Add New User</a></h1>';
	if($res){
		$n=mysql_num_rows($res);
		for($i=0;$i<$n;$i=$i+1){
			$line=mysql_fetch_array($res);
			GetProjectsFromUser($line["user"],$line["img_link"]);
		}
		
		$sql="SELECT * FROM TTs WHERE 1";
		$res=mysql_query($sql);
		if($res){ 
			$n=mysql_num_rows($res);
			echo '<table border=1><tr><td>Project Name</td><td>Owner</td><td>Done?</td><td>Rating</td></tr>';
			for($i=0;$i<$n;$i=$i+1){
				$line=mysql_fetch_array($res);
				echo '<tr>';
				if($line["ratenum"]){
					$rated=$line["rating"];
					$rated.="/5";
				}
				else $rated="Not rated.";
				if($line["done"]=='1'){
					$done="Done!";
				}
				else $done="Not finished";
				echo '<td><a href="project.php?proj='.$line["project"].'&own='.$line["user"].'">'.$line["project"].'</a></td><td>'.$line["user"].'</td><td>'.$done.'</td><td>'.$rated.'</td>';
				echo '</tr>';
			}
			echo '</table>';
		}
	}
?>