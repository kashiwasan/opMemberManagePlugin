<div>
<div class="partsHeading"><h1>メンバー管理</h1></div>
<?php use_helper('Javascript') ?>
<?php use_javascript('/opMemberManagePlugin/js/hyperform.js') ?>
<?php use_stylesheet('/opMemberManagePlugin/css/hyperform.css', 'last') ?>
<?php echo javascript_tag('
Event.observe(window, "load", exampleBasic);
		//
		// sample script
		//
		function exampleBasic() {
			var form = window.formExampleBasic = new window.prototype.Hyperform({
				formWidth: "90%",
				labelWidth: "130px",
				labelAlign: "right",
				onSubmit: function(data) {
					console.log(data);
				},
				fields: '.json_encode($sf_data->getRaw('profileForm')).'
                        });
                        form.render("example-basic-container");
                }

') ?>

<div id="example-basic-container"></div>

</div>
