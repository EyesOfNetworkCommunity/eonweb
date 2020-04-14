ALTER TABLE users ADD theme VARCHAR(50);
INSERT ignore INTO configs VALUE ('theme', 'Classic-v5');
