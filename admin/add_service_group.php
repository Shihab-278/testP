<form method="POST" action="add_service_group.php">
    <div class="mb-3">
        <label for="group_name" class="form-label">Group Name</label>
        <input type="text" name="group_name" class="form-control" id="group_name" required>
    </div>
    <div class="mb-3">
        <label for="description" class="form-label">Description</label>
        <textarea name="description" class="form-control" id="description"></textarea>
    </div>
    <button type="submit" class="btn btn-primary">Add Group</button>
</form>
