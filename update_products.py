from bs4 import BeautifulSoup

with open("d:\\SUCHNA PRODUCTS\\KrizaFRP\\Kriza FRP\\products.html", "r", encoding="utf-8") as f:
    soup = BeautifulSoup(f.read(), "html.parser")

links = soup.find_all("a", href=True)
for link in links:
    href = link.get("href", "")
    if href.startswith("product-") and href != "product-inquiry.html":
        link.name = "div"
        del link["href"]
        
        view_details = link.find("div", class_="flex items-center justify-between border-t border-surface-variant pt-4")
        if view_details:
            view_details.decompose()

with open("d:\\SUCHNA PRODUCTS\\KrizaFRP\\Kriza FRP\\products.html", "w", encoding="utf-8") as f:
    f.write(str(soup))
