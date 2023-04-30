#!/usr/bin/python3
import os
import sys

# Print HTTP headers
print("Cache-Control: no-cache")
print("Content-type: text/html\r\n")
# Print HTML document
print("<html><head><title>General Echo</title></head>\
<body><h1 align=center>General Echo</h1>\
<hr/>")
envvars = os.environ
query_string = envvars['QUERY_STRING']
request_body = sys.stdin.read()
print("<p><b>Request Method:</b> " + envvars['REQUEST_METHOD'] + "</p>")
print("<p><b>Query String:</b> " + envvars['SERVER_PROTOCOL'] + "</p><h2>Query:</h2><ul>")
tokens = query_string.split("&")
if len(tokens) > 0:
    for token in tokens:
        split_token = token.split("=")
        print("<li><b>" + split_token[0] + ":</b> " + split_token[1] + "</li>")
print("</ul><h2>Message Body:</h2><ul>")
tokens = request_body.split("&")
if len(tokens) > 0:
    for token in tokens:
        split_token = token.split("=")
        print("<li><b>" + split_token[0] + ":</b> " + split_token[1] + "</li>")
# Print HTML footer
print("</ul></body></html>")