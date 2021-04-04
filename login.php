<html>

    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
        <title>Hotel Account Login</title>
    </head>

    <?php
        session_start();

        $conn_string = $_SESSION['conn_string'];

        $dbconn = pg_connect($conn_string) or die('Connection failed');

        if (isset ($_POST['login']))
        {
            $sin = $_POST['input_access'];
            $_SESSION['sin'] = $_POST['input_access'];

            if (!empty($sin)) {
                #Queries to check whether sin_number is valid in either customer or employee
                $q1 = "SELECT sin_number FROM customer WHERE sin_number = $sin";
                $q2 = "SELECT sin_number FROM employee WHERE sin_number = $sin";
                $customerquery = pg_query($dbconn,$q1);
                $employeequery = pg_query($dbconn,$q2);

                if ($checksin = pg_fetch_row($customerquery)) {
                    if ($checksin[0] = $sin){
                        echo "Logging into customer...";
                    
                        header("Location: customerpage.php");
                        exit();
                    }
                } else if ($checksin = pg_fetch_row($employeequery)) {
                    if ($checksin[0] = $sin){
                        echo "Logging into employee...";

                        header("Location: employeepage.php");
                        exit();
                    }
                } else if ($sin == 'admin') {
                    echo "Logging into admin panel...";

                    header("Location: admin.php");
                    exit();
                }    
                else {
                    print "Invalid SIN.";
                }

            } else {
                print "ERROR: Please type your SIN";
            }
        }
    ?>

    <body>
        <form id="testform" name="testform" method="post" action="">
            <p>
                <label for="access">Enter your SIN:</label>
                <input name="input_access" type="text" id="input_access"/><br/>
            </p>
            <p>
                <input type="submit" name="login" value="Login" id="login"/><br/><br/>
            </p>
        </form>

        <br/>
        <a href='register.php'>Customer Register</a><br/><br/>
        <a href='employeeregister.php'>Employee Register</a>
    </body>

</html>

