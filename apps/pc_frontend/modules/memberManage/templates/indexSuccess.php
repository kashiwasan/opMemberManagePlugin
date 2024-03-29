<div class="parts">
  <div class="textbox_ttl_large">メンバー管理</div>
  <?php echo form_tag('@member_manage_all_delete_confirm') ?>
  <?php op_include_pager_navigation($members, '@member_manage_index?page=%d', array('use_current_query_string' => true)) ?>
  <table style="width: 100%;" class="table table-bordered">
    <thead>
      <tr><th style="width: 3%;"></th><th style="width: 3%;">ID</th><th style="width: 20%;">名前</th><th style="width: 24%;">メールアドレス</th><th style=" width: 10%;">ステータス</th><th style="width: 15%;">システム最終利用日</th><th style="width: 25%; text-align: center;"></th></tr>
    </thead>
    <tbody>
<?php foreach ($members->getResults() as $member): ?>
      <tr>
        <td><input type="checkbox" name="id[]" value="<?php echo $member->getId() ?>" /></td>
        <td><?php echo $member->getId() ?></td>
        <td><?php echo $member->getName() ?></td>
        <td><?php echo $member->getEmailAddress() ?></td>
        <td><?php if ($member->getIsLoginRejected()): ?>ログイン停止中<?php else: ?>アクティブ<?php endif ?></td>
        <td><?php if ($member->getLastLoginTime()) : ?><?php echo date('y-m-d<b\r />H:i:s', $member->getLastLoginTime()) ?><?php endif; ?></td>
        <td style="text-align: center;">
          <?php echo link_to(__('Edit'), '@member_manage_edit?id='.$member->getId(), array('class' => 'btn btn-success')) ?> | 
          <?php echo link_to(__('Delete'), '@member_manage_delete_confirm?id='.$member->getId(), array('class' => 'btn btn-warning')) ?> |
          <?php echo $member->getIsLoginRejected() ? link_to('解除', '@member_manage_reject_confirm?id='.$member->getId(), array('class' => 'btn btn-danger')) : link_to('停止', '@member_manage_reject_confirm?id='.$member->getId(), array('class' => 'btn btn-danger')) ?>
        </td>
      </tr>
<?php endforeach; ?>
      <tr><td colspan="7">選択したメンバーを一括で <input type="submit" name="submit" value="削除" class="btn btn-danger" /></td></tr>
    </tbody>
  </table>
  <?php echo link_to(__('New'), '@member_manage_new', array('class' => 'btn btn-inverse')) ?>
  </form>
</div>
