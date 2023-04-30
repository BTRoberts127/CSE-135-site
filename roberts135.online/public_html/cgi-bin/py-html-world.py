def main():
    # Print HTTP headers
    print("Cache-Control: no-cache")
    print("Content-type: text/html\n")
    # Print HTML document
    print("<html><head><title>Hello CGI World</title></head>\
	<body><h1 align=center>Brandon copy of Hello HTML World</h1>\
  	<hr/>")
    print("Hello World<br/>\n");
    print("This program was generated at: time\n<br/>");
    print("Your current IP address is: IP<br/>" + os.environ['REMOTE_ADDR']);
    # Print HTML footer
    print("</body></html>");