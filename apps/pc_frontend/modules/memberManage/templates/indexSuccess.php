<div class="parts">
  <div class="partsHeading"><h1>メンバー管理</h1></div>
  <?php op_include_pager_navigation($members, '@member_manage_index?page=%d', array('use_current_query_string' => true)) ?>
  <table style="width: 100%;">
    <thead>
      <tr><th style="width: 3%;"></th><th style="width: 3%;">ID</th><th style="width: 25%;">名前</th><th style="width: 30%;">メールアドレス</th><th style=" width: 9%;">ステータス</th><th style="width: 15%;">システム最終利用日</th><th style="width: 15%; text-align: center;"></th></tr>
    </thead>
    <tbody>
<?php foreach ($members->getResults() as $member): ?>
      <tr>
        <td><input type="checkbox" name="member[select][]" value="<?php echo $member->getId() ?>" /></td>
        <td><?php echo $member->getId() ?></td>
        <td><?php echo $member->getName() ?></td>
        <td><?php echo $member->getEmailAddress() ?></td>
        <td><?php if ($member->getIsLoginRejected()): ?>ログイン停止中<?php else: ?>アクティブ<?php endif ?></td>
        <td><?php if ($member->getLastLoginTime()) : ?><?php echo date('y-m-d<b\r />H:i:s', $member->getLastLoginTime()) ?><?php endif; ?></td>
        <td style="text-align: center;">
          <?php echo link_to(__('Edit'), '@member_manage_edit?id='.$member->getId()) ?> | 
          <?php echo link_to(__('Delete'), '@member_manage_delete_confirm?id='.$member->getId()) ?> |
          <?php echo $member->getIsLoginRejected() ? link_to('解除', '@member_manage_reject_confirm?id='.$member->getId()) : link_to('停止', '@member_manage_reject_confirm?id='.$member->getId()) ?>
        </td>
      </tr>
<?php endforeach; ?>
    </tbody>
  </table>
</div>
