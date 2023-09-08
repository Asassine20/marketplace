#Makes the product_id of the card equal to the row that it is in

import pymysql
from connect import get_connection

# Establish a connection
conn = get_connection()

# Create a cursor object
cursor = conn.cursor()

# SQL query to modify the table schema and make the product_id column AUTO_INCREMENT
alter_query = "ALTER TABLE Products MODIFY COLUMN product_id INT AUTO_INCREMENT PRIMARY KEY"

# Execute the SQL query to modify the table
cursor.execute(alter_query)

# Commit the transaction to apply the changes to the database
conn.commit()

# Close the database connection
cursor.close()

conn.close()
