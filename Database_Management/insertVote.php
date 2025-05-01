<?php

if (isset($_POST['f_submit'])) {

    require_once("conn.php");

    $var_reg_num = $_POST['f_reg_num'];
    $var_e_id = $_POST['f_eid'];
    $var_vote_method = $_POST['f_vote_method'];
    $var_vote_party = $_POST['f_vote_party'];


    $query = "CALL insert_record(:reg_num, :e_id, :vote_method, :vote_party, @msg);";

    try
    {
      $prepared_stmt = $dbo->prepare($query);

      $prepared_stmt->bindValue(':reg_num', $var_reg_num, PDO::PARAM_STR);
      $prepared_stmt->bindValue(':e_id',  $var_e_id, PDO::PARAM_STR);
      $prepared_stmt->bindValue(':vote_method',  $var_vote_method, PDO::PARAM_STR);
      $prepared_stmt->bindValue(':vote_party',  $var_vote_party , PDO::PARAM_STR);

      $prepared_stmt->execute();

      $result = $prepared_stmt->fetchAll();

    }
    catch (PDOException $ex)
    { // Error in database processing.
      echo $sql . "<br>" . $error->getMessage(); // HTTP 500 - Internal Server Error
    }
}

?>

<html>
  <head>
    <!-- THe following is the stylesheet file.  -->
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

<h1> Insert/Update Voting Record </h1>

    <form method="post">
    	<label for="id_reg_num">Voter Registration Number</label>
    	<input type="text" name="f_reg_num" id="id_reg_num"> 

    	<label for="id_e_id">Election Number</label>
      <select id="id_e_id" name="f_eid">
          <option value="E1">E1</option>
          <option value="E2">E2</option>
          <option value="E3">E3</option>
      </select>
      
    	<label for="id_vote_method">Voting Method</label>
    	<input type="text" name="f_vote_method" id="id_vote_method">

    	<label for="id_year">Voted to Party</label>
    	<input type="text" name="f_vote_party" id="id_vote_party">
    	
    	<input type="submit" name="f_submit" value="Submit">
    </form>


     <?php
      if (isset($_POST['f_submit'])) { 
        foreach ($result as $row){
          echo $row["msg"];
        }
    } ?> 
</html>
