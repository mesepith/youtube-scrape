import selenium
from selenium import webdriver
import time
import string


import ytb_db_model

#chrome_options = Options()
#chrome_options.add_argument('disable_infobars')
#driver = webdriver.Chrome(chrome_options=chrome_options)

# d = webdriver.Chrome()
d = webdriver.Chrome('/var/www/html/software/chromedriver_linux64/chromedriver')

d.get("https://www.youtube.com/results?search_query=keto+diet")
#d.get("https://www.switchme.in")

#time.sleep(20)
		
		
SCROLL_PAUSE_TIME = 5

# Get scroll height
last_height = d.execute_script("return document.documentElement.scrollHeight")

page_no=0

while True:
	
	print("last_height")
	print(last_height)
	
	page_no += 1
	print('Page Number');
	print(page_no);
	
	#Get url
	contents_id = d.find_element_by_id("contents")

	for contents_elm in contents_id.find_elements_by_class_name("ytd-thumbnail"):
		
		storing_content_class = contents_elm.get_attribute("class")
		
		if storing_content_class == "yt-simple-endpoint inline-block style-scope ytd-thumbnail":
			href_link = contents_elm.get_attribute("href")
			print(href_link)	
			
			#Check if href link is from ytb
			is_vdo_link = href_link.find("https://www.youtube.com/")
			if is_vdo_link == 0 :
				
				vid_id = href_link.replace('https://www.youtube.com/watch?v=','');
				
				# insert data into database table
				ytb_db_model.insertYtbData('keto diet', href_link, vid_id, page_no, last_height)
			
			#change data class value, so that our program will not extract data inside that class, in our case we change the class value to ignoreClassByZahir		
			d.execute_script("arguments[0].setAttribute('class','ignoreClassByZahir')", contents_elm)

			
	print ("###########################################")
	
	# Scroll down to bottom
	d.execute_script("window.scrollTo(0, document.documentElement.scrollHeight);")
	

	# Wait to load page
	time.sleep(SCROLL_PAUSE_TIME)
	
	
	# Calculate new scroll height and compare with last scroll height
	new_height = d.execute_script("return document.documentElement.scrollHeight")
	if new_height == last_height:
		break
	last_height = new_height


	
	
