<?php
	session_start();
	include "connection.php";
	//Validasi role
	if($_SESSION['checker']==''){
		header("Location:login.html");
	}

    $showWelcome = false;
    if (isset($_SESSION['show_welcome_modal']) && $_SESSION['show_welcome_modal'] === true) {
        $showWelcome = true;
        unset($_SESSION['show_welcome_modal']); // supaya tidak muncul lagi selama sesi aktif
    }

    $checker 		= executeScalar("select checker_name from checker where checker_code = '".$_SESSION['checker']."'");

    function headScript(){
		$headScript=file_get_contents("headScript.html");
		return $headScript;
	}

    $startDate          = date("Y-01-01");
    $endDate            = date("Y-m-d");

    $merchant 	        = executeScalar("SELECT COUNT(merchant_id) FROM merchant WHERE merchant_status = 'Aktif'");
    $weighing 	        = executeScalar("SELECT COUNT(weighing_id) FROM weighing WHERE DATE(date_check) BETWEEN '$startDate' AND '$endDate'");
    $paymentDone 	    = executeScalar("SELECT COUNT(weighing_id) FROM weighing WHERE weighing_sts = 'Lunas' AND date_pay BETWEEN '$startDate' AND '$endDate'");
    $paymentMitraDone 	= executeScalar("SELECT COUNT(weighing_id) FROM weighing_mitra WHERE weighing_sts = 'Lunas' AND date_pay BETWEEN '$startDate' AND '$endDate'");
    $cashierStaff       = executeScalar("select count(checker_id) from checker where checker_role = 'Cashier' and status_aktif = 1 and checker_name != 'Musdalifah'");
    $checkerStaff       = executeScalar("select count(checker_id) from checker where checker_role = 'Checker' and status_aktif = 1");

    $update_query = mysql_query("SELECT title FROM update_timbangan ORDER BY id DESC LIMIT 1");
    $update_data = mysql_fetch_array($update_query);
    $titleUpdate = $update_data['title'];
?>

