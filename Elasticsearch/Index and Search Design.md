# The Design of the Index and Search File
The purpose of this section is to explain the design of the index and search files.  

## TLDR;
The index file only recognizes job titles and also indexes any parent job titles and any relevant searches.

The search file processes job titles and job searches.  The job searches will only include the job openings
that include a matching job title.

The alternative search file is a more resource-intensive search that will expand a job search to 
look for any other job search synonyms and the any relevant job titles.

## The Index File - synonym_job_titles_for_index.txt
The index file only recognizes full job titles.  It does not recognize phrases that are only a part of a job title.
For example, let's say you have a job opening called "info security engineer".  The relevant line in my index would be:

    security engineer, information security engineer, cyber security engineer, security systems engineer, it security engineer, protection engineer, cybersecurity engineer, computer security engineer, security system engineer, is security engineer, data security engineer => security_engineer, cyber_security_jobs

Given a job opening called "info security engineer", the index file will recognize "security engineer".
It will then index it as both "security_engineer" and "cyber_security_jobs".

I found that if I included a line in the index for phrases like "info security", 
it could block the index from finding the job title "security engineer". 

When the index recognizes a job title, it also indexes any parent job titles or relevant job searches.  For example, a SQL Developer
is a type of Database Developer.  In addition, if someone is searching for SQL jobs, they would want a SQL Developer included in
their results.  When the index processes a "sql developer" job opening, 
the index file is setup to also index the parent job title "database developer"
and the relavent job search "sql jobs".

## The Search File - synonym_job_titles_for_search.txt
The search file processes job titles and job searches.  The job searches will only include the job openings
that include a matching job title.

The benefit of the search file is that it makes it easy for job search engines to include synonym search results.  
If a job search engine needs scalability, this method is less resource intensive than the alternative search file.

Unfortunately, there is a problem when the index cannot match any or all of the job titles in a given job opening.

Let's say recruiters posted two job openings.  One uses a job title and another only uses part of a job title:

    1. security engineer
    2. cyber security

The index file has one relevant line:

    security engineer, information security engineer, cyber security engineer, security systems engineer, it security engineer, protection engineer, cybersecurity engineer, computer security engineer, security system engineer, is security engineer, data security engineer => security_engineer, cyber_security_jobs

The index file would have recognized the the first job opening and indexed it as  " => security_engineer, cyber_security_jobs", 
but there is not a line for "cyber security" in the index.  
Elasticsearch would index the second job opening as two separate terms - cyber and security:

    1. security engineer => security_engineer, cyber_security_jobs
    2. cyber security

The search file includes two relevant lines:

    security engineer, information security engineer, cyber security engineer, security systems engineer, it security engineer, protection engineer, cybersecurity engineer, computer security engineer, security system engineer, is security engineer, data security engineer => security_engineer
    information security, cyber security, it security, cybersecurity, information systems security, computer security, info security, data security, internet security, computer systems security, is security, information technology security, database security => cyber_security_jobs

A job seeker searches for "Security Engineer".  The search file has Elasticsearch convert that search to "security_engineer".
Elasticsearch looks up "security_engineer" in the index and returns the first job opening.

Search Results:

    1. security engineer

Another job seeker searches for "Cyber Security" jobs.  The search file has Elasticsearch convert that search to "cyber_security_jobs".
Elasticsearch looks up "cyber_security_jobs" in the index and RETURNS THE FIRST job opening, BUT NOT THE SECOND job opening.

Search Results:

    1. security engineer


## The Alternative Search File - synonym_job_titles_for_search_alternative.txt
The alternative search file is a more resource-intensive search that will expand a job search to 
look for any other job search synonyms and the any relevant job titles.

The benefit of the alternative search file is that is that it provides better search results to job seekers.  

The problem is scalability.  It is resource intensive to search for all the the synonyms.

Let's continue with the example above, but use the alternative search file this time.

The search file includes two relevant lines:

    security engineer, information security engineer, cyber security engineer, security systems engineer, it security engineer, protection engineer, cybersecurity engineer, computer security engineer, security system engineer, is security engineer, data security engineer => security_engineer
    information security, cyber security, it security, cybersecurity, information systems security, computer security, info security, data security, internet security, computer systems security, is security, information technology security, database security, cyber_security_jobs

The security engineer line is the same.  The information security line does not contain a " => " and only contains commas.  

A job seeker searches for "Cyber Security" jobs.  
The alternative search file has Elasticsearch expand that search to include all the listed synonyms for cyber security 
and the term used when indexing relevant job titles.  Elasticsearch would search for "information security" and return nothing.
It would search for "cyber security" and find the second job opening.  
Eventually, Elasticsearch would look up "cyber_security_jobs" and it would find the first job opening.

Search Results:

    1. security engineer
    2. cyber security
