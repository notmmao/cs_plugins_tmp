<div class="wrap">

	<h2>Settings</h2>

	<h3 class="title">Server Environment</h3>
	
	<table class="form-table">
		<tbody>
			<tr valign="top">
				<th scope="row">Can your server convert PDF files?</th>
				<td>
					<?php if ( $actual_test ) : ?>
						<span style="color:green;font-weight:bold;font-size:16px;">Yes. You're all set.</span>
					<?php else: ?>
						<span style="color:red;font-weight:bold;font-size:16px;">No. Please contact your server administrator. The information below may help in troubleshooting the problem.</span>
					<?php endif ?>
				</td>
			</tr>
			<tr valign="top">
				<th scope="row">Ghostscript status</th>
				<td><?php echo $gs_status['message'] ?></td>
			</tr>
			<tr valign="top">
				<th scope="row">Imagemagick status</th>
				<td><?php echo $im_status['message'] ?></td>
			</tr>
			<tr valign="top">
				<th scope="row">PDFInfo status</th>
				<td><?php echo $pdfinfo_status ?></td>
			</tr>
			<tr valign="top">
				<th scope="row">Is uploads directory writable by web server?</th>
				<td><?php echo $writable ?></td>
			</tr>
		</tbody>
	</table>

</div>