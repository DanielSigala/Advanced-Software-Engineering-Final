<!doctype html>
<html>
<head>
    <meta charset="utf-8">
    <title>View Equipment Details</title>
    <link href="../assets/css/bootstrap.css" rel="stylesheet">
    <link rel="stylesheet" href="../assets/css/font-awesome.min.css">
    <link rel="stylesheet" href="../assets/css/owl.carousel.css">
    <link rel="stylesheet" href="../assets/css/owl.theme.default.min.css">
    <link rel="stylesheet" href="../assets/css/templatemo-style.css">
	<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
	<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>
</head>
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
                <a href="#" class="navbar-brand">View Equipment Details</a>
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
    <!-- HOME -->
    <section id="home"></section>
    <!-- FEATURE -->
    <section id="feature" class="container">
		<h2>Equipment Details</h2>
		<?php
		if (isset($_GET['serial_number']) && trim($_GET['serial_number']) !== '') {//if a serial number is passed in
			$serial_number = htmlspecialchars(trim($_GET['serial_number']));

			// API call setup
			$url = "https://ec2-3-142-142-169.us-east-2.compute.amazonaws.com:8080/api/view_equipment.php?serial_number=" . urlencode($serial_number);
			$ch = curl_init();
			curl_setopt($ch, CURLOPT_URL, $url);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
			curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
			curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);

			$response = curl_exec($ch);
			if (curl_errno($ch)) {//if there is a curl error
				echo "<div class='alert alert-danger'>cURL error: " . curl_error($ch) . "</div>";
			} else {
				$responseArray = json_decode($response, true);
				if ($responseArray['Status'] === 'SUCCESS') {//if succesfully retried data from api print the equipment details
					$data = $responseArray['Data'];
					echo "<div class='well'><p><strong>Serial Number:</strong> " . $data['serial_number'] . "</p>";
					echo "<p><strong>Device Type:</strong> " . $data['device_type'] . "</p>";
					echo "<p><strong>Manufacturer:</strong> " . $data['manufacturer_name'] . "</p>";
					echo "<p><strong>Status:</strong> " . $data['status'] . "</p></div>";
					echo "<a href='modify_equipment.php?serial_number=" . urlencode($serial_number) . "' class='btn btn-primary'>Modify Equipment</a>";

				} else {//print error returned by api
					echo "<div class='alert alert-danger'>" . $responseArray['MSG'] . "</div>";
				}
			}
			curl_close($ch);
		} else {
			echo "<p>No serial number provided.</p>";
		}
		?>
	</section>
</body>
</html>
