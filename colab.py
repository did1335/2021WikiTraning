!pip install requests
!pip install BeautifulSoup4

import requests
from bs4 import BeautifulSoup

res=requests.get('https://zh.wikipedia.org/wiki')
soup=BeautifulSoup(res.text,'html.parser')

print(soup.find('li')) #找第一個li

print(soup.find('li').text) #找第一個li的文字

li=soup.find_all('li')
for title in li:
  print(title.text) #印出所有li文字
