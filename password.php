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
					<div class="password-wrapper" style="margin-top:5px;">
						<input type="password" class="form-control" id="pass" name="pass" placeholder="Password Lama" />
						<button type="button" class="toggle-password" onclick="togglePassword('pass', this)">
							<i class="fa fa-eye-slash text-primary"></i>
						</button>
					</div>
				</td>
			</tr>
			<!-- Password Baru -->
			<tr>
				<td style="border: none;">
					<b>Password Baru</b><br>
					<div class="password-wrapper" style="margin-top:5px;">
						<input type="password" class="form-control" id="pass1" name="pass1" placeholder="Password Baru" />
						<button type="button" class="toggle-password" onclick="togglePassword('pass1', this)">
							<i class="fa fa-eye-slash text-primary"></i>
						</button>
					</div>
				</td>
			</tr>

			<!-- Ulangi Password Baru -->
			<tr>
				<td style="border: none;">
					<b>Ulangi Password Baru</b><br>
					<div class="password-wrapper" style="margin-top:5px;">
						<input type="password" class="form-control" id="pass2" name="pass2" placeholder="Ulangi Password Baru" />
						<button type="button" class="toggle-password" onclick="togglePassword('pass2', this)">
							<i class="fa fa-eye-slash text-primary"></i>
						</button>
					</div>
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

<script>
	function togglePassword(id, btn) {
		const input = document.getElementById(id);
		const icon = btn.querySelector('i');

		if (input.type === "password") {
			input.type = "text";
			icon.classList.remove("fa-eye-slash");
			icon.classList.add("fa-eye");
		} else {
			input.type = "password";
			icon.classList.remove("fa-eye");
			icon.classList.add("fa-eye-slash");
		}
	}
</script>