<div>
<div class="textbox_ttl_large"><?php echo link_to('メンバー管理', '@member_manage_index') ?></div>
<?php use_helper('Javascript') ?>
<?php use_javascript('/opTenpoPlugin/js/hyperform-with-prototype.js') ?>
<?php use_stylesheet('/opMemberManagePlugin/css/hyperform-1.1.css', 'last') ?>
<style type="text/css">
.hyperform td {
  overflow: visible;
}
</style>

<?php echo javascript_tag('
$(function(){
// window.tenpo.Event.observe(window, "load", generateHyperform);
  $.ajax({
    url: "'.url_for('@member_manage_get_hyperform').'",
    type: "get",
    data: { is_new: "true"},
    dataType: "json",
    success: function(json) {
      $("#form-loading").hide();
      generateHyperform(json.data);
    },
    error: function(x, r, e){
      $("#form-loading").html("<li><span style=\"color: #FF0000\">[error]</span></li>");
    },
  });

});

var validator = {
  alphanumeric: {
    warning: \'Please enter only alphanumeric characters.\',
    regex  : \'/^[a-zA-Z0-9]+$/i\'
  },
  numbers: {
    warning: \'Please enter numbers only.\',
    regex  : \'/^[0-9]+$/i\'
  },
  email: {
    warning: \'メールアドレスを正しく入力してください。\',
    regex  : /^[0-9,A-Z,a-z][0-9,a-z,A-Z,_,\.,-]+@[0-9,A-Z,a-z][0-9,a-z,A-Z,_,\.,-]+\.(af|al|dz|as|ad|ao|ai|aq|ag|ar|am|aw|ac|au|at|az|bh|bd|bb|by|bj|bm|bt|bo|ba|bw|br|io|bn|bg|bf|bi|kh|cm|ca|cv|cf|td|gg|je|cl|cn|cx|cc|co|km|cg|cd|ck|cr|ci|hr|cu|cy|cz|dk|dj|dm|do|tp|ec|eg|sv|gq|er|ee|et|fk|fo|fj|fi|fr|gf|pf|tf|fx|ga|gm|ge|de|gh|gi|gd|gp|gu|gt|gn|gw|gy|ht|hm|hn|hk|hu|is|in|id|ir|iq|ie|im|il|it|jm|jo|kz|ke|ki|kp|kr|kw|kg|la|lv|lb|ls|lr|ly|li|lt|lu|mo|mk|mg|mw|my|mv|ml|mt|mh|mq|mr|mu|yt|mx|fm|md|mc|mn|ms|ma|mz|mm|na|nr|np|nl|an|nc|nz|ni|ne|ng|nu|nf|mp|no|om|pk|pw|pa|pg|py|pe|ph|pn|pl|pt|pr|qa|re|ro|ru|rw|kn|lc|vc|ws|sm|st|sa|sn|sc|sl|sg|sk|si|sb|so|za|gs|es|lk|sh|pm|sd|sr|sj|sz|se|ch|sy|tw|tj|tz|th|bs|ky|tg|tk|to|tt|tn|tr|tm|tc|tv|ug|ua|ae|uk|us|um|uy|uz|vu|va|ve|vn|vg|vi|wf|eh|ye|yu|zm|zw|com|net|org|gov|edu|int|mil|biz|info|name|pro|jp)$/i
  },
  url: {
    warning: \'URLを正しく入力してください。\',
    regex  : \'/^(%s)://(([a-z0-9-]+\.)+[a-z]{2,6}|\d{1,3}\.\d{1,3}\.\d{1,3}\.\d{1,3})(:[0-9]+)?(/?|/\S+)$/ix\'
  }
};

function generateHyperform(fields) {
  var form = window.formExampleBasic = new window.tenpo.Hyperform({
    formWidth: "100%",
    labelWidth: "120px",
    labelAlign: "right",
    validator: validator,
    submitLabel: "'.__('Edit').'",
    disableFormWhenSubmit: true,
    onSubmit: function(data) {
      $.ajax({
        url: "'.url_for('@member_manage_edit_post?is_new=true').'",
        type: "post",
        data: data,
        dataType: "json",
        success: function(json) {
          $("#form-message").removeClass("alert-error").addClass("alert-success").text("編集に成功しました。").show();
        },
        error: function(x, r, e){
          $("#form-message").removeClass("alert-success").addClass("alert-error").text("通信に失敗しました。再読み込みをしてください").show();
        },
      });
      console.log(data); 
    },
    fields: fields,
  });
  form.render("form-container");
}

') ?>

<div id="form-loading" style="width: 100%; height: 100px; text-align: center;"><?php echo op_image_tag('/opTenpoPlugin/image/indicator.gif', array('alt' => 'Octocat-spinner-16px')) ?></div>
<div id="form-message" class="alert" style="display: none;"></div>
<div id="form-container"></div>


</div>
