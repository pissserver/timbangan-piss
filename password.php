<?php
	session_start();
	include "connection.php";
	
	// Menampilkan Isi
	$checker 	= $_SESSION['checker'];
	$query 		= mysql_query("select * from checker where checker_code = '".$_SESSION['checker']."'");
	while ($r = mysql_fetch_array($query)){
?>
<form class="form-horizontal" method="post" action="update_password.php?id=<?php echo $r['checker_id']; ?>" enctype="multipart/form-data">
	<table class="table">
		<tbody>
			<tr>
				<td style="border: none;">
					<b>Nama (Kode)</b><br>
					<input style="margin-top: 5px;" type="text" class="form-control" id="nopol" name="nopol" value="<?php echo $r['checker_name'].' ('.$r['checker_code'].')'; ?>" readonly> 
				</td width="70%">
			</tr>
			<tr>
				<td style="border: none;">
					<b>Password Lama</b><br>
					<input style="margin-top: 5px;" type="password" class="col-lg-10 form-control" id="pass" name="pass" placeholder="Password Lama"/>
				</td>
			</tr>
			<tr>
				<td style="border: none;">
					<b>Password Baru</b><br>
					<p><input style="margin-top: 5px;" type="password" class="col-lg-10 form-control" id="pass1" name="pass1" placeholder="Password Baru"/></p><br />
					<p><input style="margin-top: 5px;" type="password" class="col-lg-10 form-control" id="pass2" name="pass2" placeholder="Ulangi Password Baru"/></p>
				</td>
			</tr>
			<tr>
				<td style="border: none;">
					<button type="submit" class="btn btn-block btn-primary">Simpan</button>
				</td>
			</tr>
		</tbody>
	</table>
</form>
<?php } ?>