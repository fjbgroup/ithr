-- =============================================================
-- FJB System — Database Merge Script
-- Merges laravel_hrsys + walkie + it  →  fjb_system
--
-- HOW TO USE:
--   1. Create the new database first:
--         CREATE DATABASE fjb_system CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
--   2. Run Laravel migrations to create all tables:
--         php artisan migrate
--   3. Run this script in phpMyAdmin (select no default database)
--      or via CLI:
--         mysql -u root < database/sql/merge_to_fjb_system.sql
-- =============================================================

SET FOREIGN_KEY_CHECKS = 0;

-- ==========================
-- 1. COPY FROM laravel_hrsys
-- ==========================

INSERT INTO fjb_system.users (id, staff_no, name, email, password, role, department_id, position, company, is_active, created_at, updated_at, staff_id)
    SELECT id, staff_no, name, email, password, role, department_id, position, company, is_active, created_at, updated_at, staff_id FROM laravel_hrsys.users;
INSERT INTO fjb_system.departments          SELECT * FROM laravel_hrsys.departments;
INSERT INTO fjb_system.companies            SELECT * FROM laravel_hrsys.companies;
INSERT INTO fjb_system.staff (id, staff_no, name, position, department_id, company, company_id, email, date_joined, date_of_birth, ic_number, employment_status, last_promotion_date, gender, location, compensation_grade, management_level, job_level, job_category, is_active, phone_number, created_at, updated_at)
    SELECT id, staff_no, name, position, department_id, company, company_id, email, date_joined, date_of_birth, ic_no, employment_status, last_promotion_date, gender, location, compensation_grade, management_level, job_level, job_category, is_active, phone_number, created_at, updated_at FROM laravel_hrsys.staff;
INSERT INTO fjb_system.meeting_rooms (id, name, description, capacity, color_class, created_at, updated_at)
    SELECT id, name, description, capacity, color_class, created_at, updated_at FROM laravel_hrsys.meeting_rooms;
INSERT INTO fjb_system.room_bookings (id, room_id, booked_by_id, booked_by_name, booking_date, start_time, end_time, is_full_day, purpose, attendees, status, approved_by_id, approved_by_name, approved_at, rejection_reason, cancel_reason, proposed_room_id, proposed_date, proposed_start_time, proposed_end_time, proposed_purpose, proposed_attendees, edit_reason, created_at, updated_at)
    SELECT id, room_id, booked_by_id, booked_by_name, booking_date, start_time, end_time, is_full_day, purpose, attendees, status, approved_by_id, approved_by_name, approved_at, rejection_reason, cancel_reason, proposed_room_id, proposed_date, proposed_start_time, proposed_end_time, proposed_purpose, proposed_attendees, edit_reason, created_at, updated_at FROM laravel_hrsys.room_bookings;
INSERT INTO fjb_system.room_pics (id, room_id, user_id, level, added_by, created_at, updated_at)
    SELECT id, room_id, user_id, level, added_by, created_at, updated_at FROM laravel_hrsys.room_pics;
INSERT INTO fjb_system.training_courses (id, code, title, training_type, company, start_date, end_date, venue, duration, is_private, created_at, updated_at)
    SELECT id, code, title, training_type, company, start_date, end_date, venue, duration, is_private, created_at, updated_at FROM laravel_hrsys.training_courses;
INSERT INTO fjb_system.training_attendances (id, staff_id, course_id, status, training_type, qr_token, qr_used_at, created_at, updated_at)
    SELECT id, staff_id, course_id, status, training_type, qr_token, qr_used_at, created_at, updated_at FROM laravel_hrsys.training_attendances;
INSERT INTO fjb_system.training_feedbacks   SELECT * FROM laravel_hrsys.training_feedbacks;
INSERT INTO fjb_system.notifications        SELECT * FROM laravel_hrsys.notifications;
INSERT INTO fjb_system.update_requests      SELECT * FROM laravel_hrsys.update_requests       WHERE EXISTS (SELECT 1 FROM information_schema.tables WHERE table_schema='laravel_hrsys' AND table_name='update_requests');
INSERT INTO fjb_system.family_members (id, staff_id, name, relationship, date_of_birth, id_no, emergency_contact, phone_number, created_at, updated_at, phone_country_code, phone_device_type, region_of_birth, occupation_effective_date)
    SELECT id, staff_id, family_member_name, relationship, date_of_birth, nric_no, emergency_contact, phone_number, created_at, updated_at, phone_country_code, phone_device_type, region_of_birth, occupation_effective_date FROM laravel_hrsys.family_members;
