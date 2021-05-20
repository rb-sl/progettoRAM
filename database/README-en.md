# Application's database

## Creation of a new database instance
The database structure is contained in the [progettoRAM.sql](progettoRAM.sql) file. Some data is already set for:
* `admindata`: texts are set empty
* `datatype`: data types for Integer, Fractional and Step 5 are already present
* `grade`: grades and their color are set
* `grading`: contains the default grading table for the administrator
* `testtype`: some test types are present
* `unit`: most common units
* `user`: The `admin` user is set

Update at line `508` the `$PASSWORD` parameter to create the first user with administrator privileges; other information can be updated from the profile page.

To create the database it is sufficient to import the `progettoRAM.sql` file into MySQL.

## Model description
![ER model](../images/er_model.svg)

### User
The `user` table contains the data of the application's users, i.e.
* `user_id`: Identifier (PK)
* `username`: (UQ)
* `password`: The user's password, hashed with MD5
* `privileges`: The user's access level
* `granted_by`: Field used to know the privileges hierarchy
* `firstname`: Name to be displayed in the register section 
* `lastname`: Surname to be displayed in the register section 
* `email`: User contact
* `contact_info`: Administrator's further contact information
* `show_email`: Flag to determine if the user's (adminstrator) email is shown in the guide
* `last_password`: Date of the latest password change
* `last_login`: Most recent access
* `school_fk`: Foreign key for the user's school (Can be `NULL`)

### Grade
The `grade` table contains the grades that can be assigned by the system:
* `grade_id`: Identifier
* `grade`: Grade (UQ)
* `color`: The color used to code the grade and its associated percentiles or standard values 

### Grading
`grading` is the bridge table between `user` and `grade`; it associates each user to their grading table:
* `grading_id`: Identifier (PK)
* `user_fk`: User who owns the table
* `grade_fk`: Grade
* `percentile`: Percentile value assigned to the grade

The `user_fk` and `grade_fk` couple constitutes a Unique Key.

### School
The `school` table conatins information on schools in the system:
* `school_id`: Identifier (PK)
* `school_name` and `city`: To be displayed to users 

### Class
The system's classes are stored into the `class` table:
* `class_id`: Identifier (PK)
* `class`: Class number
* `section`: Sezione
* `class_year`: Scholastic year
* `user_fk`: Owner of the class
* `school_fk`: School of reference

The fields `class`, `section`, `class_year` and `school_fk` make a Unique Key.

### Student
The `student` table contains:
* `student_id`: Identifier (PK)
* `lastname`: Name to be displayed in the register
* `firstname`: Surname to be displayed in the register
* `gender`: Gender to be displayed in the register

### Instance
The `instance` table represents the bridge between `student` and `class`, connecting each student to all of their classes. It contains:
* `instance_id`: Identifier (PK)
* `student_fk`: Referenced student
* `class_fk`: Referenced class

`student_fk` and `class_fk` make a Unique Key.

### Results
`results` contains the test values of students:
* `result_id`: Identifier (PK)
* `date`: Date of the test execution
* `test_fk`: Reference to the executed test
* `instance_fk`: Instance of the student/class that executed the test
* `value`: Test result

A Unique Key is defined over `test_fk` and `instance_fk`.

### Test
* `test_id`: Identifier (PK)
* `test_name`: Test name (UQ)
* `positive_values`: Enum used to define if better test values correspond to greater values or vice-versa
* `datatype_fk`: Foreign key to the data type
* `testtype_fk`: Foreign key to the test type
* `unit_fk`: Foreign key to the unit of measurement
* `position`: Instructions for the student's position
* `equipment`: Instructions for the equipment to use in the test
* `execution`: Instructions for the test execution
* `suggestions`: Suggestions to the teacher
* `test_limit`: Description of the test limit
* `assessment`: Suggestions on how to assess the test (i.e. what to insert in the register)

### Data type
The `data_type` table contains the data types associated to the tests:
* `datatype_id`: Identifier (PK)
* `datatype_name`: Name appearing in the test descriptions
* `step`: Step of result values to validate the data 

### Test type
The `testtype` table describes the types of tests in the system:
* `testtype_id`: Identifier (PK)
* `datatype_name`: Test type name

### Unit
Inside `unit` are stored the system's units of measure:
* `unit_id`: Identifier (PK)
* `unit_name`: Unit name
* `symbol`: Unit symbol

### Favourites
The `favourites` table associates a user to their favourite tests; it contains:
* `user_fk`: Foreign key for the user
* `test_fk`: Foreign key for the test

Ihe two fields constitute the Primary Key.

### Admin data
The `admindata` table contains only one tuple and is used to take advantage of the concurrency properties of the database; it stores the texts that can be modified by the administrators for the description page and home announcements:
* `index_text`: Text inserted for the home announcement
* `index_compiled`: Home announcement text translated into HTML
* `project_text`: Text inserted for the project page
* `project_compiled`: Project text translated into HTML
* `data_id`: Identifier (PK)

## Trigger
In order to keep some properties of the database the following triggers are defined:
* `ADMINDATA_COUNT` and `ADMINDATA_ABORT_DELETE`: used to keep only one tuple in `admindata`
* `DELETE_ST_ON_NO_INST` and `DEL_ST_ON_NO_INST_UPDATE`: when all instances of a student are deleted, the tuple in `student` is delete too
* `NO_INST_SAME_YEAR` and `NO_INST_SAME_YEAR_UPDATE`: signal an error if a student is registered to more classes in the same year
