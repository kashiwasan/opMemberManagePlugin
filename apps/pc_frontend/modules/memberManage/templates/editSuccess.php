<div>
<div class="partsHeading"><h1><?php echo link_to('メンバー管理', '@member_manage_index') ?></h1></div>
<?php use_helper('Javascript') ?>
<?php use_javascript('/opMemberManagePlugin/js/hyperform.js') ?>

<?php use_stylesheet('/opMemberManagePlugin/css/hyperform-1.1.css', 'last') ?>
<?php echo javascript_tag('
Event.observe(window, "load", exampleBasic);
		//
		// sample script
		//
		function exampleBasic() {
			var form = window.formExampleBasic = new window.prototype.Hyperform({
				formWidth: "100%",
				labelWidth: "120px",
				labelAlign: "right",
				onSubmit: function(data) {
                                  var submitData = "";
                                  for (var i in data){
                                    submitData = submitData + "" + i + "=" + data[i] + "&";
                                  }
                                  var xhr = new XMLHttpRequest();
                                  xhr.open("post", "'.url_for('@member_manage_edit_post?id='.$member->getId()).'", true);
                                  xhr.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
                                  xhr.setRequestHeader("Content-length", data.length);
                                  xhr.setRequestHeader("Connection", "close");
                                  xhr.send(submitData);
    xhr.onreadystatechange = function(){ 
      if ( xhr.readyState == 4 ) { 
        if ( xhr.status == 200 ) 
        { 
          //success
          alert("編集完了しました。");
        }
        else
        {
          //error
          alert("エラーが発生しました。");
        }
      }
    };

					console.log(data);
				},
				fields: '.json_encode($sf_data->getRaw('profileForm')).'
                        });
                        form.render("example-basic-container");
                }

') ?>

<div id="example-basic-container"></div>

</div>
