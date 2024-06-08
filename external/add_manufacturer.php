<!doctype html>
<html>
<head>
    <meta charset="utf-8">
    <title>Add New Manufacturer</title>
    <link href="../assets/css/bootstrap.css" rel="stylesheet">
    <link rel="stylesheet" href="../assets/css/font-awesome.min.css">
    <link rel="stylesheet" href="../assets/css/owl.carousel.css">
    <link rel="stylesheet" href="../assets/css/owl.theme.default.min.css">
    <link rel="stylesheet" href="../assets/css/templatemo-style.css">
    <!-- some css bootstrap files that are used for the dropdown menu -->
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
                    <span the class="icon icon-bar"></span>
                </button>
                <a href="#" class="navbar-brand">Add New Manufacturer</a>
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
        <div class="container">
            <h2>Add New Manufacturer</h2>
            <?php
            if ($_SERVER['REQUEST_METHOD'] === 'POST' && !empty($_POST['manufacturerName'])) {
                $manufacturerName = trim($_POST['manufacturerName']);

                // validate input length just in case
                if (!is_string($manufacturerName) || strlen($manufacturerName) > 64) {
                    echo "<div class='alert alert-danger'>Manufacturer name must not exceed 64 characters.</div>";
                } else {
                    // API URL
                    $url = "https://ec2-3-142-142-169.us-east-2.compute.amazonaws.com:8080/api/add_manufacturer";
					//add data from field to post to api
                    $postData = http_build_query(['manufacturer_name' => $manufacturerName]);

					// intialize curl using link for my api
                    $ch = curl_init($url);
                    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
                    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
                    curl_setopt($ch, CURLOPT_POST, true);
                    curl_setopt($ch, CURLOPT_POSTFIELDS, $postData);
                    curl_setopt($ch, CURLOPT_HTTPHEADER, [
                        'Content-Type: application/x-www-form-urlencoded'
                    ]);

                    // execute the API call
                    $response = curl_exec($ch);
                    $responseArray = json_decode($response, true);
					//if there is a curl error
                    if (curl_errno($ch)) {
                        echo "<div class='alert alert-danger'>cURL error: " . curl_error($ch) . "</div>";
                    } elseif (isset($responseArray['Status']) && $responseArray['Status'] === 'SUCCESS') {
                        echo "<div class='alert alert-success'>{$responseArray['MSG']}</div>";
                    } else {
                        echo "<div class='alert alert-danger'>{$responseArray['MSG']}</div>";
                    }
                    curl_close($ch);
                }
            }
            ?>
            <form action="add_manufacturer.php" method="post">
                <div class="form-group">
                    <label for="manufacturerName">Manufacturer Name:</label>
                    <input type="text" name="manufacturerName" id="manufacturerName" required class="form-control">
                </div>
                <button type="submit" class="btn btn-primary">Add Manufacturer</button>
            </form>
        </div>
    </section>
</body>
</html>
