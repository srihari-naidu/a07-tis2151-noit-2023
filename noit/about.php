<?php
    session_start();
    include("inc/connection.inc.php");

    if (isset($_SESSION['userId'])) 
    {
        $userId = $_SESSION['userId'];

        $user = $con->prepare(
            "SELECT * 
             FROM User 
             WHERE Id = ?"
        );
        $user->execute([$userId]);
        $user = $user->fetch(PDO::FETCH_ASSOC);
    }
    else {
        $userId = '';
    }

    $courses = $con->prepare("SELECT * FROM Course");
    $courses->execute();
    $courseCount = $courses->rowCount();

    $students = $con->prepare("SELECT * FROM Student");
    $students->execute();
    $studentCount = $students->rowCount();

    $instructors = $con->prepare("SELECT * FROM Instructor");
    $instructors->execute();
    $instructorCount = $instructors->rowCount();

    $providers = $con->prepare("SELECT * FROM Provider");
    $providers->execute();
    $providerCount = $providers->rowCount();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="css/style.css">
    <title>NOIT - About</title>
</head>
<body>
    <?php include("inc/header.inc.php"); ?>
    <main>
        <div class="card-main">
            <h1 class="title">What is NOIT</h1>
            <div class="long">
                <div class="card">
                    <p>
                        NOIT, short for "Know Information Technology," 
                        is a comprehensive web-based Training Provider Management System 
                        designed to streamline and enhance the process of managing and delivering training courses. 
                        Built with the needs of training providers, students, and instructors in mind, 
                        NOIT offers a user-friendly platform that simplifies course management, registration, and communication. 
                        With its intuitive interface and powerful features, 
                        NOIT aims to revolutionize the way training programs are administered and accessed.
                    </p>
                </div>
            </div>
            <h1 class="title">Why choose NOIT</h1>
            <div class="long">
                <div class="card">
                    <p>
                        NOIT is your ultimate Training Provider Management System, 
                        offering a user-friendly platform that simplifies course management, registration, and communication. 
                        With efficient course creation, enhanced instructor experience, seamless student registration,
                        robust database management, and an intuitive interface, 
                        NOIT revolutionizes the way training programs are administered. 
                        Trust us to deliver a comprehensive solution that meets all your training management needs.
                    </p>
                </div>
            </div>
            <h1 class="title">Team behind NOIT</h1>
            <div class="long">
                <div class="card">
                    <p>
                        At NOIT, we are proud to have a talented and dedicated team of professionals driving our success. 
                        Our team consists of experienced software engineers, designers, and project managers 
                        who are passionate about developing innovative solutions for the education industry. 
                        With their expertise and commitment, we ensure that NOIT continues to evolve 
                        and provide exceptional user experiences. Together, we strive to deliver excellence 
                        and revolutionize the way training programs are managed and accessed.
                    </p>
                </div>
            </div>
            <h1 class="title">Stats of NOIT</h1>
            <div class="short">
                <div class="card center four">
                    <h1><?= $courseCount ?></h1> Courses
                </div>
                <div class="card center four">
                    <h1><?= $studentCount ?></h1> Students
                </div>
                <div class="card center four">
                    <h1><?= $instructorCount ?></h1> Instructors
                </div>
                <div class="card center four">
                    <h1><?= $providerCount ?></h1> Providers
                </div>
            </div>
        </div>
    </main>
<?php include("inc/footer.inc.php"); ?>    
<script src="js/script.js"></script>
</body>
</html>