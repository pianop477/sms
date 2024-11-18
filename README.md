# SHULE APP SYSTEM DOCUMENTATION

## INTRODUCTION

Shule App is a comprehensive school management system tool designed to streamline and manage essential school-related information. this program focuses on providing a centralized platform to store, access, and manage basic details about students, teachers, and other stakeholders.

## KEY FEATURES

- Student information management: maintain up-to-date records of students, including their personal details, academic performance and attendance.
- Teachers profiles: organize information about teachers, such as qualification, schedules and assigned classes.
- Administrative Efficiency: enable quick access to essential school data to improve operational efficiency.
- user authentication
- Real-time updates
- Mobile friendly design

The system is built with simplicity in mind, ensuring ease of use while supporting the core needs of educational institutions.

## USAGE

Before you run this program make sure you have installed composer on your window pc and xampp, run database migration using command "php artisan migrate --seed"

- navigate to your program files then open with command prompt then run this command "php artisan serve"
- the command will execute and provide a link as shown here below
  ![image](https://github.com/user-attachments/assets/7daea91d-ef3f-433c-9c82-98c1dd7a55ff)
- copy the link provided and then paste to your browser, once you paste your program will run in the browser as shown below;
![image](https://github.com/user-attachments/assets/bef3e37b-c7a7-44a4-aac6-78688f16507f)
this is the welcome page where the program starts.

- click get started now button and the login page will appear or click sign up to register as a new parent.
  ![image](https://github.com/user-attachments/assets/c9ece1e5-2089-4d56-b1c5-fda147dd185d)
  login page
  ![image](https://github.com/user-attachments/assets/a9539ae4-fbb8-4fc2-b127-9f77e90b33bc)
  parent registration form
  - At this form (parents registration form) allow parents to register themselves, this will enable school to get required information for a short time since every parent will register and access the system.

## USER CONTROL MANAGEMENT

Since the program is designed using Php - Laravel framework, during installation it allow you to make database migration. use this code to make migration as php artisan migrate --seed, this command migrate all tables and seed the default records which will be used by the super user admin account with the following credentials
<<<<<<< HEAD
email: <pianop477@gmail.com>
=======
email: pianop477@gmail.com
>>>>>>> d977b076efde862c6dcddcbb060d523665b7e155
password: shule@2024

## ROLES AND PERMISSION

There are several roles which have different Permission depending to its account type as follows;
Account types;

1. Admin account - indicated by usertype = 1
2. Manager account - indicated by usertype = 2
3. Teacher account - indicated by usertype = 3
4. Parent account - indicated by usertype = 4

## ADMIN ACCOUNT

- This is a super user account which manage the whole system
These are the request which handled by this account types

1. create/register managers accounts
2. register schools - if there is new
3. Manage schools (perform CRUD operations)
4. Manage other accounts and change their roles and account types
5. Generate school payment invoices

## MANAGER ACCOUNTS

This account handle the following request as follows;

1. perform CRUD operation for teachers - manage teachers at school level
2. perform CRUD operation for parents - manage parents at school level
3. perform CRUD operation for students - manage students at school level
4. manage attendance - view the school attendance by each class
5. manage results - view school results by each class
6. manage users permission and assign new roles to users at school level
7. manage school bus details

## TEACHERS ACCOUNTS

At this account type there are different roles assigned to teachers as follow;
1 - Normal teacher, 2 -  Head teacher, 3 - Academic Teacher, 4 - Class teacher
this roles are assigned to Teachers accounts depending to their position they have. Allow me to describe the roles as follows

## NORMAL TEACHER

- This teachers account, its role is normally assigned as a teacher with no any other extra permission or access to the system.
- this role allow user to perform common activities which are always assigned to a normal teacher like manage results, insert new results and manage its course specifically assigned to this teacher.

## HEAD TEACHER

- This teacher accounts types handle request and serves as a super user account at school level for day to day activities.
- Can perform different roles same to MANAGER ACCOUNT but also there are some added features or roles added to this teachers account regarding that HEAD TEACHER manage day to day activities.

## ACADEMIC TEACHER

- The roles of this teachers account is to manage different tasks which specifically about academic issues and progress like;
  1. manage students
  2. manage teachers
  3. manage parents
  4. manage results
  5. manage attendance
  6. assign class teachers and assign class course to teacher
  7. register examinations types
  8. manage his or her teaching course

## CLASS TEACHER

- The roles of this account is to manage the assigned class
- manage attendance daily and send the attendance
- manage its assigned course and manage results
- generate attendance report daily/monthly

## PARENTS ACCOUNT

This account handle the following request/roles;

- can register his or her own children
- can modify her or his child information
- can track/ manage child attendance
- can track/manage child results and track his or her progress

## GENERAL FEATURES AND ROLES

- every user accounts can able to modify/update its profile information
- change password
- upload profile picture

## DEFAULT PASSWORD 

- At the level where user password account has been forgoten, password can be resetted to the previous version as "shule@2024" >> this is our default password once account password reset or your account has been registered by either manager or head teacher.
- the above password is used as a default account to all account types from admin, manager, teacher even parents. and once the user password has reset the default password will be used.

## DATA EXPORT

- the system allow you to export data from the web browser to excel or pdf like results, attendance, students records, teachers records and school bus details.

## UNAUTHORIZED USER ACCESS

This page is displayed once if user want to perform a certain request which the role is not assigned to that account type or roles. This is done by the following middlewares;

1. auth middleware - which checks for user authentication
2. checkUsertype middleware - which checks for usertype either admin, manager, teacher or parent
3. managerOrTeacher middleware - checks for access to be performed by either head teacher or manager, they share some of the roles
4. general middleware - checks for the roles which will be performed by either head teacher, academic teacher

In general the middleware set rules for user to access a certain request in this program, and once the validation for this middleware fails it return error as show here below;
![image](https://github.com/user-attachments/assets/d47c94c5-860c-45cc-881b-d0ad2015ad4e)

## CONTACT

For any suggestion you can contact me through the following alternatives;

1. Phone +255 678 669 000
2. Email: <pianop477@gmail.com>

The program designed and developed by Piano.
<<<<<<< HEAD
Thank you.
=======
### Thank you.



>>>>>>> d977b076efde862c6dcddcbb060d523665b7e155
