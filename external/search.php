<!doctype html>
<html>
<head>
    <meta charset="utf-8">
    <title>Advanced Software Engineering</title>
    <link href="../assets/css/bootstrap.css" rel="stylesheet">
    <link rel="stylesheet" href="../assets/css/font-awesome.min.css">
    <link rel="stylesheet" href="../assets/css/owl.carousel.css">
    <link rel="stylesheet" href="../assets/css/owl.theme.default.min.css">
    <link rel="stylesheet" href="../assets/css/templatemo-style.css">
	<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
	<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>
</head>
<body id="top" data-spy="scroll" data-target=".navbar-collapse" data-offset="50">
    <section class="navbar custom-navbar navbar-fixed-top" role="navigation">
        <div class="container">
            <div class="navbar-header">
                <button class="navbar-toggle" data-toggle="collapse" data-target=".navbar-collapse">
                    <span class="icon icon-bar"></span>
                    <span class="icon icon-bar"></span>
                    <span class="icon icon-bar"></span>
                </button>
                <a href="#" class="navbar-brand">Search Equipment Database</a>
            </div>
            <div class="collapse navbar-collapse">
			<ul class="nav navbar-nav navbar-nav-first">
				<li><a href="index.php" class="smoothScroll">Home</a></li>
				<li><a href="search.php" class="smoothScroll">Search Equipment</a></li>	
				<li class="dropdown">
					<a href="#" class="dropdown-toggle" data-toggle="dropdown">Add Equipment <b class="caret"></b></a>
					<ul class="dropdown-menu">
						<li><a href="add_equipment.php" class="smoothScroll">Add Equipment</a></li>
						<li><a href="add_device.php" class="smoothScroll">Add Device</a></li>
						<li><a href="add_manufacturer.php" class="smoothScroll">Add Manufacturer</a></li>
					</ul>
				</li>
				<li class="dropdown">
					<a href="#" class="dropdown-toggle" data-toggle="dropdown">Modify Equipment <b class="caret"></b></a>
					<ul class="dropdown-menu">
						<li><a href="modify_equipment.php" class="smoothScroll">Modify Equipment</a></li>
						<li><a href="modify_device.php" class="smoothScroll">Modify Device</a></li>
						<li><a href="modify_manufacturer.php" class="smoothScroll">Modify Manufacturer</a></li>
					</ul>
				</li>
			</ul>
		</div>
        </div>
    </section>
    <section id="home"></section>
    <section id="feature" class="container">
        <div class="row">
            <h2>Search Equipment</h2>
            <form action="search.php" method="get">
				<!-- this form takes in a search query type gotten from a dropdown menu as well as a search query from an input field and a check box that states whether to include inactive equipment -->
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
                <div class="checkbox">
                    <label><input type="checkbox" name="status" value="all"> Include Inactive</label>
                </div>
                <button type="submit" class="btn btn-primary">Search</button>
            </form>
            <?php
            if ($_SERVER['REQUEST_METHOD'] == 'GET' && !empty($_GET['searchQuery'])) {
                $searchType = $_GET['searchType'];
                $searchQuery = $_GET['searchQuery'];
                $status = isset($_GET['status']) && $_GET['status'] == 'all' ? 'all' : 'active';

                $apiUrl = "https://ec2-3-142-142-169.us-east-2.compute.amazonaws.com:8080/api/search.php?" . http_build_query([//builds the query data using the search type query and status
                    'searchType' => $searchType,
                    'searchQuery' => $searchQuery,
                    'status' => $status
                ]);

                $ch = curl_init($apiUrl);
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
                curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
                $response = curl_exec($ch);
                if (curl_errno($ch)) {//if there is a curl error
                    echo "<div class='alert alert-danger'>cURL error: " . curl_error($ch) . "</div>";
                } else {
                    $responseArray = json_decode($response, true);
                    if ($responseArray['Status'] === 'SUCCESS') {//if succesfully returned results
                        echo "<table class='table'><thead><tr>";// don't show the column for the search query
                        if ($searchType !== 'device') echo "<th>Device Type</th>";
                        if ($searchType !== 'manufacturer') echo "<th>Manufacturer</th>";
                        if ($searchType !== 'serial_number') echo "<th>Serial Number</th>";
                        echo "<th>Actions</th></tr></thead><tbody>";
                        foreach ($responseArray['Data'] as $item) {
                            echo "<tr>";
                            if ($searchType !== 'device') echo "<td>{$item['device_type']}</td>";
                            if ($searchType !== 'manufacturer') echo "<td>{$item['manufacturer_name']}</td>";
                            if ($searchType !== 'serial_number') echo "<td>{$item['serial_number']}</td>";
                            echo "<td><a href='view_equipment.php?serial_number={$item['serial_number']}'>View Details</a> | ";
							echo "<a href='modify_equipment.php?serial_number=" . urlencode($item['serial_number']) . "'>Modify Equipment</a></td>";
							echo "</tr>";

                }
                        echo "</tbody></table>";
                    } else {//if api returns no data
                        echo "<div class='alert alert-danger'>No results found.</div>";
                    }
                }
                curl_close($ch);
            }
            ?>
        </div>
    </div>
</section>
</body>
</html>
