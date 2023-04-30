#!/usr/bin/python3
import os
import datetime
import json

# Print HTTP headers
print("Cache-Control: no-cache")
print("Content-type: application/json\r\n")
# Print JSON string
content = {
    'title' : 'Brandon copy of Hello, Perl!',
    'heading' : 'Hello, Perl!',
    'message' : 'This page was generated with the Perl programming language',
    'time' : str(datetime.datetime.now()),
    'IP' : os.environ['REMOTE_ADDR']
    }
print(json.dumps(content))