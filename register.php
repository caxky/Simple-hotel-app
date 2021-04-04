<html>
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
        <title>Hotel Account Register</title>
    </head>

    <?php
        session_start();

        if (isset( $_POST['save'] ))
        {
                $user = $_SESSION['username'];
                $password = $_SESSION['password'];
                $conn_string = $_SESSION['conn_string'];

                $sin = $_POST['isin'];
                $lastname = $_POST['ilastname'];
                $middlename = $_POST['imiddlename'];
                $firstname = $_POST['ifirstname'];
                $country = $_POST['icountry'];
                $stateorprovince = $_POST['istate_or_province'];
                $city = $_POST['icity'];
                $streetnumber = $_POST['istreet_number'];
                $streetname = $_POST['istreet_name'];
                $aptnumber = $_POST['iapt_number'];
                $zip = $_POST['izip'];
                $phonenumber = $_POST['iphone_number'];
                $curdate = date("Y-m-d");

            $dbconn = pg_connect($conn_string) or die('Connection failed');

            $query = "INSERT INTO public.customer(sin_number, first_name, middle_name, last_name, country, state_or_province, city, street_number, street_name, apt_number, zip, phone_number, date_of_registration) VALUES ('$sin','$firstname','$middlename','$lastname','$country','$stateorprovince','$city','$streetnumber','$streetname','$aptnumber','$zip','$phonenumber','$curdate')";
            
            $result = pg_query($dbconn,$query);

            if(!$result){
                die("Error in SQL query:" .pg_last_error());
            }

            pg_free_result($result);
            pg_close($dbconn);

            header('Location: login.php');
            exit();

        }
    ?>

    <body>
        <div id="header"> CUSTOMER REGISTRATION FORM</div>
        <form id="testform" name="testform" method="POST" action="">
            <p> <label for="isin">SIN:</label>
                    <input name="isin" type="number" id="sin" min=100000000 max=999999999 required/>
            </p>

            <p> <label for="ilastname">Last name:</label>
                    <input name="ilastname" type="text" id="ilastname" required/>
            </p>

            <p> <label for="imiddlename">Middle name:</label>
                    <input name="imiddlename" type="text" id="imiddlename" required/>
            </p>

            <p> <label for="ifirstname">First name:</label>
                    <input name="ifirstname" type="text" id="ifirstname" required/>
            </p>

            <p> <label for="icountry">Country code:</label>
                    <input name="icountry" type="text" id="icountry" placeholder="Format: US" pattern="[A-Za-z]{2}" required/>
            </p>

            <p> <label for="istate_or_province">State or province:</label>
                <input name="istate_or_province" type="text" id="istate_or_province" required/>
                <!--
                    <select name="istate_or_province" required>
                        <option value="AB">Alberta</option>
                        <option value="BC">British Columbia</option>
                        <option value="MB">Manitoba</option>
                        <option value="NB">New Brunswick</option>
                        <option value="NL">Newfoundland and Labrador</option>
                        <option value="NS">Nova Scotia</option>
                        <option value="ON">Ontario</option>
                        <option value="PE">Prince Edward Island</option>
                        <option value="QC">Quebec</option>
                        <option value="SK">Saskatchewan</option>
                        <option value="NT">Northwest Territories</option>
                        <option value="NU">Nunavut</option>
                        <option value="YT">Yukon</option>
                        <option value="AL">Alabama</option>
                        <option value="AK">Alaska</option>
                        <option value="AZ">Arizona</option>
                        <option value="AR">Arkansas</option>
                        <option value="CA">California</option>
                        <option value="CO">Colorado</option>
                        <option value="CT">Connecticut</option>
                        <option value="DE">Delaware</option>
                        <option value="DC">District Of Columbia</option>
                        <option value="FL">Florida</option>
                        <option value="GA">Georgia</option>
                        <option value="HI">Hawaii</option>
                        <option value="ID">Idaho</option>
                        <option value="IL">Illinois</option>
                        <option value="IN">Indiana</option>
                        <option value="IA">Iowa</option>
                        <option value="KS">Kansas</option>
                        <option value="KY">Kentucky</option>
                        <option value="LA">Louisiana</option>
                        <option value="ME">Maine</option>
                        <option value="MD">Maryland</option>
                        <option value="MA">Massachusetts</option>
                        <option value="MI">Michigan</option>
                        <option value="MN">Minnesota</option>
                        <option value="MS">Mississippi</option>
                        <option value="MO">Missouri</option>
                        <option value="MT">Montana</option>
                        <option value="NE">Nebraska</option>
                        <option value="NV">Nevada</option>
                        <option value="NH">New Hampshire</option>
                        <option value="NJ">New Jersey</option>
                        <option value="NM">New Mexico</option>
                        <option value="NY">New York</option>
                        <option value="NC">North Carolina</option>
                        <option value="ND">North Dakota</option>
                        <option value="OH">Ohio</option>
                        <option value="OK">Oklahoma</option>
                        <option value="OR">Oregon</option>
                        <option value="PA">Pennsylvania</option>
                        <option value="RI">Rhode Island</option>
                        <option value="SC">South Carolina</option>
                        <option value="SD">South Dakota</option>
                        <option value="TN">Tennessee</option>
                        <option value="TX">Texas</option>
                        <option value="UT">Utah</option>
                        <option value="VT">Vermont</option>
                        <option value="VA">Virginia</option>
                        <option value="WA">Washington</option>
                        <option value="WV">West Virginia</option>
                        <option value="WI">Wisconsin</option>
                        <option value="WY">Wyoming</option>
                    </select>	
                -->
            </p>

            <p> <label for="icity">City:</label>
                    <input name="icity" type="text" id="icity" required/>
            </p>

            <p> <label for="istreet_number">Street number:</label>
                    <input name="istreet_number" type="number" id="istreet_number" required/>
            </p>

            <p> <label for="istreet_name">Street name:</label>
                    <input name="istreet_name" type="text" id="istreet_name" required/>
            </p>

            <p> <label for="iapt_number">Apt number:</label>
                    <input name="iapt_number" type="number" id="iapt_number" placeholder="0 if N/A" required/>
            </p>

            <p> <label for="izip">Zip Code:</label>
                    <input name="izip" type="text" id="izip" required/>
            </p>

            <p> <label for="iphone_number">Phone Number:</label>
                    <input name="iphone_number" type="tel" id="iphone_number" placeholder="Format: 1234567890" pattern="[0-9]{10}" required/>
            </p>

            <p><input type="submit" value="Register" name="save" /></p>
        </form>

    </body>
</html>