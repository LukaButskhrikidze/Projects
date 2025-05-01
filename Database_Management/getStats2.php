


<?php
// If the all the variables are set when the Submit button is clicked...
if (isset($_POST['custom1_submit'])) {
    require_once("conn.php");
    $custom1_query = "SELECT * FROM voting_method_by_party;";
    try
    {
      $prepared_stmt_custom1 = $dbo->prepare($custom1_query);
      //Execute the query and save the result in variable named $result
      $prepared_stmt_custom1->execute();
      $custom1_result = $prepared_stmt_custom1->fetchAll();
    }
    catch (PDOException $ex)
    { // Error in database processing.
      echo $sql . "<br>" . $error->getMessage(); // HTTP 500 - Internal Server Error
    }
}
?>



<?php
// If the all the variables are set when the Submit button is clicked...
if (isset($_POST['custom2_submit'])) {
    require_once("conn.php");
    $custom2_query = "SELECT * FROM party_preference_by_zip;";
    try
    {
      $prepared_stmt_custom2 = $dbo->prepare($custom2_query);
      //Execute the query and save the result in variable named $result
      $prepared_stmt_custom2->execute();
      $custom2_result = $prepared_stmt_custom2->fetchAll();
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
    <h1> Analytics 2 </h1>
    <form method="post">
      
      <input type="submit" name="custom1_submit" value="Custom1 Stats">
      <input type="submit" name="custom2_submit" value="Custom2 Stats">
    </form>

    <!-- get constituents' party stats -->
    

    <!-- get dem regional stats -->
    

     <!-- get rep regional stats-->
    <?php
          if (isset($_POST['custom1_submit'])) {

            if ($custom1_result && $prepared_stmt_custom1->rowCount() > 0) { ?>
                 <!-- first show the header RESULT -->
              <h2>Voting Method Preference by Party</h2>
              <table>
                <thead>
                  <tr>
                    <th>Party</th>
                    <th>voting_method</th>
                    <th>vote_count</th>
                    <th>percent_within_party</th>
                  </tr>
                </thead>
                 <!-- Create rest of the the body of the table -->
                <tbody>
                  <?php foreach ($custom1_result as $custom1_row) { ?>
                
                    <tr>
                      <td><?php echo $custom1_row["Party"]; ?></td>
                      <td><?php echo $custom1_row["voting_method"]; ?></td>
                      <td><?php echo $custom1_row["vote_count"]; ?></td>
                      <td><?php echo $custom1_row["percent_within_party"]; ?></td>
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
    

     <!-- get rep gender stats-->
    <?php
          if (isset($_POST['custom2_submit'])) {

            if ($custom2_result && $prepared_stmt_custom2->rowCount() > 0) { ?>
                 <!-- first show the header RESULT -->
              <h2>Party Preference by ZIP Code</h2>
              <table>
                <thead>
                  <tr>
                    <th>Zip Code</th>
                    <th>Party</th>
                    <th>Vote Count</th>
                    <th>Percentage within Zip Code</th>
                  </tr>
                </thead>
                 <!-- Create rest of the the body of the table -->
                <tbody>
                  <?php foreach ($custom2_result as $custom2_row) { ?>
                
                    <tr>
                      <td><?php echo $custom2_row["zip_code"]; ?></td>
                      <td><?php echo $custom2_row["party"]; ?></td>
                      <td><?php echo $custom2_row["vote_count"]; ?></td>
                      <td><?php echo $custom2_row["percent_within_zip"]; ?></td>
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
 <!-- get switched voter number-->
      
    
  </body>
</html>


