#!/usr/bin/python3
import os

# Print HTTP headers
print("Cache-Control: no-cache")
print("Content-type: text/html\r\n")
# Print HTML document
print("<html><head><title>Get Echo</title></head>\
<body><h1 align=center>Get Echo</h1>\
<hr/>")
query_string = os.environ['QUERY_STRING']
print("<p><b>Query String:</b> " + query_string + "</p><ul>")
tokens = query_string.split("&")
for token in tokens:
    split_token = token.split("=")
    if len(split_token) == 2:
        print("<li><b>" + split_token[0] + ":</b> " + split_token[1] + "</li>")
# Print HTML footer
print("</ul></body></html>")