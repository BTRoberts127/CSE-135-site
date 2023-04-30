#!/usr/bin/python3
import os
import datetime
import json

# Print HTTP headers
print("Cache-Control: no-cache")
print("Content-type: application/json\r\n")
# Print JSON string
content = {
    'title' : 'Hello, Python!',
    'heading' : 'Hello, Python!',
    'message' : 'This page was generated with the Python programming language',
    'time' : str(datetime.datetime.now()),
    'IP' : os.environ['REMOTE_ADDR']
    }
print(json.dumps(content))