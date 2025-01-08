<?php

include('header.php');
include('functions.php');

?>

<h1>Material List</h1>
<hr>

<div class="row">
	
	<div class="col-xs-12">

		<div id="response" class="alert alert-success" style="display:none;">
			<a href="#" class="close" data-dismiss="alert">&times;</a>
			<div class="message"></div>
		</div>
	
		<div class="panel panel-default">
			<div class="panel-heading">
				<div class="row">
					<div class="col-xs-12">
				    <a href="product-add.php" class="btn btn-primary float-right">Add Material</a>
						<h4>Material Information</h4>
					</div>
				</div>
			</div>
			<div class="panel-body form-group form-group-sm">
				<?php getProducts(); ?>
			</div>
		</div>
	</div>
<div>

<div id="confirm" class="modal fade">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title">Delete material</h4>
      </div>
      <div class="modal-body">
        <p>Are you sure you want to delete this material?</p>
      </div>
      <div class="modal-footer">
        <button type="button" data-dismiss="modal" class="btn btn-primary" id="delete">Delete</button>
		<button type="button" data-dismiss="modal" class="btn">Cancel</button>
      </div>
    </div><!-- /.modal-content -->
  </div><!-- /.modal-dialog -->
</div><!-- /.modal -->

<?php
	include('footer.php');
?>