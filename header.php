
		<div class="container">
			<div class="navbar navbar-fixed-top">
				<div class="navbar-inner">
					<a href="index.php"><div class="logo hidden-phone"></div></a>
				<ul class="nav">
					<li <?php echo ($page == 'index') ? "class='active'" : ""; ?>>
						<a href="index.php">
							<i class="fa fa-home fa-2x" data-toggle="tooltip" data-placement="bottom" title="Home" id="home"></i></a>
					</li>
					<li <?php echo ($page == 'history') ? "class='active'" : ""; ?>>
						<a href="history.php">
							<i class="fa fa-history fa-2x" data-toggle="tooltip" data-placement="bottom" title="History" id="history"></i></a>
					</li>
					<li <?php echo ($page == 'users') ? "class='active'" : ""; ?>>
						<a href="users.php">
						<i class="fa fa-users fa-2x" data-toggle="tooltip" data-placement="bottom" title="Users" id="users"></i></a>
					</li>
					<li <?php echo ($page == 'stats') ? "class='active'" : ""; ?>>
						<a href="stats.php">
						<i class="fa fa-area-chart fa-2x" data-toggle="tooltip" data-placement="bottom" title="Stats" id="stats"></i></a>
					</li>
					<li <?php echo ($page == 'charts') ? "class='active'" : ""; ?>>
						<a href="charts.php">
						<i class="fa fa-bar-chart fa-2x" data-toggle="tooltip" data-placement="bottom" title="Charts" id="charts"></i></a>
					</li>
					<li <?php echo ($page == 'settings') ? "class='active'" : ""; ?>>
						<a href="settings.php">
						<i class="fa fa-cogs fa-2x" data-toggle="tooltip" data-placement="bottom" title="Settings" id="settings"></i></a>
					</li>
					</ul>
				</div>
			</div>
		</div>
