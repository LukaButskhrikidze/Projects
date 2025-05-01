<?php
// If the all the variables are set when the Submit button is clicked...
if (isset($_POST['constituents_submit'])) {
    require_once("conn.php");
    // constituent_stats is a database view. You need to create it.
    $constituents_query = "SELECT * FROM constituent_stats;";
    try
    {
      $prepared_stmt_constituents = $dbo->prepare($constituents_query);
      //Execute the query and save the result in variable named $result
      $prepared_stmt_constituents->execute();
      $constituents_result = $prepared_stmt_constituents->fetchAll();
    }
    catch (PDOException $ex)
    { // Error in database processing.
      echo $sql . "<br>" . $error->getMessage(); // HTTP 500 - Internal Server Error
    }
}
?>



<?php
// If the all the variables are set when the Submit button is clicked...
if (isset($_POST['dem_gender_submit'])) {
    require_once("conn.php");
    // dem_gender_stats is a database view. You need to create it.
    $dem_gender_query = "SELECT * FROM dem_gender_stats;";
    try
    {
      $prepared_stmt_dem_gender = $dbo->prepare($dem_gender_query);
      //Execute the query and save the result in variable named $result
      $prepared_stmt_dem_gender->execute();
      $dem_gender_result = $prepared_stmt_dem_gender->fetchAll();
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
    <h1> Analytics 1 </h1>
    <form method="post">
      <input type="submit" name="constituents_submit" value="Get Constituents Stats">
      <input type="submit" name="dem_gender_submit" value="Get Dem Gender Stats">
      
      <br>
    </form>

    <!-- get constituents' party stats -->
    <?php
          if (isset($_POST['constituents_submit'])) {
            if ($constituents_result && $prepared_stmt_constituents->rowCount() > 0) { ?>
                 <!-- first show the header RESULT -->
              <h2>Constituents Party Statistics</h2>
              <table>
                <thead>
                  <tr>
                    <th>Party</th>
                    <th>Count</th>
                    <th>Percentage</th>
                  </tr>
                </thead>
                 <!-- Create rest of the the body of the table -->
                <tbody>
                  <?php foreach ($constituents_result as $constituents_row) { ?>
                
                    <tr>
                      <td><?php echo $constituents_row["Party"]; ?></td>
                      <td><?php echo $constituents_row["Count"]; ?></td>
                      <td><?php echo $constituents_row["Percentage"]; ?></td>
                    </tr>
                  <?php } ?>
                  <!-- End table body -->
                </tbody>
                <!-- End table -->
            </table>

            <?php } else { ?>
              <!-- IF query execution resulted in error display the following message-->
              <h3>Sorry, no results found in the database. </h3>
            <?php }
        } ?>
   
        <!-- get dem gender stats -->
    <?php
          if (isset($_POST['dem_gender_submit'])) {

            if ($dem_gender_result && $prepared_stmt_dem_gender->rowCount() > 0) { ?>
                 <!-- first show the header RESULT -->
              <h2>Democratic Party Gender Voting Statistics</h2>
              <table>
                <thead>
                  <tr>
                    <th>Gender</th>
                    <th>Count</th>
                    <th>Percentage</th>
                  </tr>
                </thead>
                 <!-- Create rest of the the body of the table -->
                <tbody>
                  <?php foreach ($dem_gender_result as $dem_gender_row) { ?>
                
                    <tr>
                      <td><?php echo $dem_gender_row["Gender"]; ?></td>
                      <td><?php echo $dem_gender_row["Count"]; ?></td>
                      <td><?php echo $dem_gender_row["Percentage"]; ?></td>
                    </tr>
                  <?php } ?>
                  <!-- End table body -->
                </tbody>
                <!-- End table -->
            </table>

            <?php } else { ?>
              <!-- IF query execution resulted in error display the following message-->
              <h3>Sorry, no results found in the database. </h3>
            <?php }
        } ?>

    
  </body>
</html>