<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="description" content="">
        <meta name="author" content="">
        <link rel="icon" href="img/icon.png">
        <link rel="shortcut icon" href="img/icon.png" />
        <title>Dashboard Timbangan | PISS</title>

        <?php echo headScript(); ?>

        <script src="js/jquery.min.js"></script>
        <script>
            /* hide all elements & show preloader*/
            document.documentElement.className += 'js';

            /* display variabel */
            var alertLoadProcess = "<span class='alert alert-danger' onclick='clearNotif()'><i class='fa fa-spinner fa-spin'></i> Loading...</span>";
        </script>
        
    </head>
    <body>
        <div id="wrapper">
            <?php include 'components/sidebar.php'; ?>

            <div id="page-wrapper">
                <br><br><br><br>
                <div class="row">
                    <div class="col-lg-3 col-md-6">
                        <div class="panel panel-primary">
                            <div class="panel-heading">
                                <div class="row">
                                    <div class="col-xs-3">
                                        <i class="fa fa-group fa-5x"></i>
                                    </div>
                                    <div class="col-xs-9 text-right">
                                        <div class="huge" id="merchant">0</div>
                                        <div>Pedagang Aktif</div>
                                    </div>
                                </div>
                            </div>
                            <a>
                                <div class="panel-footer">
                                    <span class="pull-left">Sekarang</span>
                                    <span class="pull-right"><i class="	fa fa-calendar-check-o"></i></span>
                                    <div class="clearfix"></div>
                                </div>
                            </a>
                        </div>
                    </div>
                    <div class="col-lg-3 col-md-6">
                        <div class="panel panel-yellow">
                            <div class="panel-heading">
                                <div class="row">
                                    <div class="col-xs-3">
                                        <i class="fa fa-balance-scale fa-5x"></i>
                                    </div>
                                    <div class="col-xs-9 text-right">
                                        <div class="huge" id="weighing">0</div>
                                        <div>Transaksi Timbang</div>
                                    </div>
                                </div>
                            </div>
                            <a>
                                <div class="panel-footer">
                                    <span class="pull-left"><?php echo date('M Y', strtotime($startDate)) . ' - ' . date('M Y', strtotime($endDate))?></span>
                                    <span class="pull-right"><i class="fa fa-calendar-check-o"></i></span>
                                    <div class="clearfix"></div>
                                </div>
                            </a>
                        </div>
                    </div>
                    <div class="col-lg-3 col-md-6">
                        <div class="panel panel-red">
                            <div class="panel-heading">
                                <div class="row">
                                    <div class="col-xs-3">
                                        <i class="fa fa-credit-card fa-5x"></i>
                                    </div>
                                    <div class="col-xs-9 text-right">
                                        <div class="huge" id="paymentDone">0</div>
                                        <div>Tonase Lunas</div>
                                    </div>
                                </div>
                            </div>
                            <a>
                                <div class="panel-footer">
                                    <span class="pull-left"><?php echo date('M Y', strtotime($startDate)) . ' - ' . date('M Y', strtotime($endDate))?></span>
                                    <span class="pull-right"><i class="fa fa-calendar-check-o"></i></span>
                                    <div class="clearfix"></div>
                                </div>
                            </a>
                        </div>
                    </div>
                    <div class="col-lg-3 col-md-6">
                        <div class="panel panel-green">
                            <div class="panel-heading">
                                <div class="row">
                                    <div class="col-xs-3">
                                        <i class="fa fa-credit-card fa-5x"></i>
                                    </div>
                                    <div class="col-xs-9 text-right">
                                        <div class="huge" id="paymentMitraDone">0</div>
                                        <div>Mitra Lunas</div>
                                    </div>
                                </div>
                            </div>
                            <a>
                                <div class="panel-footer">
                                    <span class="pull-left"><?php echo date('M Y', strtotime($startDate)) . ' - ' . date('M Y', strtotime($endDate))?></span>
                                    <span class="pull-right"><i class="fa fa-calendar-check-o"></i></span>
                                    <div class="clearfix"></div>
                                </div>
                            </a>
                        </div>
                    </div>
                </div>
                
                <div class="row">
                    <div class="col-lg-6 col-md-6">
                        <div class="panel" style="background-color:rgb(211, 57, 211); color: #fff;">
                            <div class="panel-heading">
                                <div class="row">
                                    <div class="col-xs-3">
                                        <i class="fa fa-group fa-5x"></i>
                                    </div>
                                    <div class="col-xs-9 text-right">
                                    <div class="huge" id="cashier">0</div>
                                        <div>Kasir Aktif</div>
                                    </div>
                                </div>
                            </div>
                            <a>
                                <div class="panel-footer">
                                    <span class="pull-left" style="color: rgb(211, 57, 211)">Sekarang</span>
                                    <span class="pull-right"><i class="fa fa-calendar-check-o" style="color: rgb(211, 57, 211);"></i></span>
                                    <div class="clearfix"></div>
                                </div>
                            </a>
                        </div>
                    </div>

                    <div class="col-lg-6 col-md-6">
                        <div class="panel" style="background-color:rgb(136, 57, 211); color: #fff;">
                            <div class="panel-heading">
                                <div class="row">
                                    <div class="col-xs-3">
                                        <i class="fa fa-group fa-5x"></i>
                                    </div>
                                    <div class="col-xs-9 text-right">
                                        <div class="huge" id="checker">0</div>
                                        <div>Checker Aktif</div>
                                    </div>
                                </div>
                            </div>
                            <a>
                                <div class="panel-footer">
                                    <span class="pull-left" style="color: rgb(136, 57, 211);">Sekarang</span>
                                    <span class="pull-right"><i class="fa fa-calendar-check-o" style="color: rgb(136, 57, 211);"></i></span>
                                    <div class="clearfix"></div>
                                </div>
                            </a>
                        </div>
                    </div>
                </div>
                
                <br>
                <div class="row">
                    <div class="col-lg-12">
                        <ul class="nav nav-tabs nav-justified">
                            <li class="active">
                                <a data-toggle="tab" href="#transaction"
                                style="background-color: #428bca; color: #ffffff;">Transaksi Timbang</a>
                            </li>
                            <li>
                                <a data-toggle="tab" href="#tonase"
                                style="background-color: transparent; color: #428bca;">Tagihan Tonase</a>
                            </li>
                            <li>
                                <a data-toggle="tab" href="#mitra"
                                style="background-color: transparent; color: #428bca;">Tagihan Mitra</a>
                            </li>
                        </ul>

                        <div class="tab-content">
                            <div id="transaction" class="tab-pane fade in active">
                                <br />
                                <div class="row">
                                    <div class="col-lg-6">
                                        <div id="Area1"><div class="alert alert-info" onclick="clearNotif()"><center><h3><i class="fa fa-spinner fa-spin"></i> Loading...</h3></center></div></div>
                                    </div>
                                    <div class="col-lg-6">
                                        <div id="Area2"><div class="alert alert-info" onclick="clearNotif()"><center><h3><i class="fa fa-spinner fa-spin"></i> Loading...</h3></center></div></div>
                                    </div>
                                </div>
                            </div>
                            <div id="tonase" class="tab-pane fade">
                                <br />
                                <div class="row">
                                    <div class="col-lg-6">
                                        <div id="Area3"><div class="alert alert-info" onclick="clearNotif()"><center><h3><i class="fa fa-spinner fa-spin"></i> Loading...</h3></center></div></div>
                                    </div>
                                    <div class="col-lg-6">
                                        <div id="Area4"><div class="alert alert-info" onclick="clearNotif()"><center><h3><i class="fa fa-spinner fa-spin"></i> Loading...</h3></center></div></div>
                                    </div>
                                </div>
                            </div>
                            <div id="mitra" class="tab-pane fade">
                                <br />
                                <div class="row">
                                    <div class="col-lg-6">
                                        <div id="Area5"><div class="alert alert-info" onclick="clearNotif()"><center><h3><i class="fa fa-spinner fa-spin"></i> Loading...</h3></center></div></div>
                                    </div>
                                    <div class="col-lg-6">
                                        <div id="Area6"><div class="alert alert-info" onclick="clearNotif()"><center><h3><i class="fa fa-spinner fa-spin"></i> Loading...</h3></center></div></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Lottie -->
        <script src="https://unpkg.com/lottie-web/build/player/lottie.min.js"></script>
        <script src="https://unpkg.com/@lottiefiles/lottie-player@latest/dist/lottie-player.js"></script>

        <style>
            #welcomeModal .modal-dialog {
                width: 600px;
                max-width: 90%;
            }
            #welcomeModal .modal-content {
                border-radius: 15px;
                border: none;
                text-align: center;
                padding: 25px;
            }

            /* Ikon dekoratif kiri-kanan */
            .float-icon {
                position: absolute;
                font-size: 90px;
                opacity: 0.85;
                animation: floaty 3s ease-in-out infinite;
                z-index: 10;
            }
            .float-left {
                top: -25px;
                left: -50px;
                transform: rotate(-15deg);
            }
            .float-right {
                top: -25px;
                right: -50px;
                transform: rotate(15deg);
            }

            /* Animasi lembut naik-turun */
            @keyframes floaty {
                0% { transform: translateY(0) rotate(0deg); }
                50% { transform: translateY(-8px) rotate(3deg); }
                100% { transform: translateY(0) rotate(0deg); }
            }
        </style>

        <?php if ($showWelcome): ?>
        <!-- Modal -->
        <div id="welcomeModal" class="modal fade" tabindex="-1" role="dialog">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <!-- Ikon float -->
                <div id="floatLeft" class="float-icon float-left"></div>
                <div id="floatRight" class="float-icon float-right"></div>

                <div style="display: flex; justify-content: center; align-items: center;">
                <lottie-player 
                    src="https://assets2.lottiefiles.com/packages/lf20_xlmz9xwm.json" 
                    background="transparent" 
                    speed="1" 
                    style="width:150px;" 
                    loop 
                    autoplay>
                </lottie-player>
                </div>
                <h3>👋 Halo, <strong><?php echo htmlspecialchars($checker); ?></strong></h3>
                <p id="welcomeMessage" class="text-muted"></p>
            </div>
        </div>
        </div>
        <?php endif; ?>

        <script>
        $(document).ready(function() {
        <?php if ($showWelcome): ?>

            var today = new Date();
            var m = today.getMonth() + 1;
            var d = today.getDate();
            var theme = "default";
            var greeting = "Semoga harimu menyenangkan dan lancar bekerja! 😊";
            let icon = "✨";

            if (m === 12 && d === 25) {
                theme = "christmas";
                greeting = "🎄 Selamat Natal! Damai dan sukacita menyertaimu!";
                icon = "🎄";
            } else if (m === 1 && d === 1) {
                theme = "newyear";
                greeting = "🎉 Selamat Tahun Baru! Semoga penuh keberkahan!";
                icon = "🎉";
            } else if (m === 8 && d === 17) {
                theme = "independence";
                greeting = "🇮🇩 Dirgahayu Republik Indonesia! Merdeka!";
                icon = "🇮🇩";
            } else if (m === 10 && d === 31) {
                theme = "halloween";
                greeting = "🎃 Selamat Hari Halloween!";
                icon = "🎃";
            } else if (m === 6 && d === 17) {
                theme = "iduladha";
                greeting = "🐄 Selamat Hari Raya Idul Adha!";
                icon = "🐄";
            } else if (m === 4 && d === 10) {
                theme = "idulfitri";
                greeting = "🌙 Selamat Idul Fitri! Mohon maaf lahir dan batin!";
                icon = "🌙";
            } else if (m === 3) {
                theme = "ramadhan";
                greeting = "🌙 Ramadhan Kareem! Selamat menunaikan ibadah puasa!";
                icon = "🌙";
            }

            $('#welcomeModal').on('shown.bs.modal', function () {
                // Bersihkan container dulu
                $('#lottieContainer').html('');
                $('#welcomeMessage').html(
                    `<span style="font-size:15px; line-height:1.6;">
                        📢 <b>Update baru:</b> <?php echo $titleUpdate; ?><br>
                        <span style="font-size:15px;">${greeting}</span>
                    </span>`
                );
            });

            // Set ikon float kiri & kanan
            document.getElementById("floatLeft").innerText = icon;
            document.getElementById("floatRight").innerText = icon;

            $('#welcomeModal').modal('show');
        <?php endif; ?>
        });
        </script>

        
        <!-- jQuery -->
        <script src="js/jquery.js"></script>
        <script src="js/jquery.min.js"></script>
        <!-- highcharts -->
        <script src="highcharts/js/highcharts.js"></script>
        <script src="highcharts/js/highcharts-more.js"></script>
        <!-- Bootstrap Core JavaScript -->
        <script src="js/bootstrap.min.js"></script>
        <!-- Metis Menu Plugin JavaScript -->
        <script src="js/plugins/metisMenu/metisMenu.min.js"></script>
        <!-- Custom Theme JavaScript -->
        <script src="js/sb-admin-2.js"></script>
        <script>
            //Modal Password
			$('#modalPassword').on('shown.bs.modal', function (e) {
				$("#notifArea").html(alertLoadProcess);
				$("#data_password").load("password.php",function(){clearNotif();});
			})
        </script>
        <script>
            $(document).ready(function(){
                $("#notifArea").html(alertLoadProcess);
                setTimeout('$("html").removeClass("js")',1000); 
                loadChart();
            });
            //Meload Chart
            function loadChart(){
                $("#notifArea").html(alertLoadProcess);
                $("#Area1").load("line_transaction.php",function(){clearNotif();});
                $("#Area2").load("line_outstanding.php",function(){clearNotif();});
                $("#Area3").load("line_revenue.php",function(){clearNotif();});
                $("#Area4").load("line_revenue_unpaid.php",function(){clearNotif();});
                $("#Area5").load("line_revenue_mitra.php",function(){clearNotif();});
                $("#Area6").load("line_revenue_unpaid_mitra.php",function(){clearNotif();});
            }
            function clearNotif(){$("#notifArea").html("");}
        </script>
        <script>
            function animateValue(id, start, end, duration, isCurrency = false) {
                let obj = document.getElementById(id);
                let range = end - start;
                let current = start;
                let increment = end > start ? Math.ceil(range / (duration / 10)) : -1;
                let stepTime = 10; // Kecepatan update dalam milidetik
                let timer = setInterval(function () {
                    current += increment;
                    if (current >= end) {
                        current = end;
                        clearInterval(timer);
                    }
                    obj.innerHTML = isCurrency ? formatRupiah(current) : current;
                }, stepTime);
            }

            function formatRupiah(angka) {
                return angka.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ".") + " Staff";
            }

            $(document).ready(function () {
                animateValue("merchant", 0, <?php echo $merchant; ?>, 1500);
                animateValue("weighing", 0, <?php echo $weighing; ?>, 1500);
                animateValue("paymentDone", 0, <?php echo $paymentDone; ?>, 1500);
                animateValue("paymentMitraDone", 0, <?php echo $paymentMitraDone; ?>, 1500);
                animateValue("cashier", 0, <?php echo $cashierStaff; ?>, 1500, true);
                animateValue("checker", 0, <?php echo $checkerStaff; ?>, 1500, true);
            });
        </script>
        <script>
            $(document).ready(function() {
                $('.nav-tabs li a').on('click', function() {
                    $('.nav-tabs li a').css({'background-color': 'transparent', 'color': '#428bca'}); // Reset semua tab
                    $(this).css({'background-color': '#428bca', 'color': '#ffffff'}); // Tab yang aktif
                });
            });
        </script>
    </body>
</html>