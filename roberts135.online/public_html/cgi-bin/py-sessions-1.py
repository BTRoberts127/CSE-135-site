import sys
import os

# Headers
print("Cache-Control: no-cache")

# Get Name from Environment
username = sys.stdin.read()

# Check to see if a proper name was sent
if username[0] == 'u':
   name = username[9:]

# Set the cookie using a header, add extra \r\n to end headers
if len(name) > 0:
    print("Content-type: text/html")
    print("Set-Cookie: " + name + "\r\n")
else:
    print("Content-type: text/html\r\n")

# Body - HTML
print("<html>")
print("<head><title>Python Sessions</title></head>\n")
print("<body>")
print("<h1>Python Sessions Page 1</h1>")
print("<table>")

# First check for new Cookie, then Check for old Cookie
envvars = os.environ
if len(name) > 0:
    print("<tr><td>Cookie:</td><td>" + name + "</td></tr>\n")
elif 'HTTP_COOKIE' in envvars and envvars["HTTP_COOKIE"][0:8] != "destroyed":
    print("<tr><td>Cookie:</td><td>%s</td></tr>\n", envvars["HTTP_COOKIE"])
else:
    print("<tr><td>Cookie:</td><td>None</td></tr>\n")

print("</table>")

# Links for other pages
print("<br />")
print("<a href=\"/cgi-bin/py-sessions-2.py\">Session Page 2</a>")
print("<br />")
print("<a href=\"/py-cgiform.html\">Python CGI Form</a>")
print("<br /><br />")

# Destroy Cookie button
print("<form action=\"/cgi-bin/py-destroy-session.py\" method=\"get\">")
print("<button type=\"submit\">Destroy Session</button>")
print("</form>")

print("</body>")
print("</html>")