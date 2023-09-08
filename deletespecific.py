#Deletes specific rows from the database table

import pymysql
from connect import get_connection

# Establish a connection
conn = get_connection()

# Create a cursor object
cursor = conn.cursor()

# Define the starting set_id and ending set_id to delete within the range [starting_set_id, ending_set_id]
starting_set_id = 79578
ending_set_id = 79581


try:
    # Execute the SQL DELETE statement to remove cards within the specified range of set_ids
    sql = "DELETE FROM Products WHERE sport_id = %s"
    sport_id_value = 3
    cursor.execute(sql, (sport_id_value,))

    # Commit the transaction
    conn.commit()

    # Get the number of deleted rows (optional - for informational purposes)
    deleted_rows = cursor.rowcount
    print(f"{deleted_rows} rows deleted.")

except Exception as e:
    print("Error occurred while deleting records:", e)
    # Rollback the transaction in case of an error
    conn.rollback()

# Close the cursor and connection
cursor.close()
conn.close()

