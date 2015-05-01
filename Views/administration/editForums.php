<h1 class="adminForumHeading">Forum Administration</h1>
<?php if (isset($this->error)): ?>
    <h2> <?= $this->error; ?> </h2>
<?php else: ?>
<section class="administration">
    <h2>Edit Forums</h2>
    <form action="" method="post">
        <select name="category" class="category">
            <?php foreach ($this->categories as $category): ?>
            <option value="<?= $category['id']; ?>" <?= ($category['id'] == $this->forum['category_id']) ? 'selected' : '';?>><?= $category['name']; ?></option>
            <?php endforeach; ?>
        </select>
        <input type="text" name="name" class="forum" value="<?= $this->forum['name']; ?>">
        <input type="submit" name="submit" value="edit"/>
    </form>
</section>
<?php endif; ?>