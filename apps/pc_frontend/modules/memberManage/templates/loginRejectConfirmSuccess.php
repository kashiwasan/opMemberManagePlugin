<div id="OfferForm" class="dparts form">
<div class="parts">

<div class="partsHeading"><h3>ログイン停止確認</h3></div>
<?php if ($member->getIsLoginRejected()): ?>
「<?php echo $member->getName() ?>」をログイン解除しますか？<br />
<?php else: ?>
「<?php echo $member->getName() ?>」をログイン停止にしますか？<br />
<?php endif; ?>
<?php include_partial('member/profileListBox', array('member' => $member)) ?>

<br />
<?php

$op = array();
$op['yes_url'] = url_for('@member_manage_reject?id='.$member->getId());
$op['no_url'] = url_for('@member_manage_index');
$op['no_method'] = 'get';

$op['class'] = 'form';

op_include_yesno('memberManageLoginRejectForm', $csrfForm, $csrfForm, $op);

?>

</div>
</div>
