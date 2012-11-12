<?php
	//CSS stuff for the stars and links
	echo '
		<style type="text/css">
			.starRate {position:relative; margin:20px; overflow:hidden; zoom:1;}
			.starRate ul {width:160px; margin:0; padding:0;}
			.starRate li {display:inline; list-style:none;}
			.starRate a, .starRate b {background:url(star_rate.gif) left top repeat-x;}
			.starRate a {float:right; margin:0 80px 0 -144px; width:80px; height:16px; background-position:left 16px; color:#000; text-decoration:none;}
			.starRate a:hover {background-position:left -32px;}
			.starRate b {position:absolute; z-index:-1; width:80px; height:16px; background-position:left -16px;}
			.starRate div b {left:0px; bottom:0px; background-position:left top;}
			.starRate a span {position:absolute; left:-300px;}
			.starRate a:hover span {left:90px; width:100%;}
			a:link {color:#0000FF;}    /* unvisited link */
			a:visited {color:#0000FF;} /* visited link */
			a:hover {color:#FF0000;}   /* mouse over link */
		</style>
	';
	$proj_name=$_GET["proj"];
	$proj_own=$_GET["own"];
	mysql_connect('localhost','root','test123') or die ("database error");
	mysql_select_db("TT");
	$sql="SELECT * FROM TTs WHERE user='".$proj_own."'  AND project='".$proj_name."'";
	$res=mysql_query($sql); //get all the details for the given user&project name
	if(!$res){ //Project does not exist for the current user/project_name combination -> does not happen unless modifying the GET parameters in the url
		echo '<h1 align="center"><br><br><br>Project does not exist!<br></h1><p align=center><a href="main.php">Back</a></p>';
	}
	else{
		$projline=mysql_fetch_array($res);
		if(isset($_GET["r"])){ //someone rated the project, so we will update the number of people who rated and the rating value
			$rat=$_GET["r"];
			$ratenum=$projline["ratenum"];
			$rating=$projline["rating"];
			$newrat=$ratenum*$rating+$rat;
			$ratenum=$ratenum+1;
			$rating=floor($newrat/$ratenum*100)/100;
			$sql2="UPDATE TTs SET rating=".$rating.",ratenum=".$ratenum." WHERE user='".$proj_own."'  AND project='".$proj_name."'";
			mysql_query($sql2);
			$res=mysql_query($sql); //get the new values from the table
			$projline=mysql_fetch_array($res);
		}
		$sql2="SELECT * FROM USERS WHERE user='".$proj_own."'"; //used for user image
		$res2=mysql_query($sql2);
		$userline=mysql_fetch_array($res2);
		echo '
			<table>
				<tr> <!-- This line contains the image -->
					<td>
						<img src="'.$userline["img_link"].'" height="100" width="100"></img>
					</td>
				</tr>
				<tr> <!-- This line contains the project owner, the project name and the rating system -->
					<td valign="bottom"> 
						<h1 align="left"><a href="https://github.com/'.$userline["user"].'">'.$userline["user"].'</a> - <a href="https://github.com/'.$proj_own.'/'.$projline["project"].'">'.$projline["project"].'</a></h1>
					</td>
					<td valign="top" width="230px">
					<div class="starRate">
						<div>Currently rated: '.$projline["rating"].' stars<b></b></div>
							<ul>';
							echo '
								<li><a href="project.php?proj='.$proj_name.'&own='.$proj_own.'&r=5"><span>Give it 5 stars</span>'; if(floor($projline["rating"])==5) echo "<b></b>"; echo '</a></li>
								<li><a href="project.php?proj='.$proj_name.'&own='.$proj_own.'&r=4"><span>Give it 4 stars</span>'; if(floor($projline["rating"])==4) echo "<b></b>"; echo '</a></li>
								<li><a href="project.php?proj='.$proj_name.'&own='.$proj_own.'&r=3"><span>Give it 3 stars</span>'; if(floor($projline["rating"])==3) echo "<b></b>"; echo '</a></li>
								<li><a href="project.php?proj='.$proj_name.'&own='.$proj_own.'&r=2"><span>Give it 2 stars</span>'; if(floor($projline["rating"])==2) echo "<b></b>"; echo '</a></li>
								<li><a href="project.php?proj='.$proj_name.'&own='.$proj_own.'&r=1"><span>Give it 1 star</span>';  if(floor($projline["rating"])==1) echo "<b></b>"; echo '</a></li>
							</ul>
					</div>
					</td>
				</tr>
				<tr> <!-- This line contains the project\'s description -->
					<td colspan=3>
						<h3>Project description<br>
						'.$projline["description"].'</h3>
					</td>
				</tr>
				<tr> <!-- This line contains who is the last tester/when was the project tested and a link to test the project -->
					<td>';
						if($projline["tested"]){
							echo '
								<h3><br>Last test by<br>'.$projline["test_by"].' on
							';
							list($date,$time)=explode(" ",$projline["test_date"]);
							list($y,$m,$d)=explode("-",$date);
							echo $d,'-',$m,'-',$y,' ',$time,'</h3>';
						}
						else{
							echo '<h3><br>This project was not tested!<br></h3>';
						}
						echo '
					</td>
					<td valign="middle" align="center">
						<h3><br><a href="testit.php?proj='.$proj_name.'&own='.$proj_own.'">Test it!</a></h3>
					</td>
				</tr>
			</table>
			<br>
			<h1 align="center"><a href="main.php">Back</a></h1>
		';
	}



?>