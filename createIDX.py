import mysql.connector
from connect import get_connection

def create_index():
    try:
        print("Attempting to get database connection...")
        conn = get_connection()
        print("Connection established.")

        print("Preparing SQL query...")
        create_number_index = "CREATE INDEX idx_number ON Products (number);"
        print("SQL query prepared.")

        print("Attempting to create cursor...")
        cursor = conn.cursor()
        print("Cursor created.")

        print("Executing SQL query...")
        cursor.execute(create_number_index)
        print("Index created successfully.")

        print("Committing changes...")
        conn.commit()
        print("Changes committed.")

        print("Closing cursor...")
        cursor.close()
        print("Cursor closed.")

        print("Closing connection...")
        conn.close()
        print("Connection closed.")

    except mysql.connector.Error as error:
        print("Error:", error)

if __name__ == "__main__":
    print("Starting script...")
    create_index()
    print("Script execution complete.")

