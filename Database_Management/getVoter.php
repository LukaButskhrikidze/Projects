<?php ini_set('display_errors', 0); ?>
<?php

// If the all the variables are set when the Submit button is clicked...
if (isset($_POST['name_submit'])){
    
    // Refer to conn.php file and open a connection.
    require_once("conn.php");
    
    // Will get the value typed in the form text field and save into variable
    $var_reg_num = $_POST['reg_num'];
    
    // Save the query into variable called $name_query. Note that :num is a place holder
    $name_query = "SELECT full_name_mail FROM voter_attributes WHERE voter_reg_num = :num;";

try
    {
      // Create a prepared statement. Prepared statements are a way to eliminate SQL INJECTION.
      $prepared_stmt_name = $dbo->prepare($name_query);
      
      // bind the value saved in the variable $var_reg_num to the place holder :num
      // Use PDO::PARAM_STR to sanitize user string.
      $prepared_stmt_name->bindValue(':num', $var_reg_num, PDO::PARAM_STR);
      
      //execute the prepared statement
      $prepared_stmt_name->execute();

      // Fetch all the values based on query and save that to variable $name_result
      $name_result = $prepared_stmt_name->fetchAll();
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
    
    <h1> Search Voter by Voter Registration Number</h1>
    <!-- This is the start of the form. This form has one text field and one button.
      See the project.css file to note how form is stylized.-->
    <form method="post">

      <label for="id_reg_num">Please type the Voter Registration Number</label>
      <input type="text" name="reg_num" id = "id_reg_num">
      <br><br>
      <!-- get name button  -->
      <input type="submit" name="name_submit" value="Get Name">

    </form>
    <?php
      if (isset($_POST['name_submit'])) {
        // If the query executed (name_result is true) and the row count returned from the query is greater than 0 then...
        if ($name_result && $prepared_stmt_name->rowCount() > 0) { ?>
              <!-- first show the voter reg num dynamically -->
              <h2>Full name of voter registration number <?php echo $_POST['reg_num']; ?></h2>

              <!-- run a for loop to go throup each row and output the value from each column. IN this case, we have only full_name_mail -->
              <?php foreach ($name_result as $name_row) { ?>
               <?php echo $name_row["full_name_mail"]; ?>
               <?php } ?>

        <?php } else { ?>
          <!-- IF query execution resulted in error display the following message-->
          <h3>Sorry, no results found for voter <?php echo $_POST['reg_num']; ?>. </h3>
        <?php }
    } ?>


  </body>
</html>






