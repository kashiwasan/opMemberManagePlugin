<div id="deleteForm" class="dparts form">
<div class="parts">

<div class="partsHeading"><h3>メンバー削除確認</h3></div>
「<?php echo $member->getName() ?>」を削除しますか？<br />
<span style="color: #FF0000;">一度消すと元に戻せませんのでご注意ください。</span><br />
<?php include_partial('member/profileListBox', array('member' => $member)) ?>

<br />
<?php

$op = array();
$op['yes_url'] = url_for('@member_manage_delete?id='.$member->getId());
$op['no_url'] = url_for('@member_manage_index');
$op['no_method'] = 'get';

$op['class'] = 'form';

op_include_yesno('memberManageDeleteForm', $csrfForm, $csrfForm, $op);

?>

</div>
</div>
