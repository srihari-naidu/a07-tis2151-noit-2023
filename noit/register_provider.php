<?php
    include("inc/connection.inc.php");
    include("inc/functions.inc.php");


    if (isset($_POST['submit']))
    {
        $role = "Provider";
        $name = $_POST['name'];
        $company = $_POST['company'];
        $email =$_POST['email'];
        $pass = $_POST['password'];
        $cpass = $_POST['cpassword'];

        $thumb = $_FILES['image']['name'];
        $ext = pathinfo($thumb, PATHINFO_EXTENSION);
        $thumbnail = uniqid().'.'.$ext;
        $thumb_size = $_FILES['image']['size'];
        $thumb_tmp_name = $_FILES['image']['tmp_name'];
        $thumb_folder = 'uploads/'.$thumbnail;

        $selUser = $con->prepare(
            "SELECT * 
             FROM User 
             WHERE Email = ?"
        );
        $selUser->execute([$email]);

        if ($selUser->rowCount() > 0) 
        {
            $_SESSION['oops_email_exists'] = "The email {$email} is already in use! Please try another one.";
        }
        else 
        {
            if ($pass != $cpass) 
            {
                $_SESSION['error_password_unmatched'] = "Passwords do not match.";
            }
            else 
            {
                $insUser = $con->prepare(
                    "INSERT INTO User (Role, Name, Email, Password, ProfilePicture)
                     VALUES (?, ?, ?, ?, ?)"
                );
                $insUser->execute([$role, $name, $email, $pass, $thumbnail]);
                move_uploaded_file($thumb_tmp_name, $thumb_folder);

                $userId = $con->lastInsertId();
                $insStudent = $con->prepare(
                    "INSERT INTO Provider (ProviderId, Company)
                     VALUES (?, ?)"
                );
                $insStudent->execute([$userId, $company]);
                
                $verUser = $con->prepare(
                    "SELECT * 
                     FROM User
                     WHERE Email = ?
                     AND Password = ?"
                );
                $verUser->execute([$email, $pass]);
                if ($verUser->rowCount() > 0) 
                {
                    $_SESSION['success_account_created'] = "Your account has been created.";
                }
            }
        }
    }
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="css/style.css">
    <title>NOIT - Register</title>
</head>
<body>
    <?php include("inc/header.inc.php"); ?>
    <div class="container">
        <?php
            if (isset($_SESSION['oops_email_exists']))
            {
                $message = $_SESSION['oops_email_exists'];
                unset($_SESSION['oops_email_exists']);
                oops($message, "Back", "javascript:self.history.back()");
            }
            else if (isset($_SESSION['error_password_unmatched']))
            {
                $message = $_SESSION['error_password_unmatched'];
                unset($_SESSION['error_password_unmatched']);
                error($message, "Back", "javascript:self.history.back()");
            }
            else if (isset($_SESSION['success_account_created']))
            {
                $message = $_SESSION['success_account_created'];
                unset($_SESSION['success_account_created']);
                success($message, "Login", "login.php");
            }
            else
            {
        ?>
                <div class="card card-header">
                    <h1>Register Provider</h1>
                </div>
                <div class="card card-body">
                    <form action="" method="POST" enctype="multipart/form-data">
                        <div class="field input">
                            <label for="name">Name</label>
                            <input type="text" name="name" id="name" required>
                        </div>
                        <div class="field input">
                            <label for="company">Company</label>
                            <input type="text" name="company" id="company" required>
                        </div>
                        <div class="field input">
                            <label for="email">Email</label>
                            <input type="email" name="email" id="email" required>
                        </div>
                        <div class="field input">
                            <label for="password">Password</label>
                            <input type="password" name="password" id="password" required>
                        </div>
                        <div class="field input">
                            <label for="cpassword">Confirm Password</label>
                            <input type="password" name="cpassword" id="cpassword" required>
                        </div>
                        <div class="field input file">
                            <label for="pfp">Profile Picture</label>
                            <input type="file" name="image" id="pfp" accept="image/*" class="file-input" required>
                        </div>

                        <div class="links">
                            <div class="field">
                                <input class="button submit gap" type="submit" name="submit" value="Register">
                            </div>
                            Already have an account? <a href="login.php">Login</a>
                        </div>
                    </form>
                </div>
         <?php } ?>
    </div>

    <?php include("inc/footer.inc.php") ?>    
    <script src="js/script.js"></script>
</body>
</html>