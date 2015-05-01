    <?php /* @var $this \ANSR\View */ ?>
    <table class="online">
        <tr>
            <th>Username</th>
            <th>Last Updated</th>
            <th>Forum Location</th>
        </tr>
        <?php if (empty($this->users)): ?>
        <tr>
            <td>No registered users online</td>
        </tr>
        <?php else: ?>
        <?php foreach ($this->users as $user): ?>
        <tr>
            <td><a href="<?= $this->url('users', 'profile', 'id', $user['id']);?>"><?= $user['username']; ?></a></td>
            <td><?= $user['last_click']; ?></td>
            <td><a href="<?= $this->url(strtolower($user['controller']), $user['action']);?>"> <?= $user['page']; ?></a></td>
        </tr>
        <?php endforeach; ?>
        <?php endif; ?>
    </table>