-- INSERT INTO fjb_system.staff_ir          SELECT * FROM laravel_hrsys.staff_irs     -- table does not exist in laravel_hrsys
-- INSERT INTO fjb_system.business_travel   SELECT * FROM laravel_hrsys.business_travels -- table does not exist in laravel_hrsys
INSERT INTO fjb_system.transport_modes      SELECT * FROM laravel_hrsys.transport_modes;
INSERT INTO fjb_system.activity_logs        SELECT * FROM laravel_hrsys.activity_logs;
-- INSERT INTO fjb_system.password_reset_tokens -- table does not exist in laravel_hrsys
-- INSERT INTO fjb_system.it_request_forms  SELECT * FROM laravel_hrsys.it_request_forms -- table does not exist in laravel_hrsys
INSERT INTO fjb_system.cache                SELECT * FROM laravel_hrsys.cache;
INSERT INTO fjb_system.jobs                 SELECT * FROM laravel_hrsys.jobs;

-- ==========================
-- 2. COPY FROM walkie
-- ==========================

-- wt_users  ←  walkie.users
INSERT INTO fjb_system.wt_users (user_id, staff_id, username, full_name, department, position, phone_no, password, role)
    SELECT user_id, staff_id, username, full_name, department, position, phone_no, password, role FROM walkie.users;

-- All other WT tables keep their names
INSERT INTO fjb_system.walkie_talkies           SELECT * FROM walkie.walkie_talkies;
INSERT INTO fjb_system.access_requests (id,user_id,request_type,accessory_request_mode,replacement_return_note,radio_id,walkie_inventory_id,assigned_walkie_inventory_ids,assigned_radio_ids,assigned_serial_number,assigned_serial_numbers,full_name,staff_id,request_date,end_date,department,position,ownership_type,shared_with,bay_from,accessories,submit_to_admin_id,handled_by,sector,location,event_name,quantity,duration_days,pic_details,pickup_method,pickup_representative_name,requested_pickup_at,pickup_note,justifications,status,approval_remark,return_date,return_person,return_department,return_phone_no,return_status,created_at)
    SELECT id,user_id,request_type,accessory_request_mode,replacement_return_note,radio_id,walkie_inventory_id,assigned_walkie_inventory_ids,assigned_radio_ids,assigned_serial_number,assigned_serial_numbers,full_name,staff_id,request_date,end_date,department,position,ownership_type,shared_with,bay_from,accessories,submit_to_admin_id,handled_by,sector,location,event_name,quantity,duration_days,pic_details,pickup_method,pickup_representative_name,requested_pickup_at,pickup_note,justifications,status,approval_remark,return_date,return_person,return_department,return_phone_no,return_status,created_at FROM walkie.access_requests;
INSERT INTO fjb_system.walkie_talkie_handovers (id,access_request_id,user_id,radio_id,walkie_talkie_id,staff_name,shared_with,staff_no,position,department,notes,issued_at,returned_at,created_at,updated_at)
    SELECT id,access_request_id,user_id,radio_id,walkie_talkie_id,staff_name,shared_with,staff_no,position,department,notes,issued_at,returned_at,created_at,updated_at FROM walkie.walkie_talkie_handovers;
INSERT INTO fjb_system.maintenance_records (maintenance_id,walkie_id,temporary_spare_walkie_id,temporary_spare_requested,temporary_spare_request_note,temporary_spare_assigned_at,temporary_spare_returned_at,original_returned_at,original_returned_by,radio_id,serial_number,model,current_ownership,department_name,received_date,ict_received_at,ict_received_by,repair_date,done,finish_date,issue_description,issue,remarks,maintenance_date,status,request_source,submit_to_admin_id,handled_by,reporter_name,reporter_staff_id,designation,phone_no,handover_person,handover_at,pickup_person,pickup_at,ownership_type,shared_with,sector,location,problem_possible,evidence_paths)
    SELECT maintenance_id,walkie_id,temporary_spare_walkie_id,temporary_spare_requested,temporary_spare_request_note,temporary_spare_assigned_at,temporary_spare_returned_at,original_returned_at,original_returned_by,radio_id,serial_number,model,current_ownership,department_name,received_date,ict_received_at,ict_received_by,repair_date,done,finish_date,issue_description,issue,remarks,maintenance_date,status,request_source,submit_to_admin_id,handled_by,reporter_name,reporter_staff_id,designation,phone_no,handover_person,handover_at,pickup_person,pickup_at,ownership_type,shared_with,sector,location,problem_possible,evidence_paths FROM walkie.maintenance_records;
-- INSERT INTO fjb_system.spare  -- walkie.spare table does not exist
INSERT INTO fjb_system.user_activity_logs       SELECT * FROM walkie.user_activity_logs;

-- wt_password_reset_requests  ←  walkie.password_reset_requests
INSERT INTO fjb_system.wt_password_reset_requests
SELECT * FROM walkie.password_reset_requests;

-- wt_notifications  ←  walkie.notifications
INSERT INTO fjb_system.wt_notifications
SELECT * FROM walkie.notifications;

-- ==========================
-- 3. COPY FROM fjb_inventory_laravel (IT database)
-- ==========================

