# Using Synonyms in Elasticsearch

## Overview
The purpose of this section is to demonstrate how to include the thesaurus of job titles in Elasticsearch.  

## Current Issues / Warning
I have not figured out how to setup the thesaurus without changing permissions on the Elasticsearch configuration directory.  
Changing permissions might make it easier for an attacker to exploit your system.

Second, the results are questionable when job titles overlap each other.  When I initially setup the synonyms file, I included synonyms for cyber security.  I found that a search for "security engineer" did not include some jobs that that used the phrase "info security engineer".  I fixed some of the issues by removing phrases that are not job titles.  Another example of an overlap is for a "SQL DBA" and a "DBA."  I redesigned the synonyms text file so that when it indexes "SQL DBA", it will also add "DBA" to the index.

## Demonstration: Implementing a Thesaurus at Index Time
I assume you have Elasticsearch running on your local computer.

### Setup the synonyms file
Download the synonym_job_titles_for_index.txt and synonym_job_titles_for_search.txt files from the Thesaurus-of-Job-Titles and save them in the $ES_HOME directory (/etc/elasticsearch).

I update the permissions the synonyms text files:

    sudo chown root:elasticsearch synonym_job_titles_for_index.txt
    sudo chmod 664 synonym_job_titles_for_index.txt
    sudo chown root:elasticsearch synonym_job_titles_for_search.txt
    sudo chmod 664 synonym_job_titles_for_search.txt


### Check existing indexes
Ask Elasticsearch if it currently has any indexes.

    curl -XGET 'localhost:9200/_cat/indices?v&pretty'

If it already contains a jobs index, then proceed at your own risk.  You would need to delete the jobs index for this to work

    curl -XDELETE 'localhost:9200/jobs?pretty'

### Create a jobs index

I send a request to Elasticsearch to create a jobs index that uses the synonym_job_titles.txt file.
It contains mappings for a type called job that contains a field called job_title.

    curl -XPUT 'http://localhost:9200/jobs/?pretty' -H 'Content-Type: application/json'  -d '
    {
       "settings" : {
          "analysis" : {
             "filter" : {
                "my_job_title_filter_for_index" : {
                   "type" : "synonym",
                   "synonyms_path" : "synonym_job_titles_for_index.txt"
                },
                "my_job_title_filter_for_search" : {
                   "type" : "synonym",
                   "synonyms_path" : "synonym_job_titles_for_search.txt"
                }
             },
             "analyzer" : {
                "my_job_title_analyzer_for_index" : {
                   "filter" : [
                      "standard",
                      "lowercase",
                      "stop",
                      "my_job_title_filter_for_index"
                   ],
                   "type" : "custom",
                   "tokenizer" : "standard"
                },
                "my_job_title_analyzer_for_search" : {
                   "filter" : [
                      "standard",
                      "lowercase",
                      "stop",
                      "my_job_title_filter_for_search"
                   ],
                   "type" : "custom",
                   "tokenizer" : "standard"
                }
             }
          }
       },
       "mappings" : {
          "job" : {
             "properties" : {
                "job_title" : {
                   "type" : "text",
                   "analyzer" : "my_job_title_analyzer_for_index",
                   "search_analyzer" : "my_job_title_analyzer_for_search"
                }
             }
          }
       }
    }
    '


### Check existing indexes
Check if the jobs index was created successfully.

    curl -XGET 'localhost:9200/_cat/indices?v&pretty'


My results looked like this:

    health status index uuid                   pri rep docs.count docs.deleted store.size pri.store.size
    yellow open   jobs  hCU2hhdSR1GmFx61tiOZfw   5   1          0            0       591b           591b


### Download jobfeed-example.json
For the demonstration, download the jobfeed-example.json file.  I placed it into my user account's home directory.


### Load jobfeed-example.json into Elasticsearch
When I was testing a large file, I setup a shell script that would split the large file into smaller files and load them individually into Elasticsearch.

I placed these lines into a jobfeed-example.sh:

    #!/bin/sh

    # clean up previous runs
    rm /tmp/jobfeed_bulk*

    # split the main file into files containing 10,000 lines max
    split -l 100000 -a 10 jobfeed-example.json /tmp/jobfeed_bulk

    # send each split file
    BULK_FILES=/tmp/jobfeed_bulk*
    for f in $BULK_FILES; do
    #    curl -s -XPOST http://localhost:9200/_bulk --data-binary @$f
        curl -H "Content-Type: application/json" -XPOST 'http://localhost:9200/jobs/job/_bulk?pretty&refresh' --data-binary @$f
    done

I made the shell script executable:

    chmod 711 jobfeed-example.sh

I ran the script:

    ./jobfeed-example.sh

I check if the jobs index contains any documents.

    curl -XGET 'localhost:9200/_cat/indices?v&pretty'

### Query the jobs index
I search for database admin and get results with DBA and Database Administrator

    curl -XGET 'http://localhost:9200/jobs/job/_search?pretty' -H 'Content-Type: application/json' -d '
    {
       "query" : {
          "match_phrase" : {
             "job_title" : "database admin"
          }
       },
       "size" : 0
    }
    '

When I ran it, the query returned 35 results.

    {
      "took" : 8,
      "timed_out" : false,
      "_shards" : {
        "total" : 5,
        "successful" : 5,
        "failed" : 0
      },
      "hits" : {
        "total" : 25,
        "max_score" : 0.0,
        "hits" : [ ]
      }
    }

I search for SQL DBA and only get the SQL DBA results.

    curl -XGET 'http://localhost:9200/jobs/job/_search?pretty' -H 'Content-Type: application/json' -d '
    {
       "query" : {
          "match_phrase" : {
             "job_title" : "sql dba"
          }
       },
       "size" : 0
    }
    '



### Fix Permissions
If you changed the permissions on the /etc/elasticsearch directory, switch them back.


