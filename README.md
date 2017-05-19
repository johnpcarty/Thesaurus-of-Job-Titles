# Thesaurus of Job Titles
An open source thesaurus of job titles in US English

This thesaurus is not perfect, but I hope it is a good start.

There is a disconnect between how people search for jobs and how those jobs are posted.  People search for internships, but jobs are posted for interns.  This is the synonym problem.  It has a solution - a thesaurus.

### For Job Seekers and Recruiters
First things first, you can check out my site [www.EnlightenJobs.com](http://www.enlightenjobs.com) for an online version of the thesaurus.

If you are here for the data, the files you are probably looking for are the assigned_role.txt and the job_title_dictionary.txt.
- assigned_role.txt - contains my list of detailed occupations along with an abbreviated list of synonyms and searches.
- job_title_dictionary - contains my list of words and phrases found in job titles.

### For Developers, ATS's and Search Engines
For search engines, I built a synonyms file for Elasticsearch. Check the Elasticsearch folder for a demonstration.
- synonym_job_titles.txt - a file that contains job title synonyms in a Solr format.

Under the Developers folder, I publish the thesaurus as a database in a MySql format.

I developed a [job title generator](http://www.enlightenjobs.com/job-title-generator.php) that analyzes a job title, matches it to an Assigned Role, and suggests a better job title and keywords, if applicable.  Check out the Developers subfolder for more information.

### About the Thesaurus of Job Titles
Mission - Reduce frictional unemployment

Strategy - Improve the information flowing between recruiters and job seekers.  Improve how recruiters and job seekers create job postings and resumes/online profiles.  Improve how recruiters and job seekers search for candidates and jobs.

Economics says that [search frictions](http://www.kva.se/globalassets/priser/ekonomi/2010/sciback_ek_10.pdf), such as imperfect information, create frictional unemployment.  Information retrieval says that information provided in search results can be improved with a [thesaurus](http://nlp.stanford.edu/IR-book/html/htmledition/query-expansion-1.html#sec:query-expansion).  Despite this, some of the most well known job and candidate search engines have not implemented a thesaurus.  

Let's create a thesaurus of job titles and a few tools to make it easy to use.  Hopefully, we can reduce unemployment in the long-term.

## A Note about Copyright
The list of synonyms cannot be copyrighted. Check out [Feist Publications, Inc., v. Rural Telephone Service Co.](https://en.wikipedia.org/wiki/Feist_Publications,_Inc.,_v._Rural_Telephone_Service_Co.) for more information.

The programming code in this project will be covered under GPLv3.
