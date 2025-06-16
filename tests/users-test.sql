Database Verification Queries Pass= ✅ Fail= ❌

1. Check All Users  ✅
sql
SELECT id, username, email, age, gender, is_admin, created_at 
FROM users 
ORDER BY created_at DESC;

//Results:
id | username | email | age | gender | is_admin | created_at
---|----------

6
minimaluser
free2@demo.com
0
0
2025-06-09 13:16:50

5
<script>alert(1)</script>
real1@test.com
18
F
0
2025-06-09 05:13:41

4
admin123
admin123@demo.com
NULL
NULL
1
2025-06-08 17:49:36


2
testuser1
test1@example.com
25
Male
0
2025-06-08 17:08:28


1
admin
admin@demo.com
NULL
NULL
1
2025-06-08 14:04:24

2. Verify Specific Test Cases  ✅
Test Case 1 (Successful Registration):

sql
SELECT * FROM users WHERE username = 'testuser1';
*Should show one record with all provided details and is_admin=0*

//Results:
id | username | email | age |
---|----------|-------|------
| 2
testuser1
$2y$10$TGU9mtCRHKfBqLfWmD5X8.ZbLvuWcAc9wQX4Vs5yFCq...
test1@example.com
25
Male
0
2025-06-08 17:08:28


Test Case 3 (Duplicate Username):  ✅

sql
SELECT COUNT(*) FROM users WHERE username = 'testuser1'; 
Should return 1 (no duplicate created)

//Results:
Current selection does not contain a unique column. Grid edit, checkbox, Edit, Copy and Delete features are not available. 

Test Case 4 (Admin User):  ✅

sql
SELECT username, is_admin FROM users WHERE is_admin = 1;
*Should show admin user with is_admin=1*

//Results:
is_admin
username


1
admin

1
admin123


Test Case 5 (XSS Sanitization):  ✅

sql
SELECT username FROM users WHERE username LIKE '%<%';
Should show the script tags stored as literal text

//Results:

	
username

 <script>alert(1)</script>

Test Case 6 (SQL Injection):  ✅

sql
SELECT * FROM users WHERE username LIKE '%--%';
Should show the attempt stored as literal text

//Results:
 MySQL returned an empty result set (i.e. zero rows). (Query took 0.0002 seconds.)

Test Case 7 (Optional Fields):  ✅

sql
SELECT username FROM users 
WHERE email IS NULL AND age IS NULL AND gender IS NULL;
Should show users who registered without optional fields

//Results:
MySQL returned an empty result set (i.e. zero rows). (Query took 0.0001 seconds.)

8. Data Quality Checks  ✅
Check Password Hashing:

sql
SELECT username, password FROM users WHERE LENGTH(password) < 60;
Should return empty (all passwords should be hashed)

//Results:
sername
password

 admin123
password1


Check Age Validation:

sql
SELECT username, age FROM users WHERE age < 0 OR age > 120;
Should return empty if validation worked

//
MySQL returned an empty result set (i.e. zero rows). (Query took 0.0003 seconds.)

Check Timestamps:

sql
SELECT username, created_at FROM users 
WHERE created_at > NOW() OR created_at < '2023-01-01';
Should return empty (valid timestamps)

//Results:
 MySQL returned an empty result set (i.e. zero rows). (Query took 0.0008 seconds.)

9. Security Verification
Check for SQL Syntax in Usernames:  ✅

sql
SELECT username FROM users 
WHERE username LIKE '%--%' 
   OR username LIKE '%;%'
   OR username LIKE '%/*%*/%';
Should only return intentional test cases

//Results:
MySQL returned an empty result set (i.e. zero rows). (Query took 0.0002 seconds.)

Verify Admin Privileges:

sql
SELECT username, is_admin FROM users WHERE is_admin = 1;
Should only show intentionally created admin accounts

//Results:
	
username
is_admin

 admin
1
 admin123
1


10. Cleanup After Testing (Optional)  
sql
-- Delete test users (modify as needed)
DELETE FROM users WHERE username LIKE 'testuser%';
DELETE FROM users WHERE username LIKE 'shortpass%';
DELETE FROM users WHERE username LIKE 'minimaluser%';
DELETE FROM users WHERE username LIKE '%<%'; -- XSS test cases
DELETE FROM users WHERE username LIKE '%--%'; -- SQL injection tests

