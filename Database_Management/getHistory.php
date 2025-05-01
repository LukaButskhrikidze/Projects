<?php ini_set('display_errors', 0); ?>
<?php
// If the all the variables are set when the Submit button is clicked...
if (isset($_POST['history_submit'])){

    // Refer to conn.php file and open a connection.
    require_once("conn.php");

    // Will get the value typed in the form text field and save into variable
    $var_reg_num = $_POST['reg_num'];

    // The following procedure should accept voting registration number and then return the election ID, Voting MEthod and voted party. Note that :num is a place holder. Save the stored procedure query into variable called $history_query. Note that :num is a place holder
    $history_query = "CALL get_voting_record(:num)";


try
    {
      // Create a prepared statement. Prepared statements are a way to eliminate SQL INJECTION.
      $prepared_stmt_history = $dbo->prepare($history_query);
      
      //bind the value saved in the variable $var_reg_num to the place holder :num in the query. Use PDO::PARAM_STR to sanitize user string. Sanitization of inputs help in db security.
      $prepared_stmt_history->bindValue(':num', $var_reg_num, PDO::PARAM_STR);

      //Execute the prepared statement.
      $prepared_stmt_history->execute();

      // Fetch all the values based on query and save that to variable $history_result
      $history_result = $prepared_stmt_history->fetchAll();
    }

    catch (PDOException $ex)
    { // Error in database processing.
      echo $sql . "<br>" . $error->getMessage(); // HTTP 500 - Internal Server Error
    }
}


?>
<!-- get voter name result -->
<html>
<!-- Any thing inside the HEAD tags are not visible on page.-->
  <head>
    <!-- THe following is the stylesheet file. The CSS file decides look and feel -->
    <link rel="stylesheet" type="text/css" href="project.css" />
  </head> 
<!-- Everything inside the BODY tags are visible on page.-->
  <body>
    <div id="navbar">
      <ul>
        <!-- Links in navigation -->
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
    
    <h1> Search Voting History by Voter Registration Number</h1>
    <!-- This is the start of the form. This form has one text field and one button. See the project.css file to note how form is stylized.-->
    <form method="post">

      <label for="id_reg_num">Please type the Voter Registration Number</label>
      <input type="text" name="reg_num" id = "id_reg_num">
      <br><br>
      
      <!-- get history button  -->
      <input type="submit" name="history_submit" value="Get Voting History">

    </form>
 
<!-- get voter history form. This has mix of PHP and HTML. NOt very elegant. But gets the work done. -->
    <?php
          if (isset($_POST['history_submit'])) {
            
            // If the query executed (result is true) and the row count returned from the query is greater than 0 then...
            if ($history_result && $prepared_stmt_history->rowCount() > 0) { ?>

              <!-- first show the header RESULT -->
              <h2>Voting history of voter registration number <?php echo $_POST['reg_num']; ?></h2>
              
              <!-- THen create a table like structure. See the project.css how table is stylized. -->
              
              <table>
                <!-- Create the first row of table as table head (thead). -->
                <thead>
                   
                   <!-- The top row is table head with three columns named -->
                  <tr>
                    <th>Election ID</th>
                    <th>Voting Method</th>
                    <th>Party</th>
                  </tr>
                </thead>
                 
                 <!-- Create rest of the the body of the table -->
                <tbody>

                   <!-- For each row saved in the $result variable ... -->
                   <?php foreach ($history_result as $history_row) { ?>
                    
                    <!-- IN each row (tr), write one value in each cell of the table (td) -->
                    <tr>
                      <td><?php echo $history_row["Election ID"]; ?></td>
                      <td><?php echo $history_row["Voting Method"]; ?></td>
                      <td><?php echo $history_row["Party"]; ?></td>
                    </tr>
                  <?php } ?>

                  <!-- End table body -->
                </tbody>
                <!-- End table -->
            </table>

            <?php } else { ?>

              <!-- IF query execution resulted in error display the following message-->
              
              <h3>Sorry, no results found for voter <?php echo $_POST['reg_num']; ?>. </h3>
            <?php }
        } ?>

  </body>
</html>






