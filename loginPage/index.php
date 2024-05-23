<?php
    require_once 'includes/login_view.inc.php';
?>

<!DOCTYPE html>
<html>
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Login Page</title>
        <link rel="stylesheet" type="text/css" href="style.css">
        <script defer src="script.js" type="text/javascript"></script>
    </head>
    <body>
        <div class="login-container">
            <div class="center">
                <div class="logo-container">
                    <img src="../images/n2n.png" alt="n2n logo">
                </div>
            </div>
            <h1>N2N SOLUTIONS</h1>
            <div class="form-container">
                <form id="loginForm" action="includes/login.inc.php" method="post">
                    <div>
                        <label for="email" id="email">Email</label>
                        <input type="email" id="email" name="email" placeholder="Email" required>
                    </div>
                    <div>
                        <label for="password">Password</label>
                        <input type="password" id="pwd" name="pwd" placeholder="Password" required>
                    </div>
                    <div class="row">
                        <div>
                            <input type="checkbox" id="rememberMe" name="rememberMe">
                            <label for="rememberMe">Stay signed in</label>
                        </div>
                        <div class="align-right">
                            <button type="submit">Login</button>
                        </div>
                    </div>
                </form>
            </div>
            <?php
                check_login_errors();
            ?>
        </div>
    </body>
</html>
