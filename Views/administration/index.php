<?php /** @var \ANSR\View $this */ ?>
<?php $this->partial('adminheader.php'); ?>
    <div class="category">
        <a href="#" id="toggledCategoryAdd">Add category</a> | <a href="<?= $this->url('administration', 'addforum');?>">Add forum</a>
        <div id="categoryAddMenu" style="display:none">
            <input type="text" id="categoryName" placeholder="Category name [Press enter to submit]" />
        </div>
        <?php foreach ($this->forums as $forum): ?>
        <p><?= $forum['name']; ?></p>
        <div class="editButtons">
            <a href="#" onclick="deleteForum(<?=$forum['id'];?>)">Delete</a>
            <a href="<?= $this->url('administration', 'editforums', 'id', $forum['id']);?>">Edit</a>
        </div>
        <?php endforeach; ?>
    </div>
</section>

<script>    
    function deleteForum(forum_id) {
        $.post("<?= $this->url('forums', 'delete', 'id'); ?>" + forum_id , {
            <?= $this->getCsrfJqueryData(); ?>
        }).done(function (response) {
            var json = $.parseJSON(response);
            if (json.success == 1) {
                window.location = "";
            }
        });
    }
    
    $(document).ready(function() {
        $("#toggledCategoryAdd").click(function() {
            $("#categoryAddMenu").show();
        });
    
        $("#categoryName").keypress(function(e) {
            if (e.keyCode == 13) {
                $.post("<?= $this->url('administration', 'addcategory'); ?>", {
                    name: $("#categoryName").val(),
                    <?= $this->getCsrfJqueryData(); ?>
                }).done(function (response) {
                    var json = $.parseJSON(response);
                    if (json.success == 1) {
                        window.location = "";
                    }
                });
            }
        });
    });
</script>