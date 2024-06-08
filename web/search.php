<!doctype html>
<html>
<head>
<meta charset="utf-8">
<title>Advanced Software Engineering</title>
<link href="../assets/css/bootstrap.css" rel="stylesheet">
<link rel="stylesheet" href="../assets/css/font-awesome.min.css">
<link rel="stylesheet" href="../assets/css/owl.carousel.css">
<link rel="stylesheet" href="../assets/css/owl.theme.default.min.css">

<!-- MAIN CSS -->
<link rel="stylesheet" href="../assets/css/templatemo-style.css">
</head>
<body>
<body id="top" data-spy="scroll" data-target=".navbar-collapse" data-offset="50">
     <!-- MENU -->
     <section class="navbar custom-navbar navbar-fixed-top" role="navigation">
          <div class="container">
               <div class="navbar-header">
                    <button class="navbar-toggle" data-toggle="collapse" data-target=".navbar-collapse">
                         <span class="icon icon-bar"></span>
                         <span class="icon icon-bar"></span>
                         <span class="icon icon-bar"></span>
                    </button>

                    <!-- lOGO TEXT HERE -->
                    <a href="#" class="navbar-brand">Search Equipment Database</a>
               </div>
               <!-- MENU LINKS -->
               <div class="collapse navbar-collapse">
                    <ul class="nav navbar-nav navbar-nav-first">
                         <li><a href="index.php" class="smoothScroll">Home</a></li>
                         <li><a href="search.php" class="smoothScroll">Search Equipment</a></li>
                         <li><a href="add.php" class="smoothScroll">Add Equipment</a></li>
                    </ul>
               </div>
          </div>
     </section>
 <!-- HOME -->
     <section id="home">
          </div>
     </section>
     <!-- SEARCH FORM -->
<section id="feature">
    <div class="container">
        <div class="row">
            <h2>Search Equipment</h2>
			<!-- Form used to search based on user's choice of device, manufacturer, or serial number  -->
            <form action="search.php" method="get">
                <div class="form-group">
                    <label for="searchType">Search By:</label>
                    <select class="form-control" id="searchType" name="searchType">
                        <option value="device">Device</option>
                        <option value="manufacturer">Manufacturer</option>
                        <option value="serial_number">Serial Number</option>
                    </select>
                </div>
                <div class="form-group">
                    <label for="searchQuery">Search Query:</label>
                    <input type="text" class="form-control" id="searchQuery" name="searchQuery">
                </div>
                <button type="submit" class="btn btn-primary">Search</button>
            </form>
		
			<?php
                if (isset($_GET['searchQuery']) && !empty($_GET['searchQuery'])) {
                    include("../db_connect.php");
                    require_once("../log_function.php");
                    $dblink = db_connect();

                    $searchType = $_GET['searchType'];
                    $searchQuery = mysqli_real_escape_string($dblink, $_GET['searchQuery']);
                    $sql = "";
					//load equipment based on search query
                    if ($searchType == "device") {
                        $sql = "SELECT s.serial_id, s.serial_number, d.device_type, m.manufacturer_name 
                                FROM devices d 
                                JOIN serials s ON d.device_id = s.device_id 
                                JOIN manufacturers m ON s.manufacturer_id = m.manufacturer_id 
                                WHERE d.device_type LIKE '%" . $searchQuery . "%' 
                                LIMIT 1000";
                    } elseif ($searchType == "manufacturer") {
                        $sql = "SELECT s.serial_id, s.serial_number, d.device_type, m.manufacturer_name 
                                FROM manufacturers m 
                                JOIN serials s ON m.manufacturer_id = s.manufacturer_id 
                                JOIN devices d ON s.device_id = d.device_id 
                                WHERE m.manufacturer_name LIKE '%" . $searchQuery . "%' 
                                LIMIT 1000";
                    } elseif ($searchType == "serial_number") {
                        $sql = "SELECT s.serial_id, s.serial_number, d.device_type, m.manufacturer_name 
                                FROM serials s 
                                JOIN devices d ON s.device_id = d.device_id 
                                JOIN manufacturers m ON s.manufacturer_id = m.manufacturer_id 
                                WHERE s.serial_number = '" . $searchQuery . "' 
                                LIMIT 1000";
                    }

                    $result = $dblink->query($sql);
					//depending on the search type, it shows every column except the search query type. 
					//for example if the search type is device, then don't show the device column 
                    if ($result && $result->num_rows > 0) {
                        echo "<table class='table'><thead><tr><th>ID</th>";
                        //create the header based on the search type
                        if ($searchType !== 'device') echo "<th>Device Type</th>";
                        if ($searchType !== 'manufacturer') echo "<th>Manufacturer</th>";
                        if ($searchType !== 'serial_number') echo "<th>Serial Number</th>";
                        echo "</tr></thead><tbody>";
                        while ($row = $result->fetch_assoc()) {
                            echo "<tr><td>" . htmlspecialchars($row['serial_id']) . "</td>";
                            if ($searchType !== 'device') echo "<td>" . htmlspecialchars($row['device_type']) . "</td>";
                            if ($searchType !== 'manufacturer') echo "<td>" . htmlspecialchars($row['manufacturer_name']) . "</td>";
                            if ($searchType !== 'serial_number') echo "<td>" . htmlspecialchars($row['serial_number']) . "</td>";
							echo "<td><a href='view_equipment.php?serial_id=" . $row['serial_id'] . "'>View Details</a></td>";
							echo "<td><a href='modify_equipment.php?serial_id=" . $row['serial_id'] . "'>Modify</a>
							</td>";
                            echo "</tr>";
                        }
                        echo "</tbody></table>";
						//log when a seach attempt is successful.
						log_action('Search Attempt', "Succesful Search performed for: $searchType with query: $searchQuery");
                    } else {
						//log when a seach attempt is unsuccessful.
						log_action('Search Attempt', "Unsuccessful Search performed for: $searchType with query: $searchQuery");
                        echo "<p>No results found for '" . htmlspecialchars($searchQuery) . "'.</p>";
                    }
					
                    
                }
                ?>

        </div>
    </div>
</section>
</body>
</html>