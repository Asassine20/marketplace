# Populates the color variant column in database

from connect import get_connection

def update_color_variant(cursor):
    colors = [
        'Red', 'Blue', 'Chrome', 'Gold', 'Silver', 'Black',
        'Green', 'Pink', 'Diamond', 'Orange', 'Purple', 'Bronze',
        'Yellow', 'Red White And Blue', 'Lime Green', 'White'
    ]
    for color in colors:
        query = f"UPDATE Products SET color_variant = %s WHERE set_name LIKE %s"
        cursor.execute(query, (color, f'%{color}%'))

def main():
    try:
        # Get the connection
        conn = get_connection()

        # Create a cursor
        cursor = conn.cursor()

        # Update the color_variant column
        update_color_variant(cursor)

        # Commit the transaction
        conn.commit()

    except Exception as e:
        print(f"An error occurred: {e}")
    finally:
        # Close the cursor and connection
        cursor.close()
        conn.close()

if __name__ == "__main__":
    main()
