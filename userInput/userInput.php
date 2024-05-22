<!DOCTYPE html>
<html lang="en">
	<head>
		<meta charset="UTF-8" />
		<meta name="viewport" content="width=device-width, initial-scale=1.0" />
		<title>Users - Input</title>
		<link rel="stylesheet" type="text/css" href="userInput.css" />
		<script defer src="userInput.js" type="text/javascript"></script>
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
				<button>
					<i class="fa-solid fa-table-columns" style="color: #ffffff;"></i>
					<span>Dashboard</span>
				</button>
				<button class="active">
					<i class="fa-solid fa-user" style="color: #ffffff;"></i>
					<span>User</span>
				</button>
				<button>
					<i class="fa-solid fa-dolly" style="color: #ffffff;"></i>
					<span>Inventory</span>
				</button>
				<button>
					<i class="fa-solid fa-truck" style="color: #ffffff;"></i>
					<span>Shipments</span>
				</button>
				<button>
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
				<span>Add new user</span>
				<a href=""><p>Back to list</p></a>
			</div>
			<div class="box-container">
				<form class="form-container" action="includes/formhandler.inc.php" method="post">
					<div class="form-group">
						<label for="email">Email</label>
						<input type="email" id="email" name="email" placeholder="Email" required/>
					</div>
					<div class="form-group">
						<label for="password">Password</label>
						<input type="password" id="password" name="pwd" placeholder="Password" required />
					</div>
					<button>Add user</button>
				</form>
			</div>
		</div>
	</body>
</html>
