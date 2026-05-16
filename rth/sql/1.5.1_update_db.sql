ALTER table bugmonitor ADD Monitor CHAR(1) DEFAULT 'N';

ALTER TABLE project_user_assoc CHANGE email_new_bug email_new_bug CHAR(1) NOT NULL DEFAULT 'N';
ALTER TABLE project_user_assoc CHANGE email_update_bug email_update_bug CHAR(1) NOT NULL DEFAULT 'N'
ALTER TABLE project_user_assoc ADD email_assigned_bug CHAR(1) NOT NULL DEFAULT 'N' AFTER email_update_bug;
ALTER TABLE project_user_assoc ADD email_bugnote_bug CHAR(1) NOT NULL DEFAULT 'N'AFTER email_assigned_bug;
ALTER TABLE project_user_assoc ADD email_status_bug CHAR(1) NOT NULL DEFAULT 'N' AFTER email_bugnote_bug 