-- it_users  ←  fjb_inventory_laravel.users
INSERT INTO fjb_system.it_users (id,username,password,full_name,email,role,department,dept_name,staff_id,avatar,signature_img,is_active,must_change_password,last_login,created_at,updated_at)
    SELECT id,username,password,full_name,email,role,department,dept_name,staff_id,avatar,signature_img,is_active,must_change_password,last_login,created_at,updated_at FROM fjb_inventory_laravel.users;

-- All other IT tables keep their names
-- INSERT INTO fjb_system.asset_groups  -- table does not exist in fjb_inventory_laravel
INSERT INTO fjb_system.asset_classes (id,name,type,sort_order)
    SELECT id,name,type,sort_order FROM fjb_inventory_laravel.asset_classes;
INSERT INTO fjb_system.inventory_items (id,asset_number,asset_class,description,serial_number,brand,model,location,condition_status,item_status,purchase_date,purchase_price,notes,created_by,fa_code,years_purchase,total_cost,accumulated,nbv_at,created_at,updated_at)
    SELECT id,asset_number,asset_class,description,serial_number,brand,model,location,condition_status,item_status,purchase_date,purchase_price,notes,created_by,fa_code,years_purchase,total_cost,accumulated,nbv_at,created_at,updated_at FROM fjb_inventory_laravel.inventory_items;
INSERT INTO fjb_system.ewaste_items         SELECT * FROM fjb_inventory_laravel.ewaste_items;
INSERT INTO fjb_system.ewaste_requests (id,type,requested_by,inventory_id,asset_number,asset_class,description,serial_number,notes,status,reviewed_by,reviewed_at)
    SELECT id,type,requested_by,inventory_id,asset_number,asset_class,description,serial_number,notes,status,reviewed_by,reviewed_at FROM fjb_inventory_laravel.ewaste_requests;
INSERT INTO fjb_system.disposal_items       SELECT * FROM fjb_inventory_laravel.disposal_items;
INSERT INTO fjb_system.non_it_assets (id,asset_number,asset_class,description,location,item_status,condition_status,notes,created_by,date_registered,fa_code,years_purchase,total_cost,accumulated,nbv_at,created_at,updated_at)
    SELECT id,asset_number,asset_class,description,location,item_status,condition_status,notes,created_by,date_registered,fa_code,years_purchase,total_cost,accumulated,nbv_at,created_at,updated_at FROM fjb_inventory_laravel.non_it_assets;
INSERT INTO fjb_system.add_asset_requests (id,requested_by,asset_number,asset_class,description,serial_number,brand,model,location,notes,status,reviewed_by,reviewed_at)
    SELECT id,requested_by,asset_number,asset_class,description,serial_number,brand,model,location,notes,status,reviewed_by,reviewed_at FROM fjb_inventory_laravel.add_asset_requests;
INSERT INTO fjb_system.delete_requests (id,inventory_id,requested_by,reason,asset_number,asset_class,asset_description,status,reviewed_by,reviewed_at)
    SELECT id,inventory_id,requested_by,reason,asset_number,asset_class,asset_description,status,reviewed_by,reviewed_at FROM fjb_inventory_laravel.delete_requests;
INSERT INTO fjb_system.edit_asset_requests (id,asset_type,asset_id,requested_by,status,asset_number,asset_class,fa_code,description,serial_number,brand,model,location,condition_status,purchase_date,purchase_price,years_purchase,total_cost,accumulated,nbv_at,notes,reviewed_by,reviewed_at)
    SELECT id,asset_type,asset_id,requested_by,status,asset_number,asset_class,fa_code,description,serial_number,brand,model,location,condition_status,purchase_date,purchase_price,years_purchase,total_cost,accumulated,nbv_at,notes,reviewed_by,reviewed_at FROM fjb_inventory_laravel.edit_asset_requests;
INSERT INTO fjb_system.email_settings       SELECT * FROM fjb_inventory_laravel.email_settings;

-- it_activity_log  ←  fjb_inventory_laravel.activity_log
INSERT INTO fjb_system.it_activity_log
SELECT * FROM fjb_inventory_laravel.activity_log;

-- it_notifications  ←  fjb_inventory_laravel.notifications
INSERT INTO fjb_system.it_notifications
SELECT * FROM fjb_inventory_laravel.notifications;

-- it_password_reset_requests  ←  fjb_inventory_laravel.password_reset_requests
INSERT INTO fjb_system.it_password_reset_requests (id,user_id,username,full_name,staff_id,status,resolved_at,resolved_by)
    SELECT id,user_id,username,full_name,staff_id,status,resolved_at,resolved_by FROM fjb_inventory_laravel.password_reset_requests;

-- it_request_forms  ←  fjb_inventory_laravel.it_request_forms
INSERT INTO fjb_system.it_request_forms SELECT * FROM fjb_inventory_laravel.it_request_forms;

SET FOREIGN_KEY_CHECKS = 1;

SELECT 'Migration complete. All data copied to fjb_system.' AS status;
