<?php /* @var $this \ANSR\View */ ?>
<script>
    var topicId = 0;
    function showForumTopics(){
        var topicDiv = document.createElement('div');
        topicDiv.setAttribute('id', 'topic' + topicId);
        document.getElementById('mainSection').innerHTML = "";
        topicDiv.innerHTML = "<?php foreach ($this->getFrontController()->getController()->getApp()->TopicModel->getTopics() as $topic):?>" +
            "<div class=\"topics\">" +
            "<a href=\"<?php $this->url('topics', 'view', 'id', $topic['id']);?>\"><?= $topic['summary']; ?> </a> [ <?= $topic['created_on']; ?> ]" +
            "</div>" +
            "<?php endforeach; ?>";
            document.getElementById('mainSection').appendChild(topicDiv);
            topicId++;
    }
</script>
<ul id="aside">
    <?php foreach ($this->getFrontController()->getController()->getApp()->CategoryModel->getCategories() as $category): ?>

        <li class="aside"><?php echo $category["name"]; ?>
            <ul>
               <?php foreach ($this->getFrontController()->getController()->getApp()->ForumModel->getForums() as $forum): ?>

               <li class="asideForums" onclick="showForumTopics()"><?php echo $forum["name"]; ?></li>
                <?php endforeach ?>
            </ul>
        </li>
    <?php endforeach ?>
</ul>

<div id="mainSection">
