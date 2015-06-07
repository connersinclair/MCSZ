<?php

require 'required/dbcon.php';

if($con == false){
    $error = "No MySQL connection available";
}
?>

<?php require 'nav/nav-servers.php'; ?>

<!DOCTYPE html>
<head>
    <title>Contact - MCSZ</title>
</head>

<body>
    <div class="container">
        <br>
        <h1>Contact</h1>
        <form>
          <div class="form-group">
            <label for="exampleInputName1"><h6>Full Name</h6></label>
            <input type="name" class="form-control" id="exampleInputName1" placeholder="Enter name">
          </div>
          <div class="form-group">
            <label for="exampleInputEmail1"><h6>Email</h6></label>
            <input type="Email" class="form-control" id="exampleInputEmail1" placeholder="Enter email">
          </div>
          <div class="form-group">
            <label for="exampleInputSubject1"><h6>Subject</h6></label>
            <input type="Subject" class="form-control" id="exampleInputSubject1" placeholder="Enter Info">
          </div>
          <button type="submit" class="btn btn-primary">Submit</button>
        </form>
        
        <hr>
        
        <p>Once your email has been submitted, the workers at mcsz will respond. Be aware this can take up to 24 hours.<p><br>
        
    </div>
</body>

<?php require 'footer/footer.php'; ?>