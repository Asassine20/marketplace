# Populates the variant column in database

from connect import get_connection

def update_variant(cursor):
    variants = [
        'Cracked Ice', 'Prizm', 'Refractor', 'Signature', 'Prospect',
        'Memorabilia', 'Rookie', 'Rated Rookie', 'Autograph', 'Wave',
        'Parallel', 'Laser', 'Lazer', 'Reactive', 'Short Print', 'Serial', 'Holo'
    ]
    for variant in variants:
        query = f"UPDATE Products SET variant = %s WHERE set_name LIKE %s"
        cursor.execute(query, (variant, f'%{variant}%'))

def main():
    try:
        # Get the connection
        conn = get_connection()

        # Create a cursor
        cursor = conn.cursor()

        # Update the color_variant column
        update_variant(cursor)

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
