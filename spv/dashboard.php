<?php
	session_start();
	include "../connection.php";
	include "components/role_session.php";

    function headScript(){
		$headScript=file_get_contents("headScript.html");
		return $headScript;
	}

    $startDate          = date("Y-01-01");
    $endDate            = date("Y-m-d");

    $omzetAllStartDate  = date("Y-01-01"); // 1 Januari tahun ini
    $omzetAllEndDate    = date("Y-m-t", strtotime("last month")); // Akhir bulan lalu

    $omzetStartDate  = date("Y-m-01"); // Awal bulan ini
    $omzetEndDate    = date("Y-m-d");  // Hari ini

    $merchant 	        = executeScalar("SELECT COUNT(merchant_id) FROM merchant WHERE merchant_status = 'Aktif'");
    $weighing 	        = executeScalar("SELECT COUNT(weighing_id) FROM weighing WHERE DATE(date_check) BETWEEN '$startDate' AND '$endDate'");
    $paymentDone 	    = executeScalar("SELECT COUNT(weighing_id) FROM weighing WHERE weighing_sts = 'Lunas' AND date_pay BETWEEN '$startDate' AND '$endDate'");
    $paymentMitraDone 	= executeScalar("SELECT COUNT(weighing_id) FROM weighing_mitra WHERE weighing_sts = 'Lunas' AND date_pay BETWEEN '$startDate' AND '$endDate'");
    
    $omzetAll1           = executeScalar("SELECT SUM(cost_real_tonase) FROM weighing WHERE weighing_sts != 'Batal' AND date_pay BETWEEN '$omzetAllStartDate' AND '$omzetAllEndDate'");
    $omzetMitraAll1      = executeScalar("SELECT SUM(cost) FROM weighing_mitra WHERE weighing_sts != 'Batal' AND date_pay BETWEEN '$omzetAllStartDate' AND '$omzetAllEndDate'");
    $omzet1              = executeScalar("SELECT SUM(cost_real_tonase) FROM weighing WHERE weighing_sts != 'Batal' AND date_pay BETWEEN '$omzetStartDate' AND '$omzetEndDate'");
    $omzetMitra1         = executeScalar("SELECT SUM(cost) FROM weighing_mitra WHERE weighing_sts != 'Batal' AND date_pay BETWEEN '$omzetStartDate' AND '$omzetEndDate'");
    
    $omzetAll2           = executeScalar("SELECT SUM(netto) FROM weighing WHERE weighing_sts != 'Batal' AND date_pay BETWEEN '$omzetAllStartDate' AND '$omzetAllEndDate'");
    $omzetMitraAll2      = executeScalar("SELECT SUM(netto) FROM weighing_mitra WHERE weighing_sts != 'Batal' AND date_pay BETWEEN '$omzetAllStartDate' AND '$omzetAllEndDate'");
    $omzet2              = executeScalar("SELECT SUM(netto) FROM weighing WHERE weighing_sts != 'Batal' AND date_pay BETWEEN '$omzetStartDate' AND '$omzetEndDate'");
    $omzetMitra2         = executeScalar("SELECT SUM(netto) FROM weighing_mitra WHERE weighing_sts != 'Batal' AND date_pay BETWEEN '$omzetStartDate' AND '$omzetEndDate'");

