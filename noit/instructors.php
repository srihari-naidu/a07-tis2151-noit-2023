<?php
    session_start();
    include("inc/connection.inc.php");
    include("inc/functions.inc.php");
    
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
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="css/style.css">
    <title>NOIT - Instructors</title>
</head>
<body>
    <?php include("inc/header.inc.php"); ?>
    <main>
        <div class="card-main">
            <h1 class="title">Instructors</h1>
            <?php
                $instructors = $con->prepare(
                    "SELECT * 
                     FROM Instructor 
                     JOIN User ON InstructorId = User.Id");
                $instructors->execute();
                if ($instructors->rowCount() > 0) 
                {
                    echo "<div class=\"short\">";
                    while ($instructor = $instructors->fetch(PDO::FETCH_ASSOC)) 
                    {
                        $instructorId = $instructor['Id'];

                        $instructorCourses = $con->prepare(
                            "SELECT * 
                             FROM Course 
                             JOIN CourseInstructor ON Course.Id = CourseInstructor.CourseId
                             WHERE CourseInstructor.InstructorId = ?"
                        );
                        $instructorCourses->execute([$instructorId]);
                        $instructorCourseCount = $instructorCourses->rowCount();

                        instructor($instructor);
                    }
                    echo "</div>";
                } 
                else 
                {
                    sorry("We couldn't find any instructors yet.", $gap=false);
                }
            ?>
        </div>
    </main>

<?php include("inc/footer.inc.php") ?>    
<script src="js/script.js"></script>
</body>
</html>