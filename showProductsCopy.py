#Shows specific rows from the database

import pymysql
from connect import get_connection
from tabulate import tabulate

#Establish a connection
conn = get_connection()

#Create cursor object
cursor = conn.cursor()

#define the Select statement
sql = "SELECT * FROM Products ORDER BY set_id DESC LIMIT 1000"

# Execute the SQL query with the set_id value as a parameter
sport_id_value = 4
cursor.execute(sql)

#fetch all rows from the result set
rows = cursor.fetchall()

#Get the column names from the cursor description
column_names = [desc[0] for desc in cursor.description]

#create a list to hold the data in tabular format
table_data = []

#append column names as first row
table_data.append(column_names)

#append rows to the table data
for row in rows:
  table_data.append(row)
  
#print the table using tabulate
print(tabulate(table_data, headers="firstrow"))

#close the cursor and connection
cursor.close()
conn.close()