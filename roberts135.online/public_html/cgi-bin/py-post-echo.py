#!/usr/bin/python3
import sys

# Print HTTP headers
print("Cache-Control: no-cache")
print("Content-type: text/html\r\n")
# Print HTML document
print("<html><head><title>Post Echo</title></head>\
<body><h1 align=center>Post Echo</h1>\
<hr/><ul>")
request_body = sys.stdin.read()
tokens = request_body.split("&")
for token in tokens:
    split_token = token.split("=")
    if len(split_token) == 2:
        print("<li><b>" + split_token[0] + ":</b> " + split_token[1] + "</li>")
# Print HTML footer
print("</ul></body></html>")