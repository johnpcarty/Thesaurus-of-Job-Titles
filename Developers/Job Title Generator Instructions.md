#Enabling the Job Title Generator

WARNING: This software is covered under the GPLv3 license. Please read the License file before testing this system. I have tried to design the code to prevent someone from successfully hacking the system, but I cannot guarantee that my efforts will work 100% of the time. Putting the php file on a web server will create a risk that a hacker could attempt to use it to get into your systems. See my comments about SQL injection in the PHP file.

I developed a system that analyzes a job title, matches it to an Assigned Role, and suggests a better job title, if applicable.  You can check out a [prototype job title generator](http://www.enlightenjobs.com/job-title-generator.php) on my site.  These are the instructions to set it up on your site (requires PHP and MySql.)

This sets up two stored procedures to analyze the job titles. The spTagJobTitles stored procedure is used to analyze multiple job titles and store the results in the "given_job_titles" table.  The spEvaluateJobTitle stored procedure is used to evaluate one job title and returns the results in a Select statement.  The open_source_job_title_generator.php calls the spEvaluateJobTitle stored procedure and displays the results to the user.

WARNING: This software is covered under the GPLv3 license.  Please read the License file before testing this system. I have tried to design the code to prevent someone from successfully hacking the system, but I cannot guarantee that my efforts will work 100% of the time. Putting the php file on a web server will create a risk that a hacker could attempt to use it to get into your systems. See my comments about SQL injection in the PHP file.

1. Create a MySql schema called "thesaurus_of_job_titles"
2. Create a MySql user with a strong password.
  - Give the user Execute permission on the "thesaurus_of_job_titles" schema
3. Import the data and stored procedures into your MySql server
  - Import the thesaurus_of_job_titles.sql into the "thesaurus_of_job_titles" schema
4. Update the spTagJobTitles stored procedure.
  - I include a stored procedure called spTagJobTitles to show how you can analyze a job title
    with the thesaurus.  
  - The spTagJobTitles stored procedure processes job titles listed in the given_job_titles table.
    You must update and insert statement in the stored procedure to fill the given_job_titles table
    before spTagJobTitles processes the table.
  - It expects a Job Identifier that is an mysql bigint.  If you have an alphanumeric identifier,
    you'll have to tweak the given_job_titles table and the spTagJobTitles stored procedure.
5. Test the spTagJobTitles stored procedure
  - Connect to the MySql database server 
  - Run "call spTagJobTitles;"
6. Update the open_source_job_title_generator.php file.
  - Fill in the database connection information in the open-source-job-title-generator.php.
  - Update the relative path and file name variable with where you will place it on your site.
7. Test open_source_job_title_generator.php
  - Open the web page in your web browser and test a job title.

