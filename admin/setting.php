<form method="post" action="update_settings.php">
    <label for="telegram_bot_token">Telegram Bot Token:</label>
    <input type="text" id="telegram_bot_token" name="telegram_bot_token" value="<?php echo htmlspecialchars($botToken); ?>" required>
    
    <label for="telegram_chat_id">Telegram Chat ID:</label>
    <input type="text" id="telegram_chat_id" name="telegram_chat_id" value="<?php echo htmlspecialchars($chatId); ?>" required>
    
    <button type="submit">Save Settings</button>
</form>
