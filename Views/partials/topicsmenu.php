<?php if (isset($this->topic)): ?>

<?php $forum = $this->getFrontController()->getController()->getApp()->ForumModel->getForumById($this->topic['forum_id']); ?>
<li>
    <li><a href="<?= $this->url('forums', 'view', 'id', $this->topic['forum_id']); ?>"><?= $forum['name']; ?></a><span> -></span></li>
    <li><a href="<?= $this->url('topics', 'view', 'id', $this->topic['id']); ?>"><?= htmlentities($this->topic['summary']); ?></a><span></span></li>
</li>
<?php endif; ?>