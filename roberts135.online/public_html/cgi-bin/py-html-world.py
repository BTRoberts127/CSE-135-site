#!/usr/bin/python3
import os
import datetime

# Print HTTP headers
print("Cache-Control: no-cache")
print("Content-type: text/html\r\n")
# Print HTML document
print("<html><head><title>Hello CGI World</title></head>\
<body><h1 align=center>Brandon copy of Hello HTML World</h1>\
<hr/>")
print("Hello World<br/>\n")
print("This program was generated at: " + str(datetime.datetime.now()) + "\n<br/>")
print("Your current IP address is: " + os.environ['REMOTE_ADDR'] + "</br>")
# Print HTML footer
print("</body></html>")