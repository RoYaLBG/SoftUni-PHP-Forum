<?php /* @var $this \ANSR\View */ ?>
<?php if ($this->loginRequired): ?>
<script>
    $(document).ready((function() {
        (function() {
            $("#loginButton").click(); 
        }());
    }))
    
</script>
<?php endif; ?>
<table class="mainTable">
    <tr>
        <th>
            <a href="#">Category</a>
        </th>
        <th>
            Topics
        </th>
        <th>
            Posts
        </th>
        <th>
            Last post
        </th>
    </tr>
    <?php foreach ($this->categories as $category): ?>
    <tr>
        <th colspan="4">
            <a href="#"><?=$category['name']; ?></a>
        </th>
    </tr>
        <?php foreach ($category['forums'] as $forum): ?>
        <?php $userInfo = $this->getFrontController()->getController()->getApp()->ForumModel->getLastAuthorInfo($forum['id']);?>
        <tr>
            <td>
                <a href="<?=$this->url('forums', 'view', 'id', $forum['id']);?>"> <?= $forum['name']; ?> </a>
            </td>
            <td>
                <?= $this->getFrontController()->getController()->getApp()->ForumModel->getTopicsCount($forum['id']); ?>
            </td>
            <td>
                <?= $this->getFrontController()->getController()->getApp()->ForumModel->getPostsCount($forum['id']); ?>
            </td>
            <td>
                by <a href="<?= $this->url('users', 'profile', 'id', $this->getFrontController()->getController()->getApp()->UserModel->getIdByUsername($userInfo['username']));?>"><?= htmlentities($userInfo['username']); ?></a><br/>
                <span><?= $userInfo['created_on']; ?></span>
            </td>
        </tr>
        <?php endforeach; ?>
    <?php endforeach; ?>
</table>