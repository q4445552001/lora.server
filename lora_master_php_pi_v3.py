#!/usr/bin/env python
# -*- coding: UTF-8 -*-

import serial , time , datetime , os , urllib2 , urllib

mysqlip = "http://192.168.3.12/lora.php"
path = "/var/camera/"
updatefile = path + "updata.txt"
errorlog = path + "MasterLora_Error_log.txt"
disk="/dev/root"
warn = 80

logtime = time.strftime("%Y%m%d_%H%M")
os.system("touch %s"%(path) + "MasterLora_log_%s.txt"%(logtime))

ser = serial.Serial("/dev/ttyAMA0", baudrate=115200, timeout=0.5)
ser.open #open serial
LED_before_staus = [8,8,8,8,8,8,8,8,8]

try :
	while True:
		disksize = os.popen(('df -h | grep %s')%(disk)).read()
		size = int(disksize.split("G  ")[3].strip("% /\n"))

		if size > warn:
			buff = os.popen("ls -ltr %s*.txt | head -n 1"%(path)).read()
			os.system("sudo rm %s"%(path) + "%s"%(buff.split(path)[1].rstrip()))

		if ser.read() != "": #沒有空白時讀取
			nowtime = str(datetime.datetime.now()) 
			nowtime = nowtime.replace("-","/") #符號替換
			logdata = nowtime[:-7] + " : " + (ser.readline().strip()) #回傳值 #strip 刪除非字元符號
			print logdata
			filename = path + "MasterLora_log_%s.txt"%(logtime) #log路徑
			file = open(filename,"a+") #開啟檔案並寫入到最後一行
			lines = len(file.readlines()) #檢查行數
			file.writelines(logdata + "\n")#寫入 logdata 至 file
			file.close() #關閉檔案
			if lines > 100000 : 
				logtime = time.strftime("%Y%m%d_%H%M") #log時間
				os.system("touch %s"%(path) + "MasterLora_log_%s.txt"%(logtime)) #創建log
			try :
				if logdata.find("Node") != -1 and logdata.find("AcSip") == -1 :
					ipdata = logdata.split(" ")
					databuff = logdata.split(",")[2]
					if LED_before_staus[int(ipdata[9])] != databuff and int(databuff) < 8 and int(databuff) >= 0 :
						LED_before_staus[int(ipdata[9])] = databuff
						send_data = nowtime[:-7] + " " + (ipdata[5].zfill(3)) + (ipdata[7].zfill(3)) + (ipdata[9].zfill(3)) + " " + databuff
						post_data = {"data":send_data}
						try:
							webside = urllib2.urlopen(url = mysqlip,data = urllib.urlencode(post_data), timeout=0.5) #post_data 轉 urlencode 再丟到 URL
							#print webside.read() # 回傳 URL 值
						except Exception, serverecho:
							#伺服器狀態印出及轉存
							logdata = nowtime[:-7] + " : (ERROR) %s (DATA Write BUFF)"%(str(serverecho))
							print logdata
							file = open(filename,"a+")
							file.writelines(logdata + "\n")
							file.close()
							#存入暫存
							fo = open(updatefile, "a+")
							fo.write(send_data + "\n")
							#data = fo.readlines()[-1]
							fo.close()
			except :
				time.sleep(0.5)

		try :
			count = len(open(updatefile,"rU").readlines()) #以讀方式開啟
			fp = open(updatefile,"rw+")
			for index in range(int(count)):
				send_data = fp.next()
				post_data = {"data":send_data}
				webside = urllib2.urlopen(url = mysqlip,data = urllib.urlencode(post_data), timeout=0.5)
				#print webside.read()
				os.system("sudo rm -rf %s"%(updatefile))
		except :
			continue
	time.sleep(0.1)

except Exception,echo: 
	nowtime = str(datetime.datetime.now()) 
	logdata = nowtime[:-7] + " : (ERROR) %s "%(str(echo))
	print logdata
	file = open(errorlog,"a+")
	file.writelines(logdata + "\n")
	file.close()

finally :
	print "Python ERROR"