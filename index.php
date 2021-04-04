<html>
    <head>
        <title>Database Auth</title>
    </head>

    <?php
        session_start();

        if (isset($_POST['login'])) {
            $_SESSION['username'] = $_POST['input_user'];
            $_SESSION['password'] = $_POST['input_pass'];
            
            $user = $_SESSION['username'];
            $password = $_SESSION['password'];
            $_SESSION['conn_string'] = "host=web0.eecs.uottawa.ca port=15432 dbname=group_b02_g05 user=$user password=$password";

            header('Location: login.php');
            exit();
        }
    ?>

    <body>
        <p>Input details for uOttawa database authentication (web0.eecs.uottawa.ca)</p>

        <form id="testform" name="testform" method="post" action="">
            <p>
                <label for="user">Enter your username:</label>
                <input name="input_user" type="text" id="input_user" required/><br/>
            </p>
            <p>
                <label for="pass">Enter your password:</label>
                <input name="input_pass" type="password" id="input_pass" required/><br/>
            </p>
            <p>
                <input type="submit" name="login" value="Login" id="login"/><br/><br/>
            </p>
        </form>
    </body>
</html>