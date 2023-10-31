<?php
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
<header>
    <div class="nav">
        <div class="logo">
            <a href="home.php">
                <img src="static/logo/NOIT-logo.png" alt="NOIT Logo">
            </a>
        </div>
        <div class="mid-links">
            <a href="home.php">Home</a>
            <a href="about.php">About Us</a>
            <a href="courses.php">Courses</a>
            <a href="instructors.php">Instructors</a>
        </div>
        <?php
            if ($userId == '')
            {
        ?>
                <div class="right-links">
                    <a href="login.php">
                        <button class="button submit">Login</button>
                    </a>
                    <a href="home.php#joinUs">
                        <button class="button submit">Register</button>
                    </a>
                </div>
        <?php
            }
            else if ($userId != '') 
            {
        ?>
                <div class="profile-box">
                    <div class="profile-dropdown">
                        <button class="button dropdown">
                            <img src="uploads/<?= $user['ProfilePicture']; ?>" alt="" class="profile-pic">
                            <?php
                                if(isset($provider))
                                { echo "<h4>{$provider['Company']}</h4>"; }
                                else
                                { echo "<h4>{$user['Name']}</h4>"; }
                            ?>
                        </button>
                    </div>
                    <div class="profile-menu">
                        <a href="profile.php"<?= $user['Id']; ?>">Profile </a>
                        <a href="inc/logout.php">Logout </a>
                    </div>
                </div>
        <?php
            }
        ?>

    </div>
</header>