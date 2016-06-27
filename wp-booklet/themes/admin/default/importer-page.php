<div class="wrap">

	<h2>Importer</h2>
	
	<div class="importer-page-message updated success">
		<p><strong>Import successful.</strong></p>
	</div>
	
	<p>Import your WP Booklet 1.x booklets to WP Booklet 2. Click the button below to get started. Please keep this page open until the process completes.</p>

	<form method="post" id="wp-booklet2-import-form">
		<table class="form-table">
		
			<tbody>
				<tr valign="top">
					<th scope="row"><label for="wp-booklet-reimport">Remaining booklets</label></th>
					<td>
						<span class="remaining-count"><?php echo $remaining_booklets ?></span>
					</td>
				</tr>
			</tbody>
			
		</table>
		<p class="submit">
			<input type="submit" class="button-primary" value="Import" />
		</p>
	</form>
	
</div>
