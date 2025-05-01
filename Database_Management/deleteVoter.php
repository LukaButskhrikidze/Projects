<?php
// If the all the variables are set when the Submit button is clicked...
if (isset($_POST['field_submit'])) {
    require_once("conn.php");
    $reg_num = $_POST['reg_num'];
    // Stored Procedure to delete a voter. This should delete the voter data and
    // their voting history.
    $query = "CALL delete_voter (:num);";
    
    try
    {
      $prepared_stmt = $dbo->prepare($query);
      //bind the value saved in the variable $reg_num to the place holder :num after //verifying (using PDO::PARAM_STR) that the user has typed a valid string.
      $prepared_stmt->bindValue(':num', $reg_num, PDO::PARAM_STR);
      //Execute the query and save the result in variable named $result
      $result = $prepared_stmt->execute();

    }
    catch (PDOException $ex)
    { // Error in database processing.
      echo $sql . "<br>" . $error->getMessage(); // HTTP 500 - Internal Server Error
    }
}

?>

<html>
  <head>
    <link rel="stylesheet" type="text/css" href="project.css" />
  </head> 

  <body>
    <div id="navbar">
      <ul>
         <li><a href="index.html">Home</a></li>
        <li><a href="getVoter.php">Search Voter</a></li>
        <li><a href="getHistory.php">Voting History</a></li>
        <li><a href="insertVote.php">Insert Record</a></li>
        <li><a href="deleteVoter.php">Remove Voter</a></li>
        <li><a href="getStats.php">View 1</a></li>
        <li><a href="getStats2.php">View 2</a></li>
        <li><a href="audit.php">Audit</a></li>
      </ul>
    </div>
    <h1> Delete an Existing Voter </h1>
    <form method="post">

      <label for="id_title">Voter Registration Number</label>
      <input type="text" name="reg_num" id="id_title">
      <input type="submit" name="field_submit" value="Delete Voter">
    </form>

    <?php
      if (isset($_POST['field_submit'])) {
        if ($result) { 
    ?>
          Voter was deleted successfully.
    <?php 
        } else { 
    ?>
          <h3> Sorry, there was an error. Voter data was not deleted. </h3>
    <?php 
        }
      } 
    ?>

    
  </body>
</html>


