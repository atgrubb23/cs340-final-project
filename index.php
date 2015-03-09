<?php
include('connect.php');
?>
<!DOCTYPE html>
<html>
	<head>
		<meta charset='UTF-8'/>
		<title>Velo Database</title>
		<style>
		fieldset {
      display: inline-block;
		}
		label {
      display: block;
		}
		input[type='text']{
      margin: 6px;
		}
    input[type='submit'] {
      display: block;
      margin: 2px;
    }
    table {
      margin: 2px;
      padding: 2px;
      border: 2px solid black;
    }
    td {
      margin: 2px;
      padding: 2px;
      border: 1px solid black;	
    }
    th {
      margin: 2px;
      padding: 2px;
      border: 2px solid black;	
    }
    fieldset {
      margin: 10px;
    }
		</style>
	</head>

	<body>
		<?php
			/******************************************************************
			*Check for $_GET parameters and update database tables accordingly*
			******************************************************************/
			//Check and INSERT INTO table Team
			if(isset($_GET['teamInsert']) && $_GET['teamInsert'] === 'true') {
				$teamName = $_GET['teamName'];
				$teamManager = $_GET['teamManager'];
				$numRiders = $_GET['numRiders'];
				if(empty($teamName)) {
					echo "<p>Insertion into table failed. You must provide a valid name for the Team.";
					echo "<p><a href='http://web.engr.oregonstate.edu/~grubba/cs340/final project/index.php'>Reload the page</a>";
				}
				else {
					//Set null values for variables if they werent entered by user
					if(empty($teamManager)) { $teamManager = NULL; }
					if(empty($numRiders)) { $numRiders = NULL; }
					$statement = $myDb->prepare("INSERT INTO Team (name, manager, numRiders) VALUES (?, ?, ?);");
					$statement->bind_param('ssi', $teamName, $teamManager, $numRiders);
					if(!$statement->execute()) {
    					echo "<p>Insertion into table failed: Error Number: " . $statement->errno . " Error Message: " . $statement->error;
    					echo "<p><a href='http://web.engr.oregonstate.edu/~grubba/cs340/final project/index.php'>Reload the page</a>";
					}
					else {
						echo "<p>Insertion into table success!";
					}
					$statement->close();
				}
			}
			//Check and INSERT INTO table Bike
			if(isset($_GET['bikeInsert']) && $_GET['bikeInsert'] === 'true') {
				$bikeMake = $_GET['bikeMake'];
				$bikeModel = $_GET['bikeModel'];
				if(empty($bikeMake)) {
					echo "<p>Insertion into table failed. You must provide a valid make for the Bike.";
					echo "<p><a href='http://web.engr.oregonstate.edu/~grubba/cs340/final project/index.php'>Reload the page</a>";
				}
				else {
					$statement = $myDb->prepare("INSERT INTO Bike (make, model) VALUES (?, ?);");
					$statement->bind_param('ss', $bikeMake, $bikeModel);
					if(!$statement->execute()) {
    					echo "<p>Insertion into table failed: Error Number: " . $statement->errno . " Error Message: " . $statement->error;
    					echo "<p><a href='http://web.engr.oregonstate.edu/~grubba/cs340/final project/index.php'>Reload the page</a>";
					}
					else {
						echo "<p>Insertion into table success!";
					}
					$statement->close();
				}
			}
			//Check and INSERT INTO table Cyclist
			if(isset($_GET['cyclistInsert']) && $_GET['cyclistInsert'] === 'true') {
				$cyclistFirstName = $_GET['cyclistFirstName'];
				$cyclistLastName = $_GET['cyclistLastName'];
				$cyclistCategory = $_GET['cyclistCategory'];
				$cyclistTeamName = $_GET['cyclistTeamName'];
				$cyclistBikeMake = $_GET['cyclistBikeMake'];
				$cyclistBikeModel = $_GET['cyclistBikeModel'];
				$cyclistTID;
				$cyclistBID;
				if(empty($cyclistFirstName) || empty($cyclistLastName) || empty($cyclistTeamName) || empty($cyclistBikeMake)) {
					echo "<p>Insertion into table failed. You must provide a valid first name, last name, team name and bike make for the Cyclist.";
					echo "<p><a href='http://web.engr.oregonstate.edu/~grubba/cs340/final project/index.php'>Reload the page</a>";
				}
				else {
					//Run SELECT queries to get tid and bid for the cyclist
					$statement = $myDb->prepare("SELECT tid FROM Team WHERE name = ?;");
					$statement->bind_param('s', $cyclistTeamName);
					if(!$statement->execute()) {
						echo "<p>Failed to retrieve tid for the cyclist.";
						echo "<p><a href='http://web.engr.oregonstate.edu/~grubba/cs340/final project/index.php'>Reload the page</a>";
					}
					$statement->bind_result($thisID);
					$statement->fetch();
					$cyclistTID = $thisID;
					$statement->close();
					if(empty($cyclistBikeModel)) {
						$statement = $myDb->prepare("SELECT bid FROM Bike WHERE make = ?;");
						$statement->bind_param('s', $cyclistBikeMake);
					}
					else {
						$statement = $myDb->prepare("SELECT bid FROM Bike WHERE make = ? AND model = ?;");
						$statement->bind_param('ss', $cyclistBikeMake, $cyclistBikeModel);
					}
					if(!$statement->execute()) {
						echo "<p>Failed to retrieve bid for the cyclist.";
						echo "<p><a href='http://web.engr.oregonstate.edu/~grubba/cs340/final project/index.php'>Reload the page</a>";
					}
					$statement->bind_result($thisID);
					$statement->fetch();
					$cyclistBID = $thisID;
					$statement->close();
					//Set null values for variables if they werent entered by user
					if(empty($cyclistCategory)) { $cyclistCategory = NULL; }
					//Run INSERT INTO query Cyclist table
					$statement = $myDb->prepare("INSERT INTO Cyclist (firstName, lastName, category, tid, bid) VALUES (?, ?, ?, ?, ?);");
					$statement->bind_param('ssiii', $cyclistFirstName, $cyclistLastName, $cyclistCategory, $cyclistTID, $cyclistBID);
					if(!$statement->execute()) {
    					echo "<p>Insertion into table failed: Error Number: " . $statement->errno . " Error Message: " . $statement->error;
    					echo "<p><a href='http://web.engr.oregonstate.edu/~grubba/cs340/final project/index.php'>Reload the page</a>";
					}
					else {
						echo "<p>Insertion into table success!";
					}
					$statement->close();
				}
			}
			//Check and INSERT INTO table Event
			if(isset($_GET['eventInsert']) && $_GET['eventInsert'] === 'true') {
				$eventName = $_GET['eventName'];
				$eventNumStages = $_GET['eventNumStages'];
				$eventCountryLoc = $_GET['eventCountryLoc'];
				$eventCityLoc = $_GET['eventCityLoc'];
				$eventCategory = $_GET['eventCategory'];
				if(empty($eventName) || empty($eventNumStages)) {
					echo "<p>Insertion into table failed. You must provide a valid name and number of stages.";
					echo "<p><a href='http://web.engr.oregonstate.edu/~grubba/cs340/final project/index.php'>Reload the page</a>";
				}
				else {
					//Set null values for variables if they werent entered by user
					if(empty($eventCountryLoc)) { $eventCountryLoc = NULL; }
					if(empty($eventCityLoc)) { $eventCityLoc = NULL; }
					if(empty($eventCategory)) { $eventCategory = NULL; }
					$statement = $myDb->prepare("INSERT INTO Event (name, countryLoc, cityLoc, numStages, category) VALUES (?, ?, ?, ?, ?);");
					$statement->bind_param('sssii', $eventName, $eventCountryLoc, $eventCityLoc, $eventNumStages, $eventCategory);
					if(!$statement->execute()) {
    					echo "<p>Insertion into table failed: Error Number: " . $statement->errno . " Error Message: " . $statement->error;
    					echo "<p><a href='http://web.engr.oregonstate.edu/~grubba/cs340/final project/index.php'>Reload the page</a>";
					}
					else {
						echo "<p>Insertion into table success!";
					}
					$statement->close();
				}
			}
			//Check and INSERT INTO table Stage
			if(isset($_GET['stageInsert']) && $_GET['stageInsert'] === 'true') {
				$stageName = $_GET['stageName'];
				$stageDistance = $_GET['stageDistance'];
				$stageType = $_GET['stageType'];
				$stageMaxElevation = $_GET['stageMaxElevation'];
				$stageAvgGrade = $_GET['stageAvgGrade'];
				$stageEventName = $_GET['stageEventName'];
				$stageEID;
				if(empty($stageName) || empty($stageDistance)) {
					echo "<p>Insertion into table failed. You must provide a valid name and distance.";
					echo "<p><a href='http://web.engr.oregonstate.edu/~grubba/cs340/final project/index.php'>Reload the page</a>";
				}
				else {
					//Run SELECT query to get eid 
					$statement = $myDb->prepare("SELECT eid FROM Event WHERE name = ?;");
					$statement->bind_param('s', $stageEventName);
					if(!$statement->execute()) {
						echo "<p>Failed to retrieve eid for the stage.";
						echo "<p><a href='http://web.engr.oregonstate.edu/~grubba/cs340/final project/index.php'>Reload the page</a>";
					}
					$statement->bind_result($thisID);
					$statement->fetch();
					$stageEID = $thisID;
					$statement->close();
					//Set null values for variables if they werent entered by user
					if(empty($stageEID)) { $stageEID = NULL; }
					if(empty($stageType)) { $stageType = NULL; }
					if(empty($stageMaxElevation)) { $stageMaxElevation = NULL; }
					if(empty($stageAvgGrade)) { $stageAvgGrade = NULL; }
					$statement = $myDb->prepare("INSERT INTO Stage (name, eid, stageType, distance, maxElevation, avgGrade) VALUES (?, ?, ?, ?, ?, ?);");
					$statement->bind_param('sisiii', $stageName, $stageEID, $stageType, $stageDistance, $stageMaxElevation, $stageAvgGrade);
					if(!$statement->execute()) {
    					echo "<p>Insertion into table failed: Error Number: " . $statement->errno . " Error Message: " . $statement->error;
    					echo "<p><a href='http://web.engr.oregonstate.edu/~grubba/cs340/final project/index.php'>Reload the page</a>";
					}
					else {
						echo "<p>Insertion into table success!";
					}
					$statement->close();
				}
			}

			?>
		<div>
			<form action='http://web.engr.oregonstate.edu/~grubba/cs340/final project/index.php' method='GET'>
			<fieldset>
			<legend>Tables</legend>
				<select name='tableSelection' onchange='this.form.submit()'>
				<option value=''>Select a Table...</option>
				<option value='Team' <?php if(isset($_GET['tableSelection']) && $_GET['tableSelection'] === 'Team') { echo "selected='selected'"; } ?>>Team</option>
				<option value='Bike' <?php if(isset($_GET['tableSelection']) && $_GET['tableSelection'] === 'Bike') { echo "selected='selected'"; } ?>>Bike</option>
				<option value='Cyclist' <?php if(isset($_GET['tableSelection']) && $_GET['tableSelection'] === 'Cyclist') { echo "selected='selected'"; } ?>>Cyclist</option>
				<option value='Event' <?php if(isset($_GET['tableSelection']) && $_GET['tableSelection'] === 'Event') { echo "selected='selected'"; } ?>>Event</option>
				<option value='Stage' <?php if(isset($_GET['tableSelection']) && $_GET['tableSelection'] === 'Stage') { echo "selected='selected'"; } ?>>Stage</option>
				</select>
			</fieldset>
			</form>
		</div>
		<?php
			/*********************************************************
			*Check selected table and change webpage view accordingly*
			*********************************************************/
			//Check and change for Team
			if(isset($_GET['tableSelection']) && $_GET['tableSelection'] === 'Team') {
				//Display INSERT INTO queries and accept user input
				echo "<form action='http://web.engr.oregonstate.edu/~grubba/cs340/final project/index.php' method='GET'>
					<fieldset>
					<legend>Add Entry to Team</legend>
						<input type='hidden' name='tableSelection' value='Team'></input>				
						<input type='text' name='teamName' placeholder='Team name (required)'></input>
						<input type='text' name='teamManager' placeholder='Team manager'></input>
						<input type='number' min='0' name='numRiders' placeholder='Number of riders'></input>
						<button type='submit' name='teamInsert' value='true'>Add!</input>
					</fieldset>				
					</form>";

				//Retrieve data for team SELECT queries drop down menu
				$statement = $myDb->prepare("SELECT name FROM Team;");
				if(!$statement->execute()) {
  					echo "<p>Query of Team names failed. Error Number: " . $statement->errno . ". Error Message: " . $statement->error;
				}
				$categoryArray = array();
				$categoryArray[] = "Select a team...";
				$statement->bind_result($thisCategory);
				while($statement->fetch()) {
  					$categoryArray[] = $thisCategory;
				}
				$statement->close();
				//Display SELECT dropdown for Teams
				echo "<form action='http://web.engr.oregonstate.edu/~grubba/cs340/final project/index.php' method='GET'>
					<input type='hidden' name='tableSelection' value='Team'></option>
					<fieldset>
					<legend>Select Team to Display</legend>
						<select name='teamSelection' onchange='this.form.submit()'>";
				$categoryArray = array_unique($categoryArray);
				$count = -1;
				foreach($categoryArray as $value) {
					$count++;
  					echo "<option value=" . $count . ">$value</option>";
  				}
  				$count = -1;
				echo "</select>
					</fieldset>				
					</form>";
				//If team has been selected run SELECT query and display result in a table	
				if(isset($_GET['teamSelection'])) {
					$selectedTeam = $categoryArray[$_GET['teamSelection']];
					$statement = $myDb->prepare("SELECT name, manager, numRiders FROM Team WHERE name = ?");
					$statement->bind_param('s', $selectedTeam);
					if(!$statement->execute()) {
						echo "<p>Query of Team " . $selectedTeam . " failed. Error Number: " . $statement->errno . ". Error Message: " . $statement->error;
					}
					$statement->bind_result($name, $manager, $numRiders);
					$statement->fetch();
					$statement->close();
					echo "<table>
							<th>Team Name</th> <th>Team Manager</th> <th>Number of Riders</th>
							<tr><td>" . $name . "</td> <td>" . $manager . "</td> <td>" . $numRiders . "</td></tr>
						</table>
					";
				}
			}
			//Check and change for Bike
			if(isset($_GET['tableSelection']) && $_GET['tableSelection'] === 'Bike') {
				echo "<form >
					<fieldset>
					<legend>Add Entry to Bike</legend>
						<input type='hidden' name='tableSelection' value='Bike'></input>
						<input type='text' name='bikeMake' placeholder='Bike make (required)'></input>
						<input type='text' name='bikeModel' placeholder='Bike model'></input>
						<button type='submit' name='bikeInsert' value='true'>Add</input>
					</fieldset>				
					</form>";
				//Run SELECT query and display results of all Bike entries in a table
				$statement = $myDb->prepare("SELECT make, model FROM Bike;");
				if(!$statement->execute()) {
					echo "<p>Query of Bike failed. Error Number: " . $statement->errno . ". Error Message: " . $statement->error;
				}
				$statement->bind_result($make, $model);
				echo "<table>
						<th>Bike Make</th> <th>Bike Model</th>";
				while($statement->fetch()) {
					echo "<tr><td>" . $make . "</td>" . "<td>" . $model . "</td></tr>";
				}
				echo "</table>";
				$statement->close();
			}
			//Check and change for Cyclist
			if(isset($_GET['tableSelection']) && $_GET['tableSelection'] === 'Cyclist') {
				echo "<form >
					<fieldset>
					<legend>Add Entry to Cyclist</legend>
						<input type='hidden' name='tableSelection' value='Cyclist'></input>
						<input type='text' name='cyclistFirstName' placeholder='First name (required)'></input>
						<input type='text' name='cyclistLastName' placeholder='Last name (required)'></input>
						<input type='text' name='cyclistTeamName' placeholder='Team name (required)'></input>
						<input type='text' name='cyclistBikeMake' placeholder='Bike make (required)'></input>
						<input type='text' name='cyclistBikeModel' placeholder='Bike model'></input>
						<input type='number' min='1' name='cyclistCategory' placeholder='Category of cyclist'></input>
						<button type='submit' name='cyclistInsert' value='true'>Add</input>
					</fieldset>				
					</form>";
				//Run SELECT query and display results of all Cyclist entries in a table
				$statement = $myDb->prepare("SELECT c.lastName, c.firstName, t.name, b.make, b.model FROM Cyclist c INNER JOIN Team t ON c.tid = t.tid INNER JOIN Bike b ON c.bid = b.bid ORDER BY t.name, c.lastName ASC;");
				if(!$statement->execute()) {
					echo "<p>Query of Cyclist failed. Error Number: " . $statement->errno . ". Error Message: " . $statement->error;
				}
				$statement->bind_result($lastName, $firstName, $teamName, $bikeMake, $bikeModel);
				echo "<table>
						<th>Last Name</th> <th>First Name</th> <th>Team Name</th> <th>Bike Make</th> <th>Bike Model</th>";
				while($statement->fetch()) {
					echo "<tr><td>" . $lastName . "</td>" . "<td>" . $firstName . "</td>" . "<td>" . $teamName . "</td>". "<td>" . $bikeMake . "</td>" . "<td>" . $bikeModel . "</td></tr>";
				}
				echo "</table>";
				$statement->close();
			}
			if(isset($_GET['tableSelection']) && $_GET['tableSelection'] === 'Event') {
				echo "<form >
					<fieldset>
					<legend>Add Entry to Event</legend>
						<input type='hidden' name='tableSelection' value='Event'></input>
						<input type='text' name='eventName' placeholder='Event name (required)'></input>
						<input type='text' name='eventCountryLoc' placeholder='Country location'></input>
						<input type='text' name='eventCityLoc' placeholder='City location'></input>
						<input type='number' min='0' name='eventNumStages' placeholder='Number stages (required)'></input>
						<input type='number' min='1' name='eventCategory' placeholder='Category of event'></input>
						<button type='submit' name='eventInsert' value='true'>Add</input>
					</fieldset>				
					</form>";
				//Run SELECT query and display results of all Event entries in a table
				$statement = $myDb->prepare("SELECT name, numStages, countryLoc, cityLoc, category FROM Event");
				if(!$statement->execute()) {
					echo "<p>Query of Event failed. Error Number: " . $statement->errno . ". Error Message: " . $statement->error;
				}
				$statement->bind_result($name, $numStages, $countryLoc, $cityLoc, $category);
				echo "<table>
						<th>Event Name</th> <th>Number of Stages</th> <th>Country Location</th> <th>City Location</th> <th>Category of Event</th>";
				while($statement->fetch()) {
					echo "<tr><td>" . $name . "</td>" . "<td>" . $numStages . "</td>" . "<td>" . $countryLoc . "</td>". "<td>" . $cityLoc . "</td>" . "<td>" . $category . "</td></tr>";
				}
				echo "</table>";
				$statement->close();
			}
			if(isset($_GET['tableSelection']) && $_GET['tableSelection'] === 'Stage') {
				echo "<form >
					<fieldset>
					<legend>Add Entry to Stage</legend>
						<input type='hidden' name='tableSelection' value='Stage'></input>
						<input type='text' name='stageName' placeholder='Stage name (required)'></input>
						<input type='text' name='stageEventName' placeholder='Event name'></input>
						<input type='text' name='stageType' placeholder='Type of stage'></input>
						<input type='number' min='0' name='stageDistance' placeholder='Distance (required)'></input>
						<input type='number' min='0' name='stageMaxElevation' placeholder='Max elevation'></input>
						<input type='number' name='stageAvgGrade' placeholder='Average grade'></input>
						<button type='submit' name='stageInsert' value='true'>Add</input>
					</fieldset>				
					</form>";
				//Run SELECT query and display results of all Stage entries in a table
				$statement = $myDb->prepare("SELECT name, distance, stageType, maxElevation, avgGrade FROM Stage");
				if(!$statement->execute()) {
					echo "<p>Query of Stage failed. Error Number: " . $statement->errno . ". Error Message: " . $statement->error;
				}
				$statement->bind_result($name, $distance, $stageType, $maxElevation, $avgGrade);
				echo "<table>
						<th>Stage Name</th> <th>Distance of Stage</th> <th>Type of Stage</th> <th>Maximum Elevation</th> <th>Average Grade</th>";
				while($statement->fetch()) {
					echo "<tr><td>" . $name . "</td>" . "<td>" . $distance . "</td>" . "<td>" . $stageType . "</td>". "<td>" . $maxElevation . "</td>" . "<td>" . $avgGrade . "</td></tr>";
				}
				echo "</table>";
				$statement->close();
			}
		?>
	</body>
</html>