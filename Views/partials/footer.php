<?php /* @var $this \ANSR\View */ ?>
<?php $lastUserInfo = $this->getFrontController()->getController()->getApp()->UserModel->getLastRegisteredUser(); ?>
</main>
<footer>
    <section class="whoIsOnline">
        <a href="<?= $this->url('users', 'online'); ?>"><h3>Who is Online </h3></a>
        <a href="<?= $this->url('users', 'rankings'); ?>""><h3>Rankings</h3></a>
        <div>
            <p>Our users have posted <span><?= $this->getFrontController()->getController()->getApp()->TopicModel->getTopicsCount();?></span> articles</p>
            <p>The newest registered user is <a href="<?= $this->url('users', 'profile', 'id', $lastUserInfo['id']);?>"><?= htmlspecialchars($lastUserInfo['username']); ?></a>.</p>
        </div>
    </section>
    <?php if ($this->getFrontController()->getController()->getApp()->UserModel->isAdmin(isset($_SESSION['user_id']) ? $_SESSION['user_id'] : 0)): ?>
        <a class="adminButton" href="<?= $this->url('administration', 'index');?>"><h4>Go to Admin panel</h4></a>
    <?php endif; ?>
    <a href="https://github.com/RoYaLBG/ANSR_Framework"><h4>Powered By ANSR Framework</h4></a>
</footer>
</div>
</body>
</html>