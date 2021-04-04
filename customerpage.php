<html>
	<head>
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
		<title>Customer Page</title>
	</head>

	<?php
        session_start();

        $conn_string = $_SESSION['conn_string'];

        $dbh = pg_connect($conn_string) or die('Connection failed');

        $sin = $_SESSION['sin'];

        #Table showing all available hotel rooms
        if (empty($_SESSION['result'])) {
            unset($_SESSION['result']);

            $hotel_rooms = "SELECT hotel_id,room_id,room_number,price,room_capacity,view_type,room_status FROM public.hotel_room WHERE hotel_room.room_status=$1 ORDER BY hotel_id ASC";
            $stmt = pg_prepare($dbh,"ps",$hotel_rooms);
            $result = pg_execute($dbh,"ps",array('available'));

            if(!$result){
                die("Error in SQL query:" .pg_last_error());
            }

            $_SESSION['result'] = $result;
        }

        #Sorting for table by max price
        if (isset( $_POST['price_submit'] )) {
            unset($_SESSION['result']);

            $maxprice = $_POST['price'];
            $hotel_rooms = "SELECT hotel_id,room_id,room_number,price,room_capacity,view_type,room_status FROM public.hotel_room WHERE hotel_room.price<=$1 AND hotel_room.room_status=$2 ORDER BY hotel_room.price ASC";
            $stmt = pg_prepare($dbh,"pricesort",$hotel_rooms);
            $result = pg_execute($dbh,"pricesort",array($maxprice,'available'));

            if(!$result){
                die("Error in SQL query:" .pg_last_error());
            }

            $_SESSION['result'] = $result;
        }

        #Sorting for table by room capacity
        if (isset( $_POST['roomcap_submit'] )) {
            unset($_SESSION['result']);

            $roomcap = $_POST['roomcap'];
            $hotel_rooms = "SELECT hotel_id,room_id,room_number,price,room_capacity,view_type,room_status FROM public.hotel_room WHERE hotel_room.room_capacity>=$1 AND hotel_room.room_status=$2 ORDER BY hotel_room.room_capacity ASC";
            $stmt = pg_prepare($dbh,"roomcapsort",$hotel_rooms);
            $result = pg_execute($dbh,"roomcapsort",array($roomcap,'available'));

            if(!$result){
                die("Error in SQL query:" .pg_last_error());
            }

            $_SESSION['result'] = $result;
        }

        #Sorting for table by view type
        if (isset( $_POST['viewtype_submit'] )) {
            unset($_SESSION['result']);

            $viewtype = $_POST['viewtype'];
            $hotel_rooms = "SELECT hotel_id,room_id,room_number,price,room_capacity,view_type,room_status FROM public.hotel_room WHERE hotel_room.view_type=$1 AND hotel_room.room_status=$2 ORDER BY hotel_id ASC";
            $stmt = pg_prepare($dbh,"viewtypesort",$hotel_rooms);
            $result = pg_execute($dbh,"viewtypesort",array($viewtype,'available'));

            if(!$result){
                die("Error in SQL query:" .pg_last_error());
            }

            $_SESSION['result'] = $result;
        }

        
        #Submitting customer booking
        if (isset( $_POST['submit_booking'])) {
            $book_roomid = $_POST['room_id'];
            $book_occupants = $_POST['occupants'];
            $book_startdate = $_POST['startdate'];
            $book_enddate = $_POST['enddate'];

            if ($book_startdate <= $book_enddate) {
                $customer_booking = "SELECT CASE WHEN NOT EXISTS (SELECT * FROM public.hotel_room WHERE room_id = $1 AND room_capacity >= $2 AND room_status='available') THEN 'TRUE' WHEN EXISTS (SELECT * FROM public.booking_detail WHERE starting_date BETWEEN DATE '$book_startdate' AND DATE '$book_enddate' AND room_id = $1 UNION SELECT * FROM public.booking_detail WHERE ending_date BETWEEN DATE '$book_startdate' AND DATE '$book_enddate' AND room_id = $1) THEN 'TRUE' ELSE 'FALSE' END";
                $stmt = pg_prepare($dbh,"customerbook",$customer_booking);
                $validbookingresult = pg_execute($dbh,"customerbook",array($book_roomid,$book_occupants));

                
                if(!$validbookingresult){
                    die("Error in SQL query:" .pg_last_error());
                }

                if ($checkvalidbooking = pg_fetch_row($validbookingresult)) {
                    if ($checkvalidbooking[0] == 'TRUE') {
                        echo 'Invalid booking date, room, or number of occupants. Redo booking.';
                    } else if ($checkvalidbooking[0] == 'FALSE'){

                        $customer_booking_insert = "INSERT INTO public.booking_detail VALUES ((SELECT COUNT(distinct(booking_id))+1 FROM public.booking_detail), $sin, $book_roomid, $book_occupants, 'booked', DATE '$book_startdate', DATE '$book_enddate')";
                        $stmt = pg_prepare($dbh,"customerbookinsert",$customer_booking_insert);
                        $insertresult = pg_execute($dbh,"customerbookinsert",array());

                        
                        if(!$insertresult){
                            die("Error in SQL query:" .pg_last_error());
                        }

                        echo "Valid booking date and room: BOOKING SUBMITTED (Room ID: $book_roomid) (Occupants: $book_occupants) ($book_startdate to $book_enddate)";
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
            <h2>Hotel Room Booking</h2>
        </div>

        <div id="userinfo">
            <?php echo "Current user SIN: $sin"?>
        </div>
        <br></br>

        <form id="bookingdetails" method="POST">
            <h3>Book a room:</h3>
            <div id="bookinginputs">
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
        <br>

        <form id="sorting" method="POST">
            <h3>Filters</h3>
            <p>
                <input type="submit" name="price_submit" value="Max Price" id="price_submit"/>
                <input name="price" type="number" id="price"/>
            </p>
            <p>
                <input type="submit" value="Min Capacity" id="roomcap_submit" name="roomcap_submit"/>
                <input name="roomcap" type="number" id="roomcap"/>
            </p>
            <p>
                <input type="submit" value="View Type" id="viewtype_submit" name="viewtype_submit"/>
                <select name="viewtype" id=viewtype>
                        <option value="beach">Beach</option>
                        <option value="mountain">Mountain</option>
                        <option value="none">None</option>
                </select>
            </p>
        </form>
        <br><br>

		<table>
            <h3>Available Rooms</h3>
            
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
			?>

		</table>
	</body>

</html>
