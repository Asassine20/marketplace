#Connects code to MySQL Database


import pymysql

def get_connection():
    # Establish a connection
    conn = pymysql.connect(
        host="127.0.0.1",
        user="Andrew",
        password="Summer99",
        database="data_schema"
    )
    return conn
