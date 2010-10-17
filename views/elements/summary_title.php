<form method=post action="<?php echo "/{$this->_request->params['project']}/branch_redirect"; ?>">
<div class=gittitle>Summary :: 
<select name=branch>
<option selected value=>select a branch</option>
<?php foreach(array_keys($branches) as $br) : ?>
	<option value="<?php echo $br; ?>">"<?php echo $br; ?>"</option>
<?php endforeach; ?>
</select> or <select name=tag>
<option selected value=>select a tag</option>
<?php foreach(array_keys($tags) as $br) : ?>
<option value="<?php echo $br; ?>">"<?php echo $br; ?>"</option>
<?php endforeach; ?>
</select> and press <input type=submit name=branch_or_tag value=GO>
</div></form>
