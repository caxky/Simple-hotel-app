<html>
	<head>
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
		<title>Employee Panel</title>
	</head>

	<?php
        session_start();

        $conn_string = $_SESSION['conn_string'];

        $dbh = pg_connect($conn_string) or die('Connection failed');

        $sin = $_SESSION['sin'];

        #Getting hotel ID of employee
        $employee_hotelid_query = "SELECT hotel_id FROM public.employee WHERE sin_number=$1";
        $stmt = pg_prepare($dbh,"employeehotelid",$employee_hotelid_query);
        $employee_hotelid = pg_execute($dbh,"employeehotelid",array($sin));

        if(!$employee_hotelid){
            die("Error in SQL query:" .pg_last_error());
        }

        if ($checkemployeehotelid = pg_fetch_row($employee_hotelid)) {
            $employeehotelid = $checkemployeehotelid[0];
        }



        if (empty($_SESSION['result'])) {
            unset($_SESSION['result']);

            $hotel_rooms = "SELECT hotel_id,room_id,room_number,price,room_capacity,view_type,room_status FROM public.hotel_room WHERE hotel_room.hotel_id=$3 AND hotel_room.room_status=$1 OR hotel_room.hotel_id=$3 AND hotel_room.room_status=$2 ORDER BY hotel_id ASC";
            $stmt = pg_prepare($dbh,"ps",$hotel_rooms);
            $result = pg_execute($dbh,"ps",array('available', 'reserved', $employeehotelid));

            if(!$result){
                die("Error in SQL query:" .pg_last_error());
            }
            
            $_SESSION['result'] = $result;
        }


        #checking either available or booked rooms
        if (isset( $_POST['avail_submit'] )) {
            unset($_SESSION['result']);

            $availability = $_POST['avail'];

            if ($availability == "all"){
                $hotel_rooms = "SELECT hotel_id,room_id,room_number,price,room_capacity,view_type,room_status FROM public.hotel_room WHERE hotel_room.hotel_id=$3 AND hotel_room.room_status=$1 OR hotel_room.hotel_id=$3 AND hotel_room.room_status=$2 ORDER BY hotel_id ASC";
                $stmt = pg_prepare($dbh,"availall",$hotel_rooms);
                $result = pg_execute($dbh,"availall",array('available', 'reserved', $employeehotelid));

                if(!$result){
                    die("Error in SQL query:" .pg_last_error());
                }
                
                $_SESSION['result'] = $result;
            } else {
                $avail_query = "SELECT hotel_id,room_id,room_number,price,room_capacity,view_type,room_status FROM public.hotel_room WHERE hotel_room.hotel_id=$2 AND hotel_room.room_status=$1 ORDER BY hotel_id ASC";
                $stmt = pg_prepare($dbh,"availabilitysort",$avail_query);
                $result = pg_execute($dbh,"availabilitysort",array($availability, $employeehotelid));

                if(!$result){
                    die("Error in SQL query:" .pg_last_error());
                }

                $_SESSION['result'] = $result;
            }
        }


        #Checking in customer
        if (isset ($_POST['submit_checkin'])) {
            $checkin_customersin = $_POST['check_customersin'];
            $checkin_roomid = $_POST['check_roomid'];

            $validcheckinquery = "SELECT CASE WHEN EXISTS (SELECT * FROM public.booking_detail WHERE room_id=$checkin_roomid AND sin_number=$checkin_customersin) THEN 'TRUE' ELSE 'FALSE' END";
            $validcheckin = pg_query($dbh,$validcheckinquery);

            if ($checkvalidcheckin = pg_fetch_row($validcheckin)) {
                if ($checkvalidcheckin[0] == 'FALSE') {
                    echo 'Customer has not booked room ID ' . $checkin_roomid;
                } else if ($checkvalidcheckin[0] == 'TRUE') {
                    $checkin_insert = "INSERT INTO public.stays_in VALUES ($1,$2)";
                    $stmt = pg_prepare($dbh,"checkininsert",$checkin_insert);
                    $checkin_insert_result = pg_execute($dbh,"checkininsert",array($checkin_customersin,$checkin_roomid));

                    if(!$checkin_insert_result){
                        die("Error in SQL query:" .pg_last_error());
                    }

                    echo "Check-in submitted for Customer SIN: $checkin_customersin for Room ID: $checkin_roomid";
                    
                }
            }
        }

        #Checking out customer
        if (isset ($_POST['submit_checkout'])) {
            $checkout_customersin = $_POST['check_customersin'];
            $checkout_roomid = $_POST['check_roomid'];

            $validcheckoutquery = "SELECT CASE WHEN EXISTS (SELECT * FROM public.stays_in WHERE room_id=$checkout_roomid AND sin_number=$checkout_customersin) THEN 'TRUE' ELSE 'FALSE' END";
            $validcheckout = pg_query($dbh,$validcheckoutquery);

            if ($checkvalidcheckout = pg_fetch_row($validcheckout)) {
                if ($checkvalidcheckout[0] == 'FALSE') {
                    echo 'Customer is not currently staying in room ID ' . $checkout_roomid;
                } else if ($checkvalidcheckout[0] == 'TRUE') {
                    $checkout_insert = "DELETE FROM public.stays_in WHERE sin_number=$1 AND room_id=$2";
                    $stmt = pg_prepare($dbh,"checkoutinsert",$checkout_insert);
                    $checkout_insert_result = pg_execute($dbh,"checkoutinsert",array($checkout_customersin,$checkout_roomid));

                    if(!$checkout_insert_result){
                        die("Error in SQL query:" .pg_last_error());
                    }

                    echo "Check-out submitted for Customer SIN: $checkout_customersin for Room ID: $checkout_roomid";
                    
                }
            }
        }
        

        #Submitting booking for customer
        if (isset( $_POST['submit_booking'])) {
            $book_roomid = $_POST['room_id'];
            $book_occupants = $_POST['occupants'];
            $book_startdate = $_POST['startdate'];
            $book_enddate = $_POST['enddate'];
            $book_sin = $_POST['customersin'];

            if ($book_startdate <= $book_enddate) {
                $employee_booking = "SELECT CASE WHEN NOT EXISTS (SELECT * FROM public.hotel_room WHERE room_id = $1 AND room_capacity >= $2 AND room_status='available' AND hotel_id=$3) THEN 'TRUE' WHEN EXISTS (SELECT * FROM public.booking_detail WHERE starting_date BETWEEN DATE '$book_startdate' AND DATE '$book_enddate' AND room_id = $1 UNION SELECT * FROM public.booking_detail WHERE ending_date BETWEEN DATE '$book_startdate' AND DATE '$book_enddate' AND room_id = $1) THEN 'TRUE' ELSE 'FALSE' END";
                $stmt = pg_prepare($dbh,"employeebook",$employee_booking);
                $validbookingresult = pg_execute($dbh,"employeebook",array($book_roomid,$book_occupants,$employeehotelid));

                
                if(!$validbookingresult){
                    die("Error in SQL query:" .pg_last_error());
                }

                if ($checkvalidbooking = pg_fetch_row($validbookingresult)) {
                    if ($checkvalidbooking[0] == 'TRUE') {
                        echo 'Invalid booking date, room, or number of occupants. Redo booking.';
                    } else if ($checkvalidbooking[0] == 'FALSE'){

                        $customersinquery = "SELECT CASE WHEN NOT EXISTS (SELECT * FROM public.customer WHERE sin_number=$book_sin) THEN 'TRUE' ELSE 'FALSE' END";
                        $customersin = pg_query($dbh, $customersinquery);

                        if ($checkcustomersin = pg_fetch_row($customersin)) {
                            if ($checkcustomersin[0] == 'TRUE'){
                                echo 'Invalid customer SIN. Please ensure they have an account.';
                            } else if ($checkcustomersin[0] == 'FALSE'){

                                $employee_booking_insert = "INSERT INTO public.booking_detail VALUES ((SELECT COUNT(distinct(booking_id))+1 FROM public.booking_detail), $book_sin, $book_roomid, $book_occupants, 'rented', DATE '$book_startdate', DATE '$book_enddate')";
                                $stmt = pg_prepare($dbh,"employeebookinsert",$employee_booking_insert);
                                $insertresult = pg_execute($dbh,"employeebookinsert",array());

                                
                                if(!$insertresult){
                                    die("Error in SQL query:" .pg_last_error());
                                }

                                echo "Valid renting date and room: RENTING SUBMITTED (Customer SIN: $book_sin) (Room ID: $book_roomid) (Occupants: $book_occupants) ($book_startdate to $book_enddate)";
                                
                            } else {
                                echo 'ERROR: could not obtain row value';
                            }
                        }

                    } else {
                        echo 'ERROR: could not obtain row value';
                    }
                }
            } else {
                echo 'Invalid booking date. Redo booking.';
            }

        }
	?>

	<body>
        <div id="backtologin">
            <a href="login.php">Back to Login</a>
        </div>

		<div id="header">
            <h2>Employee Panel</h2>
        </div>

        <div id="userinfo">
            <?php 
                echo "Employee SIN: $sin &ensp;&ensp;Employee Hotel ID: $employeehotelid";
            ?>
        </div>
        <br></br>

        <form id="checkincustomer" method="POST">
            <h3>Check-in/check-out customer</h3>
            <div id="checkindetails">
                <label for="check_customersin">Customer SIN: </label>
                <input type="number" name="check_customersin" id="check_customersin" min=100000000 max=999999999/>

                <label for="check_roomid">Room ID: </label>
                <input type="number" name="check_roomid" id="check_roomid"/>
            </div>

            <input type="submit" name="submit_checkin" value="Check-in" id="submit_checkin"/>
            <input type="submit" name="submit_checkout" value="Check-out" id="submit_checkout"/>
        </form>
        <br>

        <form id="bookingdetails" method="POST">
            <h3>Rent room for customer:</h3>
            <div id="bookinginputs">
                <label for="customersin">Customer SIN: </label>
                <input type="number" name="customersin" id="customersin" min=100000000 max=999999999 required/>
            
                <label for="room_id">Room ID: </label>
                <input type="text" name="room_id" id="room_id" required/>

                <label for="occupants">Occupants: </label>
                <input type="text" name="occupants" id="occupants" required/>

                <label for="startdate">Start Date: </label>
                <input type="date" name="startdate" id="startdate" required/>

                <label for="enddate">End Date: </label>
                <input type="date" name="enddate" id="enddate" required/>
            </div>

            <input type="submit" name="submit_booking" value="Submit Booking" id="submit_booking"/>
        </form>
        <br></br>

        <form id="sorting" method="POST">
            <h3>Sort by availability:</h3>
            <p>
                <input type="submit" value="Availability" id="avail_submit" name="avail_submit"/>
                <select name="avail" id="avail">
                        <option value="available">Available</option>
                        <option value="reserved">Reserved</option>
                        <option value="all">All</option>
                </select>
            </p>
        </form>

        <br>

		<table>
            
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
			$resultArr = pg_fetch_all($_SESSION['result']);

            if ($resultArr){
                foreach($resultArr as $array)
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
            } else {
                echo '<tr></tr>';
                echo '</table>';
            }
			?>

		</table>
	</body>

</html>
