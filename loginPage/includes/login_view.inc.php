<?php
function check_login_errors() {
    if (isset($_GET['error'])) {
        $error = $_GET['error'];
        if ($error == "empty_input") {
            echo '<p class="error">Fill in all fields!</p>';
        } else if ($error == "login_incorrect") {
            echo '<p class="error">Incorrect email or password!</p>';
        }
    }
}