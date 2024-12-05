<?php
include('header.php');
?>

<h2>Add Material</h2>
<hr>

<div id="response" class="alert alert-success" style="display:none;">
    <a href="#" class="close" data-dismiss="alert">&times;</a>
    <div class="message"></div>
</div>
				
<form method="post" id="add_product" enctype="multipart/form-data">
    <input type="hidden" name="action" value="add_product">	

    <div class="row">
        <div class="col-xs-12">
            <div class="panel panel-default">
                <div class="panel-heading">
                    <h4>Material Information</h4>
                </div>
                <div class="panel-body form-group form-group-sm">
                    <div class="row">
                        <div class="col-xs-4">
                            <label for="product_name">Product Name:</label>
                            <input type="text" class="form-control required" name="product_name" placeholder="Enter Material Name">
                        </div>
                        <div class="col-xs-4">
                            <label for="product_desc">Description:</label>
                            <input type="text" class="form-control required" name="product_desc" placeholder="Enter Material Description">
                        </div>
                        <div class="col-xs-4">
                            <label for="product_price">Price:</label>
                            <div class="input-group">
                                <span class="input-group-addon">₱</span> <!-- Peso sign -->
                                <input type="number" name="product_price" class="form-control required" placeholder="0.00" aria-describedby="sizing-addon1">
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-xs-12 margin-top">
                            <label for="product_image">Upload Image:</label>
                            <input type="file" id="product_image" name="product_image" class="form-control" accept=".jpg, .jpeg, .png, .gif">
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-xs-12 margin-top btn-group">
                            <input type="submit" id="action_add_product" class="btn btn-success float-right" value="Add Material" data-loading-text="Adding...">
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
</form>

<?php
include('footer.php');
?>
