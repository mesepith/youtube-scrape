import mysql.connector
import commands
import json

mydb = mysql.connector.connect(
  host="localhost",
  user="root",
  passwd="",
  database="zahir"
)

mycursor = mydb.cursor()

mycursor.execute("SELECT * FROM ytb")

myresult = mycursor.fetchall()

# print("myresult")
# print(myresult)

for key in myresult:
	# print("key")
	# print(key)
	# print(key[0])
	unique_id = key[0]
	url = key[2]
	# print("value")
	# print(value)
	
	
	
	ytb_file_data = commands.getstatusoutput('youtube-dl --dump-json '+url)
	# ytb_file_data = commands.getstatusoutput('youtube-dl --skip-download --get-filename --get-duration --get-title --get-thumbnail --get-description '+url)
	
	print("ytb_file_data")
	print(ytb_file_data)
	
	ytb_file_data_json = json.dumps(ytb_file_data)
	# ytb_file_data_json = json.dumps(ytb_file_data, indent=4, separators=(". ", " = "))
	print("ytb_file_data_json")
	print(ytb_file_data_json)
	
	sql = "UPDATE ytb SET ytb_file_data_json = %s WHERE id = %s"
	val = (ytb_file_data_json, unique_id)

	mycursor.execute(sql, val)

	mydb.commit()




