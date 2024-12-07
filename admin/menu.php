<form action="admin_process_menu.php" method="POST">
    <label for="name">Menu Name:</label>
    <input type="text" id="name" name="name" required>
    
    <label for="url">URL:</label>
    <input type="text" id="url" name="url" required>
    
    <label for="parent_id">Parent Menu:</label>
    <select id="parent_id" name="parent_id">
        <option value="NULL">None (Top Level)</option>
    </select>

    <label for="position">Position:</label>
    <input type="number" id="position" name="position" required>

    <!-- Simple Submit button -->
    <button type="submit">Save</button>
</form>
