<?php $project = (isset($this->_request->params['project'])) ? $this->_request->params['project'] : 'projectname'; ?>
<div id="git-help">
<table>
<tr><td>To clone: </td><td>git clone <?php echo $this->_config['http_method_prefix'] . $project; ?> yourpath</td></tr>
<tr><td>To communicate: </td><td><a href=<?php echo $this->_config['help_link']; ?>>Visit this page</a></td></tr>
</table>
</div>
