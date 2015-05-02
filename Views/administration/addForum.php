<h1 class="adminForumHeading">Forum Administration</h1>
<section class="administration">
    <h2>Add Forums</h2>
    <form action="" method="post">
        <select name="category" class="category">
            <?php foreach ($this->categories as $category): ?>
            <option value="<?= $category['id']; ?>"><?= $category['name']; ?></option>
            <?php endforeach; ?>
        </select>
        <input type="text" name="name" class="forum" placeholder="Forum name">
        <?= $this->csrfValidator; ?>
        <input type="submit" name="submit" value="Add"/>
    </form>
</section>