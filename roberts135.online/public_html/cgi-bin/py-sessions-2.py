#!/usr/bin/python3
import os

# Headers
print("Cache-Control: no-cache")
print("Content-type: text/html\r\n")

# Body - HTML
print("<html>")
print("<head><title>Python Sessions</title></head>\n")
print("<body>")
print("<h1>Python Sessions Page 2</h1>")
print("<table>")

envvars = os.environ
if "HTTP_COOKIE" in envvars and envvars["HTTP_COOKIE"][0:8] != "destroyed":
    print("<tr><td>Cookie:</td><td>" + envvars["HTTP_COOKIE"] + "</td></tr>\n")
else:
    print("<tr><td>Cookie:</td><td>None</td></tr>\n")

print("</table>")

# Links for other pages
print("<br />")
print("<a href=\"/cgi-bin/py-sessions-1.py\">Session Page 1</a>")
print("<br />")
print("<a href=\"/py-cgiform.html\">Python CGI Form</a>")
print("<br /><br />")

# Destroy Cookie button
print("<form action=\"/cgi-bin/py-destroy-session.py\" method=\"get\">")
print("<button type=\"submit\">Destroy Session</button>")
print("</form>")

print("</body>")
print("</html>")