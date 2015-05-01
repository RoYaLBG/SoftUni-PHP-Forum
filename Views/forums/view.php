<?php /* @var $this \ANSR\View */ ?>

<script>
    $(document).ready(function () {
        $('#gotoforum').click(function () {
            var option = $('#forums option:selected').val();
            window.location = "<?=$this->url('forums', 'view', 'id');?>" + option;
        });
    });
    
    function searchByTag(tag) {
        $.post("<?= $this->url('topics', 'find'); ?>", {
            tag: tag
        }).done(function (response) {
            var json = $.parseJSON(response);
            if (json.success == 0) {
                $('#topics').html('<p class="noResults">No results found</p>')
            } else {
                $('#topics').html('<h2>Search results</h2>')
                $.each(json, function (i, item) {
                var href = "<a href='<?= HOST; ?>topics/view/id/" + item.id + "'>" + item.summary + "</a><br />";
                    $('#topics').append(href);
                })
            }
        });
    }
</script>

<section class="topics">
    <h2><?= $this->forum['name']; ?></h2>
    <table>
        <tr>
            <th>Topics</th>
            <th>Replies</th>
            <th>Views</th>
            <th>Last post</th>
        </tr>
        <?php foreach ($this->topics as $topic): ?>
            <?php $userInfo = $this->getFrontController()->getController()->getApp()->TopicModel->getLastAuthorInfo($topic['id']); ?>
            <?php $tags = $this->getFrontController()->getController()->getApp()->TopicModel->getTopicTags($topic['id']); ?>
            <tr>
                <td>
                    <a href="<?= $this->url('topics', 'view', 'id', $topic['id']); ?>"><?= htmlentities($topic['summary']); ?></a>
                    <br/>
                    <span>by <a
                            href="<?= $this->url('users', 'profile', 'id', $topic['user_id']); ?>"><?= htmlspecialchars($this->getFrontController()->getController()->getApp()->UserModel->getUsernameById($topic['user_id'])); ?></a></span>
                    <span>
                        <br />
                        Tags: 
                        <?php foreach ($tags as $tag): ?>
                            <a href="#" onclick="searchByTag('<?=htmlentities($tag['tag']);?>')"><?=htmlentities($tag['tag']);?></a> |
                        <?php endforeach; ?>
                    </span>
                </td>
                <td><?= $this->getFrontController()->getController()->getApp()->TopicModel->getPostsCount($topic['id']); ?></td>
                <td><?= $topic['views']; ?></td>
                <td>
                    by <a
                        href="<?= $this->url('users', 'profile', 'id', $this->getFrontController()->getController()->getApp()->UserModel->getIdByUsername($userInfo['username'])); ?>"><?= htmlentities($userInfo['username']); ?></a><br/>
                    <span><?= $userInfo['created_on']; ?></span>
                </td>
            </tr>
        <?php endforeach; ?>
    </table>
    <div class="topicOptions">
        <div class="topicButtons">
            <a class="button" href="<?= $this->url('topics', 'add', 'forumid', $this->forum['id']) ?>">New Topic</a>
            <a class="button" href="<?= $this->url('welcome', 'index'); ?>">Return to Index</a>
        </div>
        <div class="selectForums">
            <select id="forums">
                <?php foreach ($this->forums as $forum): ?>
                    <option value="<?= $forum['id']; ?>"><?= $forum['name']; ?></option>
                <?php endforeach; ?>
            </select>
            <button id="gotoforum">Go</button>
        </div>
    </div>

</section>

