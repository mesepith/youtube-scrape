import mysql.connector

mydb = mysql.connector.connect(
  host="localhost",
  user="root",
  passwd="",
  database="zahir"
)

mycursor = mydb.cursor()

def insertYtbData(search_word, url, vid_id, page_no, page_height):
	sql = "INSERT IGNORE INTO ytb (search_word, url, vid_id, page_no, page_height) VALUES (%s, %s, %s, %s, %s)"
	val = (search_word, url, vid_id, page_no, page_height)
	mycursor.execute(sql, val)

	mydb.commit()


