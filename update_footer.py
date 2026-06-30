import os
import glob
import re

# 1. Read index.html to extract the footer
with open("index.html", "r", encoding="utf-8") as f:
    index_content = f.read()

footer_match = re.search(r'(?s)<footer\b.*?>.*?</footer>', index_content)
if not footer_match:
    print("Error: Could not find footer in index.html")
    exit(1)

footer_html = footer_match.group(0)

# 2. Iterate through all other HTML files and replace their footer
html_files = glob.glob("*.html")
for file in html_files:
    if file == "index.html" or file == "catalog.html" or file == "products_test.html":
        continue
    
    with open(file, "r", encoding="utf-8") as f:
        content = f.read()
    
    # Replace the existing footer with the new footer
    new_content = re.sub(r'(?s)<footer\b.*?>.*?</footer>', footer_html, content)
    
    with open(file, "w", encoding="utf-8") as f:
        f.write(new_content)
        
print("Successfully synced footer to all HTML pages!")
