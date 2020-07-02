import httplib, urllib
import urllib2
import simplejson
import MySQLdb
from collections import OrderedDict

# Mysql db structure stuff
# CREATE TABLE `tvs` (
  # `id` int(11) NOT NULL PRIMARY KEY AUTO_INCREMENT,
  # `rank` int(11) DEFAULT '0',
  # `tvs_username` varchar(100) NOT NULL,
  # `legion` int(1) DEFAULT '0',
  # `level` int(1) DEFAULT '0',
  # `hp` int(11) DEFAULT '0',
  # `maxhp` int(11) DEFAULT '0',
  # `ship` int(2) DEFAULT '0',
  # `score` int(11) DEFAULT '0',
  # `kills` int(4) DEFAULT '0',
  # `deaths` int(4) DEFAULT '0',
  # `online` tinyint(1) DEFAULT '0',
  # `ts` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  # `turn` int(4) NOT NULL
# ) ENGINE=InnoDB DEFAULT CHARSET=latin1;

class TVSDump():

    def __init__(self):

		#{"start":1591959600,"start_offset":3600,"now":1593521485,"now_offset":3600,"tick_length":10800,"tick":145,"secs_left":4115}
        response = urllib2.urlopen("https://www.violetsector.com/json/timer.php")
        data = simplejson.load(response, object_pairs_hook=OrderedDict)
        #print(data)
        tick = int(data['tick'])
#       print(tick)
#       
        response = urllib2.urlopen("https://www.violetsector.com/test.php")
        data = simplejson.load(response, object_pairs_hook=OrderedDict)
#       print(data)
#       print(data['rankings_pilots'])

        self.db = MySQLdb.connect(host="localhost",    # your host, usually localhost
                                 user="",         # your username
                                 passwd="",  # your password
                                 db="tvs")        # name of the data base
        cursor = self.db.cursor()
        rank=1
        for value in data['rankings_pilots']:
            #print value['tvs_username'].encode('utf-8').decode('cp1252')
            sql = "INSERT INTO `tvs` (`rank`,`tvs_username`, `legion`, `level`, `hp`, `maxhp`, `ship`, `score`, `kills`, `deaths`, `online`, `turn`) VALUES (%s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s)"
            cursor.execute(sql, (rank,value['tvs_username'].encode('utf-8').decode('cp1252'),int(value['legion']),int(value['level']),int(value['hp']),int(value['maxhp']),int(value['ship']),int(value['score']),int(value['kills']),int(value['deaths']),int(value['online']),tick))
            rank += 1
        cursor.execute('COMMIT')
        self.db.close()

if __name__ == "__main__":

    bot = TVSDump()
