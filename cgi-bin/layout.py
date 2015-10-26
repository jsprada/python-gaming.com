#!/usr/bin/python3
import cgitb
cgitb.enable()

from versiondiff import Changes
import cgi
from header import Menu
change = Changes()
form = cgi.FieldStorage()
if form:
    try:
        if form['search'].value == 'viewall':
            search_text = change.viewall()
            searched = ''
            searched_text = '<b>Show entire database</b>'
        else:
            change.search(form['search'].value.strip())
            search_text = change.html_output()
            searched = form['search'].value
            searched_text = '<b>Search results containing: </b>'
    except KeyError:
        search_text = change.viewall()
        searched = ''
        searched_text = '<b>Show entire database</b>'
else:
    search_text = ''
    searched = ''
    searched_text = ''
html = """Content-type: text/html\n

<html>
<head>
<title>Python Porting Guide</title>
{MENU}
</head>
<body>
<br>
Input all or a portion of the known syntax for either Python2.x or Python3.x <br />
Results are based only on version differences, not every module in existence to python<br />

<form method=POST action='portingguide.py'>
        <p><input type=text name=search placeholder="'viewall' for everything">
        <p><input type=submit value='Search'> <!-- <input type="button" onclick="change.viewall()" value="viewall"> -->
<br /><br />
{SEARCHED_TEXT}{SEARCHED}<br /><br />
{SEARCH_TEXT} <br /><br />
</body>
</html>
""".format(
        MENU=Menu().get_content(),
        SEARCHED=searched,
        SEARCH_TEXT=search_text,
        SEARCHED_TEXT=searched_text,
        )
print(html)
