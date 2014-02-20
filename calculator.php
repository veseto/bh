<div id="calc">
<table>
	<thead>
		<tr>
			<td>#</td>
			<td> step </td>
			<td><input type="number" placeholder="odds"></td>
			<td><input type="number" placeholder="bet"></td>
			<td><input type="number" placeholder="profit"></td>
		</tr>
	</thead>
	<tbody>
		<?php
			for ($i=0; $i < 31; $i ++) {
				?>
				<tr>
					<td class="0 <?=$i ?>"><?=$i ?></td>
					<td>
						<select class="1 <?=$i ?>">
							<option value="-">-</option>
							<option value="1.1">1.1</option>
							<option value="1.2">1.2</option>
							<option value="1.3">1.3</option>
						</select>
					</td>
					<td><input class="2 <?=$i ?>" type="number" placeholder="odds"></td>
					<td><input class="3 <?=$i ?>" type="number" placeholder="bet"></td>
					<td><input class="4 <?=$i ?>" type="number" placeholder="profit"></td>
				</tr>
				<?php
			}
		?>
	</tbody>
</table>
</div>
<?php
		
?>

