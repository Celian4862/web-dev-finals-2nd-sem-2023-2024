<!DOCTYPE html>
<html lang="en">
	<head>
		<meta charset="UTF-8" />
		<meta name="viewport" content="width=device-width, initial-scale=1.0" />
		<title>Inventory - Input</title>
		<link rel="stylesheet" type="text/css" href="../css/style.css" />
		<link rel="stylesheet" type="text/css" href="../inventoryInput/style.css" />
		<!-- <script defer src="userInput.js" type="text/javascript"></script> -->
		<script src="https://kit.fontawesome.com/f3364d5594.js" crossorigin="anonymous"></script>
	</head>
	<body class="container">
		<div class="navbar">
			<div class="center">
				<div class="logo-container">
					<img src="../images/n2n.png" alt="n2n logo" />
				</div>
			</div>
			<div class="tabs">
				<button onclick="dashboard_link()">
					<script>
						function dashboard_link() {
							location.href = "../dashboard/index.html";
						}
					</script>
					<i class="fa-solid fa-table-columns" style="color: #ffffff;"></i>
					<span>Dashboard</span>
				</button>
				<button onclick="user_link()">
					<script>
						function user_link() {
							location.href = "../userInput/userInput.php";
						}
					</script>
					<i class="fa-solid fa-user" style="color: #ffffff;"></i>
					<span>User</span>
				</button>
				<button class="active">
					<i class="fa-solid fa-dolly" style="color: #ffffff;"></i>
					<span>Inventory</span>
				</button>
				<button onclick="shipment_link()">
					<script>
						function shipment_link() {
							location.href = "../shipments/index.html";
						}
					</script>
					<i class="fa-solid fa-truck" style="color: #ffffff;"></i>
					<span>Shipments</span>
				</button>
				<button onclick="reception_link()">
					<script>
						function reception_link() {
							location.href = "../receptions/index.html";
						}
					</script>
					<i class="fa-solid fa-truck-ramp-box" style="color: #ffffff;"></i>
					<span>Reception</span>
				</button>
			</div>
			<button class="return">
				<span class="fa-solid fa-arrow-right-to-bracket fa-2xl"></span>
			</button>
		</div>
		<div class="content-container">
			<div class="header"></div>
			<div class="content-header">
				<span>Add new item</span>
				<a href=""><p>Back to list</p></a>
			</div>
			<div class="box-container">
				<form class="form-container" action="includes/formhandler.inc.php" method="post">
					<div class="form-group">
						<label for="product-label">Product Label</label>
						<input type="text" id="product-label" name="label" placeholder="Product label" required/>
					</div>
					<div class="form-group">
						<label for="description">Description</label>
						<textarea id="description" name="product_description" placeholder="Description" required></textarea>
					</div>
					<div class="form-group">
						<label for="warranty">Warranty</label>
						<input type="text" id="warranty" name="warranty" placeholder="Warranty" required />
					</div>
					<button>Add item</button>
				</form>
			</div>
		</div>
	</body>
</html>
