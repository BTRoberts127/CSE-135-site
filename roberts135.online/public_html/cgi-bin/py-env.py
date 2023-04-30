#!/usr/bin/python3
import os

# Print HTTP headers
print("Cache-Control: no-cache")
print("Content-type: text/html\r\n")
# Print HTML document
print("<html><head><title>Environment Variables</title></head>\
<body><h1 align=center>Environment Variables</h1>\
<hr/><ul>")
envvars = os.environ
for key in envvars:
    print("<li><b>" + key + ":</b> " + envvars[key] + "</li>")
# Print HTML footer
print("</ul></body></html>")