<div class="parts">
  <div class="partsHeading"><h1>メンバー管理</h1></div>
  <?php op_include_pager_navigation($members, '@member_manage_index?page=%d', array('use_current_query_string' => true)) ?>
  <table width="100%">
    <thead>
      <tr><td></td><td>ID</td><td>名前</td><td>メールアドレス</td><td>ステータス</td><td>システム最終利用日</td><td></td></tr>
    </thead>
    <tbody>
<?php foreach ($members->getResults() as $member): ?>
      <tr>
        <td><input type="checkbox" name="member[select][]" value="<?php echo $member->getId() ?>" /></td>
        <td><?php echo $member->getId() ?></td>
        <td><?php echo $member->getName() ?></td>
        <td><?php echo $member->getEmailAddress() ?></td>
        <td>社員</td>
        <td><?php if ($member->getLastLoginTime()) : ?><?php echo date('y-m-d<b\r />H:i:s', $member->getLastLoginTime()) ?><?php endif; ?></td>
        <td></td>
      </tr>
<?php endforeach; ?>
    </tbody>
  </table>
</div>
