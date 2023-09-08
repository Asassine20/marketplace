#Scrapes product details from the website and adds it all into the database

import pymysql
import requests
from lxml import etree
from connect import get_connection

# Establish a connection
conn = get_connection()

# Create a cursor object
cursor = conn.cursor()

# URL of the web page to scrape
url = "http://www.sportscarddatabase.com/CardSetList.aspx?sp=4"

# Send a GET request to the URL
response = requests.get(url)

# Create an XML parser with the response content
parser = etree.HTMLParser()
tree = etree.fromstring(response.content, parser)

# XPath for the elements on the page
xpath = "//a[contains(@href, '/CardSet.aspx?sid=')]"
elements = tree.xpath(xpath)

# Initialize a variable to keep track of the last set_id processed
last_processed_set_id = 79577

# Function to extract and insert data into the "Products_copy" table
def insert_into_products_copy(link, set_id):
    # Send a GET request to the URL of the individual set
    response = requests.get(link)

    # Create an XML parser with the response content
    parser = etree.HTMLParser()
    tree = etree.fromstring(response.content, parser)

    # XPath for the elements on the set page
    set_xpath = "//a[contains(@href, '/CardItem.aspx?id=')]"
    set_elements = tree.xpath(set_xpath)




    # Extract the data and insert into the "Products_copy" table
    for idx, element in enumerate(set_elements, start=1):
        data_point = element.text.strip()
        parts = data_point.split("#")
        year_and_set = parts[0]
        number_and_name = parts[1]
        year_set = year_and_set.split()
        number_name = number_and_name.split()    
        # Check if number_name has at least one element
        try:
            number = number_name[0]
        except IndexError:
            number = ""  # Set a default value if number_name is empty
        
        # Check if number_name has at least two elements
        if len(number_name) >= 2:
            first_name = number_name[1]
        else:
            first_name = ""

        # Check if number_name has at least three elements
        if len(number_name) >= 3:
            last_name = " ".join(number_name[2:])
        else:
            last_name = ""

        player_name = f"{first_name} {last_name}"
        season = year_set[0]
        set_name = " ".join(year_set[1:])
        

        # Insert a row for each grade (10 to 1)
        for grade in range(1, 11):
            # Add the values to the "Products_copy" table using SQL INSERT statement
            sql = "INSERT INTO Products_copy (season, set_name, number, player_name, set_id, sport_id, grade) VALUES (%s, %s, %s, %s, %s, %s, %s)"
            values = (season, set_name, number, player_name, set_id, 4, grade)
            cursor.execute(sql, values)

            # Commit the transaction for each grade
            conn.commit()

            
# Reverse the order of elements to start from the bottom of the page
elements.reverse()

# Initialize last_set_id to 0
last_set_id = 79577




# Loop through the links from the specified page and populate the "Products_copy" table
for element in elements:
    link = "http://www.sportscarddatabase.com" + element.get("href")
    # Increment the set_id for each new link
    last_set_id += 1
    if last_set_id != last_processed_set_id:
        print(f"Set ID: {last_set_id}")
    insert_into_products_copy(link, last_set_id)


# Close the database connection
cursor.close()
conn.close()