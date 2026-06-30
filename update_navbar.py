import os
import glob
import re

desktop_dropdown = """
        <div class="relative group nav-dropdown-container">
          <button class="text-on-surface-variant dark:text-primary-fixed-dim font-medium hover:text-secondary dark:hover:text-secondary-fixed-dim transition-all duration-300 flex items-center gap-1 cursor-pointer">
            Resources
            <span class="material-symbols-outlined text-[18px]">expand_more</span>
          </button>
          <div class="nav-dropdown-menu absolute left-0 top-full pt-2 w-48 transition-all duration-300 z-50">
            <div class="bg-surface border border-outline-variant rounded-md shadow-lg py-2 overflow-hidden">
              <a href="catalog.html" class="block px-4 py-2 text-sm text-on-surface-variant hover:bg-surface-container hover:text-secondary transition-colors">Catalog / Brochure</a>
            </div>
          </div>
        </div>
"""

mobile_dropdown = """
        <div class="flex flex-col gap-2">
          <button onclick="document.getElementById('mobile-resources-dropdown').classList.toggle('hidden')" class="flex items-center justify-between text-on-surface-variant font-medium w-full text-left">
            <span>Resources</span>
            <span class="material-symbols-outlined text-[18px]">expand_more</span>
          </button>
          <div id="mobile-resources-dropdown" class="hidden flex-col gap-2 pl-4 pt-1">
            <a class="text-on-surface-variant font-medium hover:text-secondary transition-colors" href="catalog.html">- Catalog / Brochure</a>
          </div>
        </div>
"""

for file in glob.glob("*.html"):
    if file == "index.html":
        continue
    
    with open(file, "r", encoding="utf-8") as f:
        content = f.read()
    
    if 'Resources' in content and 'mobile-resources-dropdown' in content:
        # Already inserted, skip
        continue

    # 1. Desktop Dropdown
    desktop_pattern = r'(<div class="hidden md:flex absolute left-1/2 -translate-x-1/2 items-center space-x-8">.*?)(<a[^>]+href="products\.html">Products</a>)'
    def repl_desktop(m):
        return m.group(1) + m.group(2) + desktop_dropdown
    
    content = re.sub(desktop_pattern, repl_desktop, content, flags=re.DOTALL)
    
    # 2. Mobile Dropdown
    mobile_pattern = r'(<div id="mobile-menu".*?)(<a[^>]+href="products\.html">Products</a>)'
    def repl_mobile(m):
        return m.group(1) + m.group(2) + mobile_dropdown
        
    content = re.sub(mobile_pattern, repl_mobile, content, flags=re.DOTALL)
    
    # 3. Remove Desktop Catalog button
    content = re.sub(r'<button onclick="openCatalogModal\(\)" class="hidden md:flex[^>]+>.*?Catalog</button>', '', content, flags=re.DOTALL)
    
    # 4. Remove Mobile Catalog button
    content = re.sub(r'<button onclick="openCatalogModal\(\)" class="bg-secondary-fixed[^>]+>.*?Catalog</button>', '', content, flags=re.DOTALL)

    with open(file, "w", encoding="utf-8") as f:
        f.write(content)

print("Updated all HTML files!")
