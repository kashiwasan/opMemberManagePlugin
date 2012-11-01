<?php slot('firstRow'); ?>
以下のメンバーを一括削除しますか？
<span style="color: #FF0000;">一度消すと元に戻せませんのでご注意ください。</span><br />

<ul>
<?php foreach ($members as $member): ?>
<li> <?php echo link_to($member->getName(), '@obj_member_profile?id='.$member->getId()) ?></li>
<?php endforeach; ?>
</ul>
<br />
<?php end_slot(); ?>

<?php

$op = array();
$op['title'] = 'メンバー一括削除確認画面';
$op['yes_url'] = url_for('@member_manage_all_delete');
$op['no_url'] = url_for('@member_manage_index');
$op['no_method'] = 'get';
$op['body'] = get_slot('firstRow');
$op['class'] = 'form';

op_include_yesno('memberManageAllDeleteForm', $csrfForm, $csrfForm, $op);

?>