?>
<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="description" content="">
        <meta name="author" content="">
        <link rel="icon" href="../img/icon.png">
        <link rel="shortcut icon" href="../img/icon.png" />
        <title>Dashboard Timbangan | PISS</title>

        <?php echo headScript(); ?>

        <script src="../js/jquery.min.js"></script>
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
                                    <div class="col-xs-2">
                                        <i class="fa fa-money fa-5x"></i>
                                    </div>
                                    <div class="col-xs-10 text-right">
                                    <div class="huge"><span id="omzetAll1">0</span> (<span id="omzetAll2" style="color:rgb(243, 255, 71);">0</span>)</div>
                                        <div>Total Omzet Tonase</div>
                                    </div>
                                </div>
                            </div>
                            <a>
                                <div class="panel-footer">
                                    <span class="pull-left" style="color: rgb(211, 57, 211)"><?php echo date('M Y', strtotime($omzetAllStartDate)) . ' - ' . date('M Y', strtotime($omzetAllEndDate))?></span>
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
                                    <div class="col-xs-2">
                                        <i class="fa fa-money fa-5x"></i>
                                    </div>
                                    <div class="col-xs-10 text-right">
                                        <div class="huge"><span id="omzetMitraAll1">0</span> (<span id="omzetMitraAll2" style="color:rgb(243, 255, 71);">0</span>)</div>
                                        <div>Total Omzet Mitra</div>
                                    </div>
                                </div>
                            </div>
                            <a>
                                <div class="panel-footer">
                                    <span class="pull-left" style="color: rgb(136, 57, 211);"><?php echo date('M Y', strtotime($omzetAllStartDate)) . ' - ' . date('M Y', strtotime($omzetAllEndDate))?></span>
                                    <span class="pull-right"><i class="fa fa-calendar-check-o" style="color: rgb(136, 57, 211);"></i></span>
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
                                    <div class="col-xs-2">
                                        <i class="fa fa-money fa-5x"></i>
                                    </div>
                                    <div class="col-xs-10 text-right">
                                    <div class="huge"><span id="omzet1">0</span> (<span id="omzet2" style="color:rgb(243, 255, 71);">0</span>)</div>
                                        <div>Omzet Tonase</div>
                                    </div>
                                </div>
                            </div>
                            <a>
                                <div class="panel-footer">
                                    <span class="pull-left" style="color: rgb(211, 57, 211)">Bulan ini</span>
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
                                    <div class="col-xs-2">
                                        <i class="fa fa-money fa-5x"></i>
                                    </div>
                                    <div class="col-xs-10 text-right">
                                        <div class="huge"><span id="omzetMitra1">0</span> (<span id="omzetMitra2" style="color:rgb(243, 255, 71);">0</span>)</div>
                                        <div>Omzet Mitra</div>
                                    </div>
                                </div>
                            </div>
                            <a>
                                <div class="panel-footer">
                                    <span class="pull-left" style="color: rgb(136, 57, 211);">Bulan ini</span>
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
        
        <!-- jQuery -->
        <script src="../js/jquery.js"></script>
        <script src="../js/jquery.min.js"></script>
        <script src="../js/jquery.colorbox.js"></script>
        <!-- highcharts -->
        <script src="../highcharts/js/highcharts.js"></script>
        <script src="../highcharts/js/highcharts-more.js"></script>
        <!-- Bootstrap Core JavaScript -->
        <script src="../js/bootstrap.min.js"></script>
        <!-- Metis Menu Plugin JavaScript -->
        <script src="../js/plugins/metisMenu/metisMenu.min.js"></script>
        <!-- Custom Theme JavaScript -->
        <script src="../js/sb-admin-2.js"></script>
        <script>
            //Modal Password
			$('#modalPassword').on('shown.bs.modal', function (e) {
				$("#notifArea").html(alertLoadProcess);
				$("#data_password").load("password.php",function(){clearNotif();});
			})
        </script>
        <script>
            function openColorBox(){
                $.colorbox({iframe:true, width:"80%", height:"80%", href: "modal_deadline.php"});
            }
            setTimeout(openColorBox, 500);
            $(document).ready(function(){
                $("#notifArea").html(alertLoadProcess);
                setTimeout('$("html").removeClass("js")',1000); 
                loadChart();
                
            });
            //Meload Chart
            function loadChart(){
                $("#notifArea").html(alertLoadProcess);
                $("#Area1").load("../line_transaction.php",function(){clearNotif();});
                $("#Area2").load("../line_outstanding.php",function(){clearNotif();});
                $("#Area3").load("../line_revenue.php",function(){clearNotif();});
                $("#Area4").load("../line_revenue_unpaid.php",function(){clearNotif();});
                $("#Area5").load("../line_revenue_mitra.php",function(){clearNotif();});
                $("#Area6").load("../line_revenue_unpaid_mitra.php",function(){clearNotif();});
            }
            function clearNotif(){$("#notifArea").html("");}
        </script>
        <script>
            function animateValue(id, start, end, duration, formatType = null) {
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
                    
                    if (formatType === "currency") {
                        obj.innerHTML = formatRupiah(current);
                    } else if (formatType === "kg") {
                        obj.innerHTML = formatKg(current);
                    } else {
                        obj.innerHTML = current.toLocaleString("id-ID");
                    }
                }, stepTime);
            }

            function formatRupiah(angka) {
                return "Rp " + angka.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ".");
            }

            function formatKg(angka) {
                return angka.toLocaleString("id-ID") + " Kg";
            }

            $(document).ready(function () {
                animateValue("merchant", 0, <?php echo $merchant; ?>, 1500);
                animateValue("weighing", 0, <?php echo $weighing; ?>, 1500);
                animateValue("paymentDone", 0, <?php echo $paymentDone; ?>, 1500);
                animateValue("paymentMitraDone", 0, <?php echo $paymentMitraDone; ?>, 1500);
                animateValue("omzetAll1", 0, <?php echo $omzetAll1; ?>, 1500, "currency");
                animateValue("omzetAll2", 0, <?php echo $omzetAll2; ?>, 1500, "kg");
                animateValue("omzetMitraAll1", 0, <?php echo $omzetMitraAll1; ?>, 1500, "currency");
                animateValue("omzetMitraAll2", 0, <?php echo $omzetMitraAll2; ?>, 1500, "kg");
                animateValue("omzet1", 0, <?php echo $omzet1; ?>, 1500, "currency");
                animateValue("omzet2", 0, <?php echo $omzet2; ?>, 1500, "kg");
                animateValue("omzetMitra1", 0, <?php echo $omzetMitra1; ?>, 1500, "currency");
                animateValue("omzetMitra2", 0, <?php echo $omzetMitra2; ?>, 1500, "kg");
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