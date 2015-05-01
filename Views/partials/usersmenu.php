<?php if (isset($this->user)): ?>
<li>
    <li><a href="<?= $this->url('users', 'profile', 'id', $this->user['id']); ?>">Users</a><span></span></li>
</li>
<?php endif; ?>