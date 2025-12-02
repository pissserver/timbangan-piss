<?php
	session_start();
	include "connection.php";

	if($_SESSION['checker']==''){
		header("Location:login.html");
	}
	$checker 		= executeScalar("select checker_name from checker where checker_code = '".$_SESSION['checker']."'");

	function headScript(){
		$headScript=file_get_contents("headScript.html");
		return $headScript;
	}
?>
<!DOCTYPE html>
<html lang="en">
	<head>
		<meta charset="utf-8">
		<meta http-equiv="X-UA-Compatible" content="IE=edge">
		<meta name="viewport" content="width=device-width, initial-scale=1">
		<meta name="description" content="">
		<meta name="author" content="">
		<link rel="icon" href="img/logo.png">
		<link rel="shortcut icon" href="img/logo.png" />
		<title>Update Log | PISS</title>

		<?php echo headScript(); ?>

	</head>
	<body>
		<div id="wrapper">
		<?php include "components/sidebar.php" ?>

			<div id="page-wrapper">
				<br><br><br><br>
				<div class="row">
					<div class="col-md-12">
						<div class="panel panel-primary">
							<div class="panel-heading">
								<h4 class="panel-title">
									<i class="fa fa-history"></i> Update Log / Changelog
								</h4>
							</div>
							<div class="panel-body">
								<div class="panel-group" id="accordionChangelog">

								<?php
								    $first = true;
									$query = mysql_query("SELECT * FROM update_timbangan ORDER BY id DESC");

									while($data = mysql_fetch_array($query)){
										$update_id = $data['id'];
										$semantic = $data['semantic'];
										$version = $data['version'];
										$title = $data['title'];
										$date = date("d F Y", strtotime($data['date']));
										$panelClass = $semantic == 'major' ? 'panel-success' :
														($semantic == 'minor' ? 'panel-info' : 'panel-default');
									?>

								<div class="panel <?php echo $panelClass; ?>">
									<div class="panel-heading">
										<h4 class="panel-title">
											<a data-toggle="collapse" data-parent="#accordionChangelog" href="#ver<?= $update_id ?>">
												<i class="fa fa-plus"></i> <?= "Version $version - $date" ?>
											</a>
										</h4>
									</div>

									<div id="ver<?= $update_id ?>" class="panel-collapse collapse <?= $first ? 'in' : '' ?>"">
										<div class="panel-body">
											<h5># <?= $title; ?></h5>
											<ul>
												<?php
													$desc_query = mysql_query("SELECT * FROM update_desc_timbangan WHERE update_timbangan_id = '$update_id'");
													$emoji = [
																// Objects
																'💡','📌','📍','📎','✏','✒','🖊','🖋','📝','📖','📚','📦','📁','📂','🗂',
																'💼','👜','🎒','💻','🖥','🖨','⌨','🖱','💾','📱','📲','☎','📞',

																// Weather
																'☀','🌤','⛅','🌥','🌦','🌧','⛈','🌩','🌨','❄','🌬','💨','🌪','🌈',

																// Hearts / Symbols
																'❤️','🧡','💛','💚','💙','💜','🖤','🤍','🤎',
																'💔','❣','💕','💞','💓','💗','💖','💘','💝','💟',
																'✨','⭐','🌟','💫','🔥','💥','⚡','🌟','🎇','🎆',

																// Food
																'🍏','🍎','🍐','🍊','🍋','🍌','🍉','🍇','🍓','🫐','🍈','🍒','🍑','🥭','🍍',
																'🥥','🥝','🍅','🍆','🥑','🥦','🥬','🥒','🌶','🫑','🌽','🥕','🧄','🧅','🥔',

																// Misc fun
																'🎲','🎯','🎮','🎰','🎵','🎶','🎤','🎧',
																'🧩','🎎','🎏','🎐','🎀','🎁','🎉','🎊','🎈','🏆','🥇','🥈','🥉',
																'🚀','✈','🚁','🚗','🚌','🚜','🚲','🏍','🛵','🚂'
															];

													while($desc_data = mysql_fetch_array($desc_query)){

														$descriptions = $desc_data['description'];

														// ambil emoji acak
														$random_emoji = $emoji[array_rand($emoji)];

														echo "<li>$random_emoji $descriptions</li>";
													}
												?>
											</ul>
										</div>
									</div>
								</div>

								<?php 
									$first = false;
								} ?>

								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
			<!-- End Main Content -->
		</div>

		<!-- jQuery -->
		<script src="js/jquery.js"></script>
		<!-- Bootstrap Core JavaScript -->
		<script src="js/bootstrap.min.js"></script>
		<!-- Metis Menu Plugin JavaScript -->
		<script src="js/plugins/metisMenu/metisMenu.min.js"></script>
		<!-- Custom Theme JavaScript -->
		<script src="js/sb-admin-2.js"></script>
		<script>
			$(document).ready(function(){
				//Modal Password
				$('#modalPassword').on('shown.bs.modal', function (e) {
					$("#notifArea").html(alertLoadProcess);
					$("#data_password").load("password.php",function(){clearNotif();});
				})
			});
		</script>
	</body>
</html>