import os
import re

with open("index.html", "r", encoding="utf-8") as f:
    index_content = f.read()

footer_match = re.search(r'(?s)<footer\b.*?>.*?</footer>', index_content)
if not footer_match:
    print("Error: Could not find footer in index.html")
    exit(1)

footer_html = footer_match.group(0)

# Fix contact-us.html specifically
with open("contact-us.html", "r", encoding="utf-8") as f:
    content = f.read()

# Since it's missing </footer>, we'll match from <footer up to <script>
new_content = re.sub(r'(?s)<footer\b.*?(?=\s*<script>)', footer_html, content)

with open("contact-us.html", "w", encoding="utf-8") as f:
    f.write(new_content)

print("Successfully fixed contact-us.html footer!")
