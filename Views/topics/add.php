<?php /** @var \ANSR\View $this */ ?>
    <script>
        function addTopic() {
            $.post("", {
                summary: $('#summary').val(),
                body: $('#body').val(),
                tags: $('#tags').val(),
                <?= $this->getCsrfJqueryData(); ?>
            }).done(function (response) {
                var json = $.parseJSON(response);
                if (json.success == 1) {
                    $("#response").html("<h1>Topic has been added successfully</h1>");
                    window.location = "<?= $this->url('topics', 'view', 'id');?>" + json.topic_id
                }
            });
        }
    </script>
    <div id="addTopic">
        <div id="response"><h2>New Topic</h2></div>
        <input type="text" name="summary" id="summary" placeholder="Summary"/>
        <textarea name="body" id="body" placeholder="Description"></textarea>
        <input type="text" id="tags" placeholder="tags"/>
        <?= $this->csrfValidator; ?>
        <button onclick="addTopic()">Add topic</button>
    </div>


