# Thesaurus of Job Titles Technical Details

The thesaurus_of_job_titles.sql file contains a version of the database in a MySql format.

This thesaurus is not perfect, but I hope it is a good start.  The relations between an assigned role and its parents and its related job searches needs some work.

## Tables
* **assigned_role** - lists detailed occupations and contains abbreviated lists of synonyms.
* **assigned_role_job_searches** - links an assigned role to its relevant job searches.
* **assigned_role_parent_child** - links an assigned role to one or more parent assigned roles. (Example: SQL Developer to Database Developer)
**assigned_role_parent_child_job_searches_calculated** - shows how an assigned role is linked to a relevant job searches and parent job titles. It is a table calculated from the assigned_role_job_searches and the assigned_role_parent_child tables.
**assigned_role_synonym_text_file_for_index** - used for building the index file.  It is calculated using spAssignedRole_Calculate_Synonym_Text_File_For_Index stored procedure.
**given_job_titles** - the job title generator can be run against this table to evaluate a given set of job openings
**job_title_dictionary** - lists phrases and links a job title to an assigned role.
**temp_job_title_dictionary_in_memory** - used in the job title generator.  It is stored in memory to speed up the evaluation.

## Views
**synonym_job_titles_for_index** - used to output the index file that can be used with Elasticsearch
**synonym_job_titles_for_search** - used ot output the search file that can be used with Elasticsearch.
**synonym_job_titles_for_search_alternative** - used to ouput the alternative search file that can be used with Elaticsearch.

## Stored Procedures
**spAssignedRole_Calculate_ParentChildJobSearches_For_Index** - this build the assigned_role_parent_child_job_searches_calculated table that maps an assigned role to all its parents and ancestors and those assigned roles to their related job searches.
**spAssignedRole_Calculate_Synonym_Text_File_For_Index** - this outputs assigned_role_synonym_text_file_for_index for use in the index file for Elasticsearch
**spEvaluateGivenJobTitle** - used in the job title generator to evaluate a given job title.
**spProcessJobTitle** - used in the job title generator to evaluate a given job title.
**spReloadJobTitleDictionaryIntoMemory** - used to load the temp_job_title_dictionary_in_memory table for use in the job title generator.
**spTagJobTitles** - used in the job title generator.

WARNING: This software is covered under the GPLv3 license.  Please read the License file before testing this system. I have tried to design the code to prevent someone from successfully hacking the system, but I cannot guarantee that my efforts will work 100% of the time. Putting the php file on a web server will create a risk that a hacker could attempt to use it to get into your systems. See my comments about SQL injection in the PHP file.
