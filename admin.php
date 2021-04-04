<html>
	<head>
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
		<title>Admin Panel</title>
	</head>

	<?php
        session_start();

        $conn_string = $_SESSION['conn_string'];

        $dbh = pg_connect($conn_string) or die('Connection failed');

        $sin = $_SESSION['sin'];

        #Hotel info
        $hotels = "SELECT * FROM public.hotel ORDER BY hotel_id ASC";
        $stmt = pg_prepare($dbh,"adminhotel",$hotels);
        $hotelsresult = pg_execute($dbh,"adminhotel",array());

        if(!$hotelsresult){
            die("Error in SQL query:" .pg_last_error());
        }

        #Hotel room info
        $hotel_rooms = "SELECT hotel_id,room_id,room_number,price,room_capacity,view_type,room_status FROM public.hotel_room ORDER BY hotel_id ASC";
        $stmt = pg_prepare($dbh,"adminhotelroom",$hotel_rooms);
        $roomresult = pg_execute($dbh,"adminhotelroom",array());

        if(!$roomresult){
            die("Error in SQL query:" .pg_last_error());
        }

        #Customer info
        $customers = "SELECT * FROM public.customer ORDER BY sin_number ASC";
        $stmt = pg_prepare($dbh,"admincustomers",$customers);
        $customersresult = pg_execute($dbh,"admincustomers",array());

        if(!$customersresult){
            die("Error in SQL query:" .pg_last_error());
        }

        #Employee info
        $employees = "SELECT * FROM public.employee ORDER BY sin_number ASC";
        $stmt = pg_prepare($dbh,"adminemployees",$employees);
        $employeesresult = pg_execute($dbh,"adminemployees",array());

        if(!$employeesresult){
            die("Error in SQL query:" .pg_last_error());
        }

        #Queries
        if (isset( $_POST['query_submit'] )) {
            $query = $_POST['query'];
            $queryresult = pg_query($dbh,$query);

            if(!$queryresult){
                die("Error in SQL query:" .pg_last_error());
            }
        }

	?>

	<body>
        <div id="backtologin">
            <a href="login.php">Back to Login</a>
        </div>

		<div id="header">
            <h2>Admin Panel</h2>
        </div>

        <div id="shortcuts">
            <p>
                Shortcuts: 
                <a href="#hotelinfo">Hotel Information</a>&ensp;
                <a href="#hotelroominfo">Hotel Room Information</a>&ensp;
                <a href="#customerinfo">Customer Information</a>&ensp;
                <a href="#employeeinfo">Employee Information</a>
            </p>
        </div>

        <br>

        <form id="adminqueries" method="POST">
            <p>
                Use the query line to insert, delete, and update information about customers, employees, hotels, and rooms.
                <input name="query" type="text" id="query" size="80" placeholder="DELETE FROM public.employee WHERE sin_number='123456789'"/>
                <input type="submit" name="query_submit" value="Query" id="query_submit"/>
            </p>
        </form>

        <br>

        <div id="hotelinfo">
            <table>
                <h3>Hotel Information (Table name: hotel)</h3>
                
                <tr>
                    <th>Hotel ID</th>
                    <th>Brand Name</th>
                    <th>Stars</th>
                    <th>Number of Rooms</th>
                    <th>Country</th>
                    <th>State/Province</th>
                    <th>City</th>
                    <th>Street Number</th>
                    <th>Street Name</th>
                    <th>Apt Number</th>
                    <th>Zip Code</th>
                    <th>Contact Email</th>
                </tr>

                <?php
                $resultArr1 = pg_fetch_all($hotelsresult);

                foreach($resultArr1 as $array)
                {
                    echo '<tr>
                                        <td>'. $array['hotel_id'].'</td>
                                        <td>'. $array['brand_name'].'</td>
                                        <td>'. $array['star_category'].'</td>
                                        <td>'. $array['number_of_rooms'].'</td>
                                        <td>'. $array['country'].'</td>
                                        <td>'. $array['state_or_province'].'</td>
                                        <td>'. $array['city'].'</td>
                                        <td>'. $array['street_number'].'</td>
                                        <td>'. $array['street_name'].'</td>
                                        <td>'. $array['apt_number'].'</td>
                                        <td>'. $array['zip'].'</td>
                                        <td>'. $array['contact_email'].'</td>
                        </tr>';
                }
                echo '</table>';
                ?>

            </table>
        </div>

        <br><br>

        <div id="hotelroominfo">
            <table>
                <h3>Hotel Room Information (Table name: hotel_room)</h3>
                
                <tr>
                    <th>Hotel ID</th>
                    <th>Room ID</th>
                    <th>Room Number</th>
                    <th>Price</th>
                    <th>Room Capacity</th>
                    <th>View Type</th>
                    <th>Room Status</th>
                </tr>

                <?php
                $resultArr2 = pg_fetch_all($roomresult);

                foreach($resultArr2 as $array)
                {
                    echo '<tr>
                                        <td>'. $array['hotel_id'].'</td>
                                        <td>'. $array['room_id'].'</td>
                                        <td>'. $array['room_number'].'</td>
                                        <td>'. $array['price'].'</td>
                                        <td>'. $array['room_capacity'].'</td>
                                        <td>'. $array['view_type'].'</td>
                                        <td>'. $array['room_status'].'</td>
                        </tr>';
                }
                echo '</table>';
                ?>

            </table>
        </div>

        <br><br>

        <div id="customerinfo">
            <table>
                <h3>Customer Information (Table name: customer)</h3>
                
                <tr>
                    <th>SIN</th>
                    <th>First Name</th>
                    <th>Middle Name</th>
                    <th>Last Name</th>
                    <th>Country</th>
                    <th>State/Province</th>
                    <th>City</th>
                    <th>Street Number</th>
                    <th>Street Name</th>
                    <th>Apt Number</th>
                    <th>ZIP Code</th>
                    <th>Phone Number</th>
                    <th>Date of Registration</th>
                </tr>

                <?php
                $resultArr3 = pg_fetch_all($customersresult);

                foreach($resultArr3 as $array)
                {
                    echo '<tr>
                                        <td>'. $array['sin_number'].'</td>
                                        <td>'. $array['first_name'].'</td>
                                        <td>'. $array['middle_name'].'</td>
                                        <td>'. $array['last_name'].'</td>
                                        <td>'. $array['country'].'</td>
                                        <td>'. $array['state_or_province'].'</td>
                                        <td>'. $array['city'].'</td>
                                        <td>'. $array['street_number'].'</td>
                                        <td>'. $array['street_name'].'</td>
                                        <td>'. $array['apt_number'].'</td>
                                        <td>'. $array['zip'].'</td>
                                        <td>'. $array['phone_number'].'</td>
                                        <td>'. $array['date_of_registration'].'</td>
                        </tr>';
                }
                echo '</table>';
                ?>

            </table>
        </div>

        <br><br>
        
        <div id="employeeinfo">
            <table>
                <h3>Employee Information (Table name: employee)</h3>
                
                <tr>
                    <th>SIN</th>
                    <th>First Name</th>
                    <th>Middle Name</th>
                    <th>Last Name</th>
                    <th>Country</th>
                    <th>State/Province</th>
                    <th>City</th>
                    <th>Street Number</th>
                    <th>Street Name</th>
                    <th>Apt Number</th>
                    <th>ZIP Code</th>
                    <th>Salary</th>
                    <th>Role</th>
                </tr>

                <?php
                $resultArr4 = pg_fetch_all($employeesresult);

                foreach($resultArr4 as $array)
                {
                    echo '<tr>
                                        <td>'. $array['sin_number'].'</td>
                                        <td>'. $array['first_name'].'</td>
                                        <td>'. $array['middle_name'].'</td>
                                        <td>'. $array['last_name'].'</td>
                                        <td>'. $array['country'].'</td>
                                        <td>'. $array['state_or_province'].'</td>
                                        <td>'. $array['city'].'</td>
                                        <td>'. $array['street_number'].'</td>
                                        <td>'. $array['street_name'].'</td>
                                        <td>'. $array['apt_number'].'</td>
                                        <td>'. $array['zip'].'</td>
                                        <td>'. $array['salary'].'</td>
                                        <td>'. $array['role'].'</td>
                        </tr>';
                }
                echo '</table>';
                ?>

            </table>
        </div>
	</body>

</html>
