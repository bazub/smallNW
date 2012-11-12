<?php
	$proj_name=$_GET["proj"];
	$proj_own=$_GET["own"];
	if(isset($_POST["user"])){ //user has given his name as input
		mysql_connect('annomewhen.db.9892216.hostedresource.com','annomewhen','Numaidebine1');
		mysql_select_db("annomewhen");
		$date=date("Y-m-j G:i:s");
		$sql="UPDATE TTs SET test_by='".$_POST["user"]."',test_date='".$date."',tested=1 WHERE user='".$proj_own."'  AND project='".$proj_name."'";
		mysql_query($sql); //update Last tested by fields
		header("Location: https://github.com/".$proj_own."/".$proj_name.""); //redirect to github page
	}
	else
	//form for inputting tester's name
	echo '
		<h1>What\'s your name?</h1>
		<form method= "post" action="testit.php?proj='.$proj_name.'&own='.$proj_own.'">
			<table>
				<tr>
					<td><label class="user">Name:</label></td>
					<td><input type="text" name= "user"></td>
				</tr>
			</table>
			<input type="submit" value= "Submit">
		</form>
	';
?>