-- Update existing database to add contact details columns
USE lost_and_found;

-- Add contact details columns to items table
ALTER TABLE items 
ADD COLUMN contact_phone VARCHAR(20) NULL AFTER date,
ADD COLUMN contact_email VARCHAR(100) NULL AFTER contact_phone;

-- Verify the changes
DESCRIBE items; 