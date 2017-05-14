# Using Synonyms in Elasticsearch

## Overview
The purpose of this section is to demonstrate how to include the thesaurus of job titles in Elasticsearch.  

## Current Issues / Warning
I have not figured out how to setup the thesaurus without changing permissions on the Elasticsearch configuration directory.  
Changing permissions might make it easier for an attacker to exploit your system.

## Demonstration
I assume you have Elasticsearch running on your local computer.

### Setup the synonyms file
Download the synonym-job-titles.txt from the Thesaurus-of-Job-Titles and save it in the $ES_HOME directory (/etc/elasticsearch).

I update the permissions the synonyms text file:

    sudo chown root:elasticsearch synonym_job_titles.txt
    sudo chmod 664 synonym_job_titles.txt







