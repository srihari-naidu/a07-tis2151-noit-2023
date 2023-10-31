<?php
    session_start();
    include("inc/connection.inc.php");
    include("inc/functions.inc.php");

    if (isset($_POST['submit']))
    {
        $email = $_POST['email'];
        $pass = $_POST['password'];
        
        $verUser = $con->prepare(
            "SELECT * FROM User
             WHERE Email = ?
             AND Password = ?"
        );
        $verUser->execute([$email, $pass]);
        $user = $verUser->fetch(PDO::FETCH_ASSOC);

        if ($verUser->rowCount() > 0) 
        {
            $_SESSION['userId'] = $user['Id'];
            header('Location: home.php');
        }
        else
        {
            $_SESSION['error_credentials_unmatched'] = "Credentials do not match. Try again.";
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
    <title>NOIT - Login</title>
</head>
<body
    <?php include("inc/header.inc.php"); ?>
    <div class="container">
        <?php
            if (isset($_SESSION['error_credentials_unmatched']))
            {
                $message = $_SESSION['error_credentials_unmatched'];
                unset($_SESSION['error_credentials_unmatched']);
                error($message, "Back", "javascript:self.history.back()");
            }
            else
            {
        ?>
                <div class="card card-header">
                    <h1>Login</h1>
                </div>
                <div class="card card-body">
                    <form action="" method="POST">
                        <div class="field input">
                            <label for="email">Email</label>
                            <input type="text" name="email" id="email" required>
                        </div>
                        
                        <div class="field input">
                            <label for="password">Password</label>
                            <input type="password" name="password" id="password" required>
                        </div>
                        
                        <div class="links">
                            <div class="field">
                                <input class="button submit gap" type="submit" name="submit" value="Login">
                            </div>
                            Don't have an account yet? <a href="home.php#joinUs">Join Us!</a>
                        </div>
                    </form>
                </div>
        <?php 
            } 
        ?>
    </div>

<?php include("inc/footer.inc.php"); ?>    
<script src="js/script.js"></script>
</body>
</html>