##Thesaurus of Job Titles Technical Details

The thesaurus_of_job_titles.sql file contains a version of the database in a MySql format.

The wn_s.pl file contains a version of the job title dictionary in a format used in WordNet's Prolog wn_s.pl file.  This is highly experimental and HAS NOT BEEN TESTED yet.  As I understand it, some of the open source search engines, such as Lucene and ElasticSearch, can use a file in this format as a thesaurus.

WARNING: This software is covered under the GPLv3 license.  Please read the License file before testing this system. I have tried to design the code to prevent someone from successfully hacking the system, but I cannot guarantee that my efforts will work 100% of the time. Putting the php file on a web server will create a risk that a hacker could attempt to use it to get into your systems. See my comments about SQL injection in the PHP file